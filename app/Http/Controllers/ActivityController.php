<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\User;
use App\Repositories\ActivityRepository;
use App\Repositories\ContactRepository;
use App\Repositories\DealRepository;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ActivityController extends Controller
{
    protected $activities;
    protected $contacts;
    protected $deals;

    public function __construct(ActivityRepository $activities, ContactRepository $contacts, DealRepository $deals)
    {
        $this->middleware('auth');
        $this->activities = $activities;
        $this->contacts = $contacts;
        $this->deals = $deals;
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $type = $request->input('type');

        return Inertia::render('Activities/Index', [
            'activities' => $this->activities->paginateVisible($request->user(), $search, $type),
            'filters' => ['search' => $search, 'type' => $type],
            'types' => ActivityRepository::TYPES,
        ]);
    }

    public function create(Request $request)
    {
        return Inertia::render('Activities/Create', $this->formOptions($request->user()));
    }

    public function store(Request $request)
    {
        $this->activities->create($this->validateActivity($request), $request->user()->id);

        return redirect()->route('activities.index')->with('success', 'Activity created successfully.');
    }

    public function edit(Request $request, Activity $activity)
    {
        $this->authorize('manage-record', $activity);

        return Inertia::render('Activities/Edit', array_merge(
            ['activity' => $activity],
            $this->formOptions($request->user())
        ));
    }

    public function update(Request $request, Activity $activity)
    {
        $this->authorize('manage-record', $activity);

        $this->activities->update($activity, $this->validateActivity($request));

        return redirect()->route('activities.index')->with('success', 'Activity updated successfully.');
    }

    public function destroy(Activity $activity)
    {
        $this->authorize('manage-record', $activity);

        $this->activities->delete($activity);

        return redirect()->route('activities.index')->with('success', 'Activity deleted successfully.');
    }

    /**
     * Toggle the completed state (used by the checkbox on the list).
     */
    public function toggle(Activity $activity)
    {
        $this->authorize('manage-record', $activity);

        $this->activities->toggle($activity);

        return redirect()->back();
    }

    private function formOptions(User $user): array
    {
        return [
            'types' => ActivityRepository::TYPES,
            'contacts' => $this->contacts->options($user),
            'deals' => $this->deals->options($user),
        ];
    }

    /**
     * Validate an incoming activity payload.
     */
    private function validateActivity(Request $request): array
    {
        return $request->validate([
            'type' => ['required', 'in:'.implode(',', ActivityRepository::TYPES)],
            'subject' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'due_date' => ['nullable', 'date'],
            'completed' => ['boolean'],
            'contact_id' => ['nullable', 'exists:contacts,id'],
            'deal_id' => ['nullable', 'exists:deals,id'],
        ]);
    }
}
