# REVIEW 프로젝트 - Claude Code 지침

> 마라톤 / 러닝 대회 리뷰 플랫폼
> 공통 정의서 참고: ./project-definition.md

---

## 문서 자동 갱신 규칙

아래 시점에 **반드시** `.claude/project-definition.md` 와 `../project-definition.md` 를 최신 상태로 업데이트한다.

**트리거 조건**
1. 기능 구현 완료 후 git commit 직전
2. 사용자가 `/compact` 를 실행하기 전 (또는 컨텍스트 압축 직전)
3. 사용자가 "정의서 업데이트", "문서 갱신" 등을 요청할 때

**업데이트 항목**
- 개발 우선순위 체크리스트 (완료된 항목에 ✅ 표시)
- 새로 추가된 CORE API 엔드포인트
- 새로 추가된 DB 컬럼 / 테이블
- 변경된 아키텍처 또는 설계 결정
- v2 예정 기능 중 완료된 항목 이동

**업데이트하지 않는 항목**
- 코드 레벨 세부 구현 (그건 코드 자체가 문서)
- 일시적 버그 수정
- 스타일/오타 수정

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

## CORE API 호출 엔드포인트

```
POST http://localhost:8000/api/summarize  → 리뷰 AI 요약
GET  http://localhost:8000/api/weather   → 날씨 데이터
GET  http://localhost:8000/api/race-info → 대회 정보 크롤링
```

---

## DB 스키마

```
public 스키마 : users / crews / branches / groups  (공통)
review 스키마 : races / reviews / race_weather
```

---

## 개발 우선순위 (v1 목표)

```
1. Laravel 기본 설치 및 Supabase 연결
2. 회원 인증 (이메일 인증)
3. 대회 정보 등록 / 조회
4. 리뷰 작성 / 목록
5. AI 요약 연동 (CORE API 호출)
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
