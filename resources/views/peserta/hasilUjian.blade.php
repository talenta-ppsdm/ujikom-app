@extends('peserta.mainLayout')

@section('content')
@php
	$jumlahSoal = $jawabanUjian->count();
	$jumlahBenar = $jawabanUjian->where('is_benar', true)->count();
	$jumlahSalah = max($jumlahSoal - $jumlahBenar, 0);
	$nilaiAkhir = number_format((float) ($jadwalUjian->total_skor ?? 0), 0);
	$pilihan = ['a', 'b', 'c', 'd', 'e'];
@endphp

<div class="col-12 peserta-result-page">
	<section class="result-score-card" aria-labelledby="result-title">
		<div>
			<p class="result-overline">Nilai Akhir</p>
			<h1 class="result-score-value" id="result-title">
				{{ $nilaiAkhir }} <small>/ 100</small>
			</h1>
			<p class="result-subtitle">Uji Kompetensi Teknis&nbsp; - &nbsp;Perpindahan Jabatan</p>
		</div>
		<div class="result-score-meta">
			<span>Benar</span>
			<strong>{{ $jumlahBenar }}</strong>
		</div>
		<div class="result-score-meta is-wrong">
			<span>Salah</span>
			<strong>{{ $jumlahSalah }}</strong>
		</div>
	</section>

	<div class="result-metrics">
		<div>
			<span class="result-metric-label"><i class="bi bi-calendar3" aria-hidden="true"></i> Tanggal</span>
			<span class="result-metric-value">{{ $jadwalUjian->tanggal_ujian?->format('Y-m-d') ?? '-' }}</span>
		</div>
		<div>
			<span class="result-metric-label"><i class="bi bi-clock" aria-hidden="true"></i> Durasi</span>
			<span class="result-metric-value">{{ $jadwalUjian->durasi }} Menit</span>
		</div>
		<div>
			<span class="result-metric-label"><i class="bi bi-file-earmark-text" aria-hidden="true"></i> Jumlah Soal</span>
			<span class="result-metric-value">{{ $jumlahSoal }} Butir</span>
		</div>
		<div>
			<span class="result-metric-label"><i class="bi bi-mortarboard" aria-hidden="true"></i> Jenjang</span>
			<span class="result-metric-value">JF {{ $jadwalUjian->jenjang_tujuan }}</span>
		</div>
	</div>

	<div class="result-question-list">
		@forelse ($jawabanUjian as $index => $jawaban)
			@php
				$soal = $jawaban->soal;
				$jawabanPeserta = strtolower((string) $jawaban->jawaban_terpilih);
				$kunci = strtolower((string) $soal->kunci);
				$isBenar = (bool) $jawaban->is_benar;
			@endphp
			<article class="result-question-card" aria-labelledby="question-{{ $jawaban->id }}">
				<div class="result-question-header">
					<div class="result-question-title">
						<span class="result-question-number">{{ $index + 1 }}</span>
						<h2 id="question-{{ $jawaban->id }}">Soal {{ $index + 1 }}</h2>
					</div>
					<span class="result-status {{ $isBenar ? 'is-correct' : 'is-wrong' }}">
						<i class="bi {{ $isBenar ? 'bi-check-circle-fill' : 'bi-x-circle-fill' }}" aria-hidden="true"></i>
						{{ $isBenar ? 'Benar' : 'Salah' }}
					</span>
				</div>

				<p class="result-question-text">{{ $soal->soal }}</p>

				<div class="result-options">
					@foreach ($pilihan as $option)
						@php
							$isAnswer = $jawabanPeserta === $option;
							$isKey = $kunci === $option;
						@endphp
						<div class="result-option {{ $isKey ? 'is-key' : '' }} {{ $isAnswer ? 'is-answer' : '' }} {{ $isAnswer && !$isKey ? 'is-answer-wrong' : '' }}">
							<span class="result-option-mark">
								<i class="bi {{ $isKey ? 'bi-check-circle-fill' : ($isAnswer ? 'bi-x-circle-fill' : 'bi-circle-fill') }}" aria-hidden="true"></i>
							</span>
							<span class="result-option-text">{{ $soal->{'jawaban_' . $option} }}</span>
							@if ($isKey)
								<small class="result-option-label">Kunci</small>
							@elseif ($isAnswer)
								<small class="result-option-label">Jawaban Anda</small>
							@endif
						</div>
					@endforeach
				</div>

				<div class="result-explanation">
					<p class="result-explanation-title">Pembahasan</p>
					<div>
						<p>{{ $soal->pembahasan ?: 'Pembahasan belum tersedia.' }}</p>
					</div>
				</div>
			</article>
		@empty
			<div class="result-empty">
				<div>
					<p>Hasil ujian belum tersedia</p>
					<span>Belum ada jawaban yang dapat ditampilkan untuk ujian ini.</span>
				</div>
			</div>
		@endforelse
	</div>
</div>

@endsection