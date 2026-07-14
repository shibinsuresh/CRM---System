<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CompanyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * List companies.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        return Inertia::render('Companies/Index', [
            'companies' => Company::visibleTo(auth()->user())
                ->with('owner')
                ->withCount('contacts')
                ->when($search, function ($q) use ($search) {
                    $q->where(function ($qq) use ($search) {
                        $qq->where('name', 'like', "%{$search}%")
                            ->orWhere('industry', 'like', "%{$search}%");
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
        return Inertia::render('Companies/Create');
    }

    /**
     * Store a new company.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'industry' => ['nullable', 'string', 'max:255'],
            'website' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:255'],
        ]);

        $data['owner_id'] = $request->user()->id;
        Company::create($data);

        return redirect()->route('companies.index')->with('success', 'Company created successfully.');
    }

    /**
     * Show the edit form.
     */
    public function edit(Company $company)
    {
        $this->authorize('manage-record', $company);

        return Inertia::render('Companies/Edit', [
            'company' => $company,
            'notes' => $company->notes()->with('user')->get(),
        ]);
    }

    /**
     * Update a company.
     */
    public function update(Request $request, Company $company)
    {
        $this->authorize('manage-record', $company);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'industry' => ['nullable', 'string', 'max:255'],
            'website' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:255'],
        ]);

        $company->update($data);

        return redirect()->route('companies.index')->with('success', 'Company updated successfully.');
    }

    /**
     * Delete a company.
     */
    public function destroy(Company $company)
    {
        $this->authorize('manage-record', $company);

        $company->delete();

        return redirect()->route('companies.index')->with('success', 'Company deleted successfully.');
    }
}
