<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seasons', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_active')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index('is_active');
        });

        Schema::create('programs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('season_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('age_group', 16);
            $table->text('description')->nullable();
            $table->unsignedBigInteger('monthly_fee')->default(0);
            $table->unsignedBigInteger('registration_fee')->default(0);
            $table->unsignedSmallInteger('max_capacity')->nullable();
            $table->unsignedTinyInteger('sessions_per_week')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('hero_image_media_id')->nullable()->constrained('media')->nullOnDelete();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['season_id', 'is_active']);
        });

        Schema::create('guardians', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('phone', 40)->nullable();
            $table->string('email');
            $table->text('address')->nullable();
            $table->string('relationship')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone', 40)->nullable();
            $table->string('emergency_contact_relationship')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('email');
        });

        Schema::create('registrations', function (Blueprint $table): void {
            $table->id();
            $table->string('reference_code', 32)->unique();
            $table->foreignId('season_id')->constrained()->cascadeOnDelete();
            $table->foreignId('program_id')->constrained()->cascadeOnDelete();
            $table->foreignId('guardian_id')->constrained()->cascadeOnDelete();
            $table->string('status', 32)->default('pending_review');
            $table->string('payment_plan', 32)->default('lump_sum');
            $table->uuid('payment_token')->nullable()->unique();
            $table->timestamp('payment_token_expires_at')->nullable();
            $table->timestamp('payment_token_used_at')->nullable();
            $table->string('player_name');
            $table->date('date_of_birth')->nullable();
            $table->string('nationality')->nullable();
            $table->string('primary_position')->nullable();
            $table->string('secondary_position')->nullable();
            $table->unsignedTinyInteger('years_experience')->nullable();
            $table->text('technical_strengths')->nullable();
            $table->text('allergies')->nullable();
            $table->text('medical_history')->nullable();
            $table->boolean('fitness_certified')->default(false);
            $table->foreignId('profile_photo_media_id')->nullable()->constrained('media')->nullOnDelete();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone', 40)->nullable();
            $table->string('emergency_contact_relationship')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('rejected_reason')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'submitted_at']);
            $table->index(['season_id', 'status']);
        });

        Schema::create('players', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('registration_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('guardian_id')->constrained()->cascadeOnDelete();
            $table->foreignId('program_id')->constrained()->cascadeOnDelete();
            $table->foreignId('season_id')->constrained()->cascadeOnDelete();
            $table->string('player_code', 32)->unique();
            $table->foreignId('photo_media_id')->nullable()->constrained('media')->nullOnDelete();
            $table->string('name');
            $table->date('date_of_birth')->nullable();
            $table->string('nationality')->nullable();
            $table->string('primary_position')->nullable();
            $table->string('secondary_position')->nullable();
            $table->unsignedTinyInteger('years_experience')->nullable();
            $table->text('technical_strengths')->nullable();
            $table->text('allergies')->nullable();
            $table->text('medical_history')->nullable();
            $table->string('status', 32)->default('active');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['season_id', 'status']);
            $table->index(['program_id', 'status']);
        });

        Schema::create('coaches', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('title')->nullable();
            $table->text('bio')->nullable();
            $table->string('specialization')->nullable();
            $table->json('certifications')->nullable();
            $table->unsignedTinyInteger('experience_years')->nullable();
            $table->string('license_level')->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 40)->nullable();
            $table->foreignId('photo_media_id')->nullable()->constrained('media')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_active', 'sort_order']);
        });

        Schema::create('training_sessions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('season_id')->constrained()->cascadeOnDelete();
            $table->foreignId('program_id')->constrained()->cascadeOnDelete();
            $table->foreignId('coach_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('session_type')->nullable();
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('location')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['season_id', 'date']);
            $table->index(['program_id', 'date']);
        });

        Schema::create('session_attendance', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('training_session_id')->constrained()->cascadeOnDelete();
            $table->foreignId('player_id')->constrained()->cascadeOnDelete();
            $table->string('status', 32)->default('present');
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->unique(['training_session_id', 'player_id']);
        });

        Schema::create('performance_reports', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('season_id')->constrained()->cascadeOnDelete();
            $table->foreignId('player_id')->constrained()->cascadeOnDelete();
            $table->foreignId('coach_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedTinyInteger('passing')->nullable();
            $table->unsignedTinyInteger('dribbling')->nullable();
            $table->unsignedTinyInteger('speed')->nullable();
            $table->unsignedTinyInteger('fitness')->nullable();
            $table->unsignedTinyInteger('discipline')->nullable();
            $table->unsignedTinyInteger('teamwork')->nullable();
            $table->decimal('overall_score', 5, 2)->nullable();
            $table->text('comments')->nullable();
            $table->timestamp('reported_at')->nullable();
            $table->timestamps();

            $table->index(['player_id', 'reported_at']);
        });

        Schema::create('registration_payments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('registration_id')->constrained()->cascadeOnDelete();
            $table->foreignId('player_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('season_id')->constrained()->cascadeOnDelete();
            $table->string('type', 32);
            $table->string('provider', 32)->default('paystack');
            $table->string('reference')->unique();
            $table->string('status', 32)->default('pending');
            $table->unsignedBigInteger('amount');
            $table->string('currency', 3)->default('NGN');
            $table->json('gateway_payload')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['registration_id', 'status']);
        });

        Schema::create('academy_payments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('player_id')->constrained()->cascadeOnDelete();
            $table->foreignId('season_id')->constrained()->cascadeOnDelete();
            $table->string('type', 32);
            $table->string('provider', 32)->default('paystack');
            $table->string('reference')->unique();
            $table->string('status', 32)->default('pending');
            $table->unsignedBigInteger('amount');
            $table->string('currency', 3)->default('NGN');
            $table->json('gateway_payload')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['player_id', 'status']);
        });

        Schema::create('installment_plans', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('registration_id')->constrained()->cascadeOnDelete();
            $table->foreignId('player_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedBigInteger('amount');
            $table->date('due_date');
            $table->string('status', 32)->default('pending');
            $table->foreignId('registration_payment_id')->nullable()->constrained('registration_payments')->nullOnDelete();
            $table->timestamps();

            $table->index(['registration_id', 'due_date']);
        });

        Schema::create('player_documents', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('player_id')->constrained()->cascadeOnDelete();
            $table->foreignId('registration_id')->nullable()->constrained()->nullOnDelete();
            $table->string('document_type', 64);
            $table->foreignId('media_id')->constrained('media')->cascadeOnDelete();
            $table->string('status', 32)->default('pending');
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->index(['player_id', 'document_type']);
        });

        Schema::create('tournaments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('season_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('category')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('location')->nullable();
            $table->text('description')->nullable();
            $table->string('status', 32)->default('upcoming');
            $table->unsignedSmallInteger('max_teams')->nullable();
            $table->foreignId('featured_image_media_id')->nullable()->constrained('media')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['season_id', 'status']);
        });

        Schema::create('tournament_squads', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tournament_id')->constrained()->cascadeOnDelete();
            $table->foreignId('player_id')->constrained()->cascadeOnDelete();
            $table->string('position')->nullable();
            $table->timestamps();

            $table->unique(['tournament_id', 'player_id']);
        });

        Schema::create('announcements', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('season_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->text('body');
            $table->string('audience')->default('all');
            $table->string('channel', 32)->default('in_app');
            $table->timestamp('published_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('published_at');
        });

        Schema::create('gallery_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('media_id')->constrained('media')->cascadeOnDelete();
            $table->string('title')->nullable();
            $table->string('category', 64)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();

            $table->index(['is_published', 'sort_order']);
        });

        Schema::create('leadership_members', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('title')->nullable();
            $table->text('bio')->nullable();
            $table->foreignId('photo_media_id')->nullable()->constrained('media')->nullOnDelete();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index('sort_order');
        });

        Schema::create('timeline_events', function (Blueprint $table): void {
            $table->id();
            $table->unsignedSmallInteger('year');
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['year', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('timeline_events');
        Schema::dropIfExists('leadership_members');
        Schema::dropIfExists('gallery_items');
        Schema::dropIfExists('announcements');
        Schema::dropIfExists('tournament_squads');
        Schema::dropIfExists('tournaments');
        Schema::dropIfExists('player_documents');
        Schema::dropIfExists('installment_plans');
        Schema::dropIfExists('academy_payments');
        Schema::dropIfExists('registration_payments');
        Schema::dropIfExists('performance_reports');
        Schema::dropIfExists('session_attendance');
        Schema::dropIfExists('training_sessions');
        Schema::dropIfExists('coaches');
        Schema::dropIfExists('players');
        Schema::dropIfExists('registrations');
        Schema::dropIfExists('guardians');
        Schema::dropIfExists('programs');
        Schema::dropIfExists('seasons');
    }
};
