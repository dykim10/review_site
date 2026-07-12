<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== §9 V6 — REVIEW 예외 핸들러 ===\n";

$before = App\Models\SystemLog::where('source', 'review')->where('category', 'app_error')->count();
report(new RuntimeException('V6 검증: REVIEW app_error 테스트'));
$after = App\Models\SystemLog::where('source', 'review')->where('category', 'app_error')->count();

$latest = App\Models\SystemLog::where('source', 'review')
    ->where('category', 'app_error')
    ->latest('id')
    ->first();

$ok = ($after === $before + 1)
    && $latest
    && isset($latest->context['exception'])
    && isset($latest->context['file']);

echo ($ok ? 'PASS' : 'FAIL') . ": app_error {$before} -> {$after}\n";
if ($latest) {
    echo "  message: {$latest->message}\n";
    echo "  context keys: " . implode(', ', array_keys($latest->context ?? [])) . "\n";
}

// 404 제외
$before404 = App\Models\SystemLog::where('source', 'review')->count();
report(new Symfony\Component\HttpKernel\Exception\NotFoundHttpException('V6 404'));
$after404 = App\Models\SystemLog::where('source', 'review')->count();
$excluded = $before404 === $after404;
echo ($excluded ? 'PASS' : 'FAIL') . ": 404 excluded (count {$before404} -> {$after404})\n";

exit($ok && $excluded ? 0 : 1);
