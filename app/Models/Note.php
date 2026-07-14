<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    use HasFactory;

    protected $fillable = [
        'body',
        'user_id',
    ];

    /**
     * The record this note is attached to (contact, company, or deal).
     */
    public function notable()
    {
        return $this->morphTo();
    }

    /**
     * The user who wrote the note.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
