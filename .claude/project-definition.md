# REVIEW 프로젝트 정의서

> 원본 공통 정의서: ~/projects/project-definition.md
> 이 파일은 REVIEW(Laravel) 프로젝트에 특화된 정의입니다.

---

## 이 프로젝트의 역할

마라톤 / 러닝 **대회 리뷰 플랫폼**

- 대회 정보 등록 및 조회
- 참가자 리뷰 작성 (인증된 완주자)
- 리뷰 AI 요약 (CORE API 경유 → Claude API)
- SNS 해시태그 수집 (네이버 블로그 / 유튜브)
- 날씨 데이터 연동 (대회일 날씨 표시)

---

## 기술 스택

| 구분 | 기술 |
|---|---|
| 언어 | PHP 8.3+ |
| 프레임워크 | Laravel |
| DB | Supabase PostgreSQL |
| 프론트 | Blade 템플릿 / CSS / JS |
| HTTP 클라이언트 | Guzzle (CORE API 호출) |

---

## 경로

| 구분 | 경로 |
|---|---|
| EC2 서버 | `/var/www/review-site/` |
| 로컬 | `~/projects/review/` |
| GitHub | `https://github.com/dykim10/review_site.git` |

---

## DB 스키마

```
public 스키마 (공통)
└── users          : 통합 회원

review 스키마 (REVIEW 전용)
├── races          : 대회 정보 (이름/날짜/장소/거리/참가비/홈페이지)
├── reviews        : 리뷰 (대회/회원/거리/평점/내용/AI요약/감정)
└── race_weather   : 대회 날씨 (기온/습도/풍속/날씨상태)
```

---

## CORE API 호출

| 메서드 | 엔드포인트 | 용도 |
|---|---|---|
| POST | `http://localhost:8000/api/summarize` | 리뷰 AI 요약 |
| POST | `http://localhost:8000/api/sentiment` | 감정분석 |
| GET | `http://localhost:8000/api/weather` | 날씨 데이터 |
| GET | `http://localhost:8000/api/race-info` | 대회 정보 크롤링 |

---

## 회원 정책

- 1단계: 클로즈 베타 → 초대 코드로만 가입
- 2단계: 오픈 베타 → 이메일 인증
- 3단계: 정식 오픈 → 완주 인증 추가

**통합 회원 테이블 (public.users)**
```
id / email / password / name / nickname
crew_id / branch_id / group_id
role        : super_admin / crew_admin / group_admin / member
is_beta     : 베타 여부
invite_code : 초대 코드
created_at / updated_at / last_login_at
```

**이메일 (AWS SES)**
```
- 회원가입 이메일 인증
- 비밀번호 재설정
- 초대 코드 발송
```

---

## 관리자 기능 (/admin)

```
- 대회 정보 등록 / 수정 / 삭제
- 리뷰 신고 처리
- 회원 관리
- AI 요약 수동 트리거
```

**권한 구조**
```
super_admin → 전체 관리
crew_admin  → 크루 단위 관리
group_admin → 그룹 단위 관리
member      → 일반 구성원
```

---

## 개발 우선순위 (v1)

```
1. Laravel 기본 설치 및 Supabase 연결
2. 회원 인증 (이메일 인증)
3. 대회 정보 등록 / 조회
4. 리뷰 작성 / 목록
5. AI 요약 연동 (CORE API 호출)
```

---

## 주의사항

- DB 비밀번호 / API Key 는 `.env` 관리 / Git 커밋 금지
- 운영 환경에서 `APP_DEBUG=false` 필수
- CORE API 호출 실패 시 예외 처리 필수
