<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Company;
use App\Models\Contact;
use App\Models\Deal;
use App\Models\Lead;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();
        $openStages = ['new', 'qualified', 'proposal'];

        // Deals grouped by stage (count + total value) for the chart.
        $byStage = [];
        foreach (DealController::STAGES as $stage) {
            $rows = Deal::visibleTo($user)->where('stage', $stage);
            $byStage[] = [
                'stage' => $stage,
                'count' => (clone $rows)->count(),
                'value' => (float) (clone $rows)->sum('value'),
            ];
        }

        return Inertia::render('Dashboard', [
            'stats' => [
                'contacts' => Contact::visibleTo($user)->count(),
                'companies' => Company::visibleTo($user)->count(),
                'open_leads' => Lead::visibleTo($user)->whereNotIn('status', ['converted', 'lost'])->count(),
                'open_deals' => Deal::visibleTo($user)->whereIn('stage', $openStages)->count(),
                'pipeline_value' => (float) Deal::visibleTo($user)->whereIn('stage', $openStages)->sum('value'),
                'won_value' => (float) Deal::visibleTo($user)->where('stage', 'won')->sum('value'),
                'open_tasks' => Activity::visibleTo($user)->where('completed', false)->count(),
            ],
            'byStage' => $byStage,
            'recentDeals' => Deal::visibleTo($user)->with('company')->latest()->take(5)->get(),
            'recentLeads' => Lead::visibleTo($user)->latest()->take(5)->get(),
            'upcomingTasks' => Activity::visibleTo($user)->with(['contact', 'deal'])
                ->where('completed', false)
                ->orderByRaw('due_date IS NULL, due_date asc')
                ->take(5)
                ->get(),
        ]);
    }
}
