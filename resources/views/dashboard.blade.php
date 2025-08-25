@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<!-- Background -->
<div class="fixed inset-0 -z-10 bg-center bg-cover brightness-50 blur-sm" style="background-image: url('{{ asset('images/gedung.jpg') }}');"></div>

<!-- Loader -->
<div id="loader" class="fixed inset-0 bg-white bg-opacity-60 flex items-center justify-center z-50 hidden">
    <div class="border-4 border-sky-700 border-t-transparent rounded-full w-10 h-10 animate-spin"></div>
</div>

<!-- Konten -->
<div class="flex-grow flex flex-col">
    <div class="py-6 px-4 container mx-auto lg:px-8">
        <div class="bg-white/80 backdrop-blur-md rounded-xl shadow-md p-6 max-w-screen-lg mx-auto">

            <!-- Header & Shortcut -->
            <div class="flex items-center justify-between gap-4 mb-6">
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 whitespace-nowrap">📊 Dashboard</h1>
                <div class="hidden sm:flex gap-2">
                    <a href="javascript:void(0);" onclick="tampilkanLaporan('tiket')" class="shrink-0 px-4 py-2 rounded-md bg-white/80 backdrop-blur-md shadow hover:shadow-lg transition text-sky-800 font-semibold text-sm">Laporan Tiket</a>
                    <a href="javascript:void(0);" onclick="tampilkanLaporan('kegiatan')" class="shrink-0 px-4 py-2 rounded-md bg-white/80 backdrop-blur-md shadow hover:shadow-lg transition text-indigo-800 font-semibold text-sm">Laporan Kegiatan</a>
                </div>

                <div class="relative sm:hidden">
                    <button id="menuBtn" class="p-2 rounded-md bg-white/80 backdrop-blur-md shadow hover:shadow-md">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <div id="menuDropdown" class="absolute right-0 mt-2 w-40 bg-white/90 backdrop-blur-md rounded-md shadow-lg z-50 hidden">
                        <a href="javascript:void(0);" onclick="tampilkanLaporan('tiket')" class="block px-4 py-2 text-sm text-gray-800 hover:bg-sky-100">Laporan Tiket</a>
                        <a href="javascript:void(0);" onclick="tampilkanLaporan('kegiatan')" class="block px-4 py-2 text-sm text-gray-800 hover:bg-indigo-100">Laporan Kegiatan</a>
                    </div>
                </div>
            </div>

            <!-- Laporan Tiket -->
            <div id="laporanTiketContainer">
                <form method="GET" class="mb-6 flex flex-col sm:flex-row items-start sm:items-center gap-3" onsubmit="showLoader()">
                    <label for="tanggal" class="text-sm font-medium text-gray-700">Tampilkan grafik sampai tanggal:</label>
                    <input type="date" name="tanggal" id="tanggal" value="{{ $selectedDate }}" class="border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring focus:ring-blue-500 transition">
                    <button type="submit" class="bg-sky-700 hover:bg-sky-600 text-white text-sm font-medium px-4 py-2 rounded-md shadow inline-flex items-center gap-2 transition-all duration-200 ease-in-out">Tampilkan</button>
                </form>

                <!-- Grafik Tiket -->
                <div class="mb-10">
                    <h2 class="text-xl font-semibold text-gray-700 mb-1">📈 Tiket Harian</h2>
                    <p class="text-sm text-gray-500 mb-4">Jumlah tiket dari {{ now()->subDays(6)->format('d M') }} sampai <strong>{{ \Carbon\Carbon::parse($selectedDate)->format('d M Y') }}</strong>.</p>
                    <div class="w-full h-[300px]"><canvas id="chartHarian"></canvas></div>
                </div>

                <div class="mb-10">
                    <h2 class="text-xl font-semibold text-gray-700 mb-1">🧯 Jenis Gangguan</h2>
                    <p class="text-sm text-gray-500 mb-4">Distribusi tiket berdasarkan jenis gangguan.</p>
                    <div class="w-full h-[350px] max-w-md mx-auto"><canvas id="chartGangguan"></canvas></div>
                </div>

                <div class="mb-10">
                    <h2 class="text-xl font-semibold text-gray-700 mb-1">📆 Tiket Masuk per Bulan</h2>
                    <p class="text-sm text-gray-500 mb-4">Jumlah tiket per bulan di tahun {{ now()->year }}.</p>
                    <div class="w-full h-[300px]"><canvas id="chartBulanan"></canvas></div>
                </div>

                <!-- Statistik -->
                <div class="mt-12 overflow-x-auto sm:overflow-visible">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pb-2">
                        <x-stat-card title="Jumlah Tiket" :value="$totalTiket" color="blue" />
                        <x-stat-card title="Tiket Hari Ini" :value="$tiketHariIni" color="green" />
                        <x-stat-card title="Tiket Proses" :value="$tiketProses" color="yellow" />
                    </div>
                </div>

                <div class="mt-6 text-center text-xs text-gray-600">📅 Data terakhir diperbarui: {{ \Carbon\Carbon::parse($lastUpdated)->setTimezone('Asia/Jakarta')->format('d M Y H:i') }} WIB</div>
            </div>

            <!-- Laporan Kegiatan -->
            <div id="laporanKegiatanContainer" class="hidden" data-loaded="false">
                {{-- AJAX will load content here --}}
            </div>

        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function showLoader() {
        document.getElementById('loader')?.classList.remove('hidden');
    }

    function hideLoader() {
        document.getElementById('loader')?.classList.add('hidden');
    }

    function tampilkanLaporan(jenis) {
        const tiket = document.getElementById('laporanTiketContainer');
        const kegiatan = document.getElementById('laporanKegiatanContainer');
        const loader = document.getElementById('loader');

        if (jenis === 'kegiatan') {
            tiket.classList.add('hidden');
            kegiatan.classList.remove('hidden');

            if (kegiatan.getAttribute('data-loaded') === 'true') return;

            showLoader();
            fetch("{{ route('dashboard.laporan-kegiatan.partial') }}")
                .then(res => res.text())
                .then(html => {
                    kegiatan.innerHTML = html;
                    kegiatan.setAttribute('data-loaded', 'true');
                })
                .catch(() => {
                    kegiatan.innerHTML = `<p class="text-red-600 text-sm text-center py-4">Gagal memuat data kegiatan</p>`;
                })
                .finally(() => hideLoader());
        } else {
            tiket.classList.remove('hidden');
            kegiatan.classList.add('hidden');
        }
    }

    function cariLaporanKegiatan(url = null) {
        const tanggal = document.getElementById('filterTanggal')?.value;
        const keyword = document.getElementById('filterSearch')?.value;
        const container = document.getElementById('laporanKegiatanContainer');

        showLoader();

        const params = new URLSearchParams();
        if (tanggal) params.append('tanggal', tanggal);
        if (keyword) params.append('keyword', keyword);

        const fetchUrl = url ?? `{{ route('dashboard.laporan-kegiatan.partial') }}?${params.toString()}`;

        fetch(fetchUrl, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.text())
        .then(html => {
            container.innerHTML = html;
            container.setAttribute('data-loaded', 'true');
        })
        .catch(() => {
            container.innerHTML = `<p class="text-red-600 text-sm text-center py-4">Gagal memuat data kegiatan</p>`;
        })
        .finally(() => hideLoader());
    }

    // Global pagination click interception
    document.addEventListener('click', function (e) {
        const link = e.target.closest('.pagination a');
        const container = document.getElementById('laporanKegiatanContainer');

        if (link && container?.contains(link)) {
            e.preventDefault();
            cariLaporanKegiatan(link.href);
        }
    });

    document.addEventListener("DOMContentLoaded", () => {
        // Grafik Harian
        const ctxHarian = document.getElementById('chartHarian')?.getContext('2d');
        if (ctxHarian) {
            new Chart(ctxHarian, {
                type: 'line',
                data: {
                    labels: {!! json_encode($grafikData->pluck('tanggal')) !!},
                    datasets: [
                        {
                            label: 'Jumlah Tiket',
                            data: {!! json_encode($grafikData->pluck('jumlah')) !!},
                            borderColor: '#3b82f6',
                            backgroundColor: 'rgba(59,130,246,0.2)',
                            fill: true,
                            tension: 0.3
                        },
                        {
                            label: 'Selesai',
                            data: {!! json_encode($grafikData->pluck('selesai')) !!},
                            borderColor: '#10b981',
                            backgroundColor: 'rgba(16,185,129,0.2)',
                            fill: true,
                            tension: 0.3
                        },
                        {
                            label: 'Proses',
                            data: {!! json_encode($grafikData->pluck('proses')) !!},
                            borderColor: '#f59e0b',
                            backgroundColor: 'rgba(245,158,11,0.2)',
                            fill: true,
                            tension: 0.3
                        }
                    ]
                },
                options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } } }
            });
        }

        // Grafik Bulanan
        const ctxBulanan = document.getElementById('chartBulanan')?.getContext('2d');
        if (ctxBulanan) {
            new Chart(ctxBulanan, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($monthlyData->pluck('bulan')) !!},
                    datasets: [{
                        label: 'Tiket per Bulan',
                        data: {!! json_encode($monthlyData->pluck('total')) !!},
                        backgroundColor: '#3b82f6',
                        borderColor: '#1d4ed8',
                        borderWidth: 1
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } } }
            });
        }

        // Grafik Gangguan
        const ctxGangguan = document.getElementById('chartGangguan')?.getContext('2d');
        if (ctxGangguan) {
            const labels = {!! json_encode($gangguanData->pluck('jenis_gangguan')) !!};
            const data = {!! json_encode($gangguanData->pluck('total')) !!};
            // Tambahkan lebih banyak warna agar tiap jenis gangguan berbeda
            const colorList = [
                '#f97316', // orange
                '#60a5fa', // blue
                '#22c55e', // green
                '#eab308', // yellow
                '#a21caf', // purple
                '#ef4444', // red
                '#0ea5e9', // sky
                '#f472b6', // pink
                '#64748b', // slate
                '#f59e42', // amber
            ];
            // Pilih warna berdasarkan urutan label
            const colors = labels.map((_, i) => colorList[i % colorList.length]);

            new Chart(ctxGangguan, {
                type: 'pie',
                data: { labels, datasets: [{ data, backgroundColor: colors }] },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'right',
                            labels: {
                                boxWidth: 18,
                                padding: 16,
                                font: {
                                    size: 14,
                                    family: "'Inter', 'Arial', sans-serif"
                                },
                                color: '#374151'
                            }
                        }
                    }
                }
            });
        }

        // Menu toggle
        const menuBtn = document.getElementById("menuBtn");
        const menuDropdown = document.getElementById("menuDropdown");
        if (menuBtn && menuDropdown) {
            menuBtn.addEventListener("click", () => menuDropdown.classList.toggle("hidden"));
            document.addEventListener("click", (e) => {
                if (!menuBtn.contains(e.target) && !menuDropdown.contains(e.target)) {
                    menuDropdown.classList.add("hidden");
                }
            });
        }
    });
</script>



@endsection

@section('custom_footer')
<footer class="mt-auto w-full text-center text-sm text-gray-100 py-3 bg-gray-900 shadow-md border-t border-gray-700">
    <p>© {{ date('Y') }} PLN Sistem. Frontend by dil.chw</p>
</footer>
@endsection
