<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Contact;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ContactController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * List contacts.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        return Inertia::render('Contacts/Index', [
            'contacts' => Contact::visibleTo(auth()->user())
                ->with(['company', 'owner'])
                ->when($search, function ($q) use ($search) {
                    $q->where(function ($qq) use ($search) {
                        $qq->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
                })
                ->latest()
                ->paginate(10)
                ->withQueryString(),
            'filters' => ['search' => $search],
        ]);
    }

    /**
     * Show the create form.
     */
    public function create()
    {
        return Inertia::render('Contacts/Create', [
            'companies' => Company::visibleTo(auth()->user())->orderBy('name')->get(['id', 'name']),
        ]);
    }

    /**
     * Store a new contact.
     */
    public function store(Request $request)
    {
        $data = $this->validateContact($request);

        $data['owner_id'] = $request->user()->id;
        Contact::create($data);

        return redirect()->route('contacts.index')->with('success', 'Contact created successfully.');
    }

    /**
     * Show the edit form.
     */
    public function edit(Contact $contact)
    {
        $this->authorize('manage-record', $contact);

        return Inertia::render('Contacts/Edit', [
            'contact' => $contact,
            'companies' => Company::visibleTo(auth()->user())->orderBy('name')->get(['id', 'name']),
            'notes' => $contact->notes()->with('user')->get(),
        ]);
    }

    /**
     * Update a contact.
     */
    public function update(Request $request, Contact $contact)
    {
        $this->authorize('manage-record', $contact);

        $data = $this->validateContact($request);

        $contact->update($data);

        return redirect()->route('contacts.index')->with('success', 'Contact updated successfully.');
    }

    /**
     * Delete a contact.
     */
    public function destroy(Contact $contact)
    {
        $this->authorize('manage-record', $contact);

        $contact->delete();

        return redirect()->route('contacts.index')->with('success', 'Contact deleted successfully.');
    }

    /**
     * Shared validation rules for storing/updating a contact.
     */
    private function validateContact(Request $request): array
    {
        return $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'job_title' => ['nullable', 'string', 'max:255'],
            'company_id' => ['nullable', 'exists:companies,id'],
        ]);
    }
}
