<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * 2026_06_13_200000_fix_races_is_domestic_country 의 도시→국가 매핑에
 * 'Belgrade'가 빠져있어 벨그라드 콤트레이드 마라톤이 국내(is_domestic=true)로
 * 잘못 분류된 채 남아있었음. 또한 REVIEW v2 기획상 WA 인증 국내 4대 대회
 * (서울/대구/경주/군산) 중 경주마라톤이 races에 등록되어 있지 않았음.
 * 로컬 테스트 DB에는 tinker로 직접 반영했으나, 운영 DB(별도 Supabase 프로젝트)는
 * 반영되지 않으므로 멱등성 마이그레이션으로 작성.
 */
return new class extends Migration
{
    public function up(): void
    {
        // 1) 벨그라드 콤트레이드 마라톤 — 해외 분류로 정정
        DB::statement("
            UPDATE review.races
            SET is_domestic = false, country = '세르비아'
            WHERE city = 'Belgrade' AND is_domestic = true
        ");

        DB::statement("
            UPDATE review.race_editions re
            SET is_domestic = r.is_domestic, country = r.country
            FROM review.races r
            WHERE re.race_id = r.id AND r.city = 'Belgrade'
        ");

        // 2) 경주마라톤 신규 등록 (이미 존재하면 스킵)
        $exists = DB::table('review.races')->where('name', '경주마라톤')->exists();

        if (! $exists) {
            $raceId = DB::table('review.races')->insertGetId([
                'name'         => '경주마라톤',
                'city'         => '경주',
                'distances'    => json_encode(['풀', '하프', '10K']),
                'is_active'    => true,
                'is_domestic'  => true,
                'country'      => '대한민국',
                'wa_label'     => 'elite',
                'is_certified' => true,
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);

            DB::table('review.race_editions')->insert([
                'race_id'        => $raceId,
                'name'           => '경주마라톤',
                'year'           => 2026,
                'race_date'      => '2026-03-15',
                'city'           => '경주',
                'is_domestic'    => true,
                'country'        => '대한민국',
                'weather_stn_id' => 136,
                'status'         => '접수전',
                'is_active'      => true,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
        }
    }

    public function down(): void
    {
        // 데이터 정합성 수정 마이그레이션 — 되돌리지 않음
    }
};
