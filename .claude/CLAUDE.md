# REVIEW 프로젝트 - Claude Code 지침

> 마라톤 / 러닝 대회 리뷰 플랫폼
> 공통 정의서 참고: ./project-definition.md

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
