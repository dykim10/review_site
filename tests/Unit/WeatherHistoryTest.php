<?php

namespace Tests\Unit;

use App\Models\RaceEdition;
use App\Models\RaceWeather;
use App\Services\WeatherService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class WeatherHistoryTest extends TestCase
{
    private WeatherService $weather;

    protected function setUp(): void
    {
        parent::setUp();
        $this->weather = app(WeatherService::class);
    }

    public function test_selects_registered_past_editions_newest_first(): void
    {
        $editions = collect([
            $this->edition(2023, '2023-03-19'),
            $this->edition(2024, '2024-03-17'),
            $this->edition(2027, now()->addYear()->toDateString()),
        ]);

        $selected = $this->weather->selectHistoryEditions($editions);

        $this->assertSame([2024, 2023], $selected->pluck('year')->all());
    }

    public function test_limits_history_to_five_most_recent_registered_past_editions(): void
    {
        $editions = collect([
            $this->edition(2019, '2019-03-15'),
            $this->edition(2020, '2020-03-15'),
            $this->edition(2021, '2021-03-15'),
            $this->edition(2022, '2022-03-15'),
            $this->edition(2023, '2023-03-15'),
            $this->edition(2024, '2024-03-15'),
        ]);

        $selected = $this->weather->selectHistoryEditions($editions);

        $this->assertSame([2024, 2023, 2022, 2021, 2020], $selected->pluck('year')->all());
    }

    public function test_uses_loaded_weather_without_calling_api(): void
    {
        $edition = $this->edition(2024, '2024-03-17');
        $edition->setRelation('weather', new RaceWeather([
            'temperature' => 8.4,
            'weather_condition' => '흐림',
        ]));

        Http::fake();

        $history = $this->weather->getHistoryForEditions(collect([$edition]));

        Http::assertNothingSent();
        $this->assertCount(1, $history);
        $this->assertSame(8.4, $history[0]['weather']->temperature);
        $this->assertSame(2024, $history[0]['edition']->year);
    }

    private function edition(int $year, string $date): RaceEdition
    {
        return new RaceEdition([
            'year' => $year,
            'race_date' => $date,
            'name' => "서울마라톤 {$year}",
            'status' => 'ended',
        ]);
    }
}
