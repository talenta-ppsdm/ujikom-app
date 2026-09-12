@extends('peserta.mainLayout')

@section('content')
<!-- Hero Beranda -->
<div class="col-12 peserta-home">
    <section class="peserta-welcome" aria-labelledby="welcome-title">
        <div class="peserta-welcome-content">
            <div class="peserta-welcome-badges">
                <span class="peserta-welcome-badge">Portal Peserta Uji Kompetensi</span>
                <span class="peserta-welcome-badge peserta-welcome-badge-accent">
                    <i class="bi bi-mortarboard-fill" aria-hidden="true"></i>
                    Jenjang: {{ $user->peserta->jenjang ?? 'JF Ahli Pertama' }}
                </span>
            </div>

            <h1 id="welcome-title">Selamat Datang, {{ $user->name }}</h1>
            <p class="peserta-welcome-identity">
                NIP: {{ $user->nip }}
                <span aria-hidden="true">•</span>
                {{ $user->peserta->jabatan }}
            </p>
            <p class="peserta-welcome-unit">
                {{ $user->peserta->unit }} ({{ $user->peserta->instansi }})
            </p>
        </div>

        <div class="peserta-welcome-art" aria-hidden="true">
            <span class="welcome-art-pill welcome-art-pill-top"></span>
            <span class="welcome-art-pill welcome-art-pill-middle"></span>
            <span class="welcome-art-arch"></span>
        </div>
    </section>
</div>
<!-- END Hero Beranda -->

<div class="row g-4">
    <!-- Left Section -->
    <div class="col-xl-8 col-lg-8">
        <div class="col-12 mb-3">
            <section class="ui-card" aria-labelledby="exam-status-title">
                <div class="ui-card-heading">
                    <div>
                        <p class="ui-eyebrow">Status Partisipasi Uji Kompetensi</p>
                        <h2 class="ui-card-title" id="exam-status-title">{{ ucwords($ujian['tujuan_ujian']->value) }}</h2>
                    </div>
                    <span class="ui-status-badge">{{ $ujian['status']}}</span>
                </div>
    
                <div class="ui-info-panel">
                    <i class="bi bi-clock-history" aria-hidden="true"></i>
                    <div>
                        <p class="ui-info-title">Sesi Ujian CBT Siap Dimulai</p>
                        <p class="ui-info-text">Soal ujian akan diacak secara otomatis dari bank soal teknis sesuai jenjang <strong>{{ $ujian['jenjang_tujuan'] }}</strong>.</p>
                    </div>
                </div>
    
                <div class="ui-metric-grid">
                    <div>
                        <span class="ui-metric-label">Jumlah Soal</span>
                        <span class="ui-metric-value">50 Butir</span>
                    </div>
                    <div>
                        <span class="ui-metric-label">Durasi Ujian</span>
                        <span class="ui-metric-value">{{ $ujian->durasi }} Menit</span>
                    </div>
                    <div>
                        <span class="ui-metric-label">Jenjang Dituju</span>
                        <span class="ui-metric-value ui-metric-value-accent">JF {{ $ujian->jenjang_tujuan }}</span>
                    </div>
                    <div>
                        <span class="ui-metric-label">Cakupan Level</span>
                        <span class="ui-metric-value ui-metric-value-accent">{{ ucwords($level) }}</span>
                    </div>
                </div>
    
                <a href="{{ route('ujian.start', $ujian->id) }}">
                    <button class="ui-card-action" type="submit">
                        <i class="bi bi-file-earmark-check" aria-hidden="true"></i>
                        Masuk Ruang Ujian CBT Sekarang
                        <i class="bi bi-arrow-right" aria-hidden="true"></i>
                    </button>
                </a>
            </section>
        </div>
    
        <!-- Card Ujikom question level -->
        <div class="col-12 mb-3">
            <section class="ui-card" aria-labelledby="level-title">
                <h2 class="ui-section-heading" id="level-title">
                    <i class="bi bi-mortarboard" aria-hidden="true"></i>
                    Struktur Jenjang &amp; Tingkat Soal Uji Kompetensi
                </h2>
    
                <div class="ui-level-grid">
                    <article class="ui-level-option {{ $ujian->jenjang_tujuan == \App\Enums\JenjangJabatanEnum::AHLI_PERTAMA->value ? 'is-selected' : ''}}">
                        <div class="ui-level-topline">
                            <span class="ui-level-name">JF Ahli Pertama</span>
                            <span class="ui-level-badge">Level 1</span>
                        </div>
                        <p class="ui-level-description">Pemahaman dasar perumahan swadaya &amp; regulasi</p>
                        <!-- <span class="ui-level-selected-label"><i class="bi bi-check-circle" aria-hidden="true"></i> Jenjang Terpilih Anda</span> -->
                    </article>
                    <article class="ui-level-option {{ $ujian->jenjang_tujuan == \App\Enums\JenjangJabatanEnum::AHLI_MUDA->value ? 'is-selected' : ''}}">
                        <div class="ui-level-topline">
                            <span class="ui-level-name">JF Ahli Muda</span>
                            <span class="ui-level-badge">Level 2 &amp; 3</span>
                        </div>
                        <p class="ui-level-description">Pengawasan PSU, pengelolaan rusun &amp; koordinasi</p>
                    </article>
                    <article class="ui-level-option {{ $ujian->jenjang_tujuan == \App\Enums\JenjangJabatanEnum::AHLI_MADYA->value ? 'is-selected' : ''}}">
                        <div class="ui-level-topline">
                            <span class="ui-level-name">JF Ahli Madya</span>
                            <span class="ui-level-badge">Level 4</span>
                        </div>
                        <p class="ui-level-description">Evaluasi program, mitigasi bencana &amp; kebijakan</p>
                    </article>
                    <article class="ui-level-option {{ $ujian->jenjang_tujuan == \App\Enums\JenjangJabatanEnum::AHLI_UTAMA->value ? 'is-selected' : ''}}">
                        <div class="ui-level-topline">
                            <span class="ui-level-name">JF Ahli Utama</span>
                            <span class="ui-level-badge">Level 5</span>
                        </div>
                        <p class="ui-level-description">Strategis makro perumahan nasional &amp; perumusan standar</p>
                    </article>
                </div>
            </section>
        </div>
        <!-- END Card Ujikom question level -->
    
        <!-- Card Ujikom Rules -->
        <div class="col-12">
            <section class="ui-card" aria-labelledby="level-title">
                <h2 class="ui-section-heading" id="level-title">
                    <i class="bi bi-shield-check" aria-hidden="true"></i>
                    Petunjuk &amp; Tata Tertib Pelaksanaan Uji Kompetensi
                </h2>
    
                <ul>
                    <li class="ui-level-description">Peserta wajib menjaga integritas dan dilarang membuka tab/aplikasi lain selama ujian berlangsung.</li>
                    <li class="ui-level-description">Sistem dilengkapi dengan Autosave otomatis sehingga jawaban tersimpan secara berkala ke server.</li>
                    <li class="ui-level-description">Jika koneksi internet terputus, jangan panik. Lanjutkan pengerjaan setelah koneksi kembali terhubung; waktu ujian akan tetap berjalan.</li>
                    <li class="ui-level-description">Gunakan tombol Ragu-ragu (Kuning) untuk menandai butir soal yang ingin ditinjau kembali sebelum submit.</li>
                    <li class="ui-level-description">Ujian akan selesai otomatis jika batas waktu hitung mundur (countdown) telah habis.</li>
                </ul>
            </section>
        </div>
        <!-- END Card Ujikom Rules -->
    </div>
    <!-- END Left Section -->

    <!-- Right Section -->
    <div class="col-xl-4 col-lg-4">
        <!-- Test Session Detail Information -->
        <div class="col-12 mb-3">
            <section class="ui-card ui-session-card" aria-labelledby="session-title">
                <div>
                    <h2 class="ui-section-heading" id="session-title">
                        <i class="bi bi-calendar" aria-hidden="true"></i>
                        Informasi Sesi Ujian
                    </h2>
                </div>
                <div class="ui-session-details">
                    <p class="ui-metric-label">Tanggal pelaksanaan</p>
                    <p class="ui-metric-value mb-3">{{ $ujian->tanggal_ujian->format('Y-m-d') }}</p>

                    <p class="ui-metric-label">Waktu sesi</p>
                    <p class="ui-metric-value ui-session-time mb-3">{{ $ujian->waktu_mulai }} - {{ $ujian->waktu_selesai }}</p>

                    <p class="ui-metric-label">Pelaksanaan</p>
                    <p class="ui-metric-value">{{ $ujian->lokasi }}</p>
                </div>
                <div class="ui-session-examiner">
                    <p class="ui-metric-label">Penguji Teknis</p>

                    <div class="ui-examiner">
                        <span class="ui-examiner-avatar" aria-hidden="true">{{ mb_substr($ujian->penguji->nama, 0, 1) }}</span>
                        <strong>{{ $ujian->penguji->nama }}</strong>
                    </div>
                </div>
            </section>
        </div>
        <!-- END Test Session Detail Information -->

        <!-- Broadcast -->
        <div class="col-12 mb-3">
            <section class="ui-card ui-broadcast-card" aria-labelledby="broadcast-title">
                <div class="ui-broadcast-heading">
                    <h2 class="ui-section-heading" id="broadcast-title">
                        <i class="bi bi-bell" aria-hidden="true"></i>
                        Pengumuman Terbaru
                    </h2>
                    <span class="ui-broadcast-count">2 Baru</span>
                </div>

                <div class="ui-broadcast-list">
                    <article class="ui-broadcast-item">
                        <h3>Pedoman Teknis dan Tata Tertib Uji Kompetensi...</h3>
                        <p>Peserta diwajibkan mematuhi tata tertib pelaksanaan uji kompetensi: hadir/login 15 menit sebelum ujian, tidak...</p>
                        <time datetime="2026-08-22">2026-08-22</time>
                    </article>
                    <article class="ui-broadcast-item">
                        <h3>Pemberitahuan Pembagian Sesi Ujian Gelomba...</h3>
                        <p>Peserta yang belum terjadwal pada Gelombang I diharapkan memeriksa notifikasi sistem secara berkala...</p>
                        <time datetime="2026-08-28">2026-08-28</time>
                    </article>
                    <article class="ui-broadcast-item">
                        <h3>Pengumuman Penerbitan Surat Rekomendasi...</h3>
                        <p>Bagi peserta yang telah dinyatakan LULUS pada periode evaluasi Agustus 2026, e-Sertifikat dan Surat...</p>
                        <time datetime="2026-08-30">2026-08-30</time>
                    </article>
                </div>
            </section>
        </div>
        <!-- END Broadcast -->
    </div>
    <!-- END Right Section -->
</div>


@endsection