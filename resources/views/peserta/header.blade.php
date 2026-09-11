<header class="peserta-header">
    <a class="peserta-brand" href="{{ route('dashboard-peserta.index') }}" aria-label="Dashboard peserta">
        <span class="peserta-brand-mark">
            <img src="{{ asset('assets/images/logo-pkp-khaki.png') }}" alt="Logo Kementerian PKP">
        </span>
        <span class="peserta-brand-copy">
            <strong>Uji Kompetensi JF Penata Kelola Perumahan</strong>
            <small>Kementerian Perumahan dan Kawasan Permukiman</small>
        </span>
    </a>
    
    <nav class="peserta-nav" aria-label="Navigasi peserta">
        <a class="peserta-nav-link active" href="/beranda">
            <i class="bi bi-house-door" aria-hidden="true"></i>
            <span>Beranda</span>
        </a>
        <a class="peserta-nav-link" href="">
            <i class="bi bi-journal-check" aria-hidden="true"></i>
            <span>Ujian CBT</span>
        </a>
        <a class="peserta-nav-link peserta-nav-announcement" href="">
            <i class="bi bi-bell" aria-hidden="true"></i>
            <span>Pengumuman</span>
            <b aria-label="2 pengumuman baru">2</b>
        </a>
        <a class="peserta-nav-link peserta-nav-profile" href="">
            <i class="bi bi-person" aria-hidden="true"></i>
            <span>Profil Saya</span>
        </a>
    </nav>
    
    <div class="peserta-account" id="profil">
        <div class="peserta-account-copy">
            <strong>{{ Auth::user()->name }}</strong>
            <small>{{ Auth::user()->nip }}</small>
        </div>
        <span class="peserta-account-avatar" aria-hidden="true">A</span>
        <form action="{{ route('logout') }}" method="POST" class="d-inline">
          @csrf
          <button type="submit" class="dropdown-item text-danger" aria-label="Keluar">
              <i class="bi bi-box-arrow-right" aria-hidden="true"></i>
          </button>
        </form>
    </div>
</header>