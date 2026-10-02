@extends('peserta.mainLayout')

@section('content')
<div class="col-12 exam-history">
	<a class="exam-history-back" href="#">
		<i class="bi bi-arrow-left" aria-hidden="true"></i>
		Kembali
	</a>

	<header class="exam-history-heading">
		<h1>Riwayat Ujian</h1>
		<p>Rekapitulasi seluruh ujian yang pernah Anda ikuti</p>
	</header>

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
	<div class="table-card-custom">
		<div class="table-responsive">
		  <table id="bankSoalTable" class="table-custom w-100" data-datatable data-datatable-page-length="10" data-datatable-actions="4">
			<thead>
			  <tr>
				<th>No</th>
				<th>Tanggal Pelaksanaan</th>
				<th>Tujuan Ujian</th>
				<th>Jenjang Tujuan</th>
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
				<td>{{ ucwords($dataUjian->tujuan_ujian->value) }}</td>
				<td>{{ ucwords($dataUjian->jenjang_tujuan) }}</td>
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