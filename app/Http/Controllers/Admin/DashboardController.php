<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PageView;
use App\Models\ContactSubmission;
use App\Models\Project;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Calculate stats for last 30 days
        $thirtyDaysAgo = Carbon::now()->subDays(30);
        
        // Total unique visitors (unique session_ids)
        $totalVisitors = PageView::where('created_at', '>=', $thirtyDaysAgo)
            ->distinct('session_id')
            ->count('session_id');
        
        // Total page views
        $totalPageViews = PageView::where('created_at', '>=', $thirtyDaysAgo)->count();
        
        // Average time on site (in seconds)
        $avgTimeSeconds = PageView::where('created_at', '>=', $thirtyDaysAgo)
            ->whereNotNull('time_on_page')
            ->avg('time_on_page') ?? 0;
        
        $avgTimeOnSite = gmdate('i:s', round($avgTimeSeconds));
        
        // New contact submissions
        $newContacts = ContactSubmission::where('status', 'new')->count();
        
        $stats = [
            'totalVisitors' => $totalVisitors,
            'totalPageViews' => $totalPageViews,
            'avgTimeOnSite' => $avgTimeOnSite,
            'newContacts' => $newContacts,
        ];
        
        // Top pages
        $topPages = PageView::select('url', 'page_title')
            ->selectRaw('COUNT(*) as views_count')
            ->selectRaw('AVG(time_on_page) as avg_time')
            ->selectRaw('AVG(scroll_depth) as avg_scroll')
            ->where('created_at', '>=', $thirtyDaysAgo)
            ->groupBy('url', 'page_title')
            ->orderByDesc('views_count')
            ->limit(5)
            ->get();
        
        // Recent contacts
        $recentContacts = ContactSubmission::orderByDesc('created_at')
            ->limit(5)
            ->get();
        
        // Top blogs (projects with analytics)
        $topBlogs = Project::select('projects.*')
            ->selectRaw('(SELECT COUNT(*) FROM page_views WHERE page_views.url LIKE CONCAT("%/work/", projects.slug, "%")) as views_count')
            ->selectRaw('(SELECT AVG(time_on_page) FROM page_views WHERE page_views.url LIKE CONCAT("%/work/", projects.slug, "%")) as avg_read_time')
            ->selectRaw('(SELECT AVG(scroll_depth) FROM page_views WHERE page_views.url LIKE CONCAT("%/work/", projects.slug, "%")) as completion_rate')
            ->where('coming_soon', false)
            ->orderByDesc('views_count')
            ->limit(5)
            ->get();
        
        // Chart data for last 7 days
        $chartData = $this->getChartData(7);
        
        $newContactsCount = $newContacts;
        
        return view('admin.dashboard', compact(
            'stats',
            'topPages',
            'recentContacts',
            'topBlogs',
            'chartData',
            'newContactsCount'
        ));
    }
    
    private function getChartData($days = 7)
    {
        $labels = [];
        $visitors = [];
        $pageViews = [];
        
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $labels[] = $date->format('D');
            
            // Unique visitors for this day
            $dayVisitors = PageView::whereDate('created_at', $date->toDateString())
                ->distinct('session_id')
                ->count('session_id');
            $visitors[] = $dayVisitors;
            
            // Page views for this day
            $dayPageViews = PageView::whereDate('created_at', $date->toDateString())->count();
            $pageViews[] = $dayPageViews;
        }
        
        return [
            'labels' => $labels,
            'visitors' => $visitors,
            'pageViews' => $pageViews,
        ];
    }
}
