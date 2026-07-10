<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Lead;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LeadController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');

        return Inertia::render('Leads/Index', [
            'leads' => Lead::visibleTo(auth()->user())
                ->with('owner')
                ->when($search, function ($q) use ($search) {
                    $q->where(function ($qq) use ($search) {
                        $qq->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
                })
                ->when($status, fn ($q) => $q->where('status', $status))
                ->latest()
                ->paginate(10)
                ->withQueryString(),
            'filters' => ['search' => $search, 'status' => $status],
            'statuses' => ['new', 'contacted', 'qualified', 'lost', 'converted'],
        ]);
    }

    public function create()
    {
        return Inertia::render('Leads/Create');
    }

    public function store(Request $request)
    {
        $data = $this->validateLead($request);
        $data['owner_id'] = $request->user()->id;
        Lead::create($data);

        return redirect()->route('leads.index')->with('success', 'Lead created successfully.');
    }

    public function edit(Lead $lead)
    {
        $this->authorize('manage-record', $lead);

        return Inertia::render('Leads/Edit', [
            'lead' => $lead,
        ]);
    }

    public function update(Request $request, Lead $lead)
    {
        $this->authorize('manage-record', $lead);

        $data = $this->validateLead($request);
        $lead->update($data);

        return redirect()->route('leads.index')->with('success', 'Lead updated successfully.');
    }

    public function destroy(Lead $lead)
    {
        $this->authorize('manage-record', $lead);

        $lead->delete();

        return redirect()->route('leads.index')->with('success', 'Lead deleted successfully.');
    }

    /**
     * Convert a lead into a contact.
     */
    public function convert(Request $request, Lead $lead)
    {
        $this->authorize('manage-record', $lead);

        $parts = explode(' ', trim($lead->name), 2);

        Contact::create([
            'first_name' => $parts[0],
            'last_name' => $parts[1] ?? null,
            'email' => $lead->email,
            'phone' => $lead->phone,
            'owner_id' => $request->user()->id,
        ]);

        $lead->update(['status' => 'converted']);

        return redirect()->route('contacts.index')->with('success', 'Lead converted to contact.');
    }

    private function validateLead(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'source' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:new,contacted,qualified,lost,converted'],
            'notes' => ['nullable', 'string'],
        ]);
    }
}
