@php
    $mapId = $mapId ?? 'course-map';
    $height = $height ?? '360px';
    $coordinates = $coordinates ?? [];
    $markers = $markers ?? [];
    $kakaoKey = config('services.kakao.js_key');
    $canRender = $kakaoKey && !empty($coordinates);
@endphp

@once
    @push('styles')
    <style>
        .course-map-wrap { position: relative; border-radius: 12px; overflow: hidden; border: 1px solid var(--border, #E8EAEE); background: var(--surface2, #F7F8FA); }
        .course-map-canvas { width: 100%; }
        .course-map-fallback {
            display: flex; align-items: center; justify-content: center;
            min-height: 200px; padding: 1.5rem; text-align: center;
            font-size: 0.82rem; color: var(--muted, #9AA1AE);
        }
    </style>
    @endpush
@endonce

<div class="course-map-wrap" id="{{ $mapId }}-wrap">
    @if($canRender)
        <div id="{{ $mapId }}" class="course-map-canvas" style="height: {{ $height }};"></div>
    @else
        <div class="course-map-fallback">코스 지도를 불러올 수 없습니다.</div>
    @endif
</div>

@if($canRender)
    @once
        @push('scripts')
        <script src="//dapi.kakao.com/v2/maps/sdk.js?appkey={{ $kakaoKey }}"></script>
        <script>
        window.pacRunCourseMapInstances = window.pacRunCourseMapInstances || {};

        window.pacRunInitCourseMap = function(mapId, coordinates, markers) {
            var container = document.getElementById(mapId);
            if (!container || typeof kakao === 'undefined' || !kakao.maps) {
                return false;
            }

            if (window.pacRunCourseMapInstances[mapId]) {
                var prev = window.pacRunCourseMapInstances[mapId];
                prev.polyline.setMap(null);
                (prev.markerList || []).forEach(function(m) { m.setMap(null); });
            }

            var path = coordinates.map(function(c) {
                return new kakao.maps.LatLng(c.lat, c.lng);
            });

            if (path.length === 0) return false;

            var center = path[Math.floor(path.length / 2)];
            var map = new kakao.maps.Map(container, { center: center, level: 5 });
            var polyline = new kakao.maps.Polyline({
                path: path,
                strokeWeight: 4,
                strokeColor: '#E80043',
                strokeOpacity: 0.9,
            });
            polyline.setMap(map);

            var bounds = new kakao.maps.LatLngBounds();
            path.forEach(function(p) { bounds.extend(p); });
            map.setBounds(bounds, 40, 40, 40, 40);

            var markerList = [];
            (markers || []).forEach(function(m) {
                var pos = new kakao.maps.LatLng(m.lat, m.lng);
                var marker = new kakao.maps.Marker({ position: pos, map: map });
                if (m.label) {
                    var iw = new kakao.maps.InfoWindow({ content: '<div style="padding:4px 8px;font-size:12px;">' + m.label + '</div>' });
                    var iwOpen = false;
                    kakao.maps.event.addListener(marker, 'click', function() {
                        if (iwOpen) {
                            iw.close();
                        } else {
                            iw.open(map, marker);
                        }
                        iwOpen = !iwOpen;
                    });
                }
                markerList.push(marker);
            });

            window.pacRunCourseMapInstances[mapId] = { map: map, polyline: polyline, markerList: markerList };
            return true;
        };
        </script>
        @endpush
    @endonce

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        pacRunInitCourseMap(@json($mapId), @json($coordinates), @json($markers));
    });
    </script>
    @endpush
@endif
