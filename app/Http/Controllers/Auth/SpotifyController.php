<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SpotifyController extends Controller
{
    public function login()
    {
        return Socialite::driver('spotify')
            ->scopes(config('services.spotify.scopes'))
            ->redirect();
    }
    
    public function callback(Request $request)
    {
        $spotify_user = Socialite::driver('spotify')->user();
        
        $user = User::updateOrCreate([
            'spotify_id' => $spotify_user->id,
        ], [
            'name' => $spotify_user->name,
            'email' => $spotify_user->email,
            'spotify_token' => $spotify_user->token,
            'spotify_refresh_token' => $spotify_user->refreshToken
        ]);
        
        Auth::login($user);
        
        return redirect('/dashboard');
    }
}
