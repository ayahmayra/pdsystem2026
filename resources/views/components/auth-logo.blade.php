@php
    $orgSettings = \App\Models\OrgSettings::getInstance();
    $logoPath = $orgSettings->logo_path;
@endphp

@if($logoPath && \Storage::disk('public')->exists($logoPath))
    <img src="{{ \Storage::url($logoPath) }}" alt="{{ $orgSettings->short_name ?: 'Logo' }}" {{ $attributes->merge(['class' => 'object-contain']) }} />
@else
    <x-app-logo-icon {{ $attributes }} />
@endif

