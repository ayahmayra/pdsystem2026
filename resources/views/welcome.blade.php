<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>PdSystem - Sistem Perjalanan Dinas</title>

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&family=poppins:400,500,600,700" rel="stylesheet" />

        <!-- Styles -->
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            body {
                font-family: 'Inter', sans-serif;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                color: #333;
            }

            .container {
                max-width: 1200px;
                width: 100%;
                padding: 2rem;
            }

            .card {
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(10px);
                border-radius: 20px;
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
                overflow: hidden;
                display: grid;
                grid-template-columns: 1fr 1fr;
                min-height: 600px;
            }

            .content {
                padding: 3rem;
                display: flex;
                flex-direction: column;
                justify-content: center;
            }

            .logo-section {
                background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                padding: 3rem;
                color: white;
                position: relative;
                overflow: hidden;
            }

            .logo-section::before {
                content: '';
                position: absolute;
                top: -50%;
                left: -50%;
                width: 200%;
                height: 200%;
                background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
                animation: float 6s ease-in-out infinite;
            }

            @keyframes float {
                0%, 100% { transform: translateY(0px) rotate(0deg); }
                50% { transform: translateY(-20px) rotate(180deg); }
            }

            .app-logo {
                width: 120px;
                height: 120px;
                background: transparent;
                border-radius: 20px;
                display: flex;
                align-items: center;
                justify-content: center;
                margin-bottom: 1.5rem;
                position: relative;
                z-index: 1;
            }

            .app-logo svg {
                width: 80px;
                height: 80px;
            }

            .app-logo img {
                width: 100%;
                height: 100%;
                object-fit: contain;
            }

            .app-title {
                font-size: 2.5rem;
                font-weight: 700;
                margin-bottom: 0.5rem;
                position: relative;
                z-index: 1;
                font-family: 'Poppins', sans-serif;
            }

            .app-subtitle {
                font-size: 1.1rem;
                opacity: 0.9;
                margin-bottom: 2rem;
                position: relative;
                z-index: 1;
            }

            .features {
                list-style: none;
                margin-bottom: 2rem;
            }

            .features li {
                display: flex;
                align-items: center;
                margin-bottom: 1rem;
                font-size: 0.95rem;
                color: #666;
            }

            .features li::before {
                content: '✓';
                background: #10b981;
                color: white;
                width: 20px;
                height: 20px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                margin-right: 0.75rem;
                font-size: 0.75rem;
                font-weight: bold;
            }

            .btn {
                display: inline-block;
                padding: 0.875rem 2rem;
                border-radius: 10px;
                text-decoration: none;
                font-weight: 600;
                transition: all 0.3s ease;
                text-align: center;
                margin-right: 1rem;
                margin-bottom: 0.5rem;
            }

            .btn-primary {
                background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
                color: white;
                box-shadow: 0 4px 15px rgba(79, 70, 229, 0.4);
            }

            .btn-primary:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 25px rgba(79, 70, 229, 0.6);
            }

            .btn-secondary {
                background: transparent;
                color: #4f46e5;
                border: 2px solid #4f46e5;
            }

            .btn-secondary:hover {
                background: #4f46e5;
                color: white;
                transform: translateY(-2px);
            }


            .stats {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 1.5rem;
                margin-top: 2rem;
            }

            .stat {
                text-align: center;
                padding: 1.5rem;
                background: rgba(29, 26, 104, 0.1);
                border-radius: 15px;
                position: relative;
                z-index: 1;
            }

            .stat-number {
                font-size: 2rem;
                font-weight: 700;
                color: white;
                margin-bottom: 0.5rem;
            }

            .stat-label {
                font-size: 0.875rem;
                color: white;
            }

            .powered-by {
                position: absolute;
                bottom: 2rem;
                left: 50%;
                transform: translateX(-50%);
                font-size: 0.875rem;
                color: rgba(255, 255, 255, 0.8);
                text-align: center;
                z-index: 1;
            }

            @media (max-width: 768px) {
                .card {
                    grid-template-columns: 1fr;
                    margin: 1rem;
                }
                
                .content {
                    padding: 2rem;
                    order: 2;
                }
                
                .logo-section {
                    padding: 2rem;
                    order: 1;
                }
                
                .app-title {
                    font-size: 2rem;
                }
                
                .stats {
                    grid-template-columns: 1fr;
                }

                .powered-by {
                    position: static;
                    transform: none;
                    margin-top: 2rem;
                    color: #666;
                }
            }

            @media (max-width: 480px) {
                .container {
                    padding: 1rem;
                }
                
                .card {
                    margin: 0.5rem;
                    border-radius: 15px;
                }
                
                .content {
                    padding: 1.5rem;
                }
                
                .logo-section {
                    padding: 1.5rem;
                }
                
                .app-title {
                    font-size: 1.75rem;
                }
                
                .app-subtitle {
                    font-size: 1rem;
                }
                
                .btn {
                    padding: 0.75rem 1.5rem;
                    font-size: 0.9rem;
                }
                
                .stats {
                    gap: 1rem;
                }
                
                .stat {
                    padding: 1rem;
                }
                
                .stat-number {
                    font-size: 1.5rem;
                }
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="card">
                <div class="content">
                    @php
                        $orgSettings = \App\Models\OrgSettings::getInstance();
                    @endphp
                    <h1 class="app-title">{{ $orgSettings->short_name ?: 'PdSystem' }}</h1>
                    <p class="app-subtitle">Sistem Manajemen Perjalanan Dinas yang Terintegrasi</p>
                    
                    <p style="margin-bottom: 2rem; color: #666; line-height: 1.6;">
                        Solusi lengkap untuk mengelola perjalanan dinas dengan fitur-fitur modern dan user-friendly. 
                        Dapatkan kemudahan dalam mengatur nota dinas, SPT, SPPD, dan laporan perjalanan dinas.
                    </p>

                    <ul class="features">
                        <li>Manajemen Nota Dinas Digital</li>
                        <li>Surat Perintah Tugas (SPT) Otomatis</li>
                        <li>Surat Perjalanan Perjalanan Dinas (SPPD)</li>
                        <li>Laporan Perjalanan Dinas Terintegrasi</li>
                        <li>Rekapitulasi Data Real-time</li>
                        <li>Export PDF & Excel</li>
                    </ul>

                    <div>
                        @auth
                            <a href="{{ url('/dashboard') }}" class="btn btn-primary">Buka Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-primary">Masuk ke Sistem</a>
                            {{-- @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="btn btn-secondary">Daftar Akun</a>
                            @endif --}}
                        @endauth
                    </div>
                </div>

                <div class="logo-section">
                    <div class="app-logo">
                        @php
                            $orgSettings = \App\Models\OrgSettings::getInstance();
                            $logoPath = $orgSettings->logo_path;
                        @endphp
                        @if($logoPath && \Storage::disk('public')->exists($logoPath))
                            <img src="{{ \Storage::url($logoPath) }}" alt="Logo" />
                        @else
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                            </svg>
                        @endif
                    </div>
                    <h2 class="app-title">{{ $orgSettings->short_name ?: 'PdSystem' }}</h2>
                    <p class="app-subtitle">Sistem Perjalanan Dinas</p>
                    
                    <div class="stats">
                        <div class="stat">
                            <div class="stat-number">100%</div>
                            <div class="stat-label">Digital</div>
                        </div>
                        <div class="stat">
                            <div class="stat-number">24/7</div>
                            <div class="stat-label">Akses</div>
                        </div>
                        <div class="stat">
                            <div class="stat-number">∞</div>
                            <div class="stat-label">Efisien</div>
                        </div>
                    </div>
                    
                    <div class="powered-by">
                        powered by Tim IT BPKAD Kab. Bengkalis
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
