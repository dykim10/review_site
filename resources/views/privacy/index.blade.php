@extends('layouts.review')
@section('title', '개인정보처리방침 — PAC-RUN')

@push('styles')
<style>
    .pv-wrap { max-width: 860px; margin: 0 auto; padding: 2.75rem 1.5rem 6rem; }

    .pv-eyebrow {
        font-family: 'Archivo', sans-serif; font-size: 0.68rem; font-weight: 700; letter-spacing: 0.22em;
        text-transform: uppercase; color: #E80043; margin-bottom: 0.6rem;
    }
    .pv-title { font-size: clamp(1.8rem, 4.5vw, 2.6rem); font-weight: 700; letter-spacing: -0.01em; line-height: 1.15; color: #16181D; }
    .pv-intro { font-size: 0.88rem; color: #5A6170; line-height: 1.8; margin-top: 1.1rem; padding-bottom: 2rem; border-bottom: 1px solid #E8EAEE; }
    .pv-intro strong { color: #16181D; }

    .pv-article { margin-top: 2.5rem; }
    .pv-article h2 {
        font-size: 1.05rem; font-weight: 700; color: #16181D; margin-bottom: 0.9rem;
        padding-bottom: 0.6rem; border-bottom: 2px solid #16181D; display: inline-block;
    }
    .pv-article h3 { font-size: 0.85rem; font-weight: 700; color: #16181D; margin: 1.1rem 0 0.5rem; }
    .pv-article p, .pv-article li { font-size: 0.85rem; color: #5A6170; line-height: 1.85; }
    .pv-article strong { color: #16181D; font-weight: 600; }
    .pv-article ul, .pv-article ol { padding-left: 1.25rem; margin-bottom: 0.5rem; }
    .pv-article ul { list-style: disc outside; }
    .pv-article ol { list-style: decimal outside; }
    .pv-article ol > li, .pv-article ul > li { margin-bottom: 0.4rem; }
    .pv-article ul ul, .pv-article ol ul { margin-top: 0.35rem; }

    .pv-table-wrap { overflow-x: auto; border: 1px solid #E8EAEE; border-radius: 8px; margin: 0.75rem 0 1.25rem; }
    .pv-table { width: 100%; border-collapse: collapse; font-size: 0.78rem; }
    .pv-table th { background: #FAFAFB; color: #9AA1AE; font-size: 0.68rem; font-weight: 700; letter-spacing: 0.06em; text-transform: uppercase; text-align: left; padding: 0.65rem 0.9rem; white-space: nowrap; }
    .pv-table td { padding: 0.65rem 0.9rem; color: #5A6170; border-top: 1px solid #E8EAEE; }

    .pv-foot-note { font-size: 0.78rem; color: #9AA1AE; margin-top: 0.75rem; }
</style>
@endpush

@section('content')
<div class="pv-wrap">

    <p class="pv-eyebrow">Legal</p>
    <h1 class="pv-title">개인정보처리방침</h1>
    <p class="pv-intro">
        시행일자: 2026년 __월 __일<br>
        PAC-RUN(이하 "서비스")은 「개인정보 보호법」 등 관련 법령을 준수하며, 이용자의 개인정보를 안전하게 보호하기 위해 다음과 같이 개인정보처리방침을 수립·공개합니다.
        본 방침은 <strong>REVIEW(review.pac-run.com)</strong>와 <strong>CREW(crew.pac-run.com)</strong>에 공통 적용됩니다.
    </p>

    <div class="pv-article">
        <h2>제1조 (수집하는 개인정보 항목 및 수집 방법)</h2>

        <h3>1. 회원가입 시 수집 항목</h3>
        <div class="pv-table-wrap">
            <table class="pv-table">
                <thead><tr><th>구분</th><th>항목</th><th>필수 여부</th></tr></thead>
                <tbody>
                    <tr><td>공통</td><td>이메일 주소, 비밀번호(암호화 저장), 닉네임</td><td>필수</td></tr>
                    <tr><td>CREW</td><td>이름, 휴대폰 번호</td><td>필수</td></tr>
                    <tr><td>CREW</td><td>기수, 지역, 훈련그룹 등 크루 활동 정보</td><td>필수</td></tr>
                    <tr><td>CREW (클로즈 베타)</td><td>초대 코드</td><td>필수</td></tr>
                </tbody>
            </table>
        </div>

        <h3>2. 서비스 이용 과정에서 수집되는 항목</h3>
        <div class="pv-table-wrap">
            <table class="pv-table">
                <thead><tr><th>구분</th><th>항목</th><th>수집 방식</th></tr></thead>
                <tbody>
                    <tr><td>공통</td><td>서비스 이용 기록, 접속 로그, 접속 IP, 쿠키</td><td>자동 수집</td></tr>
                    <tr><td>CREW</td><td>러닝 기록(거리, 페이스, 심박수, 칼로리, 고도 등), 러닝 앱 스크린샷 이미지</td><td>이용자 직접 업로드</td></tr>
                    <tr><td>CREW</td><td>프로필 사진(아바타)</td><td>이용자 직접 업로드</td></tr>
                    <tr><td>REVIEW</td><td>대회 참가 후기, 별점, 완주 기록(시간, 배번호 등)</td><td>이용자 직접 입력</td></tr>
                    <tr><td>REVIEW</td><td>기록증 이미지, 워치 스크린샷 이미지</td><td>이용자 직접 업로드</td></tr>
                    <tr><td>REVIEW</td><td>GPX/TCX 파일(개인 주행 GPS 경로 — 위치정보 포함)</td><td>이용자 직접 업로드 (별도 동의 후)</td></tr>
                </tbody>
            </table>
        </div>

        <h3>3. GPX/TCX 파일(위치정보)에 관한 특별 안내</h3>
        <ul>
            <li>GPX/TCX 파일에는 이용자의 이동 경로, 시각, 심박수 등 민감할 수 있는 정보가 포함됩니다.</li>
            <li>해당 파일 업로드 시 별도의 명시적 동의를 받으며, 동의 시각을 기록·보관합니다.</li>
            <li>원본 파일은 외부 공개되지 않는 비공개 저장소에 보관되며, 파일 업로드 본인만 접근할 수 있습니다.</li>
        </ul>
    </div>

    <div class="pv-article">
        <h2>제2조 (개인정보의 처리 목적)</h2>
        <p>서비스는 수집한 개인정보를 다음 목적으로 이용합니다.</p>
        <ol>
            <li><strong>회원 관리:</strong> 회원 가입 및 본인 확인, 이메일 인증, 부정 이용 방지</li>
            <li><strong>서비스 제공:</strong> 러닝 기록 관리, 대회 리뷰 작성·조회, 이벤트 참여 및 점수 집계</li>
            <li>
                <strong>AI 기반 분석 서비스 제공:</strong>
                <ul>
                    <li>러닝 앱 스크린샷·기록증·워치 이미지에서 기록 데이터 자동 추출</li>
                    <li>리뷰 요약 및 대회 종합 분석 생성</li>
                    <li>GPX 데이터·날씨·과거 완주 기록을 활용한 맞춤형 레이스 플랜 생성</li>
                </ul>
            </li>
            <li><strong>알림 발송:</strong> 이메일(회원가입 인증, 비밀번호 재설정, 이벤트 안내), 문자메시지(크루 단체 공지)</li>
            <li><strong>서비스 개선:</strong> 이용 통계 분석, 오류 확인 및 개선</li>
        </ol>
    </div>

    <div class="pv-article">
        <h2>제3조 (개인정보의 처리 및 보유 기간)</h2>
        <ol>
            <li>서비스는 원칙적으로 회원 탈퇴 시 지체 없이 개인정보를 파기합니다.</li>
            <li>다만 관계 법령에 따라 아래 정보는 명시된 기간 동안 보관합니다.</li>
        </ol>
        <div class="pv-table-wrap">
            <table class="pv-table">
                <thead><tr><th>보관 항목</th><th>근거 법령</th><th>보관 기간</th></tr></thead>
                <tbody>
                    <tr><td>서비스 접속 기록</td><td>통신비밀보호법</td><td>3개월</td></tr>
                    <tr><td>소비자 불만 또는 분쟁 처리에 관한 기록</td><td>전자상거래법</td><td>3년</td></tr>
                </tbody>
            </table>
        </div>
        <p>백업 데이터는 최대 7일간 보관 후 자동 삭제됩니다.</p>
    </div>

    <div class="pv-article">
        <h2>제4조 (개인정보의 제3자 제공)</h2>
        <p>서비스는 이용자의 개인정보를 제3자에게 제공하지 않습니다. 다만 다음의 경우는 예외로 합니다.</p>
        <ol>
            <li>이용자가 사전에 동의한 경우</li>
            <li>법령의 규정에 의하거나 수사 목적으로 법령에 정해진 절차와 방법에 따라 수사기관의 요구가 있는 경우</li>
        </ol>
    </div>

    <div class="pv-article">
        <h2>제5조 (개인정보 처리의 위탁 및 국외 이전)</h2>
        <p>서비스는 안정적인 서비스 제공을 위해 아래와 같이 개인정보 처리를 위탁하고 있으며, 일부 수탁사는 국외 사업자입니다.</p>
        <div class="pv-table-wrap">
            <table class="pv-table">
                <thead><tr><th>수탁 업체</th><th>위탁 업무</th><th>이전 국가 / 보관 위치</th></tr></thead>
                <tbody>
                    <tr><td>Amazon Web Services, Inc. (AWS)</td><td>서버 운영, 파일(이미지·GPX) 저장</td><td>대한민국 (서울 리전)</td></tr>
                    <tr><td>Supabase, Inc.</td><td>데이터베이스 운영</td><td>대한민국 (서울 리전)</td></tr>
                    <tr><td>Anthropic, PBC</td><td>AI 분석 처리 (이미지 파싱, 리뷰 요약, 레이스 플랜 생성)</td><td>미국</td></tr>
                    <tr><td>OpenAI, L.L.C.</td><td>텍스트 임베딩 생성 (유사 사례 검색용)</td><td>미국</td></tr>
                    <tr><td>Resend, Inc.</td><td>이메일 발송</td><td>미국</td></tr>
                    <tr><td>솔라피㈜ (Solapi)</td><td>문자메시지 발송</td><td>대한민국</td></tr>
                </tbody>
            </table>
        </div>
        <ul>
            <li>국외 이전 항목: 서비스 이용 과정에서 생성·업로드된 데이터 중 각 위탁 업무 수행에 필요한 최소한의 정보</li>
            <li>이전 방법: 서비스 이용 시점에 네트워크(API)를 통한 전송</li>
            <li>이용자는 국외 이전을 거부할 수 있으나, 이 경우 해당 기능(AI 분석, 이메일 수신 등)의 이용이 제한될 수 있습니다.</li>
        </ul>
    </div>

    <div class="pv-article">
        <h2>제6조 (개인정보의 파기 절차 및 방법)</h2>
        <ol>
            <li><strong>파기 절차:</strong> 보유 기간이 경과하거나 처리 목적이 달성된 개인정보는 지체 없이 파기합니다.</li>
            <li>
                <strong>파기 방법:</strong>
                <ul>
                    <li>전자적 파일: 복구할 수 없는 기술적 방법으로 영구 삭제</li>
                    <li>저장소(S3)에 보관된 이미지·GPX 파일: 원본 및 변환본(썸네일 등) 일괄 삭제</li>
                </ul>
            </li>
        </ol>
    </div>

    <div class="pv-article">
        <h2>제7조 (이용자 및 법정대리인의 권리와 행사 방법)</h2>
        <ol>
            <li>이용자는 언제든지 자신의 개인정보를 조회·수정·삭제·처리정지 요구할 수 있습니다.</li>
            <li>권리 행사는 서비스 내 프로필 설정 또는 개인정보 보호책임자에게 이메일로 요청할 수 있으며, 서비스는 지체 없이 조치합니다.</li>
            <li>만 14세 미만 아동의 회원가입은 받지 않습니다.</li>
        </ol>
    </div>

    <div class="pv-article">
        <h2>제8조 (개인정보의 안전성 확보 조치)</h2>
        <p>서비스는 개인정보 보호를 위해 다음과 같은 조치를 취하고 있습니다.</p>
        <ol>
            <li><strong>개인정보 암호화:</strong> 이름·이메일·휴대폰 번호는 암호화하여 저장하며, 비밀번호는 일방향 암호화(해시)로 저장되어 복원이 불가능합니다.</li>
            <li><strong>접근 통제:</strong> 데이터베이스 접근 권한 최소화, 관리자 기능 접근 제한</li>
            <li><strong>전송 구간 암호화:</strong> 전체 서비스 HTTPS(TLS) 적용</li>
            <li><strong>비공개 파일 접근 제어:</strong> GPX 원본 등 민감 파일은 비공개 저장소에 보관하고, 소유자 본인에게만 시간제한 접근 링크를 발급</li>
            <li><strong>정기 백업 및 보안 점검:</strong> 일일 자동 백업, 의존성 보안 취약점 정기 스캔</li>
        </ol>
    </div>

    <div class="pv-article">
        <h2>제9조 (쿠키의 설치·운영 및 거부)</h2>
        <ol>
            <li>서비스는 로그인 세션 유지 등 서비스 제공에 필수적인 쿠키를 사용합니다.</li>
            <li>이용자는 웹 브라우저 설정을 통해 쿠키 저장을 거부할 수 있으나, 이 경우 로그인이 필요한 서비스 이용에 제한이 있을 수 있습니다.</li>
        </ol>
    </div>

    <div class="pv-article">
        <h2>제10조 (개인정보 보호책임자)</h2>
        <div class="pv-table-wrap">
            <table class="pv-table">
                <tbody>
                    <tr><td style="width:35%;color:#9AA1AE;">개인정보 보호책임자</td><td>[이름]</td></tr>
                    <tr><td style="color:#9AA1AE;">직책</td><td>운영자</td></tr>
                    <tr><td style="color:#9AA1AE;">연락처</td><td>[이메일 주소]</td></tr>
                </tbody>
            </table>
        </div>
        <p>기타 개인정보 침해에 대한 신고나 상담이 필요한 경우 아래 기관에 문의할 수 있습니다.</p>
        <ul>
            <li>개인정보침해 신고센터 (privacy.kisa.or.kr / 국번없이 118)</li>
            <li>개인정보 분쟁조정위원회 (kopico.go.kr / 1833-6972)</li>
            <li>대검찰청 사이버수사과 (spo.go.kr / 국번없이 1301)</li>
            <li>경찰청 사이버수사국 (ecrm.police.go.kr / 국번없이 182)</li>
        </ul>
    </div>

    <div class="pv-article">
        <h2>제11조 (개인정보처리방침의 변경)</h2>
        <ol>
            <li>본 방침은 법령·정책 또는 서비스 변경에 따라 수정될 수 있습니다.</li>
            <li>변경 시 시행 7일 전(이용자 권리에 중대한 변경이 있는 경우 30일 전)부터 서비스 내 공지사항을 통해 고지합니다.</li>
        </ol>
        <p class="pv-foot-note">
            공고일자: 2026년 __월 __일<br>
            시행일자: 2026년 __월 __일
        </p>
    </div>

</div>
@endsection
