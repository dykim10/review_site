# Laravel 디렉토리 구조 가이드 (학습용)

> REVIEW 프로젝트 기준으로 설명합니다.

---

## 전체 구조 한눈에 보기

```
review/
├── app/                        ← 핵심 PHP 코드 (대부분의 작업이 여기서)
│   ├── Http/
│   │   ├── Controllers/        ← 요청 수신 · 응답 반환
│   │   │   ├── Admin/          ← 관리자 전용 컨트롤러
│   │   │   └── Auth/           ← 인증 컨트롤러 (Breeze 자동 생성)
│   │   ├── Middleware/         ← 요청 전처리 (인증 확인, 권한 체크 등)
│   │   └── Requests/           ← Form Request (validate 로직 분리 시)
│   ├── Models/                 ← DB 테이블 매핑 · 쿼리
│   ├── Services/               ← 비즈니스 로직 · 데이터 정제 (직접 추가)
│   └── Providers/              ← 서비스 컨테이너 등록 (고급)
│
├── bootstrap/                  ← Laravel 부팅 설정 (건드릴 일 거의 없음)
│
├── config/                     ← 설정 파일 모음
│   ├── app.php                 ← 앱 이름, 타임존, 로케일
│   ├── database.php            ← DB 연결 설정 (search_path 등)
│   ├── mail.php                ← 메일 드라이버 설정
│   └── auth.php                ← 인증 가드 설정
│
├── database/
│   ├── migrations/             ← 테이블 생성·수정 이력 (버전 관리)
│   ├── seeders/                ← 테스트용 초기 데이터 삽입
│   └── factories/              ← 테스트용 가짜 데이터 생성
│
├── resources/
│   ├── views/                  ← Blade 템플릿 (HTML 화면)
│   │   ├── races/              ← 공개 대회 화면
│   │   ├── admin/              ← 관리자 화면
│   │   └── auth/               ← 로그인·회원가입 화면
│   ├── css/                    ← 스타일 (Tailwind 소스)
│   └── js/                     ← JavaScript (Alpine.js 등)
│
├── routes/
│   ├── web.php                 ← 브라우저용 라우트 (세션·CSRF 적용)
│   ├── api.php                 ← API 라우트 (토큰 인증)
│   └── auth.php                ← Breeze 인증 라우트
│
├── storage/
│   ├── logs/                   ← 로그 파일 (laravel.log)
│   └── app/public/             ← 업로드 파일 저장소
│
├── .env                        ← 환경변수 (DB 접속, API 키 등) — Git 제외
└── artisan                     ← CLI 명령어 실행 파일
```

---

## 계층별 역할 상세 설명

### Controller (app/Http/Controllers/)
```
역할: HTTP 요청을 받아 → Service 호출 → 응답 반환
규칙: 로직 없이 얇게 유지
```
```php
class RaceController extends Controller
{
    public function __construct(private RaceService $raceService) {}

    public function index(Request $request)
    {
        // ① 요청 파라미터 수집
        // ② Service 호출
        // ③ View 반환 — 끝
        $races = $this->raceService->getPublicList($request->only('city', 'status'));
        return view('races.index', compact('races'));
    }
}
```

---

### Service (app/Services/)
```
역할: 비즈니스 로직 처리, 데이터 정제, 여러 Model 조합
규칙: 재사용 가능하도록 작성 — 여러 Controller에서 호출 가능
```
```php
class RaceService
{
    public function create(array $validated, string $distancesRaw): Race
    {
        // ① 데이터 정제 (distances 문자열 → 배열)
        $validated['distances'] = $this->parseDistances($distancesRaw);
        // ② Model에 위임
        return Race::create($validated);
    }

    private function parseDistances(string $raw): ?array
    {
        // 재사용 가능한 정제 로직
        return array_values(array_filter(array_map('trim', explode(',', $raw))));
    }
}
```

---

### Model (app/Models/)
```
역할: DB 테이블 매핑, 쿼리 실행 (Eloquent + raw)
규칙: 재사용 가능한 쿼리는 scope / static 메서드로 정의
```
```php
class Race extends Model
{
    protected $table = 'review.races';   // ← 스키마.테이블명 명시

    // Eloquent Scope — 체이닝 가능
    public function scopeActive($query) {
        return $query->where('is_active', true);
    }

    // 복잡한 JOIN은 raw query
    public static function listWithReviewStats(): Collection
    {
        return collect(DB::select("
            SELECT r.*, COUNT(rv.id) AS review_count
            FROM review.races r
            LEFT JOIN review.reviews rv ON rv.race_id = r.id
            GROUP BY r.id
        "));
    }
}
```

---

### Migration (database/migrations/)
```
역할: 테이블 구조 변경 이력 관리 (Git처럼 DB 구조를 버전 관리)
규칙: 한 번 실행된 마이그레이션은 수정하지 않고 새 파일 추가
```
```php
// php artisan make:migration 파일명
// php artisan migrate          ← 미실행 마이그레이션 실행
// php artisan migrate:rollback ← 마지막 마이그레이션 되돌리기
// php artisan migrate:status   ← 실행 상태 확인
```
```php
// 기존 테이블 수정 예시
Schema::table('users', function (Blueprint $table) {
    if (!Schema::hasColumn('users', 'nickname')) {
        $table->string('nickname')->nullable();
    }
});
```

---

### Route (routes/web.php)
```
역할: URL → Controller 매핑
```
```php
// 단일 라우트
Route::get('/races', [RaceController::class, 'index'])->name('races.index');

// Resource 라우트 (index/create/store/show/edit/update/destroy 자동 생성)
Route::resource('races', AdminRaceController::class);

// 미들웨어 그룹 (인증 필수)
Route::prefix('admin')->middleware(['auth', 'verified'])->group(function () {
    Route::resource('races', AdminRaceController::class);
});
```

---

### Blade View (resources/views/)
```
역할: HTML 출력, PHP 로직은 최소화
```
```blade
{{-- 변수 출력 (XSS 자동 방어) --}}
{{ $race->name }}

{{-- 조건 --}}
@if($race->is_active) 활성 @endif

{{-- 반복 --}}
@foreach($races as $race)
    {{ $race->name }}
@endforeach

{{-- 폼 CSRF 보호 --}}
<form method="POST" action="/races">
    @csrf
</form>

{{-- 레이아웃 상속 --}}
@extends('layouts.app')
@section('content') ... @endsection
```

---

### .env
```
역할: 환경별 설정값 (절대 Git에 커밋하지 않음)
참고: .env.example 에 키 목록만 남겨두어 팀원과 공유
```
```
APP_ENV=local          ← local / production
APP_DEBUG=true         ← 운영 시 반드시 false
DB_CONNECTION=pgsql
DB_HOST=...
MAIL_MAILER=log        ← 개발 시 log, 운영 시 ses
```

---

## Artisan 자주 쓰는 명령어

```bash
# 개발 서버
php artisan serve

# 파일 생성
php artisan make:model    레이스
php artisan make:controller RaceController
php artisan make:migration create_races_table

# DB
php artisan migrate          # 마이그레이션 실행
php artisan migrate:status   # 실행 상태 확인
php artisan db:show          # DB 연결 및 테이블 목록

# 캐시
php artisan config:clear
php artisan cache:clear
php artisan route:list       # 등록된 라우트 목록
```

---

## 이 프로젝트의 계층 요약

```
Request (브라우저)
    ↓
routes/web.php          URL → Controller 연결
    ↓
Controller              요청 수신, validate, Service 호출, 응답
    ↓
Service                 비즈니스 로직, 데이터 정제
    ↓
Model                   DB 쿼리 실행 (Eloquent + raw)
    ↓
Supabase PostgreSQL     실제 데이터 저장/조회
```
