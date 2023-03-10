<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'spotify_id',
        'spotify_token',
        'spotify_refresh_token',
        'listen_all_started_at',
        'spotify_listen_all_playlist_id'
    ];

    protected $casts = [
        'listen_all_started_at' => 'datetime'
    ];
    
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'spotify_token',
        'spotify_refresh_token'
    ];
    
    public function songs()
    {
        return $this->belongsToMany(Song::class)->withPivot([
            "added_at", "last_played_at"
        ]);
    }
    
    public function songUsers()
    {
        return $this->hasMany(SongUser::class);
    }
}
