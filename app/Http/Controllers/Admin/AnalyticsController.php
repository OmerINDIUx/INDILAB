<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PageView;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $range = $request->get('range', 30);
        $startDate = Carbon::now()->subDays($range);

        // General Stats
        $stats = [
            'total_views' => PageView::where('created_at', '>=', $startDate)->count(),
            'unique_visitors' => PageView::where('created_at', '>=', $startDate)->distinct('session_id')->count('session_id'),
            'avg_time' => PageView::where('created_at', '>=', $startDate)->where('time_on_page', '>', 0)->avg('time_on_page') ?? 0,
            'avg_scroll' => PageView::where('created_at', '>=', $startDate)->avg('scroll_depth') ?? 0,
        ];

        // Device Breakdown
        $devices = PageView::where('created_at', '>=', $startDate)
            ->select('device_type', DB::raw('count(*) as count'))
            ->groupBy('device_type')
            ->get();

        // Top Pages
        $topPages = PageView::where('created_at', '>=', $startDate)
            ->select('url', 'page_title', DB::raw('count(*) as views'), DB::raw('avg(time_on_page) as avg_time'), DB::raw('avg(scroll_depth) as avg_scroll'))
            ->groupBy('url', 'page_title')
            ->orderByDesc('views')
            ->limit(15)
            ->get();

        // Browser Breakdown
        $browsers = PageView::where('created_at', '>=', $startDate)
            ->select('browser', DB::raw('count(*) as count'))
            ->groupBy('browser')
            ->orderByDesc('count')
            ->get();

        // Daily Traffic for Chart
        $traffic = PageView::where('created_at', '>=', $startDate)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as views'), DB::raw('count(distinct session_id) as visitors'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Recent Visitor Logs
        $visitorLogs = PageView::orderByDesc('created_at')
            ->limit(20)
            ->get();

        return view('admin.analytics', compact('stats', 'devices', 'topPages', 'browsers', 'traffic', 'range', 'visitorLogs'));
    }
}
