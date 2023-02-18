<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Library') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <form method="post" action="{{ route('spotify.library.dispatch-import.saved-songs') }}" class="mt-6 space-y-6">
                    @csrf    
                    
                    <p class="text-white">Songs in Library: {{ $song_count }}</p>

                    <div class="flex items-center gap-4">
                        <x-primary-button>{{ __('Dispatch Import Spotify Library Job in Background') }}</x-primary-button>

                    </div>
                </form>
                <form method="post" action="{{ route('spotify.player.import.recently-played') }}" class="mt-6 space-y-6">
                    @csrf

                    <div class="flex items-center gap-4">
                        <x-primary-button>{{ __('Import Spotify Recently Played') }}</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    
</x-app-layout>
