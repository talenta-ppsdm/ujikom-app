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
                    Jabatan: {{ ucfirst($user->peserta->jabatan) }}
                </span>
            </div>

            <h1 id="welcome-title">Selamat Datang, {{ $user->peserta->nama}}</h1>
            <p class="peserta-welcome-identity">
                NIP: {{ $user->peserta->nip }}
            </p>
            <p class="peserta-welcome-unit">
                {{ ucwords($user->peserta->unit) }} | {{ ucwords($user->peserta->instansi) }}
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
                        <!-- <h2 class="ui-card-title" id="exam-status-title">Status Partisipasi Uji Kompetensi</h2> -->
                    </div>
                </div>

                <!-- Card List Jadwal Ujian -->
                @foreach($ujian as $dataUjian)
                    @php
                    $statusBadgeClass = match(strtolower($dataUjian->status)){
                        'terjadwal'          => 'ui-badge-warning',
                        'sedang berlangsung' => 'ui-badge-info',
                        'selesai'            => 'ui-badge-success',
                        default              => 'ui-badge-secondary',
                    };

                    $levelUjian = match(strtolower($dataUjian->jenjang_tujuan)){
                        'ahli pertama'  => 'level 1', 
                        'ahli muda'  => 'level 2 & 3', 
                        'ahli madya'  => 'level 4', 
                        'ahli utama'  => 'level 5', 
                    };
                    @endphp
                    <div class="ui-card mb-3">
                        <div class="ui-card-heading">
                            <h2 class="ui-card-title" id="exam-status-title">{{ ucwords($dataUjian->tujuan_ujian->value) }}</h2>
                            <span class="ui-status-badge {{ $statusBadgeClass }}">{{ ucwords($dataUjian->status) }}</span>
                        </div>

                        @if($dataUjian->status == 'selesai')
                        <div class="ui-info-panel ui-info-panel-success">
                            <i class="bi bi-check-circle" aria-hidden="true"></i>
                            <div>
                                <p class="ui-info-title">Ujian Telah Diselesaikan</p>
                                <p class="ui-info-text">Hasil ujian telah tersedia. Tinjau ringkasan dan pembahasan jawaban Anda.</p>
                            </div>
                            <div class="ui-info-panel-score">
                                <span class="ui-info-panel-score-label">Nilai Akhir</span>
                                <span class="ui-info-panel-score-value">{{ number_format($dataUjian->total_skor ?? 0, 0) }}<small>/100</small></span>
                            </div>
                        </div>
                        @else
                        <div class="ui-info-panel">
                            <i class="bi bi-clock-history" aria-hidden="true"></i>
                            <div>
                                <p class="ui-info-title">Sesi Ujian CBT Siap Dimulai</p>
                                <p class="ui-info-text">Soal ujian akan diacak secara otomatis dari bank soal teknis sesuai jenjang Ahli Muda</p>
                            </div>
                        </div>
                        @endif
        
                        <div class="ui-metric-grid">
                            <div>
                                <span class="ui-metric-label">Jumlah Soal</span>
                                <span class="ui-metric-value">30 Butir</span>
                            </div>
                            <div>
                                <span class="ui-metric-label">Durasi Ujian</span>
                                <span class="ui-metric-value">{{$dataUjian->durasi}} Menit</span>
                            </div>
                            <div>
                                <span class="ui-metric-label">Level</span>
                                <span class="ui-metric-value">{{ucfirst($levelUjian)}}</span>
                            </div>
                            <div>
                                <span class="ui-metric-label">Jenjang Dituju</span>
                                <span class="ui-metric-value ui-metric-value-accent">JF {{$dataUjian->jenjang_tujuan}}</span>
                            </div>
                        </div>

                        @php
                            $status = $dataUjian->status;

                            $wordingButton = match(strtolower($dataUjian->status)){
                                'terjadwal'         => 'Kerjakan Ujian',
                                'sedang berlangsung'=> 'Kerjakan Ujian',
                                'selesai'           => 'Lihat Hasil Ujian',
                                default             => 'Lihat Selengkapnya'
                            };
                        
                            if ($status === 'selesai') {
                                $targetUrl = route('ujian.result', $dataUjian->id); 
                            } else {
                                $targetUrl = route('ujian.start', $dataUjian->id); 
                            }
                        @endphp
                        
                        <a href="{{ $targetUrl }}" class="text-decoration-none">
                            <button class="ui-card-action" type="button">
                                {{ $wordingButton }}
                                <i class="bi bi-arrow-right" aria-hidden="true"></i>
                            </button>
                        </a>
                    </div>
                @endforeach
                <!-- END Card List Ujian -->

                <div class="d-flex justify-content-center mt-3">
                    <button type="button" class="ui-card-action ui-card-action-ghost">
                        Selengkapnya
                        <i class="bi bi-arrow-right" aria-hidden="true"></i>
                    </button>
                </div>
            </section>
        </div>
    
        <!-- Card Ujikom question level -->
         @if($ujianTerjadwal !== null)
        <div class="col-12 mb-3">
            <section class="ui-card" aria-labelledby="level-title">
                <h2 class="ui-section-heading" id="level-title">
                    <i class="bi bi-mortarboard" aria-hidden="true"></i>
                    Struktur Jenjang &amp; Tingkat Soal Uji Kompetensi
                </h2>
    
                <div class="ui-level-grid">
                    <article @class(["ui-level-option", 
                        "is-selected" => strtolower($ujianTerjadwal->jenjang_tujuan) === "ahli pertama"])
                    >
                        <div class="ui-level-topline">
                            <span class="ui-level-name">JF Ahli Pertama</span>
                            <span class="ui-level-badge">Level 1</span>
                        </div>
                        <p class="ui-level-description">Pemahaman dasar perumahan swadaya &amp; regulasi</p>
                    </article>
                    <article @class(["ui-level-option", 
                        "is-selected" => strtolower($ujianTerjadwal->jenjang_tujuan) === "ahli muda"])
                    >
                        <div class="ui-level-topline">
                            <span class="ui-level-name">JF Ahli Muda</span>
                            <span class="ui-level-badge">Level 2 &amp; 3</span>
                        </div>
                        <p class="ui-level-description">Pengawasan PSU, pengelolaan rusun &amp; koordinasi</p>
                    </article>
                    <article @class(["ui-level-option", 
                        "is-selected" => strtolower($ujianTerjadwal->jenjang_tujuan) === "ahli madya"])
                    >
                        <div class="ui-level-topline">
                            <span class="ui-level-name">JF Ahli Madya</span>
                            <span class="ui-level-badge">Level 4</span>
                        </div>
                        <p class="ui-level-description">Evaluasi program, mitigasi bencana &amp; kebijakan</p>
                    </article>
                    <article @class(["ui-level-option", 
                        "is-selected" => strtolower($ujianTerjadwal->jenjang_tujuan) === "ahli utama"])
                    >
                        <div class="ui-level-topline">
                            <span class="ui-level-name">JF Ahli Utama</span>
                            <span class="ui-level-badge">Level 5</span>
                        </div>
                        <p class="ui-level-description">Strategis makro perumahan nasional &amp; perumusan standar</p>
                    </article>
                </div>
            </section>
        </div>
        @endif
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
            <section class="ui-card ui-session-card ui-summary-card" aria-labelledby="session-title">
                <div class="ui-summary-header">
                    <div class="ui-summary-title-wrap">
                        <span class="ui-summary-icon" aria-hidden="true">
                            <i class="bi bi-card-checklist"></i>
                        </span>
                        <h2 class="ui-summary-heading" id="session-title">Rekapitulasi Ujian</h2>
                    </div>
                    <span class="ui-summary-total">7 ujian</span>
                </div>

                <div class="ui-summary-list">
                    <div class="ui-summary-row ui-summary-row-success">
                        <div class="ui-summary-meta">
                            <span class="ui-summary-badge ui-badge-success">
                                <i class="bi bi-check-lg" aria-hidden="true"></i>
                            </span>
                            <span class="ui-summary-label">Ujian Lulus</span>
                        </div>
                        <span class="ui-summary-value ui-summary-value-success">4</span>
                    </div>

                    <div class="ui-summary-row ui-summary-row-danger">
                        <div class="ui-summary-meta">
                            <span class="ui-summary-badge ui-badge-danger">
                                <i class="bi bi-x-lg" aria-hidden="true"></i>
                            </span>
                            <span class="ui-summary-label">Ujian Tidak Lulus</span>
                        </div>
                        <span class="ui-summary-value ui-summary-value-danger">1</span>
                    </div>

                    <div class="ui-summary-row ui-summary-row-warning">
                        <div class="ui-summary-meta">
                            <span class="ui-summary-badge ui-badge-warning">
                                <i class="bi bi-calendar-check" aria-hidden="true"></i>
                            </span>
                            <span class="ui-summary-label">Ujian Terjadwal</span>
                        </div>
                        <span class="ui-summary-value ui-summary-value-warning">2</span>
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