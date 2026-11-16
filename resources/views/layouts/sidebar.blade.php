<div class="sidebar-wrapper" id="sidebar">
    <!-- Brand Logo / Identity -->
    <a href="{{ route('admin.dashboard') }}" class="sidebar-brand">
      <i class="bi bi-asterisk"></i>
      <span>UJIKOM PPSDM</span>
    </a>

    <!-- Navigation Menu -->
    <div class="flex-grow-1 overflow-y-auto">
      <!-- Group: Menu -->
      <div class="sidebar-menu-section">
        <div class="sidebar-menu-title">Menu Utama</div>
        <ul class="sidebar-menu-list">
          <li class="sidebar-menu-item">
            <a href="{{ route('admin.dashboard') }}" 
              class="sidebar-menu-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" 
              id="menu-dashboard" 
              title="Dashboard">
              <i class="bi bi-speedometer2"></i>
              <span>Dashboard</span>
            </a>
          </li>

          <li class="sidebar-menu-item">
            <a href="{{ route('admin.peserta.index') }}" 
              class="sidebar-menu-link {{ request()->routeIs('admin.peserta.index') ? 'active' : '' }}" 
              id="menu-peserta" 
              title="Peserta">
              <i class="bi bi-people-fill"></i>
              <span>Peserta</span>
            </a>
          </li>

          @php($jadwalMenuActive = request()->routeIs('jadwal-ujian.*', 'jadwal-cbt.*', 'jadwal-wawancara.*'))
          <li class="sidebar-menu-item">
            <button type="button"
              class="sidebar-menu-link sidebar-menu-toggle {{ $jadwalMenuActive ? 'active' : '' }}"
              data-bs-toggle="collapse"
              data-bs-target="#menu-jadwal-submenu"
              aria-expanded="{{ $jadwalMenuActive ? 'true' : 'false' }}"
              aria-controls="menu-jadwal-submenu"
              title="Jadwal">
              <i class="bi bi-calendar-check-fill"></i>
              <span>Jadwal</span>
              <i class="bi bi-chevron-down dropdown-caret"></i>
            </button>
            <ul class="sidebar-submenu collapse {{ $jadwalMenuActive ? 'show' : '' }}" id="menu-jadwal-submenu">
              <li>
                <a href="{{ route('admin.jadwal-ujian.index') }}"
                  class="sidebar-submenu-link {{ request()->routeIs('admin.jadwal-ujian.*') ? 'active' : '' }}"
                  id="menu-jadwal-ujian">
                  Jadwal Ujian
                </a>
              </li>
              <li>
                <a href="{{ route('admin.jadwal-cbt.index') }}"
                  class="sidebar-submenu-link {{ request()->routeIs('admin.jadwal-cbt.*') ? 'active' : '' }}"
                  id="menu-jadwal-cbt">
                  Jadwal CBT
                </a>
              </li>
              <li>
                <a href="{{ route('admin.jadwal-wawancara.index') }}"
                  class="sidebar-submenu-link {{ request()->routeIs('admin.jadwal-wawancara.*') ? 'active' : '' }}"
                  id="menu-jadwal-wawancara">
                  Jadwal Wawancara
                </a>
              </li>
            </ul>
          </li>

          <li class="sidebar-menu-item">
            <a href="{{ route('admin.verifikasi-pemberkasan.index') }}" 
              class="sidebar-menu-link {{ request()->routeIs('admin.verifikasi-pemberkasan.index') ? 'active' : '' }}" 
              id="menu-verifikasi-pemberkasan" 
              title="Verifikasi Pemberkasan">
              <i class="bi bi-clipboard-check-fill"></i>
              <span>Verifikasi Berkas</span>
            </a>
          </li>

          <li class="sidebar-menu-item">
            <a href="{{ route('admin.penguji.index') }}" 
              class="sidebar-menu-link {{ request()->routeIs('admin.penguji.index') ? 'active' : '' }}" 
              id="menu-penguji" 
              title="Penguji Teknis">
              <i class="bi bi-person-fill-gear"></i>
              <span>Penguji Teknis</span>
            </a>
          </li>

          <li class="sidebar-menu-item">
            <a href="{{ route('admin.banksoal.index') }}" 
              class="sidebar-menu-link {{ request()->routeIs('admin.bank-soal.index') ? 'active' : '' }}" 
              id="menu-banksoal" 
              title="Bank Soal">
              <i class="bi bi-collection-fill"></i>
              <span>Bank Soal</span>
            </a>
          </li>

          <li class="sidebar-menu-item">
            <a href="{{ route('admin.pengumuman.index') }}" 
              class="sidebar-menu-link {{ request()->routeIs('admin.pengumuman.index') ? 'active' : '' }}" 
              id="menu-pengumuman" 
              title="Pengumuman">
              <i class="bi bi-megaphone-fill"></i>
              <span>Pengumuman</span>
            </a>
          </li>

          <li class="sidebar-menu-item">
            <a href="{{ route('admin.timekeeper.index') }}" 
              class="sidebar-menu-link {{ request()->routeIs('admin.timekeeper.index') ? 'active' : '' }}" 
              id="menu-pengumuman" 
              title="Pengumuman">
              <i class="bi bi-people-fill"></i>
              <span>Timekeeper</span>
            </a>
          </li>
        </ul>
      </div>
    </div>

    <!-- Sidebar Profile Card (Dynamic Footer) -->
    <div class="sidebar-profile">
      <img src="{{ $adminPhoto ? asset('storage/' . $adminPhoto) : asset('assets/images/avatar.png') }}"
        alt="{{ Auth::user()->name }}" class="sidebar-profile-img"
        onerror="this.onerror=null;this.src='{{ asset('assets/images/avatar.png') }}'">
      <div class="sidebar-profile-info">
        <div class="sidebar-profile-name">{{ Auth::user()->name }}</div>
        <div class="sidebar-profile-email">{{ Auth::user()->email }}</div>
      </div>
    </div>
  </div>