<x-layouts.app title="Dokumentasi">
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-8">
                        <h1 class="text-3xl font-bold text-gray-900 mb-4">
                            📚 Dokumentasi Sistem Perjalanan Dinas
                        </h1>
                        <p class="text-lg text-gray-600">
                            Halaman ini berisi dokumentasi lengkap tentang update terbaru dan fitur-fitur yang tersedia dalam sistem.
                        </p>
                    </div>

                    <div class="space-y-8">
                        <!-- Update Terbaru Section -->
                        <div class="bg-blue-50 border-l-4 border-blue-400 p-6 rounded-r-lg">
                            <h2 class="text-2xl font-semibold text-blue-900 mb-4">
                                🚀 Update Terbaru
                            </h2>
                            <div class="space-y-4">

                                <div class="bg-white p-4 rounded-lg shadow-sm">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                        ✅ Perbaikan Nama unit dan Jabatan pada penanda tangan dokumen SPT, SPPD dan Kwitansi
                                    </h3>
                                    <p class="text-gray-700 mb-3">
                                        Implementasi logika jika penanda tangan adalah kepala organisasi atau bukan
                                    </p>
                                    <ul class="list-disc list-inside text-sm text-gray-600 space-y-1">
                                        <li>Tampilan kuitansi, SPPD, dan SPT PDF Blade yang diperbarui untuk menangani dan menampilkan informasi penandatangan dengan lebih baik. Kini mendukung tampilan deskripsi posisi, nama unit, dan nama organisasi dengan lebih fleksibel, dan menyesuaikan label pengguna anggaran berdasarkan apakah yang menandatangani adalah pimpinan organisasi.</li>
                                        
                                    </ul>
                                </div>

                                <div class="bg-white p-4 rounded-lg shadow-sm">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                        ✅ Collapsible Sidebar dengan Flux UI
                                    </h3>
                                    <p class="text-gray-700 mb-3">
                                        Implementasi sidebar yang dapat di-collapse dengan menggunakan komponen Flux UI yang sesuai dengan dokumentasi resmi.
                                    </p>
                                    <ul class="list-disc list-inside text-sm text-gray-600 space-y-1">
                                        <li>Sidebar dapat di-collapse pada tampilan desktop</li>
                                        <li>Mode icon-only dengan tooltip saat collapsed</li>
                                        <li>Mobile sidebar dengan toggle button yang responsif</li>
                                        <li>Overlay gelap saat sidebar terbuka di mobile</li>
                                        <li>Animasi smooth untuk transisi expand/collapse</li>
                                    </ul>
                                </div>

                                <div class="bg-white p-4 rounded-lg shadow-sm">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                        🎨 Dark Mode Disabled
                                    </h3>
                                    <p class="text-gray-700 mb-3">
                                        Sistem dark mode telah dinonaktifkan secara permanen untuk konsistensi tampilan.
                                    </p>
                                    <ul class="list-disc list-inside text-sm text-gray-600 space-y-1">
                                        <li>Dark mode dinonaktifkan di seluruh sistem</li>
                                        <li>Konsistensi tampilan light mode</li>
                                        <li>Override CSS untuk mencegah dark mode</li>
                                        <li>JavaScript prevention untuk dark mode toggle</li>
                                    </ul>
                                </div>

                                <div class="bg-white p-4 rounded-lg shadow-sm">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                        🔧 Console Errors Fixed
                                    </h3>
                                    <p class="text-gray-700 mb-3">
                                        Perbaikan berbagai error di console browser untuk pengalaman yang lebih baik.
                                    </p>
                                    <ul class="list-disc list-inside text-sm text-gray-600 space-y-1">
                                        <li>Runtime errors telah diperbaiki</li>
                                        <li>CSS preload warnings diatasi</li>
                                        <li>Global error handling untuk browser extensions</li>
                                        <li>Graceful error handling untuk Livewire components</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Fitur Utama Section -->
                        <div class="bg-green-50 border-l-4 border-green-400 p-6 rounded-r-lg">
                            <h2 class="text-2xl font-semibold text-green-900 mb-4">
                                ⭐ Fitur Utama Sistem
                            </h2>
                            <div class="grid md:grid-cols-2 gap-6">
                                <div class="bg-white p-4 rounded-lg shadow-sm">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                        📄 Manajemen Dokumen
                                    </h3>
                                    <ul class="list-disc list-inside text-sm text-gray-600 space-y-1">
                                        <li>SPT (Surat Perintah Tugas)</li>
                                        <li>SPPD (Surat Perjalanan Dinas)</li>
                                        <li>Nota Dinas</li>
                                        <li>Kwitansi</li>
                                        <li>Laporan Perjalanan Dinas</li>
                                    </ul>
                                </div>

                                <div class="bg-white p-4 rounded-lg shadow-sm">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                        👥 Master Data
                                    </h3>
                                    <ul class="list-disc list-inside text-sm text-gray-600 space-y-1">
                                        <li>Data Pegawai</li>
                                        <li>Data Pangkat</li>
                                        <li>Data Jabatan</li>
                                        <li>Data Organisasi</li>
                                        <li>Data Lokasi & Rute</li>
                                    </ul>
                                </div>

                                <div class="bg-white p-4 rounded-lg shadow-sm">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                        📊 Rekapitulasi
                                    </h3>
                                    <ul class="list-disc list-inside text-sm text-gray-600 space-y-1">
                                        <li>Rekap Global</li>
                                        <li>Rekap Per Pegawai</li>
                                        <li>Export ke Excel</li>
                                        <li>Filter berdasarkan periode</li>
                                    </ul>
                                </div>

                                <div class="bg-white p-4 rounded-lg shadow-sm">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                        ⚙️ Konfigurasi
                                    </h3>
                                    <ul class="list-disc list-inside text-sm text-gray-600 space-y-1">
                                        <li>Pengaturan Organisasi</li>
                                        <li>Format Penomoran</li>
                                        <li>Number Sequence</li>
                                        <li>Riwayat Nomor Dokumen</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Technical Information Section -->
                        {{-- <div class="bg-yellow-50 border-l-4 border-yellow-400 p-6 rounded-r-lg">
                            <h2 class="text-2xl font-semibold text-yellow-900 mb-4">
                                🔧 Informasi Teknis
                            </h2>
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                <h3 class="text-lg font-semibold text-gray-900 mb-3">
                                    Stack Teknologi
                                </h3>
                                <div class="grid md:grid-cols-2 gap-4">
                                    <div>
                                        <h4 class="font-semibold text-gray-800 mb-2">Backend:</h4>
                                        <ul class="list-disc list-inside text-sm text-gray-600 space-y-1">
                                            <li>Laravel 11</li>
                                            <li>PHP 8.2+</li>
                                            <li>MySQL/SQLite</li>
                                            <li>Livewire 3</li>
                                        </ul>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-800 mb-2">Frontend:</h4>
                                        <ul class="list-disc list-inside text-sm text-gray-600 space-y-1">
                                            <li>Flux UI</li>
                                            <li>Tailwind CSS</li>
                                            <li>Alpine.js</li>
                                            <li>Vite</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div> --}}

                        <!-- Support Section -->
                        <div class="bg-purple-50 border-l-4 border-purple-400 p-6 rounded-r-lg">
                            <h2 class="text-2xl font-semibold text-purple-900 mb-4">
                                🆘 Bantuan & Dukungan
                            </h2>
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                <p class="text-gray-700 mb-4">
                                    Jika Anda mengalami kendala atau membutuhkan bantuan, silakan hubungi administrator sistem.
                                </p>
                                <div class="grid md:grid-cols-2 gap-4">
                                    <div>
                                        <h4 class="font-semibold text-gray-800 mb-2">Kontak Support:</h4>
                                        <ul class="text-sm text-gray-600 space-y-1">
                                            <li>📧 Email: nothing4ll@gmail.com</li>
                                            <li>📞 Telepon: (+62) 8127606351</li>
                                            <li>💬 Chat: Live Support</li>
                                        </ul>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-800 mb-2">Jam Operasional:</h4>
                                        <ul class="text-sm text-gray-600 space-y-1">
                                            <li>Senin - Jumat: 08:00 - 17:00</li>
                                            <li>Sabtu: 08:00 - 12:00</li>
                                            <li>Minggu: Tutup</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Version Info -->
                        <div class="bg-gray-50 border border-gray-200 p-4 rounded-lg">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">Versi Sistem</h3>
                                    <p class="text-sm text-gray-600">v1.0.0 - {{ date('d F Y') }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm text-gray-500">Last Updated</p>
                                    <p class="text-sm font-medium text-gray-700">{{ date('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
