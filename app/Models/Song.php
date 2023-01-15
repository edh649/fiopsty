<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Song extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'spotify_id',
        'name'
    ];
    
    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot([
            "added_at", "last_played_at"
        ]);
    }
}
