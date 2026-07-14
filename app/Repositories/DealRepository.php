<?php

namespace App\Repositories;

use App\Models\Deal;
use App\Models\User;

class DealRepository
{
    /**
     * The pipeline stages, in order.
     */
    public const STAGES = ['new', 'qualified', 'proposal', 'won', 'lost'];

    public function paginateVisible(User $user, ?string $search, ?string $stage)
    {
        return Deal::visibleTo($user)
            ->with(['contact', 'company', 'owner'])
            ->when($search, function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%");
            })
            ->when($stage, function ($q) use ($stage) {
                $q->where('stage', $stage);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();
    }

    /**
     * Deals grouped by stage (with per-column totals) for the Kanban board.
     */
    public function pipeline(User $user): array
    {
        $deals = Deal::visibleTo($user)
            ->with(['contact', 'company'])
            ->orderBy('created_at', 'desc')
            ->get();

        $board = [];
        foreach (self::STAGES as $stage) {
            $stageDeals = $deals->where('stage', $stage)->values();
            $board[] = [
                'stage' => $stage,
                'deals' => $stageDeals,
                'total' => $stageDeals->sum('value'),
            ];
        }

        return $board;
    }

    /**
     * Lightweight deal options for select dropdowns.
     */
    public function options(User $user)
    {
        return Deal::visibleTo($user)
            ->orderBy('title')
            ->get(['id', 'title']);
    }

    public function create(array $data, int $ownerId): Deal
    {
        $data['owner_id'] = $ownerId;

        return Deal::create($data);
    }

    public function update(Deal $deal, array $data): Deal
    {
        $deal->update($data);

        return $deal;
    }

    public function delete(Deal $deal): void
    {
        $deal->delete();
    }

    public function updateStage(Deal $deal, string $stage): void
    {
        $deal->update(['stage' => $stage]);
    }
}
