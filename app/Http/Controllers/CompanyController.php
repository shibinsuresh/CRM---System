<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Repositories\CompanyRepository;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CompanyController extends Controller
{
    protected $companies;

    public function __construct(CompanyRepository $companies)
    {
        $this->middleware('auth');
        $this->companies = $companies;
    }

    public function index(Request $request)
    {
        $search = $request->input('search');

        return Inertia::render('Companies/Index', [
            'companies' => $this->companies->paginateVisible($request->user(), $search),
            'filters' => ['search' => $search],
        ]);
    }

    public function create()
    {
        return Inertia::render('Companies/Create');
    }

    public function store(Request $request)
    {
        $this->companies->create($this->validateCompany($request), $request->user()->id);

        return redirect()->route('companies.index')->with('success', 'Company created successfully.');
    }

    public function edit(Company $company)
    {
        $this->authorize('manage-record', $company);

        return Inertia::render('Companies/Edit', [
            'company' => $company,
            'notes' => $company->notes()->with('user')->get(),
        ]);
    }

    public function update(Request $request, Company $company)
    {
        $this->authorize('manage-record', $company);

        $this->companies->update($company, $this->validateCompany($request));

        return redirect()->route('companies.index')->with('success', 'Company updated successfully.');
    }

    public function destroy(Company $company)
    {
        $this->authorize('manage-record', $company);

        $this->companies->delete($company);

        return redirect()->route('companies.index')->with('success', 'Company deleted successfully.');
    }

    /**
     * Validate an incoming company payload.
     */
    private function validateCompany(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'industry' => ['nullable', 'string', 'max:255'],
            'website' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:255'],
        ]);
    }
}
