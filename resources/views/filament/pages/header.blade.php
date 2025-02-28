<div>
    <h1 class="text-2xl font-bold tracking-tight">
        {{ $heading }}
    </h1>
    @if (isset($subheading))
        <p class="mt-2 text-sm text-gray-500">
            {{ $subheading }}
        </p>
    @endif
</div>
