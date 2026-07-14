<?php

namespace App\Repositories;

use App\Models\Contact;
use App\Models\User;

class ContactRepository
{
    /**
     * Paginated contacts the user is allowed to see, optionally searched.
     */
    public function paginateVisible(User $user, ?string $search)
    {
        return Contact::visibleTo($user)
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
            ->withQueryString();
    }

    /**
     * Lightweight contact options for select dropdowns.
     */
    public function options(User $user)
    {
        return Contact::visibleTo($user)
            ->orderBy('first_name')
            ->get(['id', 'first_name', 'last_name']);
    }

    public function create(array $data, int $ownerId): Contact
    {
        $data['owner_id'] = $ownerId;

        return Contact::create($data);
    }

    public function update(Contact $contact, array $data): Contact
    {
        $contact->update($data);

        return $contact;
    }

    public function delete(Contact $contact): void
    {
        $contact->delete();
    }
}
