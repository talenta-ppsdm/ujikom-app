@extends('peserta.mainLayout')

@section('content')
<div class="col-12 exam-history">
	<a class="exam-history-back" href="/beranda" aria-label="Kembali ke beranda">
		<i class="bi bi-arrow-left" aria-hidden="true"></i>
		Beranda
	</a>

	<header class="exam-history-heading">
		<h1>Riwayat Ujian</h1>
		<p>Rekapitulasi seluruh ujian yang pernah Anda ikuti</p>
	</header>

	<!-- Card Ujian Terjadwal -->
	@if($recentUjianTerjadwal)
	<div class="col-12 mb-3">
		@php
		$statusBadgeClass = match(strtolower($recentUjianTerjadwal->status ?? '')) {
			'terjadwal'          => 'badge-warning',
			'sedang berlangsung' => 'badge-info',
			'selesai'            => 'badge-success',
			default              => 'badge-secondary', 
		};
		@endphp
		<div class="ui-card mb-3">
			<div class="ui-card-heading">
				<span class="ui-status-badge {{ $statusBadgeClass }}">{{ ucwords($recentUjianTerjadwal->status) }}</span>
			</div>

			@if($recentUjianTerjadwal->status == 'selesai')
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
					<p class="ui-info-text">Soal ujian akan diacak secara otomatis dari bank soal teknis sesuai jenjang <b>{{ucwords($recentUjianTerjadwal->tujuan_jenjang)}}</b></p>
				</div>
			</div>
			@endif

			<div class="ui-metric-grid">
				<div>
					<span class="ui-metric-label">Gelombang Ujian</span>
					<span class="ui-metric-value">Gelombang {{$recentUjianTerjadwal->jadwalUjian->gelombang}} Tahun {{$recentUjianTerjadwal->jadwalUjian->tahun}}</span>
				</div>
				<div>
					<span class="ui-metric-label">Tanggal</span>
					<span class="ui-metric-value">{{ \Carbon\Carbon::parse($recentUjianTerjadwal->tanggal_ujian)->translatedFormat('d F Y') }}</span>
				</div>
				<div>
					<span class="ui-metric-label">Jumlah Soal</span>
					<span class="ui-metric-value">30 Butir</span>
				</div>
				<div>
					<span class="ui-metric-label">Durasi Ujian</span>
					<span class="ui-metric-value">{{$recentUjianTerjadwal->durasi}} Menit</span>
				</div>
			</div>

			@php
				$status = $recentUjianTerjadwal->status;

				$wordingButton = match(strtolower($recentUjianTerjadwal->status)){
					'terjadwal'         => 'Kerjakan Ujian',
					'sedang berlangsung'=> 'Kerjakan Ujian',
					'selesai'           => 'Lihat Hasil Ujian',
					default             => 'Lihat Selengkapnya'
				};
			
				if ($status === 'selesai') {
					$targetUrl = route('ujian.result', $recentUjianTerjadwal->id); 
				} else {
					$targetUrl = route('ujian.start', $recentUjianTerjadwal->id); 
				}
			@endphp
			
			<a href="{{ $targetUrl }}" class="text-decoration-none">
				<button class="ui-card-action" type="button">
					{{ $wordingButton }}
					<i class="bi bi-arrow-right" aria-hidden="true"></i>
				</button>
			</a>
		</div>
	</div>
	@endif
	<!-- END Card Ujian Terjadwal -->

	<form action="{{ route('riwayat-ujian.index') }}" method="GET" id="filterYearForm">
		<div class="exam-history-toolbar">
			<label class="exam-history-year">
				<i class="bi bi-calendar-event" aria-hidden="true"></i>
				<span class="visually-hidden">Pilih tahun</span>
				<select aria-label="Pilih tahun" name="tahun" onchange="this.form.submit()">
					<option value="semua" {{ !request('tahun') || request('tahun') == 'semua' ? 'selected' : '' }}>
						Semua Tahun
					</option>
					@foreach($availableYears as $year)
						<option value="{{ $year }}" {{ request('tahun') == $year ? 'selected' : '' }}>{{ $year }}</option>
					@endforeach
				</select>
				<i class="bi bi-chevron-down exam-history-year-chevron" aria-hidden="true"></i>
			</label>
		</div>
	</form>

	<!-- Tabel Riwayat Ujian -->
	<div class="ui-card table-card-custom">
		<div class="table-responsive">
		  <table id="dataTable" class="table-custom w-100" data-datatable data-datatable-page-length="10" data-datatable-actions="4">
			<thead>
			  <tr>
				<th>No</th>
				<th>Tanggal Pelaksanaan</th>
				<th>Durasi</th>
				<th>Status</th>
				<th>Nilai</th>
			  </tr>
			</thead>
			<tbody>
			  <?php $no = 1; ?>
			  @foreach($ujian as $dataUjian)
			  	@php
				$statusBadgeClass = match(strtolower($dataUjian->status)){
					'terjadwal'          => 'ui-badge-warning',
					'sedang berlangsung' => 'ui-badge-info',
					'selesai'            => 'ui-badge-success',
					default              => 'ui-badge-secondary',
				};

				if(strtolower($dataUjian->status) === 'selesai'){
					$totalSkor = round($dataUjian->total_skor, 2);
				}else{
					$totalSkor = '-';
				}
				@endphp
			  <tr>
				<td class="table-order-id">{{ $no++ }}</td>
				<td>{{ $dataUjian->tanggal_ujian->translatedFormat('d F Y') }}</td>
				<td>{{ $dataUjian->durasi }} menit</td>
				<td>
					<span class="badge-table {{ $statusBadgeClass }}">{{ $dataUjian->status }}</span>
				</td>
				<td>{{ $totalSkor }}</td>
			  </tr>
			  @endforeach
			</tbody>
		  </table>
		</div>
	</div>
	<!-- END Tabel Riwayat Ujian -->
</div>
@endsection