@extends('peserta.mainLayout')

@section('content')
<main class="col-12 participant-profile">
	<a class="exam-history-back participant-profile-back" href="{{ route('dashboard-peserta.index') }}">
		<i class="bi bi-arrow-left" aria-hidden="true"></i>
		Beranda
	</a>

	<header class="participant-profile-heading">
		<h1>Profil Peserta</h1>
		<p>Data diri peserta uji kompetensi</p>
	</header>

	<section class="participant-profile-hero">
		<div class="participant-profile-avatar-wrap">
			<div class="participant-profile-avatar" aria-hidden="true">{{ strtoupper(substr($peserta->nama, 0, 2)) }}</div>
			<label class="participant-profile-upload" for="profile-photo" aria-label="Upload foto profil">
				<i class="bi bi-camera" aria-hidden="true"></i>
			</label>
			<input class="participant-profile-file" id="profile-photo" type="file" accept="image/*" aria-label="Pilih foto profil">
		</div>

		<div class="participant-profile-identity">
			<h2>{{ ucwords($peserta->nama) }}</h2>
			<p>{{ $peserta?->nip ?: $user->nip }} <span aria-hidden="true">•</span> {{ ucwords($peserta?->jabatan) ?: 'Jabatan belum diisi' }}</p>
			<p>{{ ucwords($peserta?->unit) ?: 'Unit belum diisi' }}</p>
		</div>
	</section>

	<section class="participant-profile-card" aria-labelledby="participant-profile-details-title">
		<div class="participant-profile-card-heading">
			<h2 id="participant-profile-details-title">Data Diri</h2>
			<button class="participant-profile-edit" type="button" data-bs-toggle="modal" data-bs-target="#editProfilModal">
				<i class="bi bi-pencil" aria-hidden="true"></i>Edit Data
			</button>
		</div>

		<div class="row">
			@if(session('success'))
			  <div class="col-12 alert-custom alert-custom-success mt-3">
				<i class="bi bi-check-circle-fill alert-custom-icon"></i>
				<div class="alert-custom-content">
				  {{ session('success') }}
				</div>
			  </div>
			@endif
		
			@if ($errors->any())
			  <div class="col-12 alert-custom alert-custom-danger mt-3">
				<i class="bi bi-exclamation-triangle-fill alert-custom-icon"></i>
				<div class="alert-custom-content">
					@foreach ($errors->all() as $error)
						{{ $error }}
					@endforeach
				</div>
			  </div>
			@endif
		</div>

		<dl class="participant-profile-details">
			<div class="participant-profile-detail">
				<span class="participant-profile-detail-icon"><i class="bi bi-person" aria-hidden="true"></i></span>
				<div><dt>Nama</dt><dd>{{ ucwords($peserta?->nama) ?: 'Belum diisi' }}</dd></div>
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
				<div><dt>Jabatan</dt><dd>{{ ucwords($peserta?->jabatan) ?: 'Belum diisi' }}</dd></div>
			</div>
			<div class="participant-profile-detail">
				<span class="participant-profile-detail-icon"><i class="bi bi-buildings" aria-hidden="true"></i></span>
				<div><dt>Unit</dt><dd>{{ ucwords($peserta?->unit) ?: 'Belum diisi' }}</dd></div>
			</div>
			<div class="participant-profile-detail">
				<span class="participant-profile-detail-icon"><i class="bi bi-bank" aria-hidden="true"></i></span>
				<div><dt>Instansi</dt><dd>{{ ucwords($peserta?->instansi) ?: 'Belum diisi' }}</dd></div>
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

<!-- Modal Edit Profil -->
<div class="modal fade modal-standard" id="editProfilModal" tabindex="-1" aria-labelledby="editProfilModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content bg-white">
      <div class="modal-header px-6 py-4 ">
        <h2 class="modal-title fs-5" id="editProfilModalLabel">Edit Profil</h2>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>
      <form action="{{ route('peserta.profil.update', $peserta->id) }}" method="POST">
		  @csrf 
		  @method('PUT') 
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-12">
              <label for="nama" class="form-label">Nama*</label>
			  <input type="text" class="form-control form-control-custom" id="nama" name="nama" value="{{ ucwords($peserta->nama) }}" required>
            </div>

			<div class="col-md-12">
              <label for="nip" class="form-label">NIP*</label>
			  <input type="text" class="form-control form-control-custom" id="nip" name="nip" value="{{ ucwords($peserta->nip) }}" required>
            </div>

			<div class="col-md-8">
              <label for="jabatan" class="form-label">Jabatan*</label>
			  <input type="text" class="form-control form-control-custom" id="jabatan" name="jabatan" value="{{ ucwords($peserta->jabatan) }}" required>
            </div>

			<div class="col-md-4">
              <label for="golongan" class="form-label">Golongan*</label>
			  <select class="form-control form-select-custom" id="golongan" name="golongan" required>
				  <option value="" disabled @selected(empty($p->golongan))>Pilih Golongan...</option>
				  <option value="IIIa" @selected($peserta->golongan === 'IIIa')>III/a</option>
				  <option value="IIIb" @selected($peserta->golongan === 'IIIb')>III/b</option>
				  <option value="IIIC" @selected($peserta->golongan === 'IIIC')>III/c</option>
				  <option value="IIId" @selected($peserta->golongan === 'IIId')>III/d</option>
			  </select>
            </div>

			<div class="col-md-12">
              <label for="instansi" class="form-label">Instansi*</label>
			  <input type="text" class="form-control form-control-custom" id="instansi" name="instansi" value="{{ $peserta->instansi }}" required>
            </div>

			<div class="col-md-12">
              <label for="unit" class="form-label">Unit*</label>
			  <input type="text" class="form-control form-control-custom" id="unit" name="unit" value="{{ $peserta->unit }}" required>
            </div>

			<div class="col-md-6">
              <label for="email" class="form-label">Email*</label>
			  <input type="email" class="form-control form-control-custom" id="email" name="email" value="{{ $peserta->email }}" required>
            </div>

			<div class="col-md-6">
              <label for="telepon" class="form-label">Telepon*</label>
			  <input type="text
			  " class="form-control form-control-custom" id="telepon" name="telepon" value="{{ $peserta->telepon }}" required>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="participant-profile-edit">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- END Edit Profil -->
@endsection
