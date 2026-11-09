@extends('layouts.mainLayout')

@section('title', 'Dashboard Admin | Sistem Uji Kompetensi')

@section('hero')
<div class="page-header">
    <div>
        <h1 class="page-title">Dashboard Admin</h1>
        <p class="page-subtitle">Ringkasan pengelolaan Uji Kompetensi JF Penata Kelola Perumahan</p>
    </div>
</div>
@endsection

@section('content')
<div class="col-12">
    <div class="row g-4 dashboard-stats">
        @foreach ($stats as $stat)
            <div class="col-12 col-md-4 col-xl-2">
                <article class="card dashboard-stat-card">
                    <div class="dashboard-stat-content">
                        <div>
                            <h2 class="dashboard-stat-value">{{ number_format($stat['value'], 0, ',', '.') }}</h2>
                            <p class="dashboard-stat-label">{{ $stat['label'] }}</p>
                        </div>
                        <span class="dashboard-stat-icon" aria-hidden="true">
                            <i class="bi {{ $stat['icon'] }}"></i>
                        </span>
                    </div>
                    <a class="dashboard-stat-link" href="{{ $stat['link'] }}">
                        <span>{{ $stat['link_label'] }}</span>
                        <span class="dashboard-stat-arrow" aria-hidden="true">
                            <i class="bi bi-arrow-right"></i>
                        </span>
                    </a>
                </article>
            </div>
        @endforeach
    </div>
</div>

<!-- Grafik Statistik Pendaftar Uji Kompetensi -->
<div class="col-8">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Statistik Pendaftar Uji Kompetensi</h3>
        </div>
        <div class="card-body">
            <div id="chart-hasil-ujian"></div>
        </div>
    </div>
</div>
<!-- END Grafik Statistik Pendaftar Uji Kompetensi -->

<!-- Grafik Status verifikasi Berkas -->
<div class="col-4">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Statistik Verifikasi Berkas</h3>
        </div>
        <div class="card-body">
            <div id="chart-status-verifikasi"></div>
        </div>
    </div>
</div>
<!-- END  Grafik Status verifikasi Berkas -->

<!-- Latest registrant -->
<div class="col-12 col-lg-8 dashboard-matched-card-column">
    <div class="card pendaftar-terbaru">
        <div class="dashboard-card-header">
            <div>
                <h3 class="dashboard-card-title">Pendaftar Terbaru</h3>
                <p class="dashboard-card-caption">Data pendaftaran terakhir</p>
            </div>
            <a class="dashboard-card-link" href="{{ route('admin.verifikasi-pemberkasan.index') }}">Lihat semua</a>
        </div>

        <div class="pendaftar-terbaru-list">
            @forelse ($latestRegistrants as $registrant)
                <div class="pendaftar-terbaru-item">
                    <div class="pendaftar-terbaru-details">
                        <h4>{{ $registrant->peserta?->nama ?? 'Nama tidak tersedia' }}</h4>
                        <p>{{ $registrant->tujuan_ujian ? ucwords($registrant->tujuan_ujian) : 'Tujuan ujian belum tersedia' }}</p>
                    </div>
                    <span class="pendaftar-terbaru-status">
                        <i class="bi bi-clock" aria-hidden="true"></i>
                        {{ ucwords($registrant->status) }}
                    </span>
                </div>
            @empty
                <p class="pendaftar-terbaru-empty">Belum ada pendaftaran terbaru.</p>
            @endforelse
        </div>
    </div>
</div>
<!-- END latest registrant -->

<!-- Latest CBT Schedule -->
<div class="col-12 col-lg-4 dashboard-matched-card-column">
    <div class="card">
       <div class="dashboard-card-header">
            <div>
                <h3 class="dashboard-card-title">Jadwal Ujian CBT Terdekat</h3>
                <p class="dashboard-card-caption">Data jadwal terakhir</p>
            </div>
            <a class="dashboard-card-link" href="{{ route('admin.jadwal-cbt.index') }}">Lihat semua</a>
        </div>
        <div class="card-body">
            <div id="cbt-schedule" class="cbt-schedule-list">
                @forelse($latestCbtSchedules as $cbtSchedule)
                    <article class="cbt-schedule-item">
                        <h4 title="{{ $cbtSchedule->peserta->nama ?? 'Nama tidak tersedia' }}">{{ $cbtSchedule->peserta->nama ?? 'Nama tidak tersedia' }}</h4>
                        <p>{{ \Carbon\Carbon::parse($cbtSchedule->tanggal_ujian)->format('d M Y') }} <span aria-hidden="true">•</span> {{ \Carbon\Carbon::parse($cbtSchedule->waktu_mulai)->format('H:i') }}-{{ \Carbon\Carbon::parse($cbtSchedule->waktu_selesai)->format('H:i') }}</p>
                        
                        @php
                        $statusBadgeClass = match(strtolower($cbtSchedule->status)){
                          'terjadwal'           => 'ui-badge-warning',
                          'sedang berlangsung'  => 'ui-badge-info',
                          'menunggu hasil'      => 'ui-badge-primary',
                          'selesai'             => 'ui-badge-success',
                            default             => 'ui-badge-info',
                        } 
                        @endphp
                        <span class="badge-table {{ $statusBadgeClass }}">
                            {{ ucfirst($cbtSchedule->status) }}
                        </span>
                    </article>
                @empty
                    <p class="pendaftar-terbaru-empty">Belum ada jadwal CBT terbaru.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
<!-- END Latest CBT Schedule -->
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<script>
    const pendaftarUjianLebel = {!! json_encode($labelUjian) !!};
    const pendaftarUjianData = {!! json_encode($jumlahPendaftar) !!};
    
    const verifikasiRaw = {!! json_encode($jumlahVerifikasiBerkas) !!};
    const verifikasiValues = Object.values(verifikasiRaw); 
    const verifikasiLabels = Object.keys(verifikasiRaw);   

    document.addEventListener("DOMContentLoaded", function () {
        
        // Grafik Statistik Pendaftar Uji Kompetensi
        var hasilUjianChart = {
            series: [{
                name: 'Jumlah Peserta',
                data: pendaftarUjianData
            }],
            chart: {
                type: 'line', 
                height: 350,
                toolbar: { show: false }
            },
            colors: ['#0d6efd'],
            dataLabels: { enabled: false },
            xaxis: {
                categories: pendaftarUjianLebel,
            },
            yaxis: {
                title: { text: 'Total Pendaftar' }
            }
        };
        var chartHasilUjian = new ApexCharts(document.querySelector("#chart-hasil-ujian"), hasilUjianChart);
        chartHasilUjian.render();

        // Grafik Status Verifikasi Berkas 
        var verifikasiBerkasChart = {
           series: verifikasiValues, 
           labels: verifikasiLabels, 
           chart: {
               type: 'pie', 
               height: 350,
               toolbar: { show: false }
           },
           colors: ['#28a745', '#dc3545', '#ffc107'], 
           
           legend: {
               position: 'bottom', 
               horizontalAlign: 'center',
               offsetY: 0,
           },
       
           responsive: [{
               breakpoint: 480,
               options: {
                   chart: { width: 200 },
                   legend: { position: 'bottom' }
               }
           }]
       };
        var chartVerifikasiBerkas = new ApexCharts(document.querySelector("#chart-status-verifikasi"), verifikasiBerkasChart);
        chartVerifikasiBerkas.render();

    });
</script>
@endsection