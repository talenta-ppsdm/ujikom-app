<header class="peserta-header">
    <a class="peserta-brand" href="{{ route('dashboard-peserta.index') }}" aria-label="Dashboard peserta">
        <span class="peserta-brand-mark">
            <img src="{{ asset('assets/images/logo-pkp-khaki.png') }}" alt="Logo Kementerian PKP">
        </span>
        <span class="peserta-brand-copy">
            <strong class="peserta-brand-compact-title">SI-JUKI</strong>
            <small class="peserta-brand-compact-subtitle">Sistem Informasi Uji Kompetensi</small>
        </span>
    </a>
    
    <nav class="peserta-nav" id="peserta-navigation" aria-label="Navigasi peserta">
        <a class="peserta-nav-link {{ request()->routeIs('dashboard-peserta.index') ? 'active' : '' }}" href="{{ route('dashboard-peserta.index') }}">
            <i class="bi bi-house-door" aria-hidden="true"></i>
            <span>Beranda</span>
        </a>
        <a class="peserta-nav-link {{ request()->routeIs('riwayat-ujian.index') ? 'active' : '' }}" href="{{ route('riwayat-ujian.index') }}">
            <i class="bi bi-journal-check" aria-hidden="true"></i>
            <span>Riwayat Ujian</span>
        </a>
        <a class="peserta-nav-link peserta-nav-announcement {{ request()->routeIs('riwayat-pengumuman.index') ? 'active' : '' }}" href="{{ route('riwayat-pengumuman.index') }}" >
            <i class="bi bi-bell" aria-hidden="true"></i>
            <span>Pengumuman</span>
            <!-- <b aria-label="2 pengumuman baru">2</b> -->
        </a>
        <a class="peserta-nav-link peserta-nav-profile {{ request()->routeIs('peserta.profil') ? 'active' : '' }}" href="{{ route('peserta.profil') }}">
            <i class="bi bi-person" aria-hidden="true"></i>
            <span>Profil Saya</span>
        </a>
        <form action="{{ route('logout') }}" method="POST" class="peserta-nav-logout">
            @csrf
            <button type="submit">
                <i class="bi bi-box-arrow-right" aria-hidden="true"></i>
                <span>Keluar</span>
            </button>
        </form>
    </nav>

    <button class="peserta-menu-toggle" type="button" aria-controls="peserta-navigation" aria-expanded="false" aria-label="Buka menu">
        <i class="bi bi-list peserta-menu-icon-open" aria-hidden="true"></i>
        <i class="bi bi-x-lg peserta-menu-icon-close" aria-hidden="true"></i>
    </button>
    
    <div class="peserta-account" id="profil">
        <div class="peserta-account-copy">
            <strong>{{ Auth::user()->name }}</strong>
            <small>{{ Auth::user()->nip }}</small>
        </div>
        <span class="peserta-account-avatar" aria-hidden="true">{{ strtoupper(substr(Auth::user()->name, 0, 2)) }}</span>
        <form action="{{ route('logout') }}" method="POST" class="d-inline">
          @csrf
          <button type="submit" class="dropdown-item text-danger" aria-label="Keluar">
              <i class="bi bi-box-arrow-right" aria-hidden="true"></i>
          </button>
        </form>
    </div>
</header>

<script>
    (() => {
        const header = document.querySelector('.peserta-header');
        const toggle = header?.querySelector('.peserta-menu-toggle');
        const navigation = header?.querySelector('.peserta-nav');

        if (!toggle || !navigation) return;

        const setMenuOpen = (isOpen) => {
            navigation.classList.toggle('is-open', isOpen);
            toggle.setAttribute('aria-expanded', String(isOpen));
            toggle.setAttribute('aria-label', isOpen ? 'Tutup menu' : 'Buka menu');
        };

        toggle.addEventListener('click', () => {
            setMenuOpen(toggle.getAttribute('aria-expanded') !== 'true');
        });

        navigation.addEventListener('click', (event) => {
            if (event.target.closest('a')) setMenuOpen(false);
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') setMenuOpen(false);
        });

        window.addEventListener('resize', () => {
            if (window.innerWidth > 1200) setMenuOpen(false);
        });
    })();
</script>