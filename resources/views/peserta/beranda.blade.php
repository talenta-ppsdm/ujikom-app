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
    <div class="col-12">
        <section class="ui-card" aria-labelledby="exam-status-title">
            <div class="ui-card-heading">
                <div>
                    <p class="ui-eyebrow">Status Partisipasi Uji Kompetensi</p>
                    <h2 class="ui-card-title" id="exam-status-title">{{ ucwords($ujian['tujuan_ujian']) }}</h2>
                </div>
                <span class="ui-status-badge">{{ $ujian['status']}}</span>
            </div>

            <div class="ui-info-panel">
                <i class="bi bi-clock-history" aria-hidden="true"></i>
                <div>
                    <p class="ui-info-title">Sesi Ujian CBT Siap Dimulai</p>
                    <p class="ui-info-text">Soal ujian akan diacak secara otomatis dari bank soal teknis sesuai jenjang <strong>{{ $user->peserta->jenjang ?? 'JF Ahli Pertama (Level 1)' }}</strong>.</p>
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
                    <span class="ui-metric-value ui-metric-value-accent">{{ $user->peserta->jenjang ?? 'JF Ahli Pertama' }}</span>
                </div>
                <div>
                    <span class="ui-metric-label">Cakupan Level</span>
                    <span class="ui-metric-value ui-metric-value-warning">Level 1 (Acak)</span>
                </div>
            </div>

            <button class="ui-card-action" type="button">
                <i class="bi bi-file-earmark-check" aria-hidden="true"></i>
                Masuk Ruang Ujian CBT Sekarang
                <i class="bi bi-arrow-right" aria-hidden="true"></i>
            </button>
        </section>
    </div>

    <div class="col-12">
        <section class="ui-card" aria-labelledby="level-title">
            <h2 class="ui-section-heading" id="level-title">
                <i class="bi bi-mortarboard" aria-hidden="true"></i>
                Struktur Jenjang &amp; Tingkat Soal Uji Kompetensi
            </h2>

            <div class="ui-level-grid">
                <article class="ui-level-option is-selected">
                    <div class="ui-level-topline">
                        <span class="ui-level-name">JF Ahli Pertama</span>
                        <span class="ui-level-badge">Level 1</span>
                    </div>
                    <p class="ui-level-description">Pemahaman dasar perumahan swadaya &amp; regulasi</p>
                    <span class="ui-level-selected-label"><i class="bi bi-check-circle" aria-hidden="true"></i> Jenjang Terpilih Anda</span>
                </article>
                <article class="ui-level-option">
                    <div class="ui-level-topline">
                        <span class="ui-level-name">JF Ahli Muda</span>
                        <span class="ui-level-badge">Level 2 &amp; 3</span>
                    </div>
                    <p class="ui-level-description">Pengawasan PSU, pengelolaan rusun &amp; koordinasi</p>
                </article>
                <article class="ui-level-option">
                    <div class="ui-level-topline">
                        <span class="ui-level-name">JF Ahli Madya</span>
                        <span class="ui-level-badge">Level 4</span>
                    </div>
                    <p class="ui-level-description">Evaluasi program, mitigasi bencana &amp; kebijakan</p>
                </article>
                <article class="ui-level-option">
                    <div class="ui-level-topline">
                        <span class="ui-level-name">JF Ahli Utama</span>
                        <span class="ui-level-badge">Level 5</span>
                    </div>
                    <p class="ui-level-description">Strategis makro perumahan nasional &amp; perumusan standar</p>
                </article>
            </div>
        </section>
    </div>
</div>


@endsection