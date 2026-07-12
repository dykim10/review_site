<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemLog extends Model
{
    protected $table = 'system_logs';

    public $timestamps = false;

    protected $fillable = [
        'source',
        'category',
        'level',
        'message',
        'context',
        'created_at',
    ];

    protected $casts = [
        'context'    => 'array',
        'created_at' => 'datetime',
    ];

    public static function levelColor(string $level): string
    {
        return match ($level) {
            'error'   => 'danger',
            'warning' => 'warning',
            default   => 'gray',
        };
    }

    public static function categoryLabel(string $category): string
    {
        return match ($category) {
            'scheduler'  => '스케줄러',
            'backup'     => '백업',
            'sms'        => 'SMS',
            'crawler'    => '크롤링',
            'ai'         => 'AI',
            'auth'       => '인증',
            'app_error'  => '앱 오류',
            default      => $category,
        };
    }
}
