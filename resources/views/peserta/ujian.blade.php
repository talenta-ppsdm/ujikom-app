@extends('peserta.mainLayout')

@section('content')
<div class="col-12 peserta-exam-page">
	<div class="row g-4" 
		id="exam-container" 
		data-jadwal-id="{{$jadwalUjian->id}}">
		
		<div class="col-xl-9 col-lg-8">
			<section class="ui-card ui-exam-question-card" aria-labelledby="question-title">
				<div class="ui-card-heading ui-exam-heading">
					<div class="ui-exam-meta">
						<span class="ui-level-badge" data-meta-level>
							{{ $soalUjian->first()?->level_name ?? 'Level -' }}
						</span>
						<span class="ui-exam-category" data-meta-category>
							{{ $soalUjian->first()?->category_nama ?? 'Kategori -' }}
						</span>
						<span class="ui-exam-points" data-meta-points>
							{{ $soalUjian->first()?->poin ?? 0 }} Poin
						</span>
					</div>
					<div class="ui-exam-timer" data-target="{{ $targetTimestamp }}" aria-live="polite">
						<i class="bi bi-clock" aria-hidden="true"></i>
						<span>00:00:00</span>
					</div>
				</div>

				<div class="ui-exam-progress" aria-label="Progress soal">
					<span></span>
				</div>

				@forelse ($soalUjian as $index => $soal)
				@php
					$saved = $keyJawabanTerpilih->get($soal->id);
					$jawabanPeserta = $keyJawabanTerpilih->where('soal_id', $soal->id)->first();
					$jawabanTerpilih = $jawabanPeserta?->jawaban_terpilih;
				@endphp
				<article class="ui-exam-question {{ $index === 0 ? 'is-current' : 'd-none' }}" 
					data-question="{{ $index }}" 
					data-question-id="{{ $soal->id }}">
			
					<p class="ui-eyebrow">{{ $index + 1 }} / {{ $soalUjian->count() }} Soal</p>
					<h1 class="ui-exam-question-title">{{ $soal->soal }}</h1>
			
					<div class="ui-exam-options">
						@foreach (['a', 'b', 'c', 'd', 'e'] as $option)
							@php
								// Cek apakah opsi ini adalah jawaban yang tersimpan di DB
								$isSelected = strtolower($jawabanTerpilih) === $option;
							@endphp
			
							<button class="ui-level-option ui-exam-option {{ $isSelected ? 'is-selected' : '' }}" 
									type="button" 
									data-answer="{{ $option }}" 
									aria-checked="{{ $isSelected ? 'true' : 'false' }}">
								<span class="ui-exam-option-letter">{{ strtoupper($option) }}</span>
								<span>{{ $soal->{'jawaban_' . $option} }}</span>
								<i class="bi bi-check-circle ui-exam-option-check" aria-hidden="true"></i>
							</button>
						@endforeach
					</div>
				</article>
				@empty
					<div class="ui-info-panel">
						<i class="bi bi-info-circle" aria-hidden="true"></i>
						<div>
							<p class="ui-info-title">Soal belum tersedia</p>
							<p class="ui-info-text">Belum ada soal yang dapat ditampilkan untuk sesi ujian ini.</p>
						</div>
					</div>
				@endforelse

				<div class="ui-exam-footer">
					<button class="ui-exam-secondary" type="button" data-previous disabled>
						<i class="bi bi-chevron-left" aria-hidden="true"></i>
						Sebelumnya
					</button>
					<button class="ui-exam-doubt" type="button" data-doubt>
						<i class="bi bi-exclamation-triangle" aria-hidden="true"></i>
						Ragu-Ragu
					</button>
					<button class="ui-card-action ui-exam-next" type="button" data-next>
						Selanjutnya
						<i class="bi bi-chevron-right" aria-hidden="true"></i>
					</button>
				</div>
			</section>
		</div>

		<div class="col-xl-3 col-lg-4">
			<aside class="ui-card ui-exam-sidebar" aria-labelledby="question-list-title">
				<div class="ui-card-heading">
					<div>
						<p class="ui-eyebrow">Navigasi ujian</p>
						<h2 class="ui-card-title" id="question-list-title">Daftar Nomor Soal</h2>
					</div>
					<strong class="ui-exam-answered-count">0/{{ $soalUjian->count() }}</strong>
				</div>

				<div class="ui-exam-legend" aria-label="Keterangan status soal">
					<span><i class="is-answered"></i> Dijawab</span>
					<span><i class="is-doubt"></i> Ragu</span>
					<span><i class="is-empty"></i> Kosong</span>
				</div>

				<div class="ui-exam-number-grid">
					@foreach ($soalUjian as $index => $soal)
						@php
							$saved = $keyJawabanTerpilih->get($soal->id);

							$hasAnswer = !is_null($saved?->jawaban_terpilih) && $saved?->jawaban_terpilih !== '';
							$isRagu = (bool) $saved?->is_ragu;
						@endphp
						<button class="ui-exam-number 
										{{ $index === 0 ? 'is-current' : '' }} 
										{{ $hasAnswer ? 'is-answered' : '' }} 
										{{ $isRagu ? 'is-doubt' : '' }}" 
								data-number="{{ $index }}"
								data-question-id="{{ $soal->id }}">
							{{ $index + 1 }}
						</button>
					@endforeach
				</div>

				<button class="ui-card-action ui-exam-submit" type="button" data-submit>
					<i class="bi bi-send" aria-hidden="true"></i>
					Selesai &amp; Kumpulkan Ujian
				</button>
			</aside>
		</div>
	</div>
</div>
@endsection

@push('scripts')
<script>
	document.addEventListener('DOMContentLoaded', () => {
		const questions = [...document.querySelectorAll('[data-question]')];
		const numbers = [...document.querySelectorAll('[data-number]')];
		const timer = document.querySelector('.ui-exam-timer');
		let current = 0;

		function saveAnswers(soalId, answer, isRagu = false) {
			const container = document.getElementById('exam-container');
			const jadwalId = container?.dataset.jadwalId;
		
			// Ambil Token CSRF
			const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
		
			if (!csrfToken) {
				console.error("CSRF Token tidak ditemukan! Pastikan <meta name='csrf-token'> sudah terpasang.");
				return;
			}
		
			fetch("/ujian/simpan", {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					'Accept': 'application/json',
					'X-CSRF-TOKEN': csrfToken
				},
				body: JSON.stringify({
					jadwal_ujian_id: Number(jadwalId),
					soal_id: Number(soalId),
					jawaban: answer,
					is_ragu: isRagu
				})
			})
			.then(async res => {
				if (!res.ok) {
					const errData = await res.json();
					throw new Error(errData.message || `HTTP error! Status: ${res.status}`);
				}
				return res.json();
			});
		}

		const renderQuestion = (index) => {
			if (!questions.length) return;
			current = Math.max(0, Math.min(index, questions.length - 1));
			questions.forEach((question, questionIndex) => question.classList.toggle('d-none', questionIndex !== current));
			numbers.forEach((number, numberIndex) => number.classList.toggle('is-current', numberIndex === current));
			
			const activeQuestion = questions[current];
			document.querySelector('[data-meta-level]').textContent = activeQuestion.dataset.level;
			document.querySelector('[data-meta-category]').textContent = activeQuestion.dataset.category;
			document.querySelector('[data-meta-points]').textContent = `${activeQuestion.dataset.points} Poin`;
			document.querySelector('[data-previous]').disabled = current === 0;
			document.querySelector('[data-next]').innerHTML = current === questions.length - 1
				? 'Kembali ke Soal Pertama <i class="bi bi-arrow-repeat" aria-hidden="true"></i>'
				: 'Selanjutnya <i class="bi bi-chevron-right" aria-hidden="true"></i>';
		};

		document.querySelectorAll('.ui-exam-option').forEach((option) => {
			option.addEventListener('click', () => {
				const question = option.closest('[data-question]');
				const soalId = question.dataset.questionId;
				const answer = option.dataset.answer;
				const isRagu = numbers[current]?.classList.contains('is-doubt') ?? false;

				question.querySelectorAll('.ui-exam-option').forEach((item) => {
					item.classList.remove('is-selected');
					item.setAttribute('aria-checked', 'false');
				});
				option.classList.add('is-selected');
				option.setAttribute('aria-checked', 'true');
				
				numbers[current].classList.add('is-answered');
				document.querySelector('.ui-exam-answered-count').textContent = `${document.querySelectorAll('.ui-exam-number.is-answered').length}/${numbers.length}`;

				// Trigger save answer
				saveAnswers(soalId, answer, isRagu);
			});
		});

		document.querySelector('[data-doubt]')?.addEventListener('click', () => {
			if (!numbers[current]) return;
	
			const isDoubt = numbers[current].classList.toggle('is-doubt');
			const activeQuestion = questions[current];
			const soalId = activeQuestion.dataset.questionId;
			
			const selectedOption = activeQuestion.querySelector('.ui-exam-option.is-selected');
			const pilihanJawaban = selectedOption ? selectedOption.dataset.answer : null;
	
			if (pilihanJawaban) {
				saveAnswers(soalId, pilihanJawaban, isDoubt);
			}
		});

		numbers.forEach((number) => number.addEventListener('click', () => renderQuestion(Number(number.dataset.number))));
		document.querySelector('[data-previous]').addEventListener('click', () => renderQuestion(current - 1));
		document.querySelector('[data-next]').addEventListener('click', () => renderQuestion(current === questions.length - 1 ? 0 : current + 1));
		document.querySelector('[data-submit]').addEventListener('click', () => window.confirm('Kumpulkan jawaban ujian sekarang?'));
		document.querySelector('[data-doubt]').addEventListener('click', () => numbers[current]?.classList.toggle('is-doubt'));

        // Handling time counter
        if (!timer) {
            console.error("Elemen .ui-exam-timer tidak ditemukan di DOM!");
            return;
        }
        const targetTimestamp = Number(timer.dataset.target);

		const updateTimer = () => {
            const nowInSeconds = Math.floor(Date.now() / 1000);
            const remaining = Math.max(0, targetTimestamp - nowInSeconds);
    
            const hours = String(Math.floor(remaining / 3600)).padStart(2, '0');
            const minutes = String(Math.floor((remaining % 3600) / 60)).padStart(2, '0');
            const seconds = String(remaining % 60).padStart(2, '0');
    
            const displaySpan = timer.querySelector('span');
            if (displaySpan) {
                displaySpan.textContent = `${hours}:${minutes}:${seconds}`;
            }
        };
		updateTimer();
		setInterval(updateTimer, 1000);

		// Handling save choosed answer
		const csrfToken = document.querySelector('meta[name=csrf-token')?.getAttribute('content');
	});
	
</script>
@endpush