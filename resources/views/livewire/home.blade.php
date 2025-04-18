<?php

use App\Record\Status;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Revolution\Bluesky\Facades\Bluesky;
use Revolution\Bluesky\Session\OAuthSession;

use function Livewire\Volt\{mount, state, on};

state(['statuses', 'profile', 'myStatus']);

mount(function () {
    $session = OAuthSession::create(session('bluesky_session'));
    if ($session->tokenExpired()) {
        return redirect(route('login'));
    }

    $this->profile = $session->get('profile');

    $res = Bluesky::withToken($session)
        ->listRecords(
            repo: Bluesky::assertDid(),
            collection: Status::NSID,
            limit: 20,
        );

    $this->statuses = $res->collect('records')
        ->map(function ($record) {
            $date = Carbon::parse(data_get($record, 'value.createdAt'));
            data_set($record, 'value.createdAt', $date);

            return $record;
        });

    $this->myStatus = data_get($this->statuses->first(), 'value.status');
});

on(['status-created' => function ($status) {
    $this->statuses->prepend([
        'uri' => 'at://'.Str::random(),
        'value' => [
            'status' => $status,
            'createdAt' => now(),
        ],
    ]);
    $this->myStatus = $status;
}]);

$logout = function () {
    session()->forget('bluesky_session');
    auth()->logout();
    session()->invalidate();
    session()->regenerateToken();

    $this->redirect('/');
};
?>

<div>
    <div class="my-5 py-3 px-8 grid grid-cols-2">
        <div>
            Hi, <strong>{{ data_get($profile, 'displayName', 'friend') }}</strong>.
            What's your status today?
        </div>
        <div class="text-right">
            <button wire:click="logout" class="text-white bg-blue-500 px-3 rounded-l-full rounded-r-full">Log out
            </button>
        </div>
    </div>

    <livewire:create-status :my-status="$myStatus"/>

    <div class="mt-5">
        @foreach($statuses as $status)
            <div class="my-3 status-line @if($loop->first) no-line @endif" wire:key="{{ data_get($status, 'uri') }}">
                <x-emoji>{{ data_get($status, 'value.status') }}</x-emoji>
                <span class="ml-1">
                    <span class="font-bold">
                        <a href="https://bsky.app/profile/{{ data_get($profile, 'did') }}"
                           target="_blank">
                            {{ '@'.data_get($profile, 'handle') }}
                        </a>
                    </span>
                    <x-status-desc :status="$status"></x-status-desc>
                </span>
            </div>
        @endforeach
    </div>
</div>
