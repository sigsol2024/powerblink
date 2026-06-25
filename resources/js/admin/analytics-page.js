/**
 * Alpine component factory for admin analytics (see x-data="analyticsPage({...})").
 * @param {object} config
 * @param {string} [config.trafficSubTemplate]
 * @param {string} config.endpoint
 * @param {number} config.range
 * @param {string} config.startDate
 * @param {string} config.endDate
 * @param {object} config.initial
 */
export function createAnalyticsPage(config) {
    return {
        trafficSubTemplate: config.trafficSubTemplate,
        endpoint: config.endpoint,
        range: config.range,
        startDate: config.startDate,
        endDate: config.endDate,
        state: config.initial,
        /** @type {null | { initCharts: Function, updateCharts: Function, destroyCharts: Function }} */
        charts: null,

        subTraffic() {
            const n = this.state?.rangeDays != null ? this.state.rangeDays : this.range;
            return (this.trafficSubTemplate || '').replace('__N__', String(n));
        },

        formatKpiDelta(v) {
            if (v === null || v === undefined) {
                return '—';
            }
            if (v === 0) {
                return '0%';
            }
            return (v > 0 ? '+' : '') + v.toFixed(1) + '%';
        },

        kpiPillClass(v) {
            if (v === null || v === undefined) {
                return 'border border-[#0a1628]/06 bg-surface-container-low/90 text-on-surface-variant';
            }
            if (v < 0) {
                return 'border border-error/20 bg-error/10 text-error';
            }
            if (v > 0) {
                return 'anx-pill-positive border border-on-tertiary-container/20 bg-gradient-to-br from-on-tertiary-container/14 to-on-tertiary-container/5 text-on-tertiary-container';
            }
            return 'border border-[#0a1628]/06 bg-surface-container-low/90 text-on-surface-variant';
        },

        kpiBarWidth(slot) {
            const s = this.state?.summary || {};
            const d = Math.max(1, Number(this.range) || 90);
            const v = Number(s.total_views || 0);
            const u = Number(s.unique_sessions || 0);
            const p = Number(s.unique_pages || 0);
            if (slot === 1) {
                return Math.min(100, Math.round((v / d / 5) * 100)) || 4;
            }
            if (slot === 2) {
                return Math.min(100, Math.round((u / d / 2) * 100)) || 4;
            }
            if (slot === 3) {
                return Math.min(100, Math.round(p * 4)) || 4;
            }
            return 0;
        },

        topProgramTitle() {
            const row = (this.state.topPrograms && this.state.topPrograms[0]) || null;
            if (!row) {
                return '—';
            }
            return row.label || row.route_name || '—';
        },

        topProgramViews() {
            const row = (this.state.topPrograms && this.state.topPrograms[0]) || null;
            return row ? row.views : 0;
        },

        deviceDotClass(i) {
            return ['bg-primary-container', 'bg-on-tertiary-container', 'bg-surface-container-high'][i % 3];
        },

        pathTitle(row) {
            const p = String(row.path || '/');
            const label = row.label;
            if (p === '/' && label) {
                return label;
            }
            return p.startsWith('/') ? p : '/' + p;
        },

        performanceScore(row) {
            const v = Number(row.views || 0);
            const s = Number(row.sessions || 0);
            if (!s || !v) {
                return 0;
            }
            return Math.max(0, Math.min(100, Math.round((s / v) * 100)));
        },

        perfBarClass(n) {
            if (n >= 90) {
                return 'bg-gradient-to-r from-on-tertiary-container to-[#8a6645]';
            }
            if (n >= 50) {
                return 'bg-gradient-to-r from-primary-container to-[#152a45]';
            }
            return 'bg-gradient-to-r from-primary-container/75 to-primary-container/55';
        },

        perfTextClass(n) {
            if (n >= 90) {
                return 'text-on-tertiary-container';
            }
            return 'text-m3-ink';
        },

        num(v) {
            return new Intl.NumberFormat().format(v || 0);
        },

        bounceProxy(row) {
            const views = Number(row.views || 0);
            const sessions = Number(row.sessions || 0);
            if (!sessions) {
                return 0;
            }
            const depth = views / sessions;
            return Math.max(0, Math.min(100, Math.round((1 / depth) * 100)));
        },

        engagementRatio() {
            if (this.state.engagementRatio?.percent != null) {
                return Number(this.state.engagementRatio.percent);
            }
            const bounce = Number(this.state.summary?.bounce_rate || 0);
            return Math.max(0, Math.min(100, 100 - bounce));
        },

        engagementLabel() {
            return this.state.engagementRatio?.label || '';
        },

        hasProgramViews() {
            return (this.state.topPrograms || []).some((r) => r.route_name);
        },

        async initCharts() {
            const mod = await import('./analytics-charts.js');
            this.charts = mod;
            await mod.initCharts({ state: this.state, refs: this.$refs });
        },

        async load() {
            const qs = new URLSearchParams({
                range: this.range,
                start_date: this.startDate,
                end_date: this.endDate,
            });
            const res = await fetch(`${this.endpoint}?${qs.toString()}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            });
            if (!res.ok) {
                return;
            }
            this.state = await res.json();
            this.$nextTick(() => {
                if (this.charts) {
                    this.charts.updateCharts(this.state);
                }
            });
        },

        applyPreset(days) {
            this.range = days;
            const end = new Date();
            const start = new Date();
            start.setDate(end.getDate() - (days - 1));
            this.startDate = start.toISOString().slice(0, 10);
            this.endDate = end.toISOString().slice(0, 10);
            this.load();
        },
    };
}
