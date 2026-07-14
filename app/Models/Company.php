<?php

namespace App\Models;

use App\Models\Concerns\ScopedToOwner;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory, ScopedToOwner;

    protected $fillable = [
        'name',
        'industry',
        'website',
        'phone',
        'address',
        'owner_id',
    ];

    /**
     * The user who owns this company.
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * The contacts that belong to this company.
     */
    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }

    public function notes()
    {
        return $this->morphMany(Note::class, 'notable')->latest();
    }
}
