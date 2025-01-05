<x-layouts.app>
    <div class="text-center text-xl">
        @auth
            <div>
                <a href="{{ route('home') }}"
                   class="text-white bg-blue-500 px-3 rounded-l-full rounded-r-full">Home</a>
            </div>
        @else
            <div>
                <a href="{{ route('login') }}">Log in</a> to set your status!
            </div>
            <div>
                <a href="{{ route('login', ['hint' => auth()->user()?->handle]) }}"
                   class="text-white bg-blue-500 px-3 rounded-l-full rounded-r-full">Log in</a>
            </div>
        @endauth
    </div>

    <div class="mt-5">
        @foreach($users as $user)
            @unless(empty($user->status))
                <div class="my-3 status-line @if($loop->first) no-line @endif">
                    <x-emoji>{{ data_get($user->status, 'value.status') }}</x-emoji>
                    <span class="ml-1">
                        <span class="font-bold">{{ '@'.Str::mask($user->handle, '*', 1) }}</span>
                        <x-status-desc :status="$user->status"></x-status-desc>
                    </span>
                </div>
            @endunless
        @endforeach
    </div>
</x-layouts.app>
