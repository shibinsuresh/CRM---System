<?php

namespace App\Repositories;

use App\Models\Lead;
use App\Models\User;

class LeadRepository
{
    public function paginateVisible(User $user, ?string $search, ?string $status)
    {
        return Lead::visibleTo($user)
            ->with('owner')
            ->when($search, function ($q) use ($search) {
                $q->where(function ($qq) use ($search) {
                    $qq->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($status, function ($q) use ($status) {
                $q->where('status', $status);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();
    }

    public function create(array $data, int $ownerId): Lead
    {
        $data['owner_id'] = $ownerId;

        return Lead::create($data);
    }

    public function update(Lead $lead, array $data): Lead
    {
        $lead->update($data);

        return $lead;
    }

    public function delete(Lead $lead): void
    {
        $lead->delete();
    }

    public function markConverted(Lead $lead): void
    {
        $lead->update(['status' => 'converted']);
    }
}
