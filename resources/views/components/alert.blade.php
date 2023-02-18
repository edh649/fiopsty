@aware(["alert"])

@if ($alert ?? null || Session::get('alert'))
    @if (array_key_exists("success", $alert ?? Session::get('alert')))
        <div class="{{"bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" }}" role="alert">
            <span class="block sm:inline">{{ $alert ? $alert["success"] : Session::get('alert')["success"] }}</span>
        </div>
    @endif
@endif
