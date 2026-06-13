<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // city → country 매핑 (크롤링 데이터 기준)
        $cityCountry = [
            // 한국 (국내)
            'Seoul'   => ['대한민국', true],
            'Daegu'   => ['대한민국', true],
            'Gunsan'  => ['대한민국', true],
            'Busan'   => ['대한민국', true],
            'Incheon' => ['대한민국', true],
            'Gwangju' => ['대한민국', true],
            'Suwon'   => ['대한민국', true],

            // 일본
            'Tokyo'    => ['일본', false],
            'Osaka'    => ['일본', false],
            'Nagoya'   => ['일본', false],
            'Sapporo'  => ['일본', false],
            'Fukuoka'  => ['일본', false],
            'Kobe'     => ['일본', false],
            'Gifu'     => ['일본', false],
            'Marugame' => ['일본', false],
            'Ōita'     => ['일본', false],
            'Hōfu'     => ['일본', false],

            // 중국
            'Beijing'       => ['중국', false],
            'Shanghai'      => ['중국', false],
            'Nanjing'       => ['중국', false],
            'Hangzhou'      => ['중국', false],
            'Chongqing'     => ['중국', false],
            'Wuhan'         => ['중국', false],
            'Shenzhen'      => ['중국', false],
            'Guangzhou'     => ['중국', false],
            'Xiamen'        => ['중국', false],
            'Qingdao'       => ['중국', false],
            'Chengdu'       => ['중국', false],
            'Suzhou'        => ['중국', false],
            'Harbin'        => ['중국', false],
            'Shenyang'      => ['중국', false],
            'Xi\'an'        => ['중국', false],
            'Kunming'       => ['중국', false],
            'Changsha'      => ['중국', false],
            'Wuxi'          => ['중국', false],
            'Tianjin'       => ['중국', false],
            'Hefei'         => ['중국', false],
            'Zhengzhou'     => ['중국', false],
            'Jilin'         => ['중국', false],
            'Fuzhou'        => ['중국', false],
            'Guilin'        => ['중국', false],
            'Guiyang'       => ['중국', false],
            'Taiyuan'       => ['중국', false],
            'Nanchang'      => ['중국', false],
            'Lanzhou'       => ['중국', false],
            'Changde'       => ['중국', false],
            'Changzhou'     => ['중국', false],
            'Yangzhou'      => ['중국', false],
            'Yichang'       => ['중국', false],
            'Shijiazhuang'  => ['중국', false],
            'Shantou'       => ['중국', false],
            'Shaoxing'      => ['중국', false],
            'Xuzhou'        => ['중국', false],
            'Meishan'       => ['중국', false],
            'Sanya'         => ['중국', false],
            'Danzhou'       => ['중국', false],
            'Yangling'      => ['중국', false],
            'Yingkou'       => ['중국', false],
            'Yiwu'          => ['중국', false],
            'Liupanshui'    => ['중국', false],
            'Fangchenggang' => ['중국', false],
            'Hengshui'      => ['중국', false],
            'Huangshi'      => ['중국', false],
            "Huai'an"       => ['중국', false],
            'Quzhou'        => ['중국', false],
            'Jinjiang'      => ['중국', false],
            'Chizhou'       => ['중국', false],
            'Zhenning'      => ['중국', false],
            'Zhoushan'      => ['중국', false],
            'Xichang'       => ['중국', false],
            'Dongyin'       => ['중국', false],

            // 태국
            'Bangkok'           => ['태국', false],
            'Chon Buri'         => ['태국', false],
            'Pattaya'           => ['태국', false],
            'Krabi'             => ['태국', false],
            'Buri Ram'          => ['태국', false],
            'Chanthaburi'       => ['태국', false],
            'Nakhon Ratchasima' => ['태국', false],
            'Lampang'           => ['태국', false],
            'Songkhla'          => ['태국', false],
            'Yala'              => ['태국', false],

            // 미국
            'Boston'       => ['미국', false],
            'New York'     => ['미국', false],
            'Chicago'      => ['미국', false],
            'Houston'      => ['미국', false],
            'Honolulu'     => ['미국', false],
            'Atlanta'      => ['미국', false],
            'Pittsburgh'   => ['미국', false],
            'Boulder'      => ['미국', false],
            'Laredo'       => ['미국', false],
            'Cape Elizabeth'=> ['미국', false],
            'Falmouth'     => ['미국', false],

            // 영국
            'London'      => ['영국', false],
            'Cardiff'     => ['영국', false],
            'Belfast'     => ['영국', false],
            'Manchester'  => ['영국', false],
            'Edinburgh'   => ['영국', false],
            'South Shields'=> ['영국', false],
            'Leicester'   => ['영국', false],
            'Bamburgh'    => ['영국', false],
            'Larne'       => ['영국', false],
            'Pulford'     => ['영국', false],

            // 독일
            'Berlin'    => ['독일', false],
            'Hamburg'   => ['독일', false],
            'Frankfurt' => ['독일', false],
            'Hanover'   => ['독일', false],
            'Mainz'     => ['독일', false],

            // 프랑스
            'Paris'     => ['프랑스', false],
            'Langueux'  => ['프랑스', false],

            // 스페인
            'Madrid'                   => ['스페인', false],
            'Barcelona'                => ['스페인', false],
            'Valencia'                 => ['스페인', false],
            'Seville'                  => ['스페인', false],
            'Bilbao'                   => ['스페인', false],
            'Ibiza'                    => ['스페인', false],
            'Santa Pola'               => ['스페인', false],
            'Castellón de la Plana'    => ['스페인', false],
            'Málaga'                   => ['스페인', false],
            'Azpeitia'                 => ['스페인', false],
            'Berango'                  => ['스페인', false],

            // 이탈리아
            'Rome'         => ['이탈리아', false],
            'Milan'        => ['이탈리아', false],
            'Venice'       => ['이탈리아', false],
            'Florence'     => ['이탈리아', false],
            'Naples'       => ['이탈리아', false],
            'Bolzano'      => ['이탈리아', false],
            'Ravenna'      => ['이탈리아', false],
            'Oderzo'       => ['이탈리아', false],
            'Castelbuono'  => ['이탈리아', false],
            'Telese Terme' => ['이탈리아', false],

            // 포르투갈
            'Lisbon'                     => ['포르투갈', false],
            'Porto'                      => ['포르투갈', false],
            'Aveiro'                     => ['포르투갈', false],
            'Sobral de Monte Agraco'     => ['포르투갈', false],

            // 네덜란드
            'Amsterdam'  => ['네덜란드', false],
            'Rotterdam'  => ['네덜란드', false],
            'Enschede'   => ['네덜란드', false],

            // 체코
            'Prague'               => ['체코', false],
            'České Budějovice'     => ['체코', false],
            'Olomouc'              => ['체코', false],
            'Ústí nad Labem'       => ['체코', false],
            'Karlovy Vary'         => ['체코', false],

            // 폴란드
            'Warsaw'   => ['폴란드', false],
            'Krakow'   => ['폴란드', false],

            // 오스트리아
            'Vienna'  => ['오스트리아', false],

            // 덴마크
            'Copenhagen' => ['덴마크', false],

            // 스웨덴
            'Stockholm' => ['스웨덴', false],

            // 스위스
            'Geneva' => ['스위스', false],

            // 슬로베니아
            'Ljubljana' => ['슬로베니아', false],

            // 크로아티아
            'Zagreb'    => ['크로아티아', false],
            'Karlovac'  => ['크로아티아', false],

            // 루마니아
            'Bucharest' => ['루마니아', false],
            'Brașov'    => ['루마니아', false],

            // 불가리아
            'Sofia' => ['불가리아', false],

            // 슬로바키아
            'Bratislava' => ['슬로바키아', false],
            'Košice'     => ['슬로바키아', false],
            'Prievidza'  => ['슬로바키아', false],

            // 그리스
            'Athens' => ['그리스', false],

            // 터키
            'Istanbul' => ['터키', false],
            'Ankara'   => ['터키', false],
            'İzmir'    => ['터키', false],
            'Mersin'   => ['터키', false],

            // 라트비아
            'Riga' => ['라트비아', false],

            // 리투아니아
            'Vilnius' => ['리투아니아', false],

            // 에스토니아
            'Tallinn' => ['에스토니아', false],

            // 아일랜드
            'Dublin' => ['아일랜드', false],

            // 모나코
            'Monaco' => ['모나코', false],

            // UAE
            'Dubai'          => ['UAE', false],
            'Abu Dhabi'      => ['UAE', false],
            'Ras Al Khaimah' => ['UAE', false],

            // 사우디아라비아
            'Riyadh'    => ['사우디아라비아', false],
            'Al Khobar' => ['사우디아라비아', false],

            // 카타르
            'Doha' => ['카타르', false],

            // 인도
            'Mumbai'      => ['인도', false],
            'Bengaluru'   => ['인도', false],
            'New Delhi'   => ['인도', false],
            'Kolkata'     => ['인도', false],
            'Pune'        => ['인도', false],
            'Hyderabad'   => ['인도', false],

            // 인도네시아
            'Jakarta'    => ['인도네시아', false],
            'Bali'       => ['인도네시아', false],
            'Borobudur'  => ['인도네시아', false],

            // 말레이시아
            'Kuala Lumpur' => ['말레이시아', false],

            // 싱가포르
            'Singapore' => ['싱가포르', false],

            // 대만
            'Taipei'        => ['대만', false],
            'New Taipei City'=> ['대만', false],
            'Dongyin'       => ['대만', false],

            // 홍콩
            'Hong Kong' => ['홍콩', false],

            // 호주
            'Sydney'         => ['호주', false],
            'Melbourne'      => ['호주', false],
            'Gold Coast'     => ['호주', false],
            'Perth'          => ['호주', false],
            'Launceston'     => ['호주', false],
            'Sunshine Coast' => ['호주', false],

            // 캐나다
            'Toronto' => ['캐나다', false],
            'Ottawa'  => ['캐나다', false],

            // 브라질
            'Sao Paulo'     => ['브라질', false],
            'São Paulo'     => ['브라질', false],
            'Rio de Janeiro'=> ['브라질', false],
            'Porto Alegre'  => ['브라질', false],

            // 아르헨티나
            'Buenos Aires' => ['아르헨티나', false],

            // 멕시코
            'Mexico City' => ['멕시코', false],
            'Guadalajara' => ['멕시코', false],
            'Monterrey'   => ['멕시코', false],
            'Puebla'      => ['멕시코', false],

            // 콜롬비아
            'Bogotá' => ['콜롬비아', false],
            'Cali'   => ['콜롬비아', false],

            // 페루
            'Lima' => ['페루', false],

            // 에티오피아
            'Addis Ababa' => ['에티오피아', false],

            // 케냐
            'Nairobi' => ['케냐', false],

            // 르완다
            'Kigali' => ['르완다', false],

            // 나이지리아
            'Lagos'    => ['나이지리아', false],
            'Okpekpe'  => ['나이지리아', false],

            // 가봉
            'Port Gentil' => ['가봉', false],

            // 남아프리카공화국
            'Cape Town' => ['남아공', false],
            'Durban'    => ['남아공', false],

            // 모로코
            'Casablanca' => ['모로코', false],
            'Marrakesh'  => ['모로코', false],
            'Rabat'      => ['모로코', false],

            // 튀니지
            'Tunis' => ['튀니지', false],

            // 우즈베키스탄
            'Tashkent' => ['우즈베키스탄', false],

            // 키르기스스탄
            'Cholpon-Ata' => ['키르기스스탄', false],

            // 미얀마
            'Dawei' => ['미얀마', false],
        ];

        foreach ($cityCountry as $city => [$country, $isDomestic]) {
            DB::statement("
                UPDATE review.races
                SET is_domestic = ?, country = ?
                WHERE city = ?
            ", [(int) $isDomestic, $country, $city]);
        }

        // race_editions도 연결된 races 값으로 동기화
        DB::statement("
            UPDATE review.race_editions re
            SET is_domestic = r.is_domestic, country = r.country
            FROM review.races r
            WHERE re.race_id = r.id
        ");
    }

    public function down(): void
    {
        DB::statement("UPDATE review.races SET is_domestic = true, country = '대한민국'");
        DB::statement("UPDATE review.race_editions SET is_domestic = true, country = '대한민국'");
    }
};
