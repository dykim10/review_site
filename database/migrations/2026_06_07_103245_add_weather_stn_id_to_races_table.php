<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('review.races', 'weather_stn_id')) {
            Schema::table('review.races', function (Blueprint $table) {
                $table->integer('weather_stn_id')->nullable()->after('location')
                    ->comment('기상청 관측 지점코드 (자동추론 또는 관리자 지정)');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('review.races', 'weather_stn_id')) {
            Schema::table('review.races', function (Blueprint $table) {
                $table->dropColumn('weather_stn_id');
            });
        }
    }
};
