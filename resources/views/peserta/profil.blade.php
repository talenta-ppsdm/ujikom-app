@extends('peserta.mainLayout')

@section('content')
@php
	$user = Auth::user();
	$peserta = $user->peserta;
	$displayName = $peserta?->nama ?: $user->name;
@endphp

<main class="col-12 participant-profile">
	<a class="exam-history-back participant-profile-back" href="{{ route('dashboard-peserta.index') }}">
		<i class="bi bi-arrow-left" aria-hidden="true"></i>
		Beranda
	</a>

	<header class="participant-profile-heading">
		<h1>Profil Peserta</h1>
		<p>Data diri peserta uji kompetensi</p>
	</header>

	<section class="participant-profile-hero" aria-label="Identitas peserta">
		<div class="participant-profile-avatar-wrap">
			<div class="participant-profile-avatar" aria-hidden="true">{{ strtoupper(substr($displayName, 0, 2)) }}</div>
			<label class="participant-profile-upload" for="profile-photo" aria-label="Upload foto profil">
				<i class="bi bi-camera" aria-hidden="true"></i>
			</label>
			<input class="participant-profile-file" id="profile-photo" type="file" accept="image/*" aria-label="Pilih foto profil">
		</div>

		<div class="participant-profile-identity">
			<h2>{{ $displayName }}</h2>
			<p>{{ $peserta?->nip ?: $user->nip }} <span aria-hidden="true">•</span> {{ $peserta?->jabatan ?: 'Jabatan belum diisi' }}</p>
			<p>{{ $peserta?->unit ?: 'Unit belum diisi' }}</p>
		</div>
	</section>

	<section class="participant-profile-card" aria-labelledby="participant-profile-details-title">
		<div class="participant-profile-card-heading">
			<h2 id="participant-profile-details-title">Data Diri</h2>
			<button class="participant-profile-edit" type="button">
				<i class="bi bi-pencil" aria-hidden="true"></i>
				Edit Data
			</button>
		</div>

		<dl class="participant-profile-details">
			<div class="participant-profile-detail">
				<span class="participant-profile-detail-icon"><i class="bi bi-person" aria-hidden="true"></i></span>
				<div><dt>Nama</dt><dd>{{ $displayName ?: 'Belum diisi' }}</dd></div>
			</div>
			<div class="participant-profile-detail">
				<span class="participant-profile-detail-icon"><i class="bi bi-fingerprint" aria-hidden="true"></i></span>
				<div><dt>NIP</dt><dd>{{ $peserta?->nip ?: $user->nip ?: 'Belum diisi' }}</dd></div>
			</div>
			<div class="participant-profile-detail">
				<span class="participant-profile-detail-icon"><i class="bi bi-award" aria-hidden="true"></i></span>
				<div><dt>Golongan</dt><dd>{{ $peserta?->golongan ?: 'Belum diisi' }}</dd></div>
			</div>
			<div class="participant-profile-detail">
				<span class="participant-profile-detail-icon"><i class="bi bi-briefcase" aria-hidden="true"></i></span>
				<div><dt>Jabatan</dt><dd>{{ $peserta?->jabatan ?: 'Belum diisi' }}</dd></div>
			</div>
			<div class="participant-profile-detail">
				<span class="participant-profile-detail-icon"><i class="bi bi-buildings" aria-hidden="true"></i></span>
				<div><dt>Unit</dt><dd>{{ $peserta?->unit ?: 'Belum diisi' }}</dd></div>
			</div>
			<div class="participant-profile-detail">
				<span class="participant-profile-detail-icon"><i class="bi bi-bank" aria-hidden="true"></i></span>
				<div><dt>Instansi</dt><dd>{{ $peserta?->instansi ?: 'Belum diisi' }}</dd></div>
			</div>
			<div class="participant-profile-detail">
				<span class="participant-profile-detail-icon"><i class="bi bi-telephone" aria-hidden="true"></i></span>
				<div><dt>No. Telepon</dt><dd>{{ $peserta?->telepon ?: 'Belum diisi' }}</dd></div>
			</div>
			<div class="participant-profile-detail">
				<span class="participant-profile-detail-icon"><i class="bi bi-envelope" aria-hidden="true"></i></span>
				<div><dt>Email</dt><dd>{{ $peserta?->email ?: $user->email ?: 'Belum diisi' }}</dd></div>
			</div>
		</dl>
	</section>
</main>
@endsection
