@extends('layouts.admin')

@section('title', 'Analytics Details | INDI Lab Admin')

@push('css')
<style>
    .analytics-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    .chart-card {
        background: var(--dashboard-card-bg);
        border: 1px solid var(--dashboard-border);
        border-radius: 12px;
        padding: 20px;
    }
    .chart-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    .chart-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--dashboard-text);
    }
    .data-table-container {
        background: var(--dashboard-card-bg);
        border: 1px solid var(--dashboard-border);
        border-radius: 12px;
        overflow: hidden;
    }
    .data-table {
        width: 100%;
        border-collapse: collapse;
    }
    .data-table th, .data-table td {
        padding: 12px 20px;
        text-align: left;
        border-bottom: 1px solid var(--dashboard-border);
    }
    .data-table th {
        background: rgba(255,255,255,0.02);
        font-weight: 600;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--dashboard-text-muted);
    }
    .data-table tr:last-child td {
        border-bottom: none;
    }
    .progress-bar-tiny {
        height: 4px;
        background: rgba(255,255,255,0.1);
        border-radius: 2px;
        overflow: hidden;
        margin-top: 5px;
    }
    .progress-fill {
        height: 100%;
        background: var(--dashboard-primary);
    }
</style>
@endpush

@section('admin_title', 'Detailed Analytics')
@section('admin_subtitle', 'Deep dive into user behavior and site performance')

@section('admin_actions')
<div class="range-selector" style="display: flex; gap: 10px; background: #1a1a1a; padding: 5px; border-radius: 8px;">
    <a href="?range=7" class="btn-range {{ $range == 7 ? 'active' : '' }}" style="padding: 5px 15px; border-radius: 5px; text-decoration: none; color: {{ $range == 7 ? '#fff' : '#888' }}; background: {{ $range == 7 ? '#333' : 'transparent' }}; font-size: 0.85rem;">7D</a>
    <a href="?range=30" class="btn-range {{ $range == 30 ? 'active' : '' }}" style="padding: 5px 15px; border-radius: 5px; text-decoration: none; color: {{ $range == 30 ? '#fff' : '#888' }}; background: {{ $range == 30 ? '#333' : 'transparent' }}; font-size: 0.85rem;">30D</a>
    <a href="?range=90" class="btn-range {{ $range == 90 ? 'active' : '' }}" style="padding: 5px 15px; border-radius: 5px; text-decoration: none; color: {{ $range == 90 ? '#fff' : '#888' }}; background: {{ $range == 90 ? '#333' : 'transparent' }}; font-size: 0.85rem;">90D</a>
</div>
@endsection

@section('admin_content')
<!-- Summary Cards -->
<div class="stats-grid" style="grid-template-columns: repeat(4, 1fr); margin-bottom: 25px;">
    <div class="stat-card">
        <div class="stat-icon" style="background: rgba(0,123,255,0.1); color: #007bff;"><i class="fas fa-eye"></i></div>
        <div class="stat-info">
            <h3>{{ number_format($stats['total_views']) }}</h3>
            <p>Total Views</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: rgba(40,167,69,0.1); color: #28a745;"><i class="fas fa-users"></i></div>
        <div class="stat-info">
            <h3>{{ number_format($stats['unique_visitors']) }}</h3>
            <p>Unique Visitors</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: rgba(255,193,7,0.1); color: #ffc107;"><i class="fas fa-clock"></i></div>
        <div class="stat-info">
            <h3>{{ gmdate('i:s', $stats['avg_time']) }}</h3>
            <p>Avg. Time</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: rgba(220,53,69,0.1); color: #dc3545;"><i class="fas fa-mouse-pointer"></i></div>
        <div class="stat-info">
            <h3>{{ round($stats['avg_scroll']) }}%</h3>
            <p>Avg. Scroll</p>
        </div>
    </div>
</div>

<div class="analytics-grid">
    <!-- Device Distribution -->
    <div class="chart-card">
        <div class="chart-header">
            <div class="chart-title">Device Distribution</div>
        </div>
        <div id="device-chart" style="height: 250px;"></div>
    </div>

    <!-- Browser Stats -->
    <div class="chart-card">
        <div class="chart-header">
            <div class="chart-title">Browser Usage</div>
        </div>
        <div id="browser-chart" style="height: 250px;"></div>
    </div>
</div>

<!-- Top Pages Table -->
<div class="data-table-container" style="margin-bottom: 30px;">
    <div style="padding: 20px; border-bottom: 1px solid var(--dashboard-border);">
        <h2 style="font-size: 1.1rem; font-weight: 600;">Top Visited Pages</h2>
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Page Path</th>
                <th>Views</th>
                <th>Avg. Time</th>
                <th>Avg. Scroll</th>
                <th>Retention</th>
            </tr>
        </thead>
        <tbody>
            @foreach($topPages as $page)
            <tr>
                <td>
                    <div style="font-weight: 500;">{{ $page->page_title ?: 'Untitled Page' }}</div>
                    <div style="font-size: 0.75rem; color: var(--dashboard-text-muted);">{{ $page->url }}</div>
                </td>
                <td>{{ number_format($page->views) }}</td>
                <td>{{ gmdate('i:s', $page->avg_time) }}</td>
                <td>{{ round($page->avg_scroll) }}%</td>
                <td>
                    <div class="progress-bar-tiny">
                        <div class="progress-fill" style="width: {{ $page->avg_scroll }}%; background: {{ $page->avg_scroll > 70 ? 'var(--dashboard-success)' : ($page->avg_scroll > 40 ? 'var(--dashboard-primary)' : '#ffc107') }}"></div>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Recent Visitor Log -->
<div class="data-table-container">
    <div style="padding: 20px; border-bottom: 1px solid var(--dashboard-border);">
        <h2 style="font-size: 1.1rem; font-weight: 600;">Visitor Activity Log</h2>
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Visitor Session</th>
                <th>Browsing Page</th>
                <th>Device / Browser</th>
                <th>Time Spent</th>
                <th>Timestamp</th>
            </tr>
        </thead>
        <tbody>
            @foreach($visitorLogs as $log)
            <tr>
                <td style="font-family: monospace; font-size: 0.8rem;">
                    {{ substr($log->session_id, 0, 12) }}...
                </td>
                <td>
                    <div style="font-size: 0.9rem;">{{ $log->url }}</div>
                </td>
                <td>
                    <span style="font-size: 0.75rem; color: var(--dashboard-text-muted);">
                        <i class="fas fa-{{ $log->device_type == 'mobile' ? 'mobile-alt' : ($log->device_type == 'tablet' ? 'tablet-alt' : 'desktop') }}"></i>
                        {{ $log->browser }}
                    </span>
                </td>
                <td>
                    {{ $log->time_on_page ? gmdate('i:s', $log->time_on_page) : '--' }}
                </td>
                <td>
                    <div style="font-size: 0.8rem;">{{ $log->created_at->diffForHumans() }}</div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.45.0/dist/apexcharts.min.js"></script>
<script>
    // Device Chart
    const deviceOptions = {
        series: {!! json_encode($devices->pluck('count')) !!},
        labels: {!! json_encode($devices->pluck('device_type')->map(fn($t) => ucfirst($t))) !!},
        chart: {
            type: 'donut',
            height: '100%',
        },
        colors: ['#007bff', '#28a745', '#ffc107'],
        theme: { mode: 'dark' },
        legend: { position: 'bottom' },
        stroke: { show: false }
    };
    new ApexCharts(document.querySelector("#device-chart"), deviceOptions).render();

    // Browser Chart
    const browserOptions = {
        series: [{
            name: 'Visits',
            data: {!! json_encode($browsers->pluck('count')) !!}
        }],
        chart: {
            type: 'bar',
            height: '100%',
            toolbar: { show: false }
        },
        plotOptions: {
            bar: {
                borderRadius: 4,
                horizontal: true,
            }
        },
        xaxis: {
            categories: {!! json_encode($browsers->pluck('browser')) !!},
            labels: { style: { colors: '#888' } }
        },
        yaxis: {
            labels: { style: { colors: '#888' } }
        },
        colors: ['#28a745'],
        theme: { mode: 'dark' }
    };
    new ApexCharts(document.querySelector("#browser-chart"), browserOptions).render();
</script>
@endpush
@endsection
