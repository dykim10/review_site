<?php

/**
 * 국내 pilot 4대회 — races + race_editions provision (TASK-16 / TASK-02).
 *
 * dates: 공식·검증된 날짜만. 미확정 연도는 키 생략 → null (추측 금지).
 * search_names: marathongo 날짜 조회 시 이름 매칭용.
 */
return [
    'pilots' => [
        'seoul' => [
            'name'            => '서울국제마라톤',
            'city'            => '서울',
            'weather_stn_id'  => 108,
            'search_names'    => ['서울마라톤', '서울국제마라톤', '동아마라톤'],
            'dates'           => [
                2024 => '2024-03-03',
                2025 => '2025-03-16',
            ],
        ],
        'daegu' => [
            'name'            => '대구마라톤',
            'city'            => '대구',
            'weather_stn_id'  => 143,
            'search_names'    => ['대구국제마라톤', '대구마라톤'],
            'dates'           => [
                2024 => '2024-04-07',
                2025 => '2025-02-23',
            ],
        ],
        'gyeongju' => [
            'name'            => '경주마라톤',
            'city'            => '경주',
            'weather_stn_id'  => 136,
            'search_names'    => ['경주마라톤', '경주국제마라톤'],
            'dates'           => [
                2024 => '2024-10-20',
                2025 => '2025-10-18',
            ],
        ],
        'gunsan' => [
            'name'            => '군산 새만금 국제 마라톤',
            'city'            => '군산',
            'weather_stn_id'  => 146,
            'search_names'    => ['군산', '새만금'],
            'dates'           => [
                2024 => '2024-04-07',
                2025 => '2025-04-06',
            ],
        ],
    ],
];
