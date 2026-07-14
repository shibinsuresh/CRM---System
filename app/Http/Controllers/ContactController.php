<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Repositories\CompanyRepository;
use App\Repositories\ContactRepository;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ContactController extends Controller
{
    protected $contacts;
    protected $companies;

    public function __construct(ContactRepository $contacts, CompanyRepository $companies)
    {
        $this->middleware('auth');
        $this->contacts = $contacts;
        $this->companies = $companies;
    }

    public function index(Request $request)
    {
        $search = $request->input('search');

        return Inertia::render('Contacts/Index', [
            'contacts' => $this->contacts->paginateVisible($request->user(), $search),
            'filters' => ['search' => $search],
        ]);
    }

    public function create(Request $request)
    {
        return Inertia::render('Contacts/Create', [
            'companies' => $this->companies->options($request->user()),
        ]);
    }

    public function store(Request $request)
    {
        $this->contacts->create($this->validateContact($request), $request->user()->id);

        return redirect()->route('contacts.index')->with('success', 'Contact created successfully.');
    }

    public function edit(Request $request, Contact $contact)
    {
        $this->authorize('manage-record', $contact);

        return Inertia::render('Contacts/Edit', [
            'contact' => $contact,
            'companies' => $this->companies->options($request->user()),
            'notes' => $contact->notes()->with('user')->get(),
        ]);
    }

    public function update(Request $request, Contact $contact)
    {
        $this->authorize('manage-record', $contact);

        $this->contacts->update($contact, $this->validateContact($request));

        return redirect()->route('contacts.index')->with('success', 'Contact updated successfully.');
    }

    public function destroy(Contact $contact)
    {
        $this->authorize('manage-record', $contact);

        $this->contacts->delete($contact);

        return redirect()->route('contacts.index')->with('success', 'Contact deleted successfully.');
    }

    /**
     * Validate an incoming contact payload.
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
