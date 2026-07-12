<?php

namespace App\Services;

use App\Models\SystemLog;
use Illuminate\Support\Facades\Log;

class SystemLogService
{
    private const LEVELS = ['info', 'warning', 'error'];

    public static function log(string $category, string $level, string $message, array $context = []): void
    {
        try {
            SystemLog::create([
                'source'   => 'review',
                'category' => $category,
                'level'    => in_array($level, self::LEVELS, true) ? $level : 'info',
                'message'  => self::sanitizeText($message),
                'context'  => self::sanitizeContext($context),
            ]);
        } catch (\Throwable $e) {
            try {
                Log::channel('single')->error("system_logs insert 실패: {$e->getMessage()}", [
                    'original_message' => mb_substr($message, 0, 500),
                ]);
            } catch (\Throwable) {
                error_log('system_logs 기록 완전 실패');
            }
        }
    }

    public static function error(string $category, string $message, array $context = []): void
    {
        self::log($category, 'error', $message, $context);
    }

    private static function sanitizeText(string $text): string
    {
        $clean = str_replace("\0", '', $text);
        $clean = mb_convert_encoding($clean, 'UTF-8', 'UTF-8');

        return mb_substr($clean, 0, 2000);
    }

    private static function sanitizeContext(array $context): array
    {
        $result = [];
        foreach ($context as $key => $value) {
            if (is_string($value)) {
                $result[$key] = self::sanitizeText($value);
            } elseif (is_scalar($value) || is_null($value)) {
                $result[$key] = $value;
            } elseif (is_array($value)) {
                $result[$key] = self::sanitizeContext($value);
            } else {
                $result[$key] = '[' . get_debug_type($value) . ']';
            }
        }

        return $result;
    }
}
