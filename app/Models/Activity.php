<?php

namespace App\Models;

use App\Models\Concerns\ScopedToOwner;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory, ScopedToOwner;

    protected $fillable = [
        'type',
        'subject',
        'notes',
        'due_date',
        'completed',
        'contact_id',
        'deal_id',
        'owner_id',
    ];

    protected $casts = [
        'due_date' => 'date',
        'completed' => 'boolean',
    ];

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    public function deal()
    {
        return $this->belongsTo(Deal::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
