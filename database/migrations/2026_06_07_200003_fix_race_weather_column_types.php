<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 초기 생성 시 잘못된 타입(SMALLINT/INTEGER)으로 만들어진 컬럼을 NUMERIC으로 수정
        $alters = [
            "ALTER TABLE review.race_weather ALTER COLUMN temperature    TYPE NUMERIC(5,2) USING temperature::NUMERIC",
            "ALTER TABLE review.race_weather ALTER COLUMN humidity       TYPE NUMERIC(5,2) USING humidity::NUMERIC",
            "ALTER TABLE review.race_weather ALTER COLUMN wind_speed     TYPE NUMERIC(5,2) USING wind_speed::NUMERIC",
            "ALTER TABLE review.race_weather ALTER COLUMN wind_direction TYPE NUMERIC(5,1) USING wind_direction::NUMERIC",
            "ALTER TABLE review.race_weather ALTER COLUMN precipitation  TYPE NUMERIC(7,2) USING precipitation::NUMERIC",
        ];

        foreach ($alters as $sql) {
            DB::statement($sql);
        }

        // 타입 변경 후 스키마 캐시 갱신
        DB::statement("SELECT pg_notify('pgrst', 'reload schema')");
    }

    public function down(): void {}
};
