@extends('peserta.mainLayout')

@section('content')
<div class="col-12 exam-history">
	<a class="exam-history-back" href="{{ route('peserta.dashboard-peserta.index') }}" aria-label="Kembali ke beranda">
		<i class="bi bi-arrow-left" aria-hidden="true"></i>
		Beranda
	</a>

	<header class="exam-history-heading">
		<h1>Riwayat Ujian</h1>
		<p>Rekapitulasi ujian CBT dan wawancara yang pernah Anda ikuti</p>
	</header>

	<div class="exam-history-toolbar">
		<div class="exam-history-year dropdown">
			<i class="bi bi-calendar-event" aria-hidden="true"></i>
			<button class="exam-history-year-toggle dropdown-toggle" type="button" id="examHistoryYearFilter" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Pilih tahun riwayat: Semua Tahun">
				<span class="exam-history-year-label">Semua Tahun</span>
				<i class="bi bi-chevron-down exam-history-year-chevron" aria-hidden="true"></i>
			</button>
			<div class="dropdown-menu dropdown-menu-custom exam-history-year-menu" role="menu" aria-labelledby="examHistoryYearFilter">
				<button class="dropdown-item exam-history-year-option active" type="button" role="menuitemradio" data-history-year="semua" aria-checked="true">Semua Tahun</button>
				@foreach($availableYears as $year)
				<button class="dropdown-item exam-history-year-option" type="button" role="menuitemradio" data-history-year="{{$year}}" aria-checked="false">{{$year}}</button>
				@endforeach
			</div>
		</div>
	</div>

	<div class="exam-history-switcher">
		<ul class="nav nav-pills exam-history-tabs" id="examHistoryTabs" role="tablist" aria-label="Pilih jenis riwayat ujian">
			<li class="nav-item" role="presentation">
				<button class="nav-link active" id="cbt-history-tab" data-bs-toggle="pill" data-bs-target="#cbt-history" type="button" role="tab" aria-controls="cbt-history" aria-selected="true">
					<i class="bi bi-pc-display-horizontal" aria-hidden="true"></i>
					Ujian CBT
					<span class="exam-history-tab-count" data-history-count="cbt">{{ count($ujianCbt) }}</span>
				</button>
			</li>
			<li class="nav-item" role="presentation">
				<button class="nav-link" id="interview-history-tab" data-bs-toggle="pill" data-bs-target="#interview-history" type="button" role="tab" aria-controls="interview-history" aria-selected="false">
					<i class="bi bi-people" aria-hidden="true"></i>
					Wawancara
					<span class="exam-history-tab-count" data-history-count="wawancara">{{ count($interviewSchedule) }}</span>
				</button>
			</li>
		</ul>

		<div class="tab-content">
			<div class="tab-pane fade show active" id="cbt-history" role="tabpanel" aria-labelledby="cbt-history-tab" tabindex="0">
				<div class="ui-card table-card-custom">
					<div class="table-responsive">
						<table class="table-custom w-100" data-history-table="cbt">
							<thead>
								<tr>
									<th>No</th>
									<th>Tanggal Pelaksanaan</th>
									<th>Gelombang</th>
									<th>Durasi</th>
									<th>Status</th>
									<th>Nilai</th>
								</tr>
							</thead>
							<tbody>
								@foreach($ujianCbt as $index => $dataCbt)
								@php
								$statusBadgeClass = match(strtolower($dataCbt->status)){
									'terjadwal'          => 'ui-badge-warning',
									'sedang berlangsung' => 'ui-badge-info',
									'selesai'            => 'ui-badge-success',
									default              => 'ui-badge-secondary',
								};

								if(strtolower($dataCbt->status) === 'selesai'){
									$totalSkor = round($dataCbt->total_skor, 2);
								}else{
									$totalSkor = '-';
								}
								@endphp

								<tr data-year="{{ substr($dataCbt->tanggal_ujian, 0, 4) }}">
									<td class="table-order-id" data-row-number>{{ $index + 1 }}</td>
									<td>{{ \Carbon\Carbon::parse($dataCbt->tanggal_ujian)->translatedFormat('d F Y') }}</td>
									<td>Gelombang {{ $dataCbt->jadwalUjian->gelombang }} Tahun {{ $dataCbt->jadwalUjian->tahun }}</td>
									<td>{{ $dataCbt->durasi }} menit</td>
									<td>
										<span class="badge-table {{ $statusBadgeClass }}">{{ ucwords($dataCbt->status) }}</span>
									</td>
									<td>
										@if(strtolower($dataCbt->status) === 'selesai')
										<div class="d-flex flex-column align-items-center gap-2">
											<span class="badge-table info">{{ $totalSkor }} / 100</span>
											<a class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1" href="{{ route('peserta.ujian.result', $dataCbt->id) }}">
												<i class="bi bi-eye" aria-hidden="true"></i>
												Lihat Hasil
											</a>
										</div>
										@else
										{{ $totalSkor }}
										@endif
									</td>
								</tr>
								@endforeach
								<tr class="exam-history-empty-row" hidden>
									<td colspan="7">Tidak ada riwayat CBT pada tahun ini.</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>

			<div class="tab-pane fade" id="interview-history" role="tabpanel" aria-labelledby="interview-history-tab" tabindex="0">
				<div class="ui-card table-card-custom">
					<div class="table-responsive">
						<table class="table-custom w-100" data-history-table="wawancara">
							<thead>
								<tr>
									<th>No</th>
									<th>Tanggal</th>
									<th>Gelombang</th>
									<th>Waktu</th>
									<th>Penguji</th>
									<th>Metode</th>
									<th>Status</th>
								</tr>
							</thead>
							<tbody>
								@foreach($interviewSchedule as $index => $dataWawancara)
								@php
									$statusBadgeClass = match(strtolower($dataWawancara->status)){
										'terjadwal'          => 'ui-badge-warning',
										'menunggu hasil' 	 => 'ui-badge-info',
										'selesai'            => 'ui-badge-success',
										default              => 'ui-badge-secondary',
									};
								@endphp
								<tr data-year="{{ substr($dataWawancara->tanggal, 0, 4) }}">
									<td class="table-order-id" data-row-number>{{ $index + 1 }}</td>
									<td>{{ \Carbon\Carbon::parse($dataWawancara->tanggal)->translatedFormat('d F Y') }}</td>
									<td>Gelombang {{ $dataWawancara->jadwalUjian->gelombang }} Tahun {{ $dataWawancara->jadwalUjian->tahun }}</td>
									<td>{{ $dataWawancara->jam_mulai }} - {{ $dataWawancara->jam_selesai }}</td>
									<td>{{ ucwords($dataWawancara->penguji->nama) }}</td>
									<td>{{ ucwords($dataWawancara->metode) }}</td>
									<td>
										<span class="badge-table {{ $statusBadgeClass }}">{{ ucwords($dataWawancara->status) }}</span>
									</td>
								</tr>
								@endforeach
								<tr class="exam-history-empty-row" hidden>
									<td colspan="7">Tidak ada riwayat wawancara pada tahun ini.</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@push('scripts')
<script>
	(function () {
		const yearFilter = document.getElementById('examHistoryYearFilter');
		const yearOptions = document.querySelectorAll('[data-history-year]');

		if (!yearFilter || yearOptions.length === 0) {
			return;
		}

		yearOptions.forEach(function (option) {
			option.addEventListener('click', function () {
				const selectedYear = option.dataset.historyYear;
				const selectedYearLabel = option.textContent.trim();
				yearFilter.querySelector('.exam-history-year-label').textContent = selectedYearLabel;
				yearFilter.setAttribute('aria-label', 'Pilih tahun riwayat: ' + selectedYearLabel);

				yearOptions.forEach(function (yearOption) {
					const isSelected = yearOption === option;
					yearOption.classList.toggle('active', isSelected);
					yearOption.setAttribute('aria-checked', String(isSelected));
				});

				document.querySelectorAll('[data-history-table]').forEach(function (table) {
					const rows = table.querySelectorAll('tbody tr[data-year]');
					let visibleCount = 0;

					rows.forEach(function (row) {
						const isVisible = selectedYear === 'semua' || row.dataset.year === selectedYear;
						row.hidden = !isVisible;

						if (isVisible) {
							visibleCount += 1;
							row.querySelector('[data-row-number]').textContent = visibleCount;
						}
					});

					table.querySelector('.exam-history-empty-row').hidden = visibleCount > 0;
					document.querySelector('[data-history-count="' + table.dataset.historyTable + '"]').textContent = visibleCount;
				});
			});
		});
	})();
</script>
@endpush
