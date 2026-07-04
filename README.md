# REVIEW — 마라톤 / 러닝 대회 리뷰 플랫폼

> **"완주자만 아는 이야기를 데이터로"**
> 마라톤·러닝 대회에 참가한 사람들의 진짜 후기를 모아, AI가 종합 분석하는 리뷰 플랫폼입니다.

- 서비스 URL : https://review.pac-run.com
- GitHub : https://github.com/dykim10/review_site.git

---

## 프로젝트 배경

마라톤/러닝 대회는 국내에만 연간 수백 건이 열립니다. 그러나 대회 정보는 여러 사이트에 흩어져 있고, 참가자들의 실질적인 후기는 블로그·카페에 묻혀 찾기 어렵습니다.

REVIEW는 이 문제를 세 가지로 접근합니다.
1. **대회 정보 집약** — 크롤링으로 주요 대회 정보를 한곳에 모은다
2. **참가자 후기 아카이브** — 실제 참가자만 작성 가능한 리뷰로 신뢰도를 높인다
3. **AI 빅데이터 분석** — 쌓인 후기를 AI가 종합해 다음 참가자에게 유의미한 인사이트를 제공한다

---

## 시스템 아키텍처

```
[사용자 브라우저]
       │
       ▼
[REVIEW (Laravel)]  ─── CRUD / 인증 / 뷰 렌더링
       │
       │ HTTP (Guzzle)
       ▼
[CORE API (Python/FastAPI)]  ─── AI 분석 / 크롤링 / 통계
       │
       ▼
[Supabase PostgreSQL]  ─── 공통 DB (public + review 스키마)
```

**설계 원칙**
- Laravel : 회원·리뷰·대회 CRUD 등 사용자 직접 입력 처리
- CORE API : AI 요약·크롤링·날씨 등 무거운 연산 처리
- 두 서버는 같은 EC2에서 실행, DB는 Supabase로 공유

---

## 기술 스택

| 구분 | 기술 | 비고 |
|---|---|---|
| 언어 | PHP 8.3+ / Python 3.x | |
| 프레임워크 | Laravel 13 / FastAPI | |
| DB | Supabase PostgreSQL | public + review 스키마 분리 |
| 프론트 | Blade + Tailwind CSS + Alpine.js | |
| AI | GPT-4o-mini (현재) → Claude API (예정) | CORE API 경유 |
| 서버 | AWS EC2 Ubuntu 24.04 + Nginx | |
| 인증 | Laravel Breeze (이메일 인증) | |

---

## DB 스키마 구조

```
public 스키마 (공통 — crew 프로젝트와 공유)
├── users          회원 (role: super_admin / crew_admin / user)
├── crews          크루 정보
├── branches       지부
└── groups         소모임

review 스키마 (이 프로젝트 전용)
├── races          대회 정보 (크롤링 + 수동 등록)
├── reviews        참가 후기 (1인 1리뷰 제한)
└── race_weather   대회 날씨 데이터
```

---

## 구현 완료 기능 (v1)

### 인증
- [x] 이메일 회원가입 / 로그인
- [x] 이메일 인증 (MustVerifyEmail)
- [x] 관리자 권한 분리 (AdminMiddleware — super_admin / crew_admin)

### 대회 정보
- [x] 대회 목록 / 상세 조회 (공개)
- [x] 대회 등록 / 수정 / 삭제 (관리자 전용)
- [x] 크롤링 연동 (marathongo.co.kr / roadrun.co.kr)
- [x] 거리 정규화 (풀 / 하프 / XK — Python + PHP 이중 처리)

### 리뷰
- [x] 리뷰 작성 / 수정 / 삭제 (이메일 인증 회원)
- [x] 1인 1리뷰 제한 (DB 유니크 인덱스 + 앱 레벨 이중 검증)
- [x] 별점 (1~5점, Alpine.js 위젯)
- [x] 참가 거리 선택

### AI 분석
- [x] 개별 리뷰 AI 요약 + 감성 분석 (positive / negative / neutral)
- [x] 대회별 AI 종합 분석 (요약 / 긍정 포인트 / 아쉬운 점 / 키워드)
- [x] 리뷰 등록·수정·삭제 시 자동 재생성 (최대 50건 기반)

---

## 앞으로의 방향

### v2 — 콘텐츠 강화

| 기능 | 설명 | 우선순위 |
|---|---|---|
| 날씨 데이터 연동 | 대회일 실제 날씨 표시 (CORE API → 기상 API) | 높음 |
| 완주 인증 | 기록 사진 업로드 → AI 파싱으로 완주 인증 | 높음 |
| SNS 후기 수집 | 네이버 블로그 / 유튜브 해시태그 크롤링 | 중간 |
| 리뷰 사진 첨부 | 대회 현장 사진 업로드 | 중간 |
| 도움됨 / 좋아요 | 리뷰 유용성 평가 | 낮음 |

### v3 — 개인화 / 커뮤니티

| 기능 | 설명 | 우선순위 |
|---|---|---|
| 대회 알림 | 관심 대회 접수 시작 알림 (이메일 / 푸시) | 높음 |
| 크루 연동 | crew 프로젝트와 회원 연계, 크루 참가 기록 | 높음 |
| AI 대회 추천 | 참가 이력 기반 맞춤 대회 추천 | 중간 |
| 러닝 기록 연동 | CORE API의 running_logs 와 연계, 대회 기록 자동 등록 | 중간 |
| 구글 OTP (TOTP) | 2단계 인증 추가 | 낮음 |

### 아이디어 영역 (논의 필요)

- **리뷰 신뢰도 검증** : 실제 완주자임을 어떻게 확인할 것인가? 기록 사진 AI 파싱 외에 다른 방법?
- **대회 주최사 계정** : 주최사가 직접 대회를 등록하고 참가자에게 응답하는 구조
- **다국어** : 외국인 참가자(서울마라톤 등)를 위한 영문 지원
- **수익 모델** : 광고 vs 프리미엄 멤버십 vs 주최사 유료 등록

---

## 아키텍처 패턴 (개발 원칙)

### Controller → Service → Model 3계층

```
Controller  요청 수신 → 검증(FormRequest) → Service 호출 → 응답 반환
Service     비즈니스 로직 / 데이터 정제 / CORE API 호출
Model       DB CRUD (단순: Eloquent / 복잡: raw 쿼리)
```

### Python ↔ PHP 데이터 교환 주의사항

- PostgreSQL 컬럼 타입은 **JSONB** 사용 권장 (`text[]` 형식은 PHP json_decode 불가)
- Python supabase-py 가 저장하는 배열 → PHP에서 Attribute accessor로 양쪽 포맷 처리
- CORE API 호출 실패 시 메인 기능(리뷰 저장 등)은 정상 처리, 로그만 기록

---

## 로컬 개발 환경 설정

```bash
# 1. 의존성 설치
composer install
npm install

# 2. 환경 설정
cp .env.example .env
php artisan key:generate
# .env 에서 DB (Supabase pooler), CORE_API_URL 설정

# 3. DB 마이그레이션
php artisan migrate

# 4. 개발 서버 실행
php artisan serve
npm run dev
```

**.env 필수 항목**

```
DB_CONNECTION=pgsql
DB_HOST=aws-1-ap-northeast-2.pooler.supabase.com
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres.[project-ref]
DB_PASSWORD=[password]

CORE_API_URL=http://localhost:8000
```

---

## EC2 배포

```bash
cd /var/www/review-site
git pull origin main
composer install --no-dev --optimize-autoloader
npm run build
php artisan migrate --force
php artisan config:cache && php artisan route:cache && php artisan view:cache
```

---

## 관련 프로젝트

| 프로젝트 | 역할 | GitHub |
|---|---|---|
| **CORE API** (Python) | AI 분석 / 크롤링 / 통계 | github.com/dykim10/python_core_api_site |
| **CREW** (Laravel) | 크루 관리 / 러닝 기록 | github.com/dykim10/crew_site |

---

> 공통 정의서: `../project-definition.md`
