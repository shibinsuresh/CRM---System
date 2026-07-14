<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Contact;
use App\Models\Deal;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DealController extends Controller
{
    /**
     * The pipeline stages, in order.
     */
    public const STAGES = ['new', 'qualified', 'proposal', 'won', 'lost'];

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $stage = $request->input('stage');

        return Inertia::render('Deals/Index', [
            'deals' => Deal::visibleTo(auth()->user())
                ->with(['contact', 'company', 'owner'])
                ->when($search, fn ($q) => $q->where('title', 'like', "%{$search}%"))
                ->when($stage, fn ($q) => $q->where('stage', $stage))
                ->latest()
                ->paginate(10)
                ->withQueryString(),
            'filters' => ['search' => $search, 'stage' => $stage],
            'stages' => self::STAGES,
        ]);
    }

    /**
     * The Kanban pipeline board: deals grouped by stage.
     */
    public function pipeline()
    {
        $deals = Deal::visibleTo(auth()->user())
            ->with(['contact', 'company'])
            ->orderBy('created_at', 'desc')
            ->get();

        $board = [];
        foreach (self::STAGES as $stage) {
            $stageDeals = $deals->where('stage', $stage)->values();
            $board[] = [
                'stage' => $stage,
                'deals' => $stageDeals,
                'total' => $stageDeals->sum('value'),
            ];
        }

        return Inertia::render('Deals/Pipeline', [
            'board' => $board,
            'stages' => self::STAGES,
        ]);
    }

    public function create()
    {
        return Inertia::render('Deals/Create', $this->formOptions());
    }

    public function store(Request $request)
    {
        $data = $this->validateDeal($request);
        $data['owner_id'] = $request->user()->id;
        Deal::create($data);

        return redirect()->route('deals.index')->with('success', 'Deal created successfully.');
    }

    public function edit(Deal $deal)
    {
        $this->authorize('manage-record', $deal);

        return Inertia::render('Deals/Edit', array_merge(
            ['deal' => $deal, 'notes' => $deal->notes()->with('user')->get()],
            $this->formOptions()
        ));
    }

    public function update(Request $request, Deal $deal)
    {
        $this->authorize('manage-record', $deal);

        $data = $this->validateDeal($request);
        $deal->update($data);

        return redirect()->route('deals.index')->with('success', 'Deal updated successfully.');
    }

    public function destroy(Deal $deal)
    {
        $this->authorize('manage-record', $deal);

        $deal->delete();

        return redirect()->route('deals.index')->with('success', 'Deal deleted successfully.');
    }

    /**
     * Update just the stage (used by the pipeline drag-and-drop).
     */
    public function updateStage(Request $request, Deal $deal)
    {
        $this->authorize('manage-record', $deal);

        $data = $request->validate([
            'stage' => ['required', 'in:'.implode(',', self::STAGES)],
        ]);

        $deal->update($data);

        return redirect()->back();
    }

    private function formOptions(): array
    {
        return [
            'contacts' => Contact::visibleTo(auth()->user())->orderBy('first_name')->get(['id', 'first_name', 'last_name']),
            'companies' => Company::visibleTo(auth()->user())->orderBy('name')->get(['id', 'name']),
            'stages' => self::STAGES,
        ];
    }

    private function validateDeal(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'value' => ['required', 'numeric', 'min:0'],
            'stage' => ['required', 'in:'.implode(',', self::STAGES)],
            'expected_close_date' => ['nullable', 'date'],
            'contact_id' => ['nullable', 'exists:contacts,id'],
            'company_id' => ['nullable', 'exists:companies,id'],
        ]);
    }
}
