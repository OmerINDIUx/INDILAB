@extends('layouts.admin')

@section('title', 'Dashboard | INDI Lab Admin')

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.45.0/dist/apexcharts.min.js"></script>
@endpush

@section('admin_content')
<!-- KPI Cards -->
<div class="kpi-grid">
    <div class="kpi-card">
        <div class="kpi-header">
            <div class="kpi-icon blue">
                <i class="fas fa-eye"></i>
            </div>
            <div class="kpi-trend up">
                <i class="fas fa-arrow-up"></i> 12%
            </div>
        </div>
        <div class="kpi-value">{{ number_format($stats['totalVisitors'] ?? 0) }}</div>
        <div class="kpi-label">Total Visitors (30d)</div>
    </div>
    
    <div class="kpi-card">
        <div class="kpi-header">
            <div class="kpi-icon green">
                <i class="fas fa-file-alt"></i>
            </div>
            <div class="kpi-trend up">
                <i class="fas fa-arrow-up"></i> 8%
            </div>
        </div>
        <div class="kpi-value">{{ number_format($stats['totalPageViews'] ?? 0) }}</div>
        <div class="kpi-label">Page Views (30d)</div>
    </div>
    
    <div class="kpi-card">
        <div class="kpi-header">
            <div class="kpi-icon yellow">
                <i class="fas fa-clock"></i>
            </div>
        </div>
        <div class="kpi-value">{{ $stats['avgTimeOnSite'] ?? '0:00' }}</div>
        <div class="kpi-label">Avg. Time on Site</div>
    </div>
    
    <div class="kpi-card">
        <div class="kpi-header">
            <div class="kpi-icon red">
                <i class="fas fa-envelope"></i>
            </div>
            @if(($stats['newContacts'] ?? 0) > 0)
            <div class="kpi-trend up">
                <i class="fas fa-bell"></i> New
            </div>
            @endif
        </div>
        <div class="kpi-value">{{ $stats['newContacts'] ?? 0 }}</div>
        <div class="kpi-label">Contact Submissions</div>
    </div>
</div>

<!-- Traffic Chart -->
<div class="chart-card">
    <div class="card-header">
        <h2 class="card-title">Traffic Overview</h2>
        <div class="card-actions">
            <button class="btn btn-secondary btn-sm">Last 7 days</button>
            <button class="btn btn-secondary btn-sm">Last 30 days</button>
        </div>
    </div>
    <div id="traffic-chart" style="height: 300px;"></div>
</div>

<!-- Two Column Layout -->
<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px; margin-bottom: 24px;">
    <!-- Top Pages -->
    <div class="chart-card">
        <div class="card-header">
            <h2 class="card-title">Top Pages</h2>
            <a href="#" class="btn btn-secondary btn-sm">View All</a>
        </div>
        
        @if(isset($topPages) && count($topPages) > 0)
        <table class="data-table">
            <thead>
                <tr>
                    <th>Page</th>
                    <th>Views</th>
                    <th>Avg. Time</th>
                    <th>Scroll %</th>
                </tr>
            </thead>
            <tbody>
                @foreach($topPages as $page)
                <tr>
                    <td>
                        <div style="font-weight: 500;">{{ $page->page_title ?? 'Untitled' }}</div>
                        <div style="font-size: 0.85rem; color: var(--dashboard-text-muted);">{{ $page->url }}</div>
                    </td>
                    <td>{{ number_format($page->views_count) }}</td>
                    <td>{{ gmdate('i:s', $page->avg_time ?? 0) }}</td>
                    <td>{{ round($page->avg_scroll ?? 0) }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="empty-state">
            <i class="fas fa-chart-bar"></i>
            <h3>No data yet</h3>
            <p>Page views will appear here once tracking is active</p>
        </div>
        @endif
    </div>
    
    <!-- Recent Contacts -->
    <div class="chart-card">
        <div class="card-header">
            <h2 class="card-title">Recent Contacts</h2>
            <a href="#" class="btn btn-secondary btn-sm">View All</a>
        </div>
        
        @if(isset($recentContacts) && count($recentContacts) > 0)
        <div style="display: flex; flex-direction: column; gap: 16px;">
            @foreach($recentContacts as $contact)
            <div style="padding: 12px; background: rgba(255,255,255,0.02); border-radius: 8px;">
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 8px;">
                    <div style="font-weight: 500;">{{ $contact->name }}</div>
                    <span class="status-badge {{ $contact->status }}">{{ ucfirst($contact->status) }}</span>
                </div>
                <div style="font-size: 0.85rem; color: var(--dashboard-text-muted); margin-bottom: 4px;">{{ $contact->email }}</div>
                <div style="font-size: 0.8rem; color: var(--dashboard-text-muted);">{{ $contact->created_at->diffForHumans() }}</div>
            </div>
            @endforeach
        </div>
        @else
        <div class="empty-state">
            <i class="fas fa-inbox"></i>
            <h3>No contacts yet</h3>
            <p>Contact submissions will appear here</p>
        </div>
        @endif
    </div>
</div>

<!-- Blog Performance -->
<div class="chart-card">
    <div class="card-header">
        <h2 class="card-title">Blog Performance</h2>
        <a href="{{ route('work.index') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i>
            New Project
        </a>
    </div>
    
    @if(isset($topBlogs) && count($topBlogs) > 0)
    <table class="data-table">
        <thead>
            <tr>
                <th>Project</th>
                <th>Views</th>
                <th>Avg. Read Time</th>
                <th>Completion Rate</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($topBlogs as $blog)
            <tr>
                <td>
                    <div style="font-weight: 500;">{{ $blog->title }}</div>
                    <div style="font-size: 0.85rem; color: var(--dashboard-text-muted);">{{ $blog->category ?? 'Uncategorized' }}</div>
                </td>
                <td>{{ number_format($blog->views_count ?? 0) }}</td>
                <td>{{ gmdate('i:s', $blog->avg_read_time ?? 0) }}</td>
                <td>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <div style="flex: 1; height: 6px; background: rgba(255,255,255,0.1); border-radius: 3px; overflow: hidden;">
                            <div style="height: 100%; background: var(--dashboard-success); width: {{ $blog->completion_rate ?? 0 }}%;"></div>
                        </div>
                        <span style="font-size: 0.85rem;">{{ round($blog->completion_rate ?? 0) }}%</span>
                    </div>
                </td>
                <td>
                    <span class="status-badge {{ !$blog->coming_soon ? 'replied' : 'new' }}">
                        {{ !$blog->coming_soon ? 'Published' : 'Coming Soon' }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div class="empty-state">
        <i class="fas fa-newspaper"></i>
        <h3>No projects yet</h3>
        <p>Create your first project to see performance metrics</p>
        <a href="{{ route('work.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            Create Project
        </a>
    </div>
    @endif
</div>

@push('scripts')
<script>
// Traffic Chart
const trafficOptions = {
    series: [{
        name: 'Visitors',
        data: {!! json_encode($chartData['visitors'] ?? [0,0,0,0,0,0,0]) !!}
    }, {
        name: 'Page Views',
        data: {!! json_encode($chartData['pageViews'] ?? [0,0,0,0,0,0,0]) !!}
    }],
    chart: {
        type: 'area',
        height: 300,
        toolbar: { show: false },
        background: 'transparent'
    },
    colors: ['#007bff', '#28a745'],
    dataLabels: { enabled: false },
    stroke: { curve: 'smooth', width: 2 },
    fill: {
        type: 'gradient',
        gradient: {
            opacityFrom: 0.6,
            opacityTo: 0.1,
        }
    },
    xaxis: {
        categories: {!! json_encode($chartData['labels'] ?? ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']) !!},
        labels: { style: { colors: '#999' } }
    },
    yaxis: {
        labels: { style: { colors: '#999' } }
    },
    grid: {
        borderColor: '#2a2a2a',
        strokeDashArray: 4
    },
    legend: {
        labels: { colors: '#eee' }
    },
    theme: { mode: 'dark' }
};

const trafficChart = new ApexCharts(document.querySelector("#traffic-chart"), trafficOptions);
trafficChart.render();
</script>
@endpush
@endsection
