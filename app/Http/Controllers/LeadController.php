<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Repositories\ContactRepository;
use App\Repositories\LeadRepository;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LeadController extends Controller
{
    public const STATUSES = ['new', 'contacted', 'qualified', 'lost', 'converted'];

    protected $leads;
    protected $contacts;

    public function __construct(LeadRepository $leads, ContactRepository $contacts)
    {
        $this->middleware('auth');
        $this->leads = $leads;
        $this->contacts = $contacts;
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');

        return Inertia::render('Leads/Index', [
            'leads' => $this->leads->paginateVisible($request->user(), $search, $status),
            'filters' => ['search' => $search, 'status' => $status],
            'statuses' => self::STATUSES,
        ]);
    }

    public function create()
    {
        return Inertia::render('Leads/Create');
    }

    public function store(Request $request)
    {
        $this->leads->create($this->validateLead($request), $request->user()->id);

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

        $this->leads->update($lead, $this->validateLead($request));

        return redirect()->route('leads.index')->with('success', 'Lead updated successfully.');
    }

    public function destroy(Lead $lead)
    {
        $this->authorize('manage-record', $lead);

        $this->leads->delete($lead);

        return redirect()->route('leads.index')->with('success', 'Lead deleted successfully.');
    }

    /**
     * Convert a lead into a contact.
     */
    public function convert(Request $request, Lead $lead)
    {
        $this->authorize('manage-record', $lead);

        $parts = explode(' ', trim($lead->name), 2);

        $this->contacts->create([
            'first_name' => $parts[0],
            'last_name' => $parts[1] ?? null,
            'email' => $lead->email,
            'phone' => $lead->phone,
        ], $request->user()->id);

        $this->leads->markConverted($lead);

        return redirect()->route('contacts.index')->with('success', 'Lead converted to contact.');
    }

    /**
     * Validate an incoming lead payload.
     */
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
