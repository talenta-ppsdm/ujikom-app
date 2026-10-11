@extends('peserta.mainLayout')

@section('content')
<div class="col-12 registration-page">
	<a class="exam-history-back" href="/beranda" aria-label="Kembali ke beranda">
		<i class="bi bi-arrow-left" aria-hidden="true"></i>
		Beranda
	</a>

	<header class="exam-history-heading">
		<h1>Pendaftaran Uji Kompetensi</h1>
		<p>Lengkapi data dan berkas pendukung untuk mengikuti uji kompetensi</p>
	</header>

	@if ($isProfileIncomplete)
		<section class="ui-card alert-custom-warning d-flex flex-column align-items-center text-center" aria-labelledby="profile-required-title">
			<span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-warning-subtle text-warning p-4 mb-3" aria-hidden="true">
				<i class="bi bi-exclamation-triangle fs-3"></i>
			</span>
			<h2 class="h4 fw-bold text-dark mb-3" id="profile-required-title">Lengkapi Profil Terlebih Dahulu</h2>
			<p class="text-secondary mb-3">
				Sebelum mengisi formulir pendaftaran berkas, Anda wajib melengkapi data profil.
			</p>
			<a class="btn-custom btn-custom-primary" href="{{ route('peserta.profil') }}">
				<i class="bi bi-person-gear me-2" aria-hidden="true"></i>
				Lengkapi Profil
			</a>
		</section>
	@else
	<section class="registration-hero" aria-labelledby="registration-title">
		<div class="registration-status">
			<i class="bi bi-check-circle" aria-hidden="true"></i>
			Pendaftaran Dibuka
		</div>
		<h1 id="registration-title">Uji Kompetensi JF Penata Kelola Perumahan</h1>
		<p class="registration-batch">Gelombang I 2026</p>

		<dl class="registration-meta">
			<div class="registration-meta-item">
				<dt><i class="bi bi-calendar-event" aria-hidden="true"></i> Periode</dt>
				<dd>1 Okt 2026 - 31 Okt 2026</dd>
			</div>
			<div class="registration-meta-item">
				<dt><i class="bi bi-people" aria-hidden="true"></i> Metode</dt>
				<dd>Tatap Muka</dd>
			</div>
			<div class="registration-meta-item">
				<dt><i class="bi bi-person-lines-fill" aria-hidden="true"></i> Kuota</dt>
				<dd>40 peserta</dd>
			</div>
		</dl>
	</section>

	<!-- Form Pemberkasan Awal -->
	<section class="ui-card registration-form-card" aria-labelledby="registration-form-title">
		<header class="registration-form-heading">
			<span class="registration-form-icon" aria-hidden="true">
				<i class="bi bi-file-earmark-text"></i>
			</span>
			<div>
				<h2 id="registration-form-title">Formulir Pendaftaran Berkas</h2>
				<p>Lengkapi data dan unggah berkas pendukung.</p>
			</div>
		</header>

		<div class="alert-custom alert-custom-info registration-notice" role="note">
			<i class="bi bi-shield-check alert-custom-icon" aria-hidden="true"></i>
			<div class="alert-custom-content">Pastikan data sudah benar. Setelah dikirim, data tidak dapat diubah dan akan masuk antrian verifikasi admin.</div>
		</div>

		@if (session('success'))
			<div class="alert-custom alert-custom-success mb-4" role="alert">
				<i class="bi bi-check-circle-fill alert-custom-icon" aria-hidden="true"></i>
				<div class="alert-custom-content">{{ session('success') }}</div>
			</div>
		@endif

		@if ($errors->any())
			<div class="alert-custom alert-custom-danger" role="alert">
				<i class="bi bi-exclamation-triangle-fill alert-custom-icon" aria-hidden="true"></i>
				<div class="alert-custom-content">
					<strong>Terjadi kesalahan saat mengunggah berkas:</strong>
					<ul class="mb-0 ps-3">
						@foreach ($errors->all() as $error)
							<li>{{ $error }}</li>
						@endforeach
					</ul>
				</div>
			</div>
		@endif

		<form class="registration-form" action="{{ route('peserta.pemberkasan.store') }}" method="POST" enctype="multipart/form-data">
			@csrf
			<div class="row g-3">
				<div class="col-12 registration-file-field">
					<label class="form-label" for="file_sk_golongan">SK Golongaan <span>*</span></label>
					<p class="registration-file-help">Unggah berkas (PDF/JPG/PNG, maks. 5 MB).</p>
					<input class="registration-file-input" 
						id="file_sk_golongan" 
						type="file"  
						aria-label="Pilih berkas pendaftaran"
						name="sk_golongan">
					<label class="registration-upload" for="file_sk_golongan">
						<i class="bi bi-upload" aria-hidden="true"></i>
						<span class="file-name-text">Klik untuk pilih berkas</span>
					</label>
				</div>
				<div class="col-12 registration-file-field">
					<label class="form-label" for="file_sk_jabatan">SK Jabatan <span>*</span></label>
					<p class="registration-file-help">Unggah berkas (PDF/JPG/PNG, maks. 5 MB).</p>
					<input class="registration-file-input" 
						id="file_sk_jabatan" 
						type="file"  
						aria-label="Pilih berkas pendaftaran"
						name="sk_jabatan">
					<label class="registration-upload" for="file_sk_jabatan">
						<i class="bi bi-upload" aria-hidden="true"></i>
						<span class="file-name-text">Klik untuk pilih berkas</span>
					</label>
				</div>
				<div class="col-12">
					<button class="btn-custom btn-custom-primary btn-custom-sm registration-submit" type="submit">
						<i class="bi bi-upload" aria-hidden="true"></i>
						Simpan
					</button>
				</div>
			</div>
		</form>
	</section>
	<!-- END Form Pemberkasan Awal -->
	@endif
</div>
@endsection

@push('scripts')
<script>
	document.addEventListener('DOMContentLoaded', function(){
		const fileInputs = document.querySelectorAll('.registration-file-input');

		fileInputs.forEach(fileInput => {
			fileInput.addEventListener('change', function(){
				const container = this.closest('.registration-file-field');
				const fileNameText	= container.querySelector('.file-name-text');
				const icon			= container.querySelector('.registration-upload i');
				
				if (this.files && this.files.length > 0) {
					const fileName				= this.files[0].name;
					fileNameText.textContent	= fileName;

					if(icon){
						icon.className = 'bi bi-file-earmark-check-fill text-success';
					}
				}else{
					fileNameText.textContent = 'Klik untuk pilih berkas';
					if (icon) {
						icon.className = 'bi bi-upload';
					}
				}
			});
		});
	});
</script>
@endpush