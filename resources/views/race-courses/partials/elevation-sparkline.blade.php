@php
    $profile = $profile ?? null;
    $width = (int) ($width ?? 120);
    $height = (int) ($height ?? 32);
    $points = (is_array($profile) && ! empty($profile['points'])) ? $profile['points'] : [];
    $canRender = count($points) >= 2;

    if ($canRender) {
        $thinned = [];
        $lastIdx = count($points) - 1;
        foreach ($points as $i => $p) {
            if ($i % 5 === 0 || $i === $lastIdx) {
                $thinned[] = $p;
            }
        }
        $totalDist = (float) ($profile['total_distance_m'] ?? end($thinned)['dist_m'] ?? 1);
        $totalDist = max($totalDist, 1);
        $minEle = (float) ($profile['min_elevation_m'] ?? min(array_column($thinned, 'ele_m')));
        $maxEle = (float) ($profile['max_elevation_m'] ?? max(array_column($thinned, 'ele_m')));
        $eleRange = max($maxEle - $minEle, 1);

        $pathParts = [];
        foreach ($thinned as $idx => $p) {
            $x = round(($p['dist_m'] / $totalDist) * $width, 2);
            $y = round($height - (($p['ele_m'] - $minEle) / $eleRange) * ($height - 4) - 2, 2);
            $pathParts[] = ($idx === 0 ? 'M' : 'L') . $x . ',' . $y;
        }
        $sparkPath = implode(' ', $pathParts);
    }
@endphp

@if($canRender)
    <svg class="elevation-sparkline" viewBox="0 0 {{ $width }} {{ $height }}" width="{{ $width }}" height="{{ $height }}" aria-hidden="true" focusable="false">
        <path d="{{ $sparkPath }}" fill="none" stroke="#FF6B35" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
@endif
