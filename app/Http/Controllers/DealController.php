<?php

namespace App\Http\Controllers;

use App\Models\Deal;
use App\Models\User;
use App\Repositories\CompanyRepository;
use App\Repositories\ContactRepository;
use App\Repositories\DealRepository;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DealController extends Controller
{
    protected $deals;
    protected $contacts;
    protected $companies;

    public function __construct(DealRepository $deals, ContactRepository $contacts, CompanyRepository $companies)
    {
        $this->middleware('auth');
        $this->deals = $deals;
        $this->contacts = $contacts;
        $this->companies = $companies;
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $stage = $request->input('stage');

        return Inertia::render('Deals/Index', [
            'deals' => $this->deals->paginateVisible($request->user(), $search, $stage),
            'filters' => ['search' => $search, 'stage' => $stage],
            'stages' => DealRepository::STAGES,
        ]);
    }

    /**
     * The Kanban pipeline board: deals grouped by stage.
     */
    public function pipeline(Request $request)
    {
        return Inertia::render('Deals/Pipeline', [
            'board' => $this->deals->pipeline($request->user()),
            'stages' => DealRepository::STAGES,
        ]);
    }

    public function create(Request $request)
    {
        return Inertia::render('Deals/Create', $this->formOptions($request->user()));
    }

    public function store(Request $request)
    {
        $this->deals->create($this->validateDeal($request), $request->user()->id);

        return redirect()->route('deals.index')->with('success', 'Deal created successfully.');
    }

    public function edit(Request $request, Deal $deal)
    {
        $this->authorize('manage-record', $deal);

        return Inertia::render('Deals/Edit', array_merge(
            [
                'deal' => $deal,
                'notes' => $deal->notes()->with('user')->get(),
            ],
            $this->formOptions($request->user())
        ));
    }

    public function update(Request $request, Deal $deal)
    {
        $this->authorize('manage-record', $deal);

        $this->deals->update($deal, $this->validateDeal($request));

        return redirect()->route('deals.index')->with('success', 'Deal updated successfully.');
    }

    public function destroy(Deal $deal)
    {
        $this->authorize('manage-record', $deal);

        $this->deals->delete($deal);

        return redirect()->route('deals.index')->with('success', 'Deal deleted successfully.');
    }

    /**
     * Update just the stage (used by the pipeline drag-and-drop).
     */
    public function updateStage(Request $request, Deal $deal)
    {
        $this->authorize('manage-record', $deal);

        $data = $request->validate([
            'stage' => ['required', 'in:'.implode(',', DealRepository::STAGES)],
        ]);

        $this->deals->updateStage($deal, $data['stage']);

        return redirect()->back();
    }

    private function formOptions(User $user): array
    {
        return [
            'contacts' => $this->contacts->options($user),
            'companies' => $this->companies->options($user),
            'stages' => DealRepository::STAGES,
        ];
    }

    /**
     * Validate an incoming deal payload.
     */
    private function validateDeal(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'value' => ['required', 'numeric', 'min:0'],
            'stage' => ['required', 'in:'.implode(',', DealRepository::STAGES)],
            'expected_close_date' => ['nullable', 'date'],
            'contact_id' => ['nullable', 'exists:contacts,id'],
            'company_id' => ['nullable', 'exists:companies,id'],
        ]);
    }
}
