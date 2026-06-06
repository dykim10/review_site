<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('nickname')->nullable();
                $table->string('email_hash')->nullable()->unique();
                $table->text('email_enc')->nullable();
                $table->text('name_enc')->nullable();
                $table->string('password')->nullable();
                $table->timestamp('email_verified_at')->nullable();
                $table->rememberToken();
                $table->unsignedBigInteger('crew_id')->nullable();
                $table->unsignedBigInteger('branch_id')->nullable();
                $table->unsignedBigInteger('group_id')->nullable();
                $table->string('role', 30)->default('member');
                $table->boolean('is_beta')->default(false);
                $table->string('invite_code')->nullable();
                $table->timestamp('last_login_at')->nullable();
                $table->timestamps();
            });
        } else {
            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'nickname'))          $table->string('nickname')->nullable()->after('id');
                if (!Schema::hasColumn('users', 'email_hash'))        $table->string('email_hash')->nullable()->unique();
                if (!Schema::hasColumn('users', 'email_enc'))         $table->text('email_enc')->nullable();
                if (!Schema::hasColumn('users', 'name_enc'))          $table->text('name_enc')->nullable();
                if (!Schema::hasColumn('users', 'email_verified_at')) $table->timestamp('email_verified_at')->nullable();
                if (!Schema::hasColumn('users', 'remember_token'))    $table->rememberToken();
                if (!Schema::hasColumn('users', 'crew_id'))           $table->unsignedBigInteger('crew_id')->nullable();
                if (!Schema::hasColumn('users', 'branch_id'))         $table->unsignedBigInteger('branch_id')->nullable();
                if (!Schema::hasColumn('users', 'group_id'))          $table->unsignedBigInteger('group_id')->nullable();
                if (!Schema::hasColumn('users', 'role'))              $table->string('role', 30)->default('member');
                if (!Schema::hasColumn('users', 'is_beta'))           $table->boolean('is_beta')->default(false);
                if (!Schema::hasColumn('users', 'invite_code'))       $table->string('invite_code')->nullable();
                if (!Schema::hasColumn('users', 'last_login_at'))     $table->timestamp('last_login_at')->nullable();
            });
        }

        if (!Schema::hasTable('password_reset_tokens')) {
            Schema::create('password_reset_tokens', function (Blueprint $table) {
                $table->string('email')->primary();
                $table->string('token');
                $table->timestamp('created_at')->nullable();
            });
        }

        if (!Schema::hasTable('sessions')) {
            Schema::create('sessions', function (Blueprint $table) {
                $table->string('id')->primary();
                $table->foreignId('user_id')->nullable()->index();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->longText('payload');
                $table->integer('last_activity')->index();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
    }
};
