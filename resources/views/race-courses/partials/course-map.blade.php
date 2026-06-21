@php
    $mapId = $mapId ?? 'course-map';
    $height = $height ?? '360px';
    $coordinates = $coordinates ?? [];
    $markers = $markers ?? [];
    $kakaoKey = config('services.kakao.js_key');
    $canRender = $kakaoKey && !empty($coordinates);
    $hasElevation = collect($markers)->contains(fn ($m) => isset($m['elev_m']));
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
        .course-map-elev-list {
            border-top: 1px solid var(--border, #E8EAEE);
            padding: 0.75rem 1rem;
            background: var(--surface, #fff);
            max-height: 160px;
            overflow-y: auto;
        }
        .course-map-elev-row {
            display: flex; justify-content: space-between; align-items: center;
            gap: 0.75rem; padding: 0.35rem 0;
            font-size: 0.75rem; color: var(--text2, #5A6170);
            border-bottom: 1px solid rgba(232, 234, 238, 0.6);
        }
        .course-map-elev-row:last-child { border-bottom: none; }
        .course-map-elev-label { font-weight: 600; color: var(--text, #16181D); white-space: nowrap; }
        .course-map-elev-gain { color: #DC2626; font-family: 'Archivo', sans-serif; font-size: 0.72rem; }
        .course-map-elev-loss { color: #2563EB; font-family: 'Archivo', sans-serif; font-size: 0.72rem; }
    </style>
    @endpush
@endonce

<div class="course-map-wrap" id="{{ $mapId }}-wrap">
    @if($canRender)
        <div id="{{ $mapId }}" class="course-map-canvas" style="height: {{ $height }};"></div>
        @if($hasElevation)
            <div class="course-map-elev-list" id="{{ $mapId }}-elev-list" aria-label="구간 고도"></div>
        @endif
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

        window.pacRunFormatMarkerHtml = function(m) {
            var lines = ['<div style="padding:6px 10px;font-size:12px;line-height:1.5;min-width:90px;">'];
            var title = m.label || (m.km + 'km');
            if (m.elev_m != null) {
                title += ' · ' + m.elev_m + 'm';
            }
            lines.push('<strong>' + title + '</strong>');
            if (m.km > 0 && (m.gain_m > 0 || m.loss_m > 0)) {
                var parts = [];
                if (m.gain_m > 0) parts.push('<span style="color:#DC2626;">+' + m.gain_m + 'm</span>');
                if (m.loss_m > 0) parts.push('<span style="color:#2563EB;">-' + m.loss_m + 'm</span>');
                lines.push('<div style="margin-top:2px;">구간 ' + parts.join(' / ') + '</div>');
            }
            lines.push('</div>');
            return lines.join('');
        };

        window.pacRunRenderElevList = function(mapId, markers) {
            var list = document.getElementById(mapId + '-elev-list');
            if (!list || !markers || !markers.length) return;
            list.innerHTML = markers.map(function(m, idx) {
                if (m.elev_m == null) return '';
                var segHtml = '';
                if (idx > 0 && (m.gain_m > 0 || m.loss_m > 0)) {
                    var parts = [];
                    if (m.gain_m > 0) parts.push('<span class="course-map-elev-gain">+' + m.gain_m + 'm</span>');
                    if (m.loss_m > 0) parts.push('<span class="course-map-elev-loss">-' + m.loss_m + 'm</span>');
                    segHtml = '<span>' + parts.join(' ') + '</span>';
                } else if (idx === 0) {
                    segHtml = '<span style="color:var(--muted,#9AA1AE);">—</span>';
                }
                return '<div class="course-map-elev-row">'
                    + '<span class="course-map-elev-label">' + (m.label || m.km + 'km') + ' · ' + m.elev_m + 'm</span>'
                    + segHtml
                    + '</div>';
            }).join('');
        };

        window.pacRunInitCourseMap = function(mapId, coordinates, markers) {
            var container = document.getElementById(mapId);
            if (!container || typeof kakao === 'undefined' || !kakao.maps) {
                return false;
            }

            if (window.pacRunCourseMapInstances[mapId]) {
                var prev = window.pacRunCourseMapInstances[mapId];
                prev.polyline.setMap(null);
                (prev.markerList || []).forEach(function(m) { m.setMap(null); });
                (prev.infoWindows || []).forEach(function(iw) { iw.close(); });
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
            var infoWindows = [];
            (markers || []).forEach(function(m) {
                var pos = new kakao.maps.LatLng(m.lat, m.lng);
                var marker = new kakao.maps.Marker({ position: pos, map: map });
                var content = window.pacRunFormatMarkerHtml(m);
                var iw = new kakao.maps.InfoWindow({ content: content });
                var iwOpen = false;
                kakao.maps.event.addListener(marker, 'click', function() {
                    infoWindows.forEach(function(other) { if (other !== iw) other.close(); });
                    if (iwOpen) {
                        iw.close();
                    } else {
                        iw.open(map, marker);
                    }
                    iwOpen = !iwOpen;
                });
                markerList.push(marker);
                infoWindows.push(iw);
            });

            window.pacRunRenderElevList(mapId, markers);

            window.pacRunCourseMapInstances[mapId] = {
                map: map, polyline: polyline, markerList: markerList, infoWindows: infoWindows
            };
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
