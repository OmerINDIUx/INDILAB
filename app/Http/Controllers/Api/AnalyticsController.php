<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PageView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AnalyticsController extends Controller
{
    public function track(Request $request)
    {
        try {
            $events = $request->input('events', []);
            $timeOnPage = $request->input('time_on_page');
            $scrollDepth = $request->input('scroll_depth');
            
            foreach ($events as $event) {
                $type = $event['type'] ?? 'unknown';
                
                if ($type === 'page_view') {
                    $this->trackPageView($event, $timeOnPage, $scrollDepth);
                } elseif ($type === 'exit') {
                    $this->updatePageView($event);
                }
            }
            
            return response()->json(['success' => true]);
            
        } catch (\Exception $e) {
            Log::error('Analytics tracking error: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
    
    private function trackPageView($event, $timeOnPage = null, $scrollDepth = null)
    {
        // Get IP address
        $ipAddress = request()->ip();
        
        // Create page view record
        PageView::create([
            'url' => $event['url'] ?? request()->path(),
            'page_title' => $event['page_title'] ?? null,
            'referrer' => $event['referrer'] ?? null,
            'user_agent' => $event['user_agent'] ?? request()->userAgent(),
            'ip_address' => $ipAddress,
            'session_id' => $event['session_id'] ?? session()->getId(),
            'user_id' => $event['user_id'] ?? auth()->id(),
            'device_type' => $event['device_type'] ?? 'desktop',
            'browser' => $event['browser'] ?? 'Unknown',
            'time_on_page' => $timeOnPage,
            'scroll_depth' => $scrollDepth,
        ]);
    }
    
    private function updatePageView($event)
    {
        // Update the most recent page view for this session
        $pageView = PageView::where('session_id', $event['session_id'] ?? session()->getId())
            ->where('url', $event['url'] ?? request()->path())
            ->latest()
            ->first();
        
        if ($pageView) {
            $pageView->update([
                'time_on_page' => $event['time_on_page'] ?? null,
                'scroll_depth' => $event['scroll_depth'] ?? null,
            ]);
        }
    }
}
