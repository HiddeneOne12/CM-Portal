<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Documentation;
use App\Models\Event;
use App\Models\Interview;
use App\Models\Report;
use App\Models\Training;
use App\Models\VisitorLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    /** Scope to frontend + portal only (exclude admin). */
    private function portalScope($query = null)
    {
        $q = $query ?? VisitorLog::query();
        return $q->whereIn('source', ['frontend', 'portal']);
    }

    public function index(Request $request): View
    {
        if (!validatePermissions('admin/dashboard')) {
            abort(403);
        }

        $stats = [
            'companies' => Company::count(),
            'interviews' => Interview::count(),
            'events' => Event::count(),
            'documents' => Documentation::count(),
            'training' => Training::count(),
            'reports' => Report::count(),
        ];

        $visitorStats = $this->getVisitorStats();
        $visitorsPerMonth = $this->getVisitorsPerMonth();
        $popularPages = $this->getPopularPages();

        return view('admin.dashboard', [
            'pageTitle' => 'Dashboard',
            'stats' => $stats,
            'visitorStats' => $visitorStats,
            'visitorsPerMonth' => $visitorsPerMonth,
            'popularPages' => $popularPages,
        ]);
    }

    private function getVisitorStats(): array
    {
        $periods = [
            '7' => [now()->subDays(7), now()->subDays(14)],
            '30' => [now()->subDays(30), now()->subDays(60)],
            '90' => [now()->subDays(90), now()->subDays(180)],
            '365' => [now()->subDays(365), now()->subDays(730)],
        ];

        $result = [];
        foreach ($periods as $label => [$since, $prevSince]) {
            $base = $this->portalScope(VisitorLog::where('visited_at', '>=', $since));
            $prev = $this->portalScope(VisitorLog::where('visited_at', '>=', $prevSince)->where('visited_at', '<', $since));

            $result['visitors_' . $label] = (int) (clone $base)->selectRaw('count(distinct session_id) as c')->value('c');
            $result['visits_' . $label] = (clone $base)->count();
            $avgSeconds = (clone $base)->whereNotNull('time_spent_seconds')->where('time_spent_seconds', '>', 0)->avg('time_spent_seconds');
            $result['avg_time_seconds_' . $label] = $avgSeconds ? (int) round($avgSeconds) : 0;

            $prevVisitors = (int) (clone $prev)->selectRaw('count(distinct session_id) as c')->value('c');
            $prevAvg = (clone $prev)->whereNotNull('time_spent_seconds')->where('time_spent_seconds', '>', 0)->avg('time_spent_seconds');
            $result['visitors_prev_' . $label] = $prevVisitors;
            $result['avg_time_prev_seconds_' . $label] = $prevAvg ? (int) round($prevAvg) : 0;
        }

        return $result;
    }

    private function getVisitorsPerMonth(): array
    {
        $months = $this->portalScope(VisitorLog::query())
            ->selectRaw("DATE_FORMAT(visited_at, '%Y-%m') as month")
            ->selectRaw("count(distinct session_id) as visitors")
            ->where('visited_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $labels = [];
        $data = [];
        for ($i = 11; $i >= 0; $i--) {
            $d = now()->subMonths($i);
            $key = $d->format('Y-m');
            $labels[] = $d->format('M Y');
            $item = $months->get($key);
            $data[] = (int) ($item->visitors ?? 0);
        }

        return ['labels' => $labels, 'data' => $data];
    }

    private function getPopularPages(): array
    {
        $since = now()->subDays(90);
        $base = $this->portalScope(VisitorLog::where('visited_at', '>=', $since)->whereNotNull('path')->where('path', '!=', ''));

        $singlePageSessions = $this->portalScope(VisitorLog::where('visited_at', '>=', $since))
            ->select('session_id')
            ->groupBy('session_id')
            ->havingRaw('count(*) = 1')
            ->pluck('session_id');

        $pages = (clone $base)
            ->selectRaw('path, count(*) as views, avg(time_spent_seconds) as avg_seconds, max(visited_at) as last_visited')
            ->groupBy('path')
            ->orderByDesc('last_visited')
            ->limit(5)
            ->get();

        $result = [];
        foreach ($pages as $row) {
            $bounceCount = (clone $base)->where('path', $row->path)->whereIn('session_id', $singlePageSessions)->count();
            $bounceRate = $row->views > 0 ? round($bounceCount / $row->views * 100, 1) : 0;
            $result[] = [
                'path' => $row->path,
                'views' => (int) $row->views,
                'bounce_rate' => $bounceRate,
                'avg_seconds' => $row->avg_seconds ? (int) round($row->avg_seconds) : 0,
            ];
        }

        return $result;
    }
}
