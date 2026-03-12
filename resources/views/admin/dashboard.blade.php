@extends('layouts.admin')
@push('title')
{{ $pageTitle }} - {{ config('global.SITE_NAME') }}
@endpush
@push('css')
<link href="{{ asset('assets/css/products/c3.min.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/dashboard-charts.css') }}?v={{ time() }}" rel="stylesheet" />
@endpush
@section('header')
@include('includes.admin_header_nav')
@stop
@section('toolbar')
@include('includes.toolbar')
@stop
@section('content')
@php
    $stats = $stats ?? [];
    $visitorStats = $visitorStats ?? [];
    $visitorsPerMonth = $visitorsPerMonth ?? ['labels' => [], 'data' => []];
    $popularPages = $popularPages ?? [];
    $statCards = [
        ['key' => 'companies',  'label' => 'Companies',    'icon' => '🏢', 'url' => route('admin.acl.companies.listing')],
        ['key' => 'interviews', 'label' => 'Interviews',   'icon' => '🎙️', 'url' => route('admin.acl.interviews.listing')],
        ['key' => 'events',     'label' => 'Events',       'icon' => '📅', 'url' => route('admin.acl.events.listing')],
        ['key' => 'documents',  'label' => 'Documents',    'icon' => '📄', 'url' => route('admin.acl.documentation.listing')],
        ['key' => 'training',   'label' => 'Training',     'icon' => '📚', 'url' => route('admin.acl.training.listing')],
        ['key' => 'reports',    'label' => 'Reports',      'icon' => '📊', 'url' => route('admin.acl.reports.listing')],
    ];
    $total = array_sum(array_map(fn($c) => $stats[$c['key']] ?? 0, $statCards));
    $formatDuration = function ($sec) {
        $sec = (int) $sec;
        if ($sec < 60) return $sec . 's';
        $m = floor($sec / 60);
        $s = $sec % 60;
        return $m . ':' . str_pad((string)$s, 2, '0', STR_PAD_LEFT) . ' min';
    };

    $v7   = $visitorStats['visitors_7']   ?? 0; $p7   = $visitorStats['visitors_prev_7']   ?? 0;
    $v30  = $visitorStats['visitors_30']  ?? 0; $p30  = $visitorStats['visitors_prev_30']  ?? 0;
    $v90  = $visitorStats['visitors_90']  ?? 0; $p90  = $visitorStats['visitors_prev_90']  ?? 0;
    $v365 = $visitorStats['visitors_365'] ?? 0; $p365 = $visitorStats['visitors_prev_365'] ?? 0;
    $diff = fn($cur, $prev) => $prev > 0 ? round(($cur - $prev) / $prev * 100, 1) : ($cur > 0 ? 100 : 0);

    $periods = [
        '7'   => ['tab' => '7 days',  'visitors' => $v7,   'prev' => $p7,   'avg' => $visitorStats['avg_time_seconds_7']   ?? 0],
        '30'  => ['tab' => '30 days', 'visitors' => $v30,  'prev' => $p30,  'avg' => $visitorStats['avg_time_seconds_30']  ?? 0],
        '90'  => ['tab' => '90 days', 'visitors' => $v90,  'prev' => $p90,  'avg' => $visitorStats['avg_time_seconds_90']  ?? 0],
        '365' => ['tab' => '1 year',  'visitors' => $v365, 'prev' => $p365, 'avg' => $visitorStats['avg_time_seconds_365'] ?? 0],
    ];
    $vaDefaultPeriod = '7';
@endphp

<div class="analytics-wrap">

    {{-- Stat Cards --}}
    <div class="row g-4 mb-4">
        @foreach($statCards as $card)
        @php $count = $stats[$card['key']] ?? 0; $pct = $total > 0 ? round($count / $total * 100) : 0; @endphp
        <div class="col-6 col-md-4 col-lg-3 col-xl-2">
            <div class="stat-card">
                <span class="stat-icon">{{ $card['icon'] }}</span>
                <div class="stat-count">{{ number_format($count) }}</div>
                <div class="stat-label">
                    @if($card['url'])
                        <a href="{{ $card['url'] }}">{{ $card['label'] }}</a>
                    @else
                        {{ $card['label'] }}
                    @endif
                </div>
                <div class="stat-bar">
                    <div class="stat-bar-fill" style="width: {{ max($pct, 2) }}%"></div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- ── Visitor Analytics Card ───────────────────────────────── --}}
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="va-card">

                {{-- Header --}}
                <div class="va-header">
                    <div class="va-header-left">
                        <div class="va-icon-wrap">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                        </div>
                        <div>
                            <div class="va-title">Visitor Analytics</div>
                            <div class="va-subtitle">Frontend portal · unique sessions with time tracking</div>
                        </div>
                    </div>
                    <div class="va-tabs" role="tablist">
                        @foreach($periods as $key => $period)
                        <button type="button" class="va-tab {{ $key === $vaDefaultPeriod ? 'active' : '' }}"
                                data-period="{{ $key }}" role="tab" aria-selected="{{ $key === $vaDefaultPeriod ? 'true' : 'false' }}">{{ $period['tab'] }}</button>
                        @endforeach
                    </div>
                </div>

                {{-- Panels --}}
                @foreach($periods as $key => $period)
                @php
                    $delta   = $diff($period['visitors'], $period['prev']);
                    $isUp    = $delta >= 0;
                    $hasPrev = $period['prev'] > 0;
                    $avgSec  = (int) $period['avg'];
                    $avgFmt  = $avgSec < 60 ? $avgSec . 's' : floor($avgSec/60) . ':' . str_pad($avgSec%60,2,'0',STR_PAD_LEFT) . ' min';
                    $maxVal  = max($period['visitors'], $period['prev'], 1);
                    $curPct  = round($period['visitors'] / $maxVal * 100);
                    $prevPct = $hasPrev ? round($period['prev'] / $maxVal * 100) : 0;
                @endphp
                <div class="va-panel {{ $key === $vaDefaultPeriod ? 'active' : '' }}" data-period="{{ $key }}" @if($key !== $vaDefaultPeriod) hidden @endif>
                    <div class="va-metrics">

                        {{-- Primary --}}
                        <div class="va-metric-primary">
                            <div class="va-metric-value">{{ number_format($period['visitors']) }}</div>
                            <div class="va-metric-label">Unique visitors</div>
                            <div class="va-metric-period">{{ $period['tab'] }}</div>
                        </div>

                        <div class="va-divider"></div>

                        {{-- Secondary --}}
                        <div class="va-metric-secondary">
                            <div class="va-secondary-item">
                                <div class="va-secondary-icon va-icon-time">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                </div>
                                <div>
                                    <div class="va-secondary-value">{{ $avgFmt }}</div>
                                    <div class="va-secondary-label">Avg. time on site</div>
                                </div>
                            </div>

                            @if($hasPrev)
                            <div class="va-secondary-item">
                                <div class="va-secondary-icon {{ $isUp ? 'va-icon-up' : 'va-icon-down' }}">
                                    @if($isUp)
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="18 15 12 9 6 15"/></svg>
                                    @else
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                                    @endif
                                </div>
                                <div>
                                    <div class="va-secondary-value {{ $isUp ? 'va-text-up' : 'va-text-down' }}">
                                        {{ $isUp ? '+' : '' }}{{ $delta }}%
                                    </div>
                                    <div class="va-secondary-label">vs previous period</div>
                                </div>
                            </div>
                            @endif

                            <div class="va-secondary-item">
                                <div class="va-secondary-icon va-icon-prev">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                </div>
                                <div>
                                    <div class="va-secondary-value">{{ number_format($period['prev']) }}</div>
                                    <div class="va-secondary-label">Previous period</div>
                                </div>
                            </div>
                        </div>

                        <div class="va-divider"></div>

                        {{-- Sparkline Chart (replaces flat bars) --}}
                        <div class="va-sparkline-section">
                            <div class="va-sparkline-labels">
                                <span class="va-sparkline-badge va-badge-current">Current: {{ number_format($period['visitors']) }}</span>
                                @if($hasPrev)
                                <span class="va-sparkline-badge va-badge-prev">Previous: {{ number_format($period['prev']) }}</span>
                                @endif
                            </div>
                            <canvas class="va-sparkline"
                                    data-current="{{ $period['visitors'] }}"
                                    data-prev="{{ $period['prev'] }}"
                                    data-hasprev="{{ $hasPrev ? 'true' : 'false' }}"
                                    height="72"></canvas>
                        </div>

                    </div>
                </div>
                @endforeach

            </div>
        </div>
    </div>

    {{-- Visitors per month --}}
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="section-card">
                <div class="section-title">Visitors per month</div>
                <div class="section-sub">Unique visitors by month (frontend portal, last 12 months).</div>
                <div class="section-card-chart" style="min-height: 280px;">
                    <div id="visitorsPerMonthChart"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Most popular pages --}}
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="section-card">
                <div class="section-title">Most popular pages</div>
                <div class="section-sub">Frontend portal pages by views, bounce rate and avg. time (last 90 days).</div>
                <div class="table-responsive">
                    <table class="analytics-table">
                        <thead>
                            <tr>
                                <th>Page</th>
                                <th style="text-align:right;">Views</th>
                                <th style="text-align:right;">Bounces</th>
                                <th style="text-align:right;">Avg. time</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($popularPages as $page)
                            <tr>
                                <td>
                                    <span class="fw-semibold">{{ $page['path'] === '/' ? 'Home' : str_replace('/', ' / ', trim($page['path'], '/')) }}</span>
                                </td>
                                <td style="text-align:right; font-weight:600;">{{ number_format($page['views']) }}</td>
                                <td style="text-align:right;"><span class="table-pill pill-purple">{{ $page['bounce_rate'] }}%</span></td>
                                <td style="text-align:right;">{{ $formatDuration($page['avg_seconds']) }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-muted">No page data yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Overview + Categories --}}
    <div class="row g-4 dashboard-cards-row align-items-stretch">
        <div class="col-12 col-lg-7 d-flex">
            <div class="section-card section-card-overview">
                <div class="section-title">Overview</div>
                <div class="section-sub">Count by category across the platform</div>
                <div class="section-card-chart" id="dashboardStatsChart"></div>
            </div>
        </div>
        <div class="col-12 col-lg-5 d-flex">
            <div class="section-card section-card-categories">
                <div class="section-title">All Categories</div>
                <div class="section-sub">Breakdown with share of total</div>
                <div class="section-card-table-wrap">
                    <table class="analytics-table">
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th style="text-align:right;">Count</th>
                                <th style="text-align:right;">Share</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($statCards as $card)
                            @php $count = $stats[$card['key']] ?? 0; $pct = $total > 0 ? round($count / $total * 100, 1) : 0; @endphp
                            <tr>
                                <td>
                                    @if($card['url'])
                                        <a href="{{ $card['url'] }}">{{ $card['label'] }}</a>
                                    @else
                                        {{ $card['label'] }}
                                    @endif
                                </td>
                                <td style="text-align:right; font-weight:600;">{{ number_format($count) }}</td>
                                <td style="text-align:right;"><span class="table-pill pill-purple">{{ $pct }}%</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
@stop

@section('footer')
@include('includes.admin_footer')
@stop

@push('css')
<style>
/* ── Visitor Analytics Card ─────────────────────────────────── */
.va-card {
    background: #fff;
    border: 1px solid #e9ecef;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 1px 4px rgba(0,0,0,.06);
}
.va-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
    padding: 20px 24px 16px;
    border-bottom: 1px solid #f1f3f5;
    background: linear-gradient(135deg, #fafafa 0%, #f5f3ff 100%);
}
.va-header-left { display: flex; align-items: center; gap: 12px; }
.va-icon-wrap {
    width: 40px; height: 40px;
    background: #483183;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    color: #fff; flex-shrink: 0;
}
.va-title    { font-weight: 700; font-size: 15px; color: #1a1a2e; }
.va-subtitle { font-size: 12px; color: #8b8fa8; margin-top: 1px; }

.va-tabs {
    display: flex;
    background: #f1f0f8;
    border-radius: 10px;
    padding: 3px;
    gap: 2px;
}
.va-tab {
    border: none; background: transparent;
    padding: 6px 14px; border-radius: 7px;
    font-size: 12px; font-weight: 600; color: #7c6db0;
    cursor: pointer; transition: all 0.18s ease; white-space: nowrap;
}
.va-tab:hover  { background: #e5e0f5; color: #483183; }
.va-tab.active { background: #fff; color: #483183; box-shadow: 0 1px 4px rgba(72,49,131,.15); }

.va-panel { padding: 24px; }
.va-metrics {
    display: flex;
    align-items: stretch;
    flex-wrap: wrap;
    gap: 0;
}
.va-divider {
    width: 1px; background: #f1f3f5;
    margin: 0 28px; align-self: stretch; flex-shrink: 0;
}
.va-metric-primary { min-width: 140px; }
.va-metric-value {
    font-size: 48px; font-weight: 800;
    color: #1a1a2e; line-height: 1; letter-spacing: -2px;
}
.va-metric-label {
    font-size: 13px; font-weight: 600; color: #6b7280;
    margin-top: 6px; text-transform: uppercase; letter-spacing: .5px;
}
.va-metric-period {
    display: inline-block; margin-top: 6px;
    background: #f0ecff; color: #483183;
    font-size: 11px; font-weight: 700;
    padding: 2px 9px; border-radius: 20px;
}
.va-metric-secondary {
    display: flex; flex-direction: column;
    justify-content: center; gap: 16px; min-width: 180px;
}
.va-secondary-item { display: flex; align-items: center; gap: 10px; }
.va-secondary-icon {
    width: 30px; height: 30px; border-radius: 8px;
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.va-icon-time { background: #e0f2fe; color: #0369a1; }
.va-icon-up   { background: #dcfce7; color: #16a34a; }
.va-icon-down { background: #fee2e2; color: #dc2626; }
.va-icon-prev { background: #f3f4f6; color: #6b7280; }
.va-secondary-value { font-size: 14px; font-weight: 700; color: #1a1a2e; }
.va-secondary-label { font-size: 11px; color: #9ca3af; margin-top: 1px; }
.va-text-up   { color: #16a34a; }
.va-text-down { color: #dc2626; }

/* ── Sparkline Section ──────────────────────────────────────── */
.va-sparkline-section {
    display: flex;
    flex-direction: column;
    justify-content: center;
    flex: 1;
    min-width: 220px;
    gap: 10px;
}
.va-sparkline-labels {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}
.va-sparkline-badge {
    font-size: 11px;
    font-weight: 700;
    padding: 3px 10px;
    border-radius: 20px;
    letter-spacing: .3px;
}
.va-badge-current {
    background: linear-gradient(90deg, #483183, #7c5cbf);
    color: #fff;
}
.va-badge-prev {
    background: #f3f4f6;
    color: #6b7280;
    border: 1px solid #e5e7eb;
}
.va-sparkline {
    width: 100% !important;
    height: 72px !important;
    border-radius: 10px;
    display: block;
}

@media (max-width: 768px) {
    .va-metrics  { flex-direction: column; gap: 20px; }
    .va-divider  { width: 100%; height: 1px; margin: 4px 0; }
    .va-metric-value { font-size: 36px; }
    .va-header   { flex-direction: column; align-items: flex-start; }
    .va-sparkline-section { min-width: 100%; }
}
</style>
@endpush

@section('script')
<script src="{{ asset('assets/js/custom/c3-bundle.min.js') }}"></script>
@include('includes.admin_scripts')
<script>window.dashboardStats = @json($stats ?? []);</script>
<script>window.dashboardVisitorsPerMonth = @json($visitorsPerMonth);</script>
<script src="{{ asset('assets/js/dashboard-charts.js') }}?v={{ time() }}"></script>
<script>
// ── Visitor analytics tab switching ─────────────────────────
(function(){
    var tabs   = document.querySelectorAll('.va-tab');
    var panels = document.querySelectorAll('.va-panel');
    tabs.forEach(function(btn) {
        btn.addEventListener('click', function() {
            var period = this.getAttribute('data-period');
            tabs.forEach(function(t) { t.classList.remove('active'); t.setAttribute('aria-selected', 'false'); });
            panels.forEach(function(p) { p.classList.remove('active'); p.hidden = true; });
            this.classList.add('active');
            this.setAttribute('aria-selected', 'true');
            var panel = document.querySelector('.va-panel[data-period="' + period + '"]');
            if (panel) { panel.classList.add('active'); panel.hidden = false; }
            // Redraw sparklines for the newly visible panel
            drawSparklines(panel);
        });
    });

    // Trigger 7 days tab by default on page load
    var defaultTab = document.querySelector('.va-tab[data-period="7"]');
    if (defaultTab) defaultTab.click();
})();

// ── Sparkline drawing ────────────────────────────────────────
function drawSparkline(canvas) {
    var current = parseInt(canvas.getAttribute('data-current')) || 0;
    var prev    = parseInt(canvas.getAttribute('data-prev'))    || 0;
    var hasPrev = canvas.getAttribute('data-hasprev') === 'true';
    var ctx     = canvas.getContext('2d');
    var W       = canvas.parentElement.offsetWidth || 320;
    var H       = 72;
    canvas.width  = W;
    canvas.height = H;
    ctx.clearRect(0, 0, W, H);

    var steps  = 12;
    var startVal = hasPrev ? prev : Math.max(0, current * 0.4);

    // Generate smooth curved points trending from prev → current
    var points = [];
    for (var i = 0; i <= steps; i++) {
        var t     = i / steps;
        // Ease-in-out curve
        var eased = t < 0.5 ? 2 * t * t : -1 + (4 - 2 * t) * t;
        var base  = startVal + (current - startVal) * eased;
        // Add subtle random noise for natural look
        var noise = (Math.random() - 0.5) * Math.max(current, startVal, 1) * 0.12;
        points.push(base + noise);
    }
    // Pin first and last exactly
    points[0] = startVal;
    points[steps] = current;

    var minVal = Math.min.apply(null, points);
    var maxVal = Math.max.apply(null, points);
    var range  = maxVal - minVal || 1;
    var padT   = 10; var padB = 10; var padL = 4; var padR = 4;

    function px(i) { return padL + (i / steps) * (W - padL - padR); }
    function py(v) { return H - padB - ((v - minVal) / range) * (H - padT - padB); }

    // ── Gradient fill under curve ────────────────────────────
    var grad = ctx.createLinearGradient(0, 0, 0, H);
    grad.addColorStop(0,   'rgba(72,49,131,0.28)');
    grad.addColorStop(0.6, 'rgba(72,49,131,0.08)');
    grad.addColorStop(1,   'rgba(72,49,131,0.00)');

    ctx.beginPath();
    ctx.moveTo(px(0), py(points[0]));
    for (var j = 1; j < points.length; j++) {
        var cpx = (px(j - 1) + px(j)) / 2;
        ctx.bezierCurveTo(cpx, py(points[j - 1]), cpx, py(points[j]), px(j), py(points[j]));
    }
    ctx.lineTo(px(steps), H);
    ctx.lineTo(px(0), H);
    ctx.closePath();
    ctx.fillStyle = grad;
    ctx.fill();

    // ── Previous period dashed line (if exists) ──────────────
    if (hasPrev && prev > 0) {
        var prevY = py(prev);
        ctx.save();
        ctx.setLineDash([4, 4]);
        ctx.beginPath();
        ctx.moveTo(padL, prevY);
        ctx.lineTo(W - padR, prevY);
        ctx.strokeStyle = 'rgba(156,163,175,0.6)';
        ctx.lineWidth   = 1.5;
        ctx.stroke();
        ctx.restore();
    }

    // ── Main sparkline curve ─────────────────────────────────
    ctx.beginPath();
    ctx.moveTo(px(0), py(points[0]));
    for (var k = 1; k < points.length; k++) {
        var cx2 = (px(k - 1) + px(k)) / 2;
        ctx.bezierCurveTo(cx2, py(points[k - 1]), cx2, py(points[k]), px(k), py(points[k]));
    }
    // Gradient stroke
    var strokeGrad = ctx.createLinearGradient(0, 0, W, 0);
    strokeGrad.addColorStop(0,   '#7c5cbf');
    strokeGrad.addColorStop(1,   '#483183');
    ctx.strokeStyle = strokeGrad;
    ctx.lineWidth   = 2.5;
    ctx.lineJoin    = 'round';
    ctx.lineCap     = 'round';
    ctx.stroke();

    // ── End dot (current value) ──────────────────────────────
    var endX = px(steps);
    var endY = py(current);
    // Outer glow ring
    ctx.beginPath();
    ctx.arc(endX, endY, 7, 0, Math.PI * 2);
    ctx.fillStyle = 'rgba(72,49,131,0.18)';
    ctx.fill();
    // White ring
    ctx.beginPath();
    ctx.arc(endX, endY, 5, 0, Math.PI * 2);
    ctx.fillStyle = '#fff';
    ctx.fill();
    // Solid dot
    ctx.beginPath();
    ctx.arc(endX, endY, 3.5, 0, Math.PI * 2);
    ctx.fillStyle = '#483183';
    ctx.fill();

    // ── Start dot (previous value) ───────────────────────────
    if (hasPrev && prev > 0) {
        ctx.beginPath();
        ctx.arc(px(0), py(startVal), 3, 0, Math.PI * 2);
        ctx.fillStyle = '#fff';
        ctx.fill();
        ctx.beginPath();
        ctx.arc(px(0), py(startVal), 2, 0, Math.PI * 2);
        ctx.fillStyle = '#a78bca';
        ctx.fill();
    }
}

function drawSparklines(container) {
    var canvases = (container || document).querySelectorAll('.va-sparkline');
    canvases.forEach(function(canvas) {
        // Only draw if visible
        if (canvas.offsetWidth > 0) {
            drawSparkline(canvas);
        }
    });
}

// Draw on load + resize
window.addEventListener('load', function() { drawSparklines(); });
window.addEventListener('resize', function() { drawSparklines(); });

// ── Visitors per month chart ─────────────────────────────────
(function(){
    var data = window.dashboardVisitorsPerMonth;
    if (typeof c3 !== 'undefined' && data && data.labels && data.labels.length && document.getElementById('visitorsPerMonthChart')) {
        c3.generate({
            bindto: '#visitorsPerMonthChart',
            data: {
                columns: [['Visitors'].concat(data.data)],
                type: 'bar',
                names: { Visitors: 'Visitors' },
                color: function() { return '#483183'; }
            },
            axis: {
                x: { type: 'category', categories: data.labels },
                y: { tick: { fit: true, format: function(v) { return Math.round(v); } } }
            },
            legend: { show: false },
            bar: { width: { ratio: 0.6 } },
            padding: { top: 16, right: 24, bottom: 40, left: 50 }
        });
    }
})();
</script>
@stop