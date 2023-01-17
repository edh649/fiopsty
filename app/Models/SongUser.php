<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SongUser extends Model
{
    protected $table = "song_user";
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'song_id',
        'user_id',
        'added_at',
        'last_played_at',
    ];
    
    protected $casts = [
        'added_at' => 'datetime',
        'last_played_at' => 'datetime'
    ];
    
    public function song() {
        return $this->belongsTo(Song::class);
    }
    
    public function user() {
        return $this->belongsTo(User::class);
    }
}
