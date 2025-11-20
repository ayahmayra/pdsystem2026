<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Appearance')" :subheading=" __('Update the appearance settings for your account')">
        <div class="space-y-4">
            <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-blue-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="text-blue-800 font-medium">Dark Mode Dinonaktifkan</span>
                </div>
                <p class="text-blue-700 text-sm mt-1">Sistem saat ini menggunakan mode terang (light mode) secara permanen.</p>
            </div>
            
            <flux:radio.group x-data variant="segmented" x-model="$flux.appearance" disabled>
                <flux:radio value="light" icon="sun" checked>{{ __('Light') }}</flux:radio>
                <flux:radio value="dark" icon="moon" disabled>{{ __('Dark') }}</flux:radio>
                <flux:radio value="system" icon="computer-desktop" disabled>{{ __('System') }}</flux:radio>
            </flux:radio.group>
        </div>
    </x-settings.layout>
</section>
