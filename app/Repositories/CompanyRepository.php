<?php

namespace App\Repositories;

use App\Models\Company;
use App\Models\User;

class CompanyRepository
{
    public function paginateVisible(User $user, ?string $search)
    {
        return Company::visibleTo($user)
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
            ->withQueryString();
    }

    /**
     * Lightweight company options for select dropdowns.
     */
    public function options(User $user)
    {
        return Company::visibleTo($user)
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    public function create(array $data, int $ownerId): Company
    {
        $data['owner_id'] = $ownerId;

        return Company::create($data);
    }

    public function update(Company $company, array $data): Company
    {
        $company->update($data);

        return $company;
    }

    public function delete(Company $company): void
    {
        $company->delete();
    }
}
