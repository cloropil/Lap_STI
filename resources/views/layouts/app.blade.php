<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Sistem') - PLN</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    maxHeight: {
                        '96': '24rem',
                    },
                },
            },
        }
    </script>

<style>
    #animated-nav {
        position: relative;
        display: flex;
        align-items: center;
    }

    #animated-nav a {
        position: relative;
        padding: 8px 12px;
        text-decoration: none;
        z-index: 1; /* Pastikan link di atas tubelight */
    }

    #animated-nav .tubelight {
        position: absolute;
        top: 50%;
        /* Hapus left: 0; di sini, akan diatur via JS */
        transform: translateY(-50%);
        height: 38px; /* Sesuaikan tinggi */
        background-color: #e0e7ff; /* Warna biru muda untuk highlight */
        border-radius: 8px;
        /* Penting: Transisi untuk left dan width */
        transition: left 400ms cubic-bezier(0.4, 0, 0.2, 1), width 400ms cubic-bezier(0.4, 0, 0.2, 1), opacity 300ms ease-in-out;
        z-index: 0; /* Di belakang link */
    }

    #animated-nav a > span {
        display: none;
    }

    #animated-nav a.active {
        font-weight: 600; /* semi-bold */
        color: #1e3a8a; /* biru tua */
    }
</style>
</head>


<body class="bg-white text-gray-800">
@php $user = Auth::user(); @endphp

<div class="flex flex-col min-h-screen">

    <div id="announcement-bar" class="bg-gray-100 text-center text-sm py-2 border-b transition-transform duration-300">
        PT. PLN Persero UID Sumatera Barat
        <a href="https://www.pln.co.id" class="text-blue-600 font-medium ml-1">→</a>
    </div>

    <header id="header" class="bg-white sticky top-0 z-50 shadow-sm">
        <div class="w-full flex justify-between items-center h-20 px-6 sm:px-10">
            <div class="flex items-center">
                <img src="{{ asset('logo.svg') }}" alt="Logo PLN" class="h-8 w-auto">
            </div>

            <nav id="animated-nav" class="hidden md:flex space-x-2">
                @php $routes = [
                    'dashboard' => 'Dashboard',
                    'inputtiket.index' => 'Tiket',
                    'kegiatan.index' => 'Kegiatan',
                    'laporan.harian' => 'Laporan',
                    'lokasi.index' => 'Lokasi'
                ]; @endphp

                <div class="tubelight"></div>

                @foreach ($routes as $route => $label)
                    @php $isActive = request()->routeIs(str_replace('.index', '.*', $route)) || request()->routeIs($route); @endphp
                    <a href="{{ route($route) }}"
                       {{-- Tambahkan data-url hanya untuk link yang akan menggunakan AJAX --}}
                       @if ($route !== 'dashboard') data-url="{{ route($route) }}" @endif
                       class="font-medium text-gray-600 hover:text-black {{ $isActive ? 'active' : '' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </nav>

            <div class="flex items-center space-x-4">
                <div class="relative" id="account-wrapper">
                    <div id="account-hover" class="flex items-center justify-center cursor-pointer">
                        <div class="w-9 h-9 rounded-full bg-blue-600 text-white flex items-center justify-center font-semibold uppercase">
                            {{ substr($user->name ?? 'P', 0, 1) }}
                        </div>
                    </div>
                    <div id="account-dropdown"
                         class="absolute left-1/2 transform -translate-x-1/2 mt-2 w-44 bg-white shadow-lg rounded-md py-2 opacity-0 invisible transition duration-200 z-40 text-sm">
                        <div class="px-4 py-2 text-gray-600 font-semibold border-b border-gray-200">
                            Hi, {{ $user->name ?? 'Pengguna' }}
                        </div>

                        @if ($user && method_exists($user, 'isSuperadmin') && $user->isSuperadmin())
                            <a href="{{ route('akun-pengguna.index') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                Tambah Pengguna
                            </a>
                        @endif

                        <a href="{{ route('akun-pengguna.settings') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                            Pengaturan Akun
                        </a>

                        <button onclick="showLogoutModal()" class="w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-100">
                            Logout
                        </button>
                    </div>
                </div>

                <button class="text-gray-600 hover:text-black focus:outline-none">
                    <i class="bi bi-bell text-xl"></i>
                </button>

                <button id="hamburger-btn" onclick="toggleMobileNav()" class="md:hidden text-gray-600 hover:text-black focus:outline-none">
                    <i class="bi bi-list text-2xl"></i>
                </button>
            </div>
        </div>
    </header>

    <div id="mobile-nav" class="fixed inset-0 bg-white/80 backdrop-blur-md z-40 hidden md:hidden transition-all duration-300">
        {{-- Konten mobile nav Anda --}}
        {{-- Anda perlu menambahkan ulang link navigasi di sini jika Anda ingin animasi tubelight juga di mobile nav --}}
    </div>

    <main id="main-content" class="flex-grow px-6 sm:px-8 py-6">
        @yield('content')
    </main>

    @hasSection('custom_footer')
        @yield('custom_footer')
    @else
        <footer class="text-center text-sm text-gray-500 py-4 border-t border-gray-200">
            © {{ date('Y') }} PLN Sistem. Frontend By dil.chw
        </footer>
    @endif
</div>

<div id="logout-modal" class="fixed inset-0 bg-black bg-opacity-40 backdrop-blur-sm flex items-center justify-center z-50 opacity-0 invisible transition duration-300 ease-in-out">
    {{-- Konten Modal Anda --}}
    <div class="bg-white p-8 rounded-lg shadow-xl max-w-sm w-full">
        <h3 class="text-xl font-semibold text-gray-900 mb-4">Konfirmasi Logout</h3>
        <p class="text-gray-700 mb-6">Apakah Anda yakin ingin keluar?</p>
        <div class="flex justify-end space-x-3">
            <button onclick="hideLogoutModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400">Batal</button>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">Logout</button>
            </form>
        </div>
    </div>
</div>

<script>
    // Script Announcement Bar
    document.addEventListener("DOMContentLoaded", function() {
        const announcementBar = document.getElementById('announcement-bar');
        if (announcementBar) {
            // Anda bisa menambahkan logika untuk menyembunyikan atau mengubah teks announcement bar di sini
            // setTimeout(() => {
            //     announcementBar.style.transform = 'translateY(-100%)';
            // }, 5000);
        }
    });

    // Script Dropdown Hover (Akun Pengguna)
    document.addEventListener("DOMContentLoaded", function() {
        const accountWrapper = document.getElementById('account-wrapper');
        const accountDropdown = document.getElementById('account-dropdown');

        if (accountWrapper && accountDropdown) {
            let timeout;

            accountWrapper.addEventListener('mouseenter', function() {
                clearTimeout(timeout);
                accountDropdown.classList.remove('invisible', 'opacity-0');
                accountDropdown.classList.add('visible', 'opacity-100');
            });

            accountWrapper.addEventListener('mouseleave', function() {
                timeout = setTimeout(() => {
                    accountDropdown.classList.remove('visible', 'opacity-100');
                    accountDropdown.classList.add('invisible', 'opacity-0');
                }, 200); // Penundaan 200ms
            });
        }
    });

    // Script Logout Modal
    function showLogoutModal() {
        const logoutModal = document.getElementById('logout-modal');
        if (logoutModal) {
            logoutModal.classList.remove('invisible', 'opacity-0');
            logoutModal.classList.add('visible', 'opacity-100');
        }
    }

    function hideLogoutModal() {
        const logoutModal = document.getElementById('logout-modal');
        if (logoutModal) {
            logoutModal.classList.remove('visible', 'opacity-100');
            logoutModal.classList.add('invisible', 'opacity-0');
        }
    }

    // Menutup modal jika klik di luar kontennya
    document.addEventListener('click', function(event) {
        const logoutModal = document.getElementById('logout-modal');
        if (logoutModal && logoutModal.classList.contains('visible')) {
            const modalContent = logoutModal.querySelector('.bg-white'); // Asumsi konten modal punya bg-white
            if (modalContent && !modalContent.contains(event.target) && event.target === logoutModal) {
                hideLogoutModal();
            }
        }
    });

    // Script Toggle Mobile Nav
    function toggleMobileNav() {
        const mobileNav = document.getElementById('mobile-nav');
        const hamburgerBtn = document.getElementById('hamburger-btn');
        if (mobileNav && hamburgerBtn) {
            mobileNav.classList.toggle('hidden');
            // Anda bisa menambahkan ikon X pada hamburger jika nav terbuka
            // hamburgerBtn.innerHTML = mobileNav.classList.contains('hidden') ? '<i class="bi bi-list text-2xl"></i>' : '<i class="bi bi-x-lg text-2xl"></i>';
        }
    }
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const nav = document.querySelector("#animated-nav");
        const light = nav.querySelector(".tubelight");
        const contentArea = document.querySelector("#main-content");
        let activeLink = null; // Inisialisasi activeLink sebagai null

        // Fungsi untuk mengatur posisi tubelight
        function setLightPosition(element) {
            if (!element) {
                // Sembunyikan tubelight jika tidak ada elemen aktif
                light.style.opacity = '0';
                return;
            }
            light.style.opacity = '1'; // Pastikan tubelight terlihat
            light.style.left = `${element.offsetLeft}px`;
            light.style.width = `${element.offsetWidth}px`;
        }

        // Fungsi untuk menginisialisasi atau memperbarui status link navigasi
        function updateNavigationState(currentUrlPathname) {
            const links = [...nav.querySelectorAll("a")]; // Ambil ulang link setiap kali dipanggil
            let foundActive = false;

            const baseUrlPath = currentUrlPathname.split('/')[1] || '';

            let newlyActiveLink = null; // Menyimpan link yang baru akan aktif

            links.forEach(link => {
                link.classList.remove("active"); // Hapus kelas 'active' dari semua link

                const linkUrlPath = new URL(link.href).pathname.split('/')[1] || '';

                if (linkUrlPath && baseUrlPath === linkUrlPath) {
                    newlyActiveLink = link; // Tandai sebagai link yang akan aktif
                    foundActive = true;
                }
            });

            // Fallback & Penentuan Final activeLink
            if (newlyActiveLink) {
                activeLink = newlyActiveLink;
            } else {
                const initialActiveLink = nav.querySelector("a.active"); // Cek link yang aktif dari Blade
                if (initialActiveLink) {
                    activeLink = initialActiveLink;
                } else if (links.length > 0) {
                    const dashboardLink = links.find(link => link.textContent.trim() === 'Dashboard');
                    if (dashboardLink) {
                        activeLink = dashboardLink;
                    } else {
                        activeLink = links[0];
                    }
                } else {
                    activeLink = null; // Tidak ada link yang dapat diaktifkan
                }
            }

            // Tambahkan kelas 'active' setelah menentukan activeLink secara final
            if (activeLink) {
                activeLink.classList.add("active");
            }

            // Panggil setLightPosition setelah activeLink ditentukan
            setLightPosition(activeLink);
        }

        // Inisialisasi awal saat halaman dimuat
        updateNavigationState(window.location.pathname);

        // Tambahkan event listener untuk klik pada link navigasi
        nav.addEventListener("click", function(e) {
            const targetLink = e.target.closest("a"); // Delegasi event
            if (!targetLink) return; // Bukan link yang diklik

            const url = targetLink.getAttribute('data-url');
            if (!url) {
                // Jika tidak ada data-url (misal: Dashboard), biarkan browser navigasi normal
                updateNavigationState(new URL(targetLink.href).pathname);
                return;
            }

            e.preventDefault(); // Mencegah navigasi default browser

            // Update Tampilan Navigasi segera setelah klik
            // Ini akan memicu animasi meluncur
            updateNavigationState(new URL(targetLink.href).pathname);

            // Lakukan Fetch AJAX
            fetchContent(url);
        });

        async function fetchContent(url) {
            try {
                contentArea.style.opacity = '0.5'; // Tambahkan efek loading

                const response = await fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest' // Header standar untuk request AJAX
                    }
                });

                if (!response.ok) {
                    throw new Error('Network response was not ok.');
                }

                const newContentHTML = await response.text();

                const parser = new DOMParser();
                const newDocument = parser.parseFromString(newContentHTML, 'text/html');
                const newMainContent = newDocument.querySelector('#main-content');
                const newTitle = newDocument.querySelector('title').innerText;

                if (newMainContent) {
                    contentArea.innerHTML = newMainContent.innerHTML;
                    document.title = newTitle;
                    window.history.pushState({ path: url, title: newTitle }, newTitle, url);
                }

            } catch (error) {
                console.error('Failed to fetch content:', error);
                window.location.href = url;
            } finally {
                contentArea.style.opacity = '1'; // Hilangkan efek loading
                // Setelah konten dimuat, panggil updateNavigationState lagi
                updateNavigationState(window.location.pathname);
            }
        }

        // Menangani tombol back/forward browser
        window.onpopstate = function(event) {
            if (event.state && event.state.path) {
                fetchContent(event.state.path);
                updateNavigationState(new URL(event.state.path).pathname);
            } else {
                updateNavigationState(window.location.pathname);
            }
        };

        // Menambahkan listener untuk event resize untuk menyesuaikan tubelight saat jendela diubah ukurannya
        window.addEventListener('resize', () => {
            setLightPosition(activeLink);
        });
    });
</script>

@stack('scripts')
</body>
</html>