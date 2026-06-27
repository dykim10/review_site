@php
    $chartId = $chartId ?? 'elevation-chart';
    $height = $height ?? '180px';
    $profile = $profile ?? null;
    $canRender = is_array($profile) && ! empty($profile['points']);
@endphp

@once
    @push('styles')
    <style>
        .elevation-profile-wrap { width: 100%; }
        .elevation-profile-stats {
            display: flex; flex-wrap: wrap; gap: 0.5rem 1rem;
            font-size: 0.78rem; color: var(--text2, #5A6170); margin-bottom: 0.75rem;
        }
        .elevation-profile-stats strong { color: var(--text, #16181D); font-weight: 600; }
        .elevation-profile-controls { display: flex; gap: 0.4rem; margin-bottom: 0.65rem; }
        .elevation-step-btn {
            padding: 0.3rem 0.75rem; border-radius: 6px; font-size: 0.75rem; font-weight: 600;
            border: 1px solid var(--border, #E8EAEE); background: var(--surface2, #F7F8FA);
            color: var(--text2, #5A6170); cursor: pointer; transition: all 0.15s;
        }
        .elevation-step-btn-active {
            background: #E80043; border-color: #E80043; color: #fff;
        }
        .elevation-chart-canvas-wrap { position: relative; width: 100%; }
        .elevation-profile-fallback {
            padding: 1.25rem; text-align: center; font-size: 0.82rem;
            color: var(--muted, #9AA1AE); background: var(--surface2, #F7F8FA);
            border-radius: 8px; border: 1px dashed var(--border, #E8EAEE);
        }
    </style>
    @endpush

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
    window.pacRunElevationChart = function(profile) {
        // Chart.js 인스턴스는 Alpine reactive data에 두면 proxy 순환 → stack overflow
        var chartInstance = null;

        function destroyChart() {
            if (chartInstance) {
                chartInstance.destroy();
                chartInstance = null;
            }
        }

        function buildChart(canvas, profileData, step) {
            if (!canvas || typeof Chart === 'undefined') return;
            if (!profileData || !profileData.points || !profileData.points.length) return;

            destroyChart();

            var data = profileData.points.map(function(p) {
                return { x: p.dist_m / 1000, y: p.ele_m };
            });
            var ctx = canvas.getContext('2d');
            var h = canvas.parentElement ? canvas.parentElement.clientHeight : 180;
            var grad = ctx.createLinearGradient(0, 0, 0, h);
            grad.addColorStop(0, 'rgba(255, 107, 53, 0.55)');
            grad.addColorStop(1, 'rgba(255, 107, 53, 0.04)');

            chartInstance = new Chart(ctx, {
                type: 'line',
                data: {
                    datasets: [{
                        data: data,
                        parsing: false,
                        fill: true,
                        backgroundColor: grad,
                        borderColor: '#FF6B35',
                        borderWidth: 1.5,
                        pointRadius: 0,
                        tension: 0.3,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: false,
                    interaction: { mode: 'index', intersect: false },
                    scales: {
                        x: {
                            type: 'linear',
                            min: 0,
                            max: profileData.total_distance_m / 1000,
                            ticks: {
                                stepSize: step,
                                callback: function(v) { return v + 'km'; },
                            },
                            grid: { display: false },
                        },
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 20 },
                            grid: { color: 'rgba(0,0,0,0.06)' },
                        },
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                title: function(items) {
                                    var p = items && items[0] && items[0].parsed;
                                    return p ? p.x.toFixed(1) + 'km' : '';
                                },
                                label: function(item) {
                                    var p = item && item.parsed;
                                    return p ? p.y.toFixed(0) + 'm' : '';
                                },
                            },
                        },
                    },
                },
            });
        }

        return {
            step: 5,
            profile: profile,
            init() {
                var self = this;
                var rootId = this.$el.id;
                this.$nextTick(function() {
                    buildChart(self.$refs.canvas, self.profile, self.step);
                });
                window.addEventListener('pac-elevation-profile-switch', function(e) {
                    if (e.detail && e.detail.rootId === rootId && e.detail.profile) {
                        self.profile = e.detail.profile;
                        buildChart(self.$refs.canvas, self.profile, self.step);
                    }
                });
            },
            setStep(s) {
                this.step = s;
                if (!chartInstance) {
                    buildChart(this.$refs.canvas, this.profile, s);
                    return;
                }
                var xScale = chartInstance.scales.x;
                if (xScale && xScale.options && xScale.options.ticks) {
                    xScale.options.ticks.stepSize = s;
                    chartInstance.update('none');
                } else {
                    buildChart(this.$refs.canvas, this.profile, s);
                }
            },
            updateProfile(nextProfile) {
                this.profile = nextProfile;
                buildChart(this.$refs.canvas, this.profile, this.step);
            },
        };
    };

    window.pacRunSwitchElevationChart = function(rootId, profile) {
        window.dispatchEvent(new CustomEvent('pac-elevation-profile-switch', {
            detail: { rootId: rootId, profile: profile },
        }));
    };
    </script>
    @endpush
@endonce

@if($canRender)
    <div class="elevation-profile-wrap"
         id="{{ $chartId }}-root"
         x-data="pacRunElevationChart(@js($profile))"
         x-init="init()">
        <div class="elevation-profile-stats">
            <span>최고 <strong>{{ number_format($profile['max_elevation_m'] ?? 0, 0) }}m</strong></span>
            <span>최저 <strong>{{ number_format($profile['min_elevation_m'] ?? 0, 0) }}m</strong></span>
            <span>누적상승 <strong>{{ number_format($profile['total_ascent_m'] ?? 0, 0) }}m</strong></span>
        </div>
        <div class="elevation-profile-controls">
            <button type="button" class="elevation-step-btn"
                    :class="step === 1 ? 'elevation-step-btn-active' : ''"
                    @click="setStep(1)">1km</button>
            <button type="button" class="elevation-step-btn"
                    :class="step === 5 ? 'elevation-step-btn-active' : ''"
                    @click="setStep(5)">5km</button>
        </div>
        <div class="elevation-chart-canvas-wrap" style="height: {{ $height }};">
            <canvas x-ref="canvas" aria-label="고저도 프로파일"></canvas>
        </div>
    </div>
@else
    <div class="elevation-profile-fallback">고저도 프로파일을 불러올 수 없습니다.</div>
@endif
