# REVIEW 프로젝트 - Claude Code 지침

@C:\Users\dykim\.claude\plugins\marketplaces\claude-plugins-official\plugins\frontend-design\skills\frontend-design\SKILL.md

> 마라톤 / 러닝 대회 리뷰 플랫폼  
> **스펙 정본:** `../../.claude/definition/05-review.md` · **진행:** `../../developer_md/STATUS.md`  
> `./project-definition.md` · `../project-definition.md`는 **레거시** — 갱신하지 않는다.

@../../developer_md/STATUS.md
@../../.claude/definition/01-overview.md
@../../.claude/definition/02-common-rules.md
@../../.claude/definition/04-api-endpoints.md
@../../.claude/definition/05-review.md

---

## 문서 자동 갱신 (doc-sync)

기능 완료·commit 직전·`/compact` 직전·"문서 갱신" 요청 시:

1. **`../../.claude/definition/05-review.md`** — REVIEW 스펙·완료/미완·다음 작업
2. **`../../developer_md/STATUS.md`** — PLAN/TASK 진행만 (스키마 중복 금지)
3. 공통 변경 시 `03-db-schema.md` · `04-api-endpoints.md` · `08-core.md` 등 해당 파일

상세 절차: 워크스페이스 루트 `.claude/skills/doc-sync.md` 또는 `/doc-sync`

**업데이트하지 않는 항목:** 코드 세부 구현, 일시적 버그/스타일 수정

---

## 기술 스택

| 구분 | 기술 |
|---|---|
| 언어 | PHP 8.3+ |
| 프레임워크 | Laravel |
| DB | Supabase PostgreSQL |
| 프론트 | Blade 템플릿 / CSS / JS |
| HTTP 클라이언트 | Guzzle (CORE-API 호출) |

---

## 디렉토리 (EC2 서버)

```
/var/www/review-site/
```

## 로컬 경로

```
~/projects/review/
```

## GitHub

```
https://github.com/dykim10/review_site.git
```

---

## 주요 기능

- 대회 정보 등록 및 조회
- 참가자 리뷰 작성 (인증된 완주자)
- 리뷰 AI 요약 (CORE API 경유 → Claude API)
- SNS 해시태그 수집 (네이버 블로그 / 유튜브)
- 날씨 데이터 연동 (대회일 날씨 표시)

---

## CORE API (로컬)

```
CORE_API_URL=http://localhost:8100  (review/.env)
```

엔드포인트 전체는 import된 `04-api-endpoints.md` · `05-review.md` 참조.

---

## DB / 기능 / 우선순위

스키마·기능·진행 현황은 import된 **`05-review.md`** · **`STATUS.md`** 가 정본이다. 아래는 로컬 작업용 요약만 유지한다.

```
review 스키마: races · race_editions · reviews · race_courses · race_plans · ...
```

---

## 주의사항

- DB 비밀번호 / API Key 는 .env 관리 / Git 커밋 금지
- 운영 환경에서 디버그 모드 반드시 비활성화 (APP_DEBUG=false)
- CORE API 호출 실패 시 예외 처리 필수

---

## 개발 규칙 (Claude Code 필수 준수)

### 기능 추가 절차
새 기능을 구현할 때 반드시 아래 순서를 따른다.

```
1. Model       — 테이블 매핑, fillable, casts 정의
2. Migration   — 기존 테이블 수정 시 Schema::table, 신규 시 Schema::create
3. Controller  — 공개(app/Http/Controllers) / 관리자(Admin/) 분리
4. Route       — web.php 에 공개 라우트 / admin 그룹 라우트 추가
5. View        — Blade + Tailwind CSS, resources/views/ 하위 구성
6. 동작 확인   — 브라우저 실제 테스트 후 완료 보고
```

### DB / 마이그레이션 규칙
- `public.users` 등 이미 존재하는 Supabase 테이블은 **Schema::table 로만 수정**, DROP/CREATE 금지
- `review` 스키마 테이블은 모델에서 `$table = 'review.테이블명'` 으로 명시
- 모든 타임스탬프 컬럼은 **TIMESTAMPTZ(6)** 으로 통일
- 마이그레이션은 `IF NOT EXISTS` / `hasColumn` 으로 멱등성 보장

### 보안 규칙
- 모든 POST/PUT/DELETE 폼에 **@csrf** 필수
- 관리자 라우트(`/admin/*`)는 반드시 `['auth', 'verified']` 미들웨어 적용
- 사용자 입력은 반드시 `$request->validate()` 로 검증 후 사용
- `.env` 값을 로그·뷰·응답에 절대 노출하지 않는다

### 코드 스타일 규칙
- 에러 메시지 / 사용자 안내 문구는 **한국어** 로 작성
- 뷰 스타일은 **Tailwind CSS** 만 사용 (인라인 style 금지)
- 주석은 WHY가 명확할 때만 작성, 코드 설명용 주석 금지

### 아키텍처 규칙 (Controller → Service → Model 3계층)

**Controller** (`app/Http/Controllers/`)
- 요청 수신, `$request->validate()` 검증, Service 호출, 응답 반환만 담당
- 비즈니스 로직 작성 금지 — 모든 처리는 Service 에 위임

**Service** (`app/Services/`)
- 비즈니스 로직 및 데이터 정제 담당
- 재사용 가능하도록 작성 (여러 Controller 에서 호출 가능)
- 정제된 데이터를 Model 메서드에 넘겨 DB 처리 위임
- 예: distances 파싱, 상태 자동 계산, CORE API 호출 등

**Model** (`app/Models/`)
- DB insert / update / delete / select 담당
- 단순 CRUD 는 Eloquent, 복잡한 JOIN·집계·통계는 `DB::select()` raw 쿼리 사용
- 재사용 가능한 쿼리는 scope 또는 static 메서드로 정의

```
새 기능 구현 순서:
1. Model    — 테이블 매핑, scope, raw 쿼리 메서드 정의
2. Service  — 로직 작성, Model 호출
3. Controller — validate → Service 호출 → 응답
4. Route    — web.php 등록
5. View     — Blade + Tailwind
6. 브라우저 확인
```
