@php
    $orgSettings = \App\Models\OrgSettings::getInstance();
    $logoPath = $orgSettings->logo_path;
@endphp

<div class="flex aspect-square size-10 items-center justify-center rounded-md bg-transparent text-accent-foreground overflow-hidden">
    @if($logoPath && \Storage::disk('public')->exists($logoPath))
        <img src="{{ \Storage::url($logoPath) }}" alt="Logo" class="size-8 object-contain" />
    @else
        <x-app-logo-icon class="size-8 fill-current text-gray-600 dark:text-gray-300" />
    @endif
</div>
<div class="ms-1 grid flex-1 text-start text-sm">
    <span class="mb-0.5 truncate leading-tight font-semibold">PDSystem</span>
</div>
