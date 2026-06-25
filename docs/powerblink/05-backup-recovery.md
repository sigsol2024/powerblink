# PowerBlink FC — Backup & Recovery Plan

**Document:** `05-backup-recovery.md`  
**Version:** 1.0  
**Status:** Phase 0.5 specification (pre-uploads)  
**Last updated:** 2026-06-24

This document defines the backup and recovery strategy for PowerBlink FC Academy Management System. It is a **required deliverable before Phase 2** accepts parent uploads (passport photos, birth certificates, medical clearance documents).

Phase 0.5 is documentation and hosting decisions only — no application code changes.

---

## Table of contents

1. [Purpose and scope](#purpose-and-scope)
2. [Data classification](#data-classification)
3. [Database backup policy](#database-backup-policy)
4. [Media backup policy](#media-backup-policy)
5. [Retention policy](#retention-policy)
6. [Storage and off-site replication](#storage-and-off-site-replication)
7. [Restore procedure](#restore-procedure)
8. [Pre-launch checklist](#pre-launch-checklist)
9. [Sensitive data handling](#sensitive-data-handling)
10. [Disaster recovery scenarios](#disaster-recovery-scenarios)
11. [Optional tooling](#optional-tooling)
12. [Roles and responsibilities](#roles-and-responsibilities)

---

## Purpose and scope

Once parents upload player documents and medical information through the registration wizard, data loss becomes a **regulatory, operational, and reputational risk**. This plan ensures:

- Recoverable database state at known points in time
- Recoverable media files referenced by the `media` table and `player_documents`
- Documented, tested restore procedure before production uploads
- Appropriate handling of sensitive personal and medical data in backups

### In scope

| Asset | Location |
|-------|----------|
| MySQL/MariaDB database | All academy and infrastructure tables |
| Player and coach photos | `public/asset/images/powerblink/players/`, `coaches/` |
| Player documents | `public/asset/images/powerblink/documents/` |
| CMS and gallery media | `public/asset/images/powerblink/cms/`, `gallery/`, etc. |
| Design assets | `public/asset/images/powerblink/` |
| Application configuration | `.env` (stored separately, encrypted — not in public backups) |

### Out of scope (Phase 0.5)

- Paystack transaction dispute recovery (handled via Paystack dashboard + `registration_payments` records)
- Email delivery logs (provider retention)
- Real-time replication / high-availability clustering

---

## Data classification

| Classification | Examples | Backup encryption | Access |
|----------------|----------|-------------------|--------|
| **Public** | Gallery images, coach bios, program descriptions | Standard | Staff + public |
| **Internal** | Attendance records, performance scores, payment amounts | Standard | Staff only |
| **Confidential** | Guardian contact details, player addresses | Encrypted at rest recommended | Admin + assigned coach |
| **Sensitive** | Medical history, allergies, medical clearance documents, birth certificates | **Encrypted at rest required** | Admin only; coach view restricted by policy |

Sensitive fields in database: `registrations.allergies`, `registrations.medical_history`, `players.allergies`, `players.medical_history`, and all `player_documents` files.

---

## Database backup policy

### Frequency

| Backup type | Frequency | Trigger | Retention |
|-------------|-----------|---------|-----------|
| **Automated daily** | Every day at 02:00 WAT (low-traffic window) | Cron / hosting panel | 30 days |
| **Pre-deploy** | Before every production deployment | CI/CD or manual | 7 days |
| **Pre-migration** | Before ecommerce table drop (Migration Step 5) | Manual | 90 days |
| **Weekly full** | Sunday 03:00 WAT | Cron | 12 weeks |
| **Monthly archive** | 1st of month | Cron | 12 months |

### Tool options

Choose one based on hosting environment:

| Tool | Best for | Command example |
|------|----------|-----------------|
| **Hosting panel** (cPanel, Plesk, Cloudways) | Managed shared/VPS hosting | Panel → Backup → Schedule |
| **mysqldump** | VPS/dedicated with SSH | See below |
| **spatie/laravel-backup** | Laravel-native; not installed today | Evaluate in Phase 0.5 |
| **Cloud provider snapshots** | AWS RDS, DigitalOcean Managed DB | Provider console |

### Recommended mysqldump command

```bash
#!/bin/bash
# /opt/scripts/powerblink-db-backup.sh
# Run as cron: 0 2 * * * /opt/scripts/powerblink-db-backup.sh

DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/var/backups/powerblink/db"
DB_NAME="powerblink"
DB_USER="backup_user"
RETENTION_DAYS=30

mkdir -p "$BACKUP_DIR"

mysqldump \
  --user="$DB_USER" \
  --single-transaction \
  --routines \
  --triggers \
  --hex-blob \
  "$DB_NAME" \
  | gzip > "$BACKUP_DIR/powerblink_${DATE}.sql.gz"

# Upload to off-site storage
# aws s3 cp "$BACKUP_DIR/powerblink_${DATE}.sql.gz" s3://powerblink-backups/db/

# Prune local files older than retention
find "$BACKUP_DIR" -name "*.sql.gz" -mtime +$RETENTION_DAYS -delete
```

### Backup verification

After each automated backup:

1. Confirm file exists and size > 0
2. Confirm off-site upload succeeded (if configured)
3. Log result to monitoring or email alert on failure

Weekly: decompress and verify header contains expected table names (`registrations`, `players`, `registration_payments`).

---

## Media backup policy

Media files are **not** included in database dumps. They require a separate backup job.

### Directories to include

```
public/asset/images/powerblink/     # All subdirectories (recursive)
public/asset/images/powerblink/     # PowerBlink FC design assets (91 JPGs)
storage/app/                        # Private uploads if used in Phase 2+
```

### Frequency

| Backup type | Frequency | Notes |
|-------------|-----------|-------|
| **Incremental sync** | Daily (after DB backup) | rsync or rclone to off-site |
| **Full archive** | Weekly | tar.gz snapshot |
| **Pre-migration** | Before ecommerce drop | One-time full copy |

### Recommended rsync command

```bash
#!/bin/bash
# /opt/scripts/powerblink-media-backup.sh
# Run as cron: 30 2 * * * /opt/scripts/powerblink-media-backup.sh

SOURCE="/var/www/powerblink/public/asset/images/"
DEST="/var/backups/powerblink/media/"
REMOTE="s3://powerblink-backups/media/"   # or rsync remote

rsync -av --delete "$SOURCE" "$DEST"
# rclone sync "$DEST" "$REMOTE"
```

### Consistency with database

Media backup should run **after** the database backup completes on the same schedule. This minimizes orphan files (media without DB reference) and missing files (DB reference without media).

For critical restores, quiesce uploads briefly or run both backups in immediate succession during low-traffic windows.

---

## Retention policy

| Tier | Frequency | Retention | Storage location |
|------|-----------|-----------|------------------|
| Daily DB | Automated | 30 days | Local + off-site |
| Daily media | Automated | 30 days | Off-site (incremental) |
| Weekly full | Sunday | 12 weeks | Off-site |
| Monthly archive | 1st of month | 12 months | Off-site cold storage |
| Pre-migration | One-time | 90 days minimum | Off-site, labeled |

### GDPR / data subject requests

When a guardian requests data deletion:

1. Soft-delete records in application (see ERD SoftDeletes policy)
2. Do **not** immediately purge backups — retain per policy above
3. Document deletion request in `admin_audit_trails`
4. On next monthly archive rotation, ensure deleted subject data is not carried forward if legally required

Consult local data protection requirements (Nigeria NDPR) for specific retention limits.

---

## Storage and off-site replication

### Requirements

- Backups must exist in **at least two geographic locations** (production server + off-site)
- Off-site storage encrypted at rest (S3 SSE, Backblaze B2 encryption, etc.)
- Access credentials stored in secrets manager — not in repository
- Backup bucket separate from production application credentials

### Recommended off-site options

| Provider | Use case |
|----------|----------|
| AWS S3 + Glacier | Scalable; lifecycle rules for monthly archives |
| Backblaze B2 | Cost-effective media sync |
| Hosting provider backup storage | Simplest if included in plan |
| Separate VPS rsync target | Budget option |

---

## Restore procedure

**Test this procedure on staging before Phase 2 file uploads go live.** Document the test date and result.

### Scenario A — Full database restore

**Estimated time:** 15–60 minutes depending on database size.

1. **Announce maintenance** — Put application in maintenance mode:
   ```bash
   php artisan down --message="Restoring from backup"
   ```

2. **Identify backup** — Select target `powerblink_YYYYMMDD_HHMMSS.sql.gz` from off-site storage.

3. **Download backup** to staging/production server:
   ```bash
   aws s3 cp s3://powerblink-backups/db/powerblink_20260624_020000.sql.gz /tmp/
   ```

4. **Stop queue workers** (if running):
   ```bash
   php artisan queue:restart
   # supervisorctl stop powerblink-worker:*
   ```

5. **Restore database:**
   ```bash
   gunzip -c /tmp/powerblink_20260624_020000.sql.gz | mysql -u root -p powerblink
   ```

6. **Verify tables:**
   ```sql
   SELECT COUNT(*) FROM registrations;
   SELECT COUNT(*) FROM players;
   SELECT COUNT(*) FROM registration_payments;
   SHOW TABLES LIKE 'vehicles';  -- Should not exist post-migration
   ```

7. **Clear application cache:**
   ```bash
   php artisan config:clear
   php artisan cache:clear
   php artisan permission:cache-reset
   ```

8. **Bring application up:**
   ```bash
   php artisan up
   ```

9. **Smoke test** — See [Pre-launch checklist](#pre-launch-checklist).

### Scenario B — Media restore

1. Identify target media backup (daily sync or weekly archive).

2. Restore to staging path first:
   ```bash
   rsync -av /var/backups/powerblink/media/powerblink/ /var/www/powerblink/public/asset/images/powerblink/
   rsync -av /var/backups/powerblink/media/powerblink/ /var/www/powerblink/public/asset/images/powerblink/
   ```

3. Verify `media` table paths resolve:
   ```sql
   SELECT id, path FROM media WHERE path LIKE '%powerblink%' LIMIT 10;
   ```
   Confirm files exist on disk for sampled rows.

4. Check `player_documents` media references load in admin UI.

### Scenario C — Point-in-time recovery (partial)

If only specific registrations were corrupted:

1. Restore latest backup to a **temporary database** (`powerblink_restore_temp`).
2. Export affected rows:
   ```sql
   SELECT * FROM powerblink_restore_temp.registrations WHERE id IN (42, 43);
   ```
3. Carefully merge into production — prefer application-level export/import over raw SQL merge.
4. Document in audit trail.

### Scenario D — Post-migration rollback

If ecommerce drop (Migration Step 5) needs reversal:

1. Restore pre-migration database backup (90-day retention).
2. Restore media backup from same timestamp.
3. Git revert code deletion commit.
4. Run `composer install` and `php artisan migrate:status`.

**Prevention is preferred** — always backup before Step 5.

---

## Pre-launch checklist

Complete before Phase 2 accepts file uploads in production.

| # | Task | Owner | Done |
|---|------|-------|------|
| 1 | Database backup cron configured and running | DevOps | ☐ |
| 2 | Media backup sync configured and running | DevOps | ☐ |
| 3 | Off-site replication verified (test download) | DevOps | ☐ |
| 4 | Backup failure alerting configured | DevOps | ☐ |
| 5 | **Staging restore test** — full DB restore documented | Dev + DevOps | ☐ |
| 6 | **Staging restore test** — media restore documented | Dev + DevOps | ☐ |
| 7 | Smoke test after restore: admin login | Dev | ☐ |
| 8 | Smoke test after restore: registration list loads | Dev | ☐ |
| 9 | Smoke test after restore: payment page token validation | Dev | ☐ |
| 10 | Smoke test after restore: player document file accessible | Dev | ☐ |
| 11 | `.env` secrets stored separately from backups | DevOps | ☐ |
| 12 | Backup encryption enabled for off-site storage | DevOps | ☐ |
| 13 | Doc signed off by project owner | Owner | ☐ |

### Smoke test script (post-restore)

```bash
# 1. Application responds
curl -s -o /dev/null -w "%{http_code}" https://staging.powerblinkfc.com/

# 2. Admin login page
curl -s -o /dev/null -w "%{http_code}" https://staging.powerblinkfc.com/login

# 3. Database connectivity
php artisan tinker --execute="echo \App\Models\Season::count();"
```

---

## Sensitive data handling

### Access control

| Data | Who can access in app | Who can access backups |
|------|----------------------|------------------------|
| Medical history | Super admin, assigned admin | DevOps with backup credentials only |
| Player documents | Super admin; parent (own child) | DevOps; encrypted storage |
| Payment records | Super admin; parent (own) | DevOps |
| Guardian PII | Admin; parent (own) | DevOps |

### Encryption

| Layer | Requirement |
|-------|-------------|
| Off-site backup storage | AES-256 encryption at rest (provider-managed or client-side) |
| Backup transfer | TLS (HTTPS/SFTP/rsync over SSH) |
| Application disk | OS-level disk encryption recommended for `documents/` |
| Database | Column-level encryption deferred to Phase 6 unless compliance requires earlier |

### Backup access audit

- Limit backup credential access to 2 people maximum
- Rotate backup storage credentials quarterly
- Never commit backup files or `.env` to git
- Never store backups in `public/` web-accessible directories

### Player document storage

Documents stored at `public/asset/images/powerblink/documents/` in Phase 2 should be served through authenticated routes (not direct public URLs) when implemented. Backups treat this directory as **sensitive** regardless.

---

## Disaster recovery scenarios

| Scenario | RTO target | RPO target | Procedure |
|----------|------------|------------|-----------|
| Database corruption | 1 hour | 24 hours | Scenario A |
| Accidental file deletion | 2 hours | 24 hours | Scenario B |
| Full server loss | 4 hours | 24 hours | Provision server → restore DB + media → deploy code from git |
| Ransomware | 8 hours | 24–48 hours | Restore from off-site backup predating incident; rotate all credentials |
| Paystack webhook missed | 15 minutes | 0 | Replay from Paystack dashboard; `registration_payments` idempotent by reference |

**RTO** = Recovery Time Objective (how long until service restored)  
**RPO** = Recovery Point Objective (maximum acceptable data loss)

---

## Optional tooling

### spatie/laravel-backup

Not installed in the codebase today. Evaluate during Phase 0.5:

| Pros | Cons |
|------|------|
| Laravel-native scheduling via `app/Console/Kernel.php` | Additional dependency |
| Combines DB + file backup in one package | Requires S3/FTP configuration |
| Built-in cleanup of old backups | Learning curve for team |
| Notification on failure | |

If adopted, configure to backup:

- Database: `powerblink`
- Files: `public/asset/images/powerblink`
- Exclude: `vendor/`, `node_modules/`, `storage/logs/`

Installation is a Phase 1 decision — this document stands alone without it.

---

## Roles and responsibilities

| Role | Responsibility |
|------|----------------|
| **Project owner** | Sign off on retention policy and pre-launch checklist |
| **Lead developer** | Verify restore procedure on staging; document test results |
| **DevOps / hosting** | Configure cron, off-site sync, encryption, alerting |
| **Admin staff** | Report data issues immediately; never attempt manual DB edits without backup |

---

## Related documents

- [01-database-erd.md](./01-database-erd.md) — Sensitive columns and `player_documents`
- [03-application-flows.md](./03-application-flows.md) — Document upload in registration Step 4
- [04-migration-plan.md](./04-migration-plan.md) — Pre-migration backup requirement (Step 5)

---

## Document approval

| Role | Name | Date | Signature |
|------|------|------|-----------|
| Project owner | | | |
| Lead developer | | | |
| DevOps | | | |

**Exit criteria (Phase 0.5):** Written restore procedure exists; at least one successful test restore on staging documented with date and result.
