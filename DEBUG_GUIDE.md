# Laravel 디버깅 가이드

> REVIEW 프로젝트 전용. 로컬 개발 환경(`APP_DEBUG=true`)에서만 사용.  
> **운영 서버에 디버깅 코드 커밋 금지.**

---

## 1. 쿼리 디버깅

### SQL 즉시 확인 (dd)
```php
$query = Race::with('reviews')->where('is_active', true);

dd($query->toSql(), $query->getBindings());
// 출력 예: "select * from "review"."races" where "is_active" = ?"
//          [true]
```

### 실행 멈추지 않고 출력 (dump)
```php
dump([
    'sql'      => $query->toSql(),
    'bindings' => $query->getBindings(),
    'count'    => $query->count(),
]);
```

### 여러 쿼리 한꺼번에 기록 (QueryLog)
```php
use Illuminate\Support\Facades\DB;

DB::enableQueryLog();

$races = Race::with('reviews')->get();  // 실행

dd(DB::getQueryLog());
// [['query' => '...', 'bindings' => [...], 'time' => 2.34], ...]
```

### 로그 파일로 기록 (화면 깨질 때)
```php
use Illuminate\Support\Facades\Log;

Log::debug('query debug', [
    'sql'      => $query->toSql(),
    'bindings' => $query->getBindings(),
    'count'    => $query->count(),
]);
// 확인: storage/logs/laravel.log
```

---

## 2. Controller 디버깅

### Request 입력값 확인
```php
public function store(Request $request)
{
    dd($request->all());           // 전체 입력값
    dd($request->validated());     // validate() 통과한 값
    dd($request->headers->all());  // 헤더 확인
    dd($request->user());          // 현재 로그인 유저
}
```

### 중간 변수 확인
```php
public function show(Race $race)
{
    $reviews = $race->reviews()->with('user')->get();

    dump($reviews->count());
    dump($reviews->first()?->toArray());

    return view('races.show', compact('race', 'reviews'));
}
```

---

## 3. 변수 / 모델 디버깅

### 단일 모델 확인
```php
$race = Race::find(1);

dd($race);                    // 전체 속성
dd($race->toArray());         // 배열로 변환
dd($race->getAttributes());   // DB 원본 값 (accessor 거치기 전)
dd($race->getDirty());        // 변경된 필드만
```

### 컬렉션 확인
```php
$races = Race::limit(5)->get();

dd($races->count());
dd($races->pluck('name', 'id'));    // id => name 맵
dd($races->toArray());
```

---

## 4. CORE API 호출 디버깅

```php
use Illuminate\Support\Facades\Log;

// RaceService.php 또는 Controller에서
$response = Http::post(config('services.core_api.url') . '/api/summarize', [...]);

Log::debug('CORE API response', [
    'status' => $response->status(),
    'body'   => $response->json(),
]);

if ($response->failed()) {
    dd($response->status(), $response->body());
}
```

---

## 5. Blade 뷰 디버깅

```blade
{{-- 변수 확인 --}}
{{ dd($race) }}

{{-- 조건 없이 덤프 --}}
@php dump($reviews->toArray()) @endphp

{{-- 변수 존재 여부 확인 --}}
@php dd(isset($race), get_defined_vars()) @endphp
```

---

## 6. 로그 파일 실시간 확인 (터미널)

```powershell
# Windows PowerShell (로컬)
Get-Content C:\src\projects\review\storage\logs\laravel.log -Tail 50 -Wait
```

```bash
# EC2 서버
tail -f /var/www/review-site/storage/logs/laravel.log
```

---

## 7. config / env 값 확인

```php
dd(config('database.connections.pgsql'));  // DB 연결 설정
dd(config('services.core_api'));           // CORE API 설정
dd(env('APP_ENV'));                        // .env 값 직접 확인 (비추: config() 우선)
```

---

## 주의사항

- `dd()` 는 페이지 렌더링을 완전히 멈춤 → 흐름 확인만 할 땐 `dump()` 사용
- `DB::enableQueryLog()` 는 메모리를 쓰므로 확인 후 반드시 제거
- `storage/logs/laravel.log` 가 너무 커지면: `php artisan log:clear`
