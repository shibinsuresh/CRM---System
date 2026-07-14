<?php

namespace App\Repositories;

use App\Models\Activity;
use App\Models\User;

class ActivityRepository
{
    /**
     * The activity types.
     */
    public const TYPES = ['call', 'meeting', 'email', 'task'];

    public function paginateVisible(User $user, ?string $search, ?string $type)
    {
        return Activity::visibleTo($user)
            ->with(['contact', 'deal'])
            ->when($search, function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%");
            })
            ->when($type, function ($q) use ($type) {
                $q->where('type', $type);
            })
            ->orderBy('completed')
            ->orderByRaw('due_date IS NULL, due_date asc')
            ->paginate(10)
            ->withQueryString();
    }

    public function create(array $data, int $ownerId): Activity
    {
        $data['owner_id'] = $ownerId;

        return Activity::create($data);
    }

    public function update(Activity $activity, array $data): Activity
    {
        $activity->update($data);

        return $activity;
    }

    public function delete(Activity $activity): void
    {
        $activity->delete();
    }

    public function toggle(Activity $activity): void
    {
        $activity->update(['completed' => ! $activity->completed]);
    }
}
