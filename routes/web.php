<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use Livewire\Volt\Volt;

Route::get('login', function (Request $request) {
    return Socialite::driver('bluesky')->hint($request->input('hint'))->redirect();
})->name('login');

Route::get('callback', function (Request $request) {
    if ($request->missing('code')) {
        dd($request->all());
    }

    /** @var \Laravel\Socialite\Two\User $socialite_user */
    $socialite_user = Socialite::driver('bluesky')->user();

    /** @var \Revolution\Bluesky\Session\OAuthSession $session */
    $session = $socialite_user->session;

    session()->put('bluesky_session', $session->toArray());

    $user = User::updateOrCreate([
        'did' => $session->did(),
    ], [
        'name' => $session->displayName(),
        'handle' => $session->handle(),
        'avatar' => $session->avatar(),
        'issuer' => $session->issuer(),
        'refresh_token' => $session->refresh(),
    ]);

    auth()->login($user, remember: true);

    return to_route('home');
})->name('bluesky.oauth.redirect');

Route::get('/', function (Request $request) {
    if (app()->isLocal() && $request->has('iss')) {
        return to_route('bluesky.oauth.redirect', $request->query());
    }

    $users = User::latest('updated_at')->limit(20)->get();

    return view('welcome')->with(compact('users'));
})->name('welcome');

Volt::route('home', 'home')
    ->name('home')
    ->middleware('auth');
