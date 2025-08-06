<?php

namespace App\Http\Controllers\dashboard;

use Carbon\Carbon;
use App\Models\News;
use App\Models\Task;
use App\Models\Image;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Task statistics
        $stats = [
            'total_tasks' => Task::where('assigned_to', $user->id)->count(),
            'completed_tasks' => Task::where('assigned_to', $user->id)->where('status', 'completed')->count(),
            'pending_tasks' => Task::where('assigned_to', $user->id)->where('status', 'pending')->count(),
            'in_progress_tasks' => Task::where('assigned_to', $user->id)->where('status', 'in_progress')->count(),
            'overdue_tasks' => Task::where('assigned_to', $user->id)
                                ->where('status', '!=', 'completed')
                                ->whereDate('due_date', '<', now())
                                ->count(),
            'upcoming_tasks' => Task::where('assigned_to', $user->id)
                                ->where('status', '!=', 'completed')
                                ->whereDate('due_date', '>', now())
                                ->whereDate('due_date', '<=', now()->addDays(7))
                                ->count(),
        ];

        // Calculate percentages
        $stats['completed_percentage'] = $stats['total_tasks'] > 0 ? round(($stats['completed_tasks'] / $stats['total_tasks']) * 100) : 0;
        $stats['pending_percentage'] = $stats['total_tasks'] > 0 ? round(($stats['pending_tasks'] / $stats['total_tasks']) * 100) : 0;
        $stats['overdue_percentage'] = $stats['total_tasks'] > 0 ? round(($stats['overdue_tasks'] / $stats['total_tasks']) * 100) : 0;
        $stats['upcoming_percentage'] = $stats['total_tasks'] > 0 ? round(($stats['upcoming_tasks'] / $stats['total_tasks']) * 100) : 0;

        // Recent tasks
        $recentTasks = Task::where('assigned_to', $user->id)
                        ->with('createdBy')
                        ->orderBy('due_date', 'asc')
                        ->limit(5)
                        ->get();

        // Upcoming deadlines
        $upcomingDeadlines = Task::where('assigned_to', $user->id)
                            ->where('status', '!=', 'completed')
                            ->whereDate('due_date', '>=', now())
                            ->orderBy('due_date', 'asc')
                            ->limit(3)
                            ->get();

        // Recent news
        $recentNews = News::with(['author', 'image'])
                        ->latest()
                        ->limit(3)
                        ->get();

        // Calendar data
        $calendarWeeks = $this->generateCalendarData();

        return view('dashboard.index', compact(
            'stats',
            'recentTasks',
            'upcomingDeadlines',
            'recentNews',
            'calendarWeeks'
        ));
    }

    private function generateCalendarData()
    {
        $now = Carbon::now();
        $daysInMonth = $now->daysInMonth;
        $firstDay = $now->copy()->startOfMonth();
        $lastDay = $now->copy()->endOfMonth();

        $calendar = [];
        $week = [];

        // Add days from previous month
        for ($i = 0; $i < $firstDay->dayOfWeek; $i++) {
            $day = $firstDay->copy()->subDays($firstDay->dayOfWeek - $i);
            $week[] = [
                'day' => $day->day,
                'is_current_month' => false,
                'is_today' => false,
                'has_events' => false
            ];
        }

        // Add days of current month
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = $now->copy()->setDay($day);
            $hasEvents = Task::where('assigned_to', Auth::id())
                          ->whereDate('due_date', $date)
                          ->exists();

            $week[] = [
                'day' => $day,
                'is_current_month' => true,
                'is_today' => $date->isToday(),
                'has_events' => $hasEvents
            ];

            if (count($week) === 7) {
                $calendar[] = $week;
                $week = [];
            }
        }

        // Add days from next month
        if (!empty($week)) {
            $nextMonthDay = 1;
            while (count($week) < 7) {
                $week[] = [
                    'day' => $nextMonthDay++,
                    'is_current_month' => false,
                    'is_today' => false,
                    'has_events' => false
                ];
            }
            $calendar[] = $week;
        }

        return $calendar;
    }
}
