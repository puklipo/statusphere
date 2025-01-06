<?php

use App\Record\Status;
use Illuminate\Validation\Rule;
use Revolution\Bluesky\Core\TID;
use Revolution\Bluesky\Facades\Bluesky;
use Revolution\Bluesky\Session\OAuthSession;

use function Livewire\Volt\{mount, state, rules};

state(['emoji', 'myStatus']);

rules(['emoji' => ['required', Rule::in(config('statusphere.status'))]]);

mount(function (?string $myStatus = null) {
    $this->myStatus = $myStatus;
});

$submit = function (string $emoji) {
    $this->emoji = $emoji;

    $this->validate();

    $session = OAuthSession::create(session('bluesky_session'));
    if ($session->tokenExpired()) {
        return redirect(route('login'));
    }

    $status = Status::create(status: $emoji);

    $res = Bluesky::withToken($session)
        ->putRecord(
            repo: Bluesky::assertDid(),
            collection: Status::NSID,
            rkey: TID::nextStr(),
            record: $status,
            validate: false,
        );

    if ($res->successful()) {
        $this->myStatus = $emoji;
        auth()->user()->touch();
        $this->dispatch('status-created');
    } else {
        session()->flash('status-error', $res->json());
    }
}
?>

<div>
    @if (session('status-error'))
        <div class="my-5 border border-red-500">
            <div class="p-1 bg-red-500 text-white font-bold">
                {{ session('status-error.error') }}
            </div>
            <div class="p-1">
                {{ session('status-error.message') }}
            </div>
        </div>
    @endif

    @if(! app()->isProduction())
        <div class="my-5 border border-yellow-500">
            <div class="p-1 bg-yellow-500 text-white font-bold">
                Warning
            </div>
            <div class="p-1">
                This posting function does not work in local. Use the <code class="bg-gray-100 dark:bg-gray-900">php
                    artisan bsky:create-status</code> command instead, which uses an app password.
            </div>
        </div>
    @endif

    <div class="flex flex-wrap gap-2">
        @foreach(config('statusphere.status') as $emoji)
            <button wire:click="submit('{{ $emoji }}')"
                    wire:loading.attr="disabled"
                    class="rounded-full p-1 shadow-sm border hover:bg-blue-200 dark:hover:bg-blue-400 disabled:opacity-50
                    @if($myStatus === $emoji)
                        border-blue-500 dark:border-blue-600 bg-blue-100 dark:bg-blue-900
                    @else
                        border-gray-200 dark:border-gray-500 bg-white dark:bg-gray-900
                    @endif">{{ $emoji }}</button>
        @endforeach
    </div>
    <div class="text-blue-500" wire:loading>
        Creating status...
    </div>
</div>
