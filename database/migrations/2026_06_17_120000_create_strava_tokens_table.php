<?php

use Illuminate\Database\Migrations\Migration;

/**
 * Strava OAuth 연동 폐기 (2026-06-17).
 * API 약관(AI/ML 금지, 유료화)으로 인해 개인 GPX 업로드 방식으로 전환.
 * 이 마이그레이션은 의도적으로 비워 두었다 — review.strava_tokens 테이블 생성 안 함.
 */
return new class extends Migration
{
    public function up(): void {}
    public function down(): void {}
};
