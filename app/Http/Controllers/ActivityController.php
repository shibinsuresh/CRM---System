<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Contact;
use App\Models\Deal;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ActivityController extends Controller
{
    public const TYPES = ['call', 'meeting', 'email', 'task'];

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $type = $request->input('type');

        return Inertia::render('Activities/Index', [
            'activities' => Activity::visibleTo(auth()->user())
                ->with(['contact', 'deal'])
                ->when($search, fn ($q) => $q->where('subject', 'like', "%{$search}%"))
                ->when($type, fn ($q) => $q->where('type', $type))
                ->orderBy('completed')
                ->orderByRaw('due_date IS NULL, due_date asc')
                ->paginate(10)
                ->withQueryString(),
            'filters' => ['search' => $search, 'type' => $type],
            'types' => self::TYPES,
        ]);
    }

    public function create()
    {
        return Inertia::render('Activities/Create', $this->formOptions());
    }

    public function store(Request $request)
    {
        $data = $this->validateActivity($request);
        $data['owner_id'] = $request->user()->id;
        Activity::create($data);

        return redirect()->route('activities.index')->with('success', 'Activity created successfully.');
    }

    public function edit(Activity $activity)
    {
        $this->authorize('manage-record', $activity);

        return Inertia::render('Activities/Edit', array_merge(
            ['activity' => $activity],
            $this->formOptions()
        ));
    }

    public function update(Request $request, Activity $activity)
    {
        $this->authorize('manage-record', $activity);

        $data = $this->validateActivity($request);
        $activity->update($data);

        return redirect()->route('activities.index')->with('success', 'Activity updated successfully.');
    }

    public function destroy(Activity $activity)
    {
        $this->authorize('manage-record', $activity);

        $activity->delete();

        return redirect()->route('activities.index')->with('success', 'Activity deleted successfully.');
    }

    /**
     * Toggle the completed state (used by the checkbox on the list).
     */
    public function toggle(Activity $activity)
    {
        $this->authorize('manage-record', $activity);

        $activity->update(['completed' => ! $activity->completed]);

        return redirect()->back();
    }

    private function formOptions(): array
    {
        return [
            'types' => self::TYPES,
            'contacts' => Contact::visibleTo(auth()->user())->orderBy('first_name')->get(['id', 'first_name', 'last_name']),
            'deals' => Deal::visibleTo(auth()->user())->orderBy('title')->get(['id', 'title']),
        ];
    }

    private function validateActivity(Request $request): array
    {
        return $request->validate([
            'type' => ['required', 'in:'.implode(',', self::TYPES)],
            'subject' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'due_date' => ['nullable', 'date'],
            'completed' => ['boolean'],
            'contact_id' => ['nullable', 'exists:contacts,id'],
            'deal_id' => ['nullable', 'exists:deals,id'],
        ]);
    }
}
