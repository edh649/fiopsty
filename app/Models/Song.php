<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Song extends Model
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
    
    public function songUsers()
    {
        return $this->hasMany(SongUser::class);
    }
}
