<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Listen All') }}
        </h2>
    </x-slot>
    
    <x-alert :alert="$alert ?? null"></x-alert>

    @if($listen_all_start_date ?? null)
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div>
                    <p class="text-white">Start Date: {{ $listen_all_start_date }}</p>
                    <p class="text-white">Unique library songs listened to: {{ $unique_library_listened_songs }}</p>
                    <p class="text-white">Total songs in Library: {{ $library_songs_count }}</p>
                    <p class="text-white">Complete: {{ $percentage_complete }}%</p>
                    <p class="text-white">Projected Finish: {{ $projected_finish ?? '??' }}</p>
                </div>
                
                <form method="post" action="{{ route('listen-all.reset') }}" class="mt-6 space-y-6">
                    @csrf    
                    @method('DELETE')

                    <div class="flex items-center gap-4">
                        <x-primary-button>{{ __('Reset listen counter') }}</x-primary-button>
                    </div>
                </form>
                <form method="post" action="{{ route('spotify.player.import.recently-played') }}" class="mt-6 space-y-6">
                    @csrf

                    <div class="flex items-center gap-4">
                        <x-primary-button>{{ __('Import Spotify Recently Played') }}</x-primary-button>
                    </div>
                    <i class="text-white text-sm">Recently played is imported every 2 to 3 hours. Click to attempt refresh now</i>
                </form>
                <hr class="mt-4">
                <form method="post" action="{{ route('listen-all.generate-playlist') }}" class="mt-6 space-y-6">
                    @csrf
                    
                    <div class="flex items-center gap-4">
                        <p class="text-white">Playlist generation strategy ({{ min(100, $library_songs_count - $unique_library_listened_songs)}} new unplayed songs):</p>
                        <select name="playlist_option">
                            <option value="random">Random</option>
                            <option value="alphabetical_song">Alphabetical Song</option>
                            {{-- <option value="alphabetical_album">Alphabetical Album</option> --}}
                            {{-- <option value="alphabetical_artist">Alphabetical Artist</option> --}}
                            <option value="recently_added">Latest Added</option>
                            <option value="earliest_added">Earliest Added</option>
                        </select>
                    </div>

                    <div class="flex items-center gap-4">
                        @if($spotify_playlist_name ?? null)
                        <x-primary-button name="replace">{{ 'Replace songs in playlist "'.$spotify_playlist_name.'"' }}</x-primary-button>
                        @endif
                        <x-primary-button name="create">{{ __('Create new playlist "Fiopsty Listen All"') }}</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @else
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <form method="post" action="{{ route('listen-all.reset') }}" class="mt-6 space-y-6">
                    @csrf    
                    @method('DELETE')

                    <div class="flex items-center gap-4">
                        <x-primary-button>{{ __('Start listening counter') }}</x-primary-button>

                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
    
    
</x-app-layout>
