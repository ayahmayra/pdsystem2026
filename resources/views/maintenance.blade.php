<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Maintenance - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-900 dark:to-gray-800">
    <div class="min-h-screen flex items-center justify-center px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl w-full space-y-8">
            <!-- Logo -->
            <div class="text-center">
                @php
                    $orgSettings = \App\Models\OrgSettings::getInstance();
                    $logoPath = $orgSettings->logo_path;
                @endphp
                
                @if($logoPath && \Storage::disk('public')->exists($logoPath))
                    <img src="{{ \Storage::url($logoPath) }}" alt="Logo" class="mx-auto h-24 w-auto" />
                @else
                    <div class="mx-auto h-24 w-24 bg-blue-600 rounded-full flex items-center justify-center">
                        <svg class="h-12 w-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                @endif
            </div>

            <!-- Maintenance Card -->
            <div class="bg-white dark:bg-gray-800 shadow-2xl rounded-2xl overflow-hidden">
                <!-- Header with icon -->
                <div class="bg-gradient-to-r from-orange-500 to-orange-600 px-6 py-8">
                    <div class="flex justify-center">
                        <div class="bg-white dark:bg-gray-800 rounded-full p-4">
                            <svg class="h-16 w-16 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Content -->
                <div class="px-6 py-8 sm:px-10 sm:py-10">
                    <div class="text-center space-y-4">
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                            Sistem Dalam Maintenance
                        </h1>
                        
                        <div class="border-t border-b border-gray-200 dark:border-gray-700 py-6 my-6">
                            <p class="text-lg text-gray-600 dark:text-gray-400 leading-relaxed">
                                {{ $message }}
                            </p>
                        </div>

                        <div class="space-y-3 text-gray-500 dark:text-gray-400">
                            <div class="flex items-center justify-center space-x-2">
                                <svg class="h-5 w-5 text-orange-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                </svg>
                                <p class="text-sm">
                                    Kami sedang melakukan pemeliharaan sistem
                                </p>
                            </div>
                            
                            <div class="flex items-center justify-center space-x-2">
                                <svg class="h-5 w-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                <p class="text-sm">
                                    Sistem akan kembali online dalam waktu dekat
                                </p>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="pt-6 flex flex-col sm:flex-row gap-3 justify-center">
                            <button 
                                onclick="location.reload()" 
                                class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-colors duration-200"
                            >
                                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                Coba Lagi
                            </button>
                            
                            <a 
                                href="/login" 
                                class="inline-flex items-center justify-center px-6 py-3 border-2 border-gray-300 dark:border-gray-600 text-base font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
                            >
                                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                                </svg>
                                Login Admin
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="bg-gray-50 dark:bg-gray-900 px-6 py-4">
                    <div class="text-center text-sm text-gray-500 dark:text-gray-400">
                        <p>Jika Anda memiliki pertanyaan, silakan hubungi administrator sistem</p>
                    </div>
                </div>
            </div>

            <!-- Additional Info -->
            <div class="text-center text-sm text-gray-500 dark:text-gray-400">
                <p>© {{ date('Y') }} {{ $orgSettings->name ?? config('app.name') }}</p>
            </div>
        </div>
    </div>

    <!-- Auto-refresh script (optional - refresh every 5 minutes) -->
    <script>
        // Auto refresh every 5 minutes to check if maintenance is over
        setTimeout(function(){
            location.reload();
        }, 300000); // 300000ms = 5 minutes
    </script>
</body>
</html>

