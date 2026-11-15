@extends('layouts.mainLayout')

@section('title', 'Profil Admin | Sistem Uji Kompetensi')

@section('hero')
<div class="page-header">
    <div>
        <h1 class="page-title">Profil Admin</h1>
        <p class="page-subtitle">Data diri admin Sistem Uji Kompetensi</p>
    </div>
</div>
@endsection

@section('content')
@php
    $profileDetails = [
        ['icon' => 'bi-person', 'label' => 'Nama Lengkap', 'value' => $admin->nama],
        ['icon' => 'bi-envelope', 'label' => 'Email', 'value' => $admin->email],
        ['icon' => 'bi-telephone', 'label' => 'No. Telepon', 'value' => $admin->telepon],
        ['icon' => 'bi-fingerprint', 'label' => 'NIP', 'value' => $admin->nip],
        ['icon' => 'bi-building', 'label' => 'Unit Kerja', 'value' => ucwords($admin->unit)],
        ['icon' => 'bi-bank', 'label' => 'Instansi', 'value' => ucwords($admin->instansi)],
    ];
@endphp

<div class="col-12">
    @if(session('success'))
      <div class="alert alert-custom alert-custom-success mt-3 w-100" role="alert">
        <i class="bi bi-check-circle-fill alert-custom-icon" aria-hidden="true"></i>
        <div class="alert-custom-content">{{ session('success') }}</div>
        <button type="button" class="alert-custom-close" data-bs-dismiss="alert" aria-label="Tutup">
          <i class="bi bi-x-lg" aria-hidden="true"></i>
        </button>
      </div>
    @endif
    @if ($errors->any())
        <div class="col-12 alert alert-custom alert-custom-danger mt-3" role="alert">
            <i class="bi bi-exclamation-triangle-fill alert-custom-icon" aria-hidden="true"></i>
            <div class="alert-custom-content">
            @if ($errors->count() === 1)
                {{ $errors->all()[0] }}
            @else
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif
            </div>
            <button type="button" class="alert-custom-close" data-bs-dismiss="alert" aria-label="Tutup">
            <i class="bi bi-x-lg" aria-hidden="true"></i>
            </button>
        </div>
    @endif

    <div class="row g-4 profile-layout">
        <div class="col-12 col-xl-4">
            <section class="card content-card profile-summary-card">
                <div class="profile-summary-header">
                    <div class="profile-avatar-wrap">
                        <div class="profile-avatar">
                            <span id="profile-avatar-initials" aria-hidden="true" @if ($admin->foto) hidden @endif>
                                {{ ucwords($admin->nama) }}
                            </span>
                            <img
                                id="profile-avatar-image"
                                class="profile-avatar-image"
                                alt="Foto profil {{ $admin->nama }}"
                                @if ($admin->foto) src="{{ asset('storage/' . $admin->foto) }}" @else hidden @endif
                            >
                        </div>
                        <form action="{{ route('admin.profil.photo', Crypt::encrypt($admin->id)) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <input
                                class="visually-hidden"
                                id="profile-photo-input"
                                name="foto"
                                type="file"
                                accept="image/jpeg,image/png,image/webp"
                                aria-describedby="profile-photo-help"
                            >
                            <label class="profile-photo-button" for="profile-photo-input" aria-label="Pilih foto profil" title="Pilih foto profil">
                                <i class="bi bi-camera-fill" aria-hidden="true"></i>
                            </label>
                        </form>
                    </div>
                    <p class="profile-photo-help" id="profile-photo-help" aria-live="polite">Pilih foto profil (maks. 1 MB)</p>
                    <h2 class="profile-name">{{ ucwords($admin->nama) }}</h2>
                    <p class="profile-email">{{ $admin->email }}</p>
                    <span class="profile-role">
                        <i class="bi bi-shield-check" aria-hidden="true"></i>
                        {{ strtoupper($admin->user->role) }}
                    </span>
                </div>

                <div class="profile-summary-body">
                    <div class="profile-meta-list">
                        <div class="profile-meta-item">
                            <span>Status Akun</span>
                            <span class="profile-status"><span aria-hidden="true"></span>Aktif</span>
                        </div>
                        <div class="profile-meta-item">
                            <span>Peran</span>
                            <strong>{{ ucfirst($admin->user->role ?: 'Admin') }}</strong>
                        </div>
                        <div class="profile-meta-item">
                            <span><i class="bi bi-calendar3" aria-hidden="true"></i> Terdaftar</span>
                            <strong>{{ $admin->user->created_at?->translatedFormat('d F Y') ?? '—' }}</strong>
                        </div>
                    </div>

                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button class="profile-logout-button" type="submit">
                            Keluar dari Akun
                        </button>
                    </form>
                </div>
            </section>
        </div>

        <div class="col-12 col-xl-8">
            <section class="card content-card profile-details-card">
                <header class="content-card-header">
                    <div class="profile-details-heading">
                        <h2 class="content-card-title">Data Diri Admin</h2>
                        <p class="content-card-description">Informasi yang terhubung dengan akun Anda.</p>
                    </div>
                    <button class="profile-edit-button" type="button" data-bs-toggle="modal" data-bs-target="#profile-edit-modal">
                        <i class="bi bi-pencil-square" aria-hidden="true"></i>
                        Edit Profil
                    </button>
                </header>

                <div class="info-grid">
                    @foreach ($profileDetails as $detail)
                        <div class="info-item">
                            <span class="info-icon" aria-hidden="true">
                                <i class="bi {{ $detail['icon'] }}"></i>
                            </span>
                            <span class="info-copy">
                                <span class="info-label">{{ $detail['label'] }}</span>
                                <span class="info-value {{ $detail['value'] ? '' : 'info-value-empty' }}">
                                    {{ $detail['value'] ?: 'Belum diisi' }}
                                </span>
                            </span>
                        </div>
                    @endforeach
                </div>
            </section>

            <div class="modal fade profile-edit-modal" id="profile-edit-modal" tabindex="-1" aria-labelledby="profile-edit-modal-title" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <div>
                                <h2 class="modal-title" id="profile-edit-modal-title">Edit Profil Admin</h2>
                                <p class="profile-edit-modal-description">Perbarui informasi data diri Anda.</p>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                        </div>
                        <div class="modal-body">
                            <form action="{{ route('admin.profil.update', Crypt::encrypt($admin->id)) }}" method="POST" id="profile-edit-form">
                                @csrf
                                @method('PUT')
                                <div class="row g-3">
                                    <div class="col-12 col-md-6">
                                        <label class="form-label" for="edit-profile-name">Nama Lengkap</label>
                                        <input class="form-control" id="edit-profile-name" name="nama" type="text" value="{{ $admin->nama }}" required>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label" for="edit-profile-email">Email</label>
                                        <input class="form-control" id="edit-profile-email" name="email" type="email" value="{{ $admin->email }}" required>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label" for="edit-profile-phone">No. Telepon</label>
                                        <input class="form-control" id="edit-profile-phone" name="telepon" type="tel" value="{{ $admin->telepon }}" required>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label" for="edit-profile-nip">NIP</label>
                                        <input class="form-control" id="edit-profile-nip" name="nip" type="text" value="{{ $admin->nip }}" required>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label" for="edit-profile-unit">Unit Kerja</label>
                                        <input class="form-control" id="edit-profile-unit" name="unit" type="text" value="{{ $admin->unit }}" required>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label" for="edit-profile-institution">Instansi</label>
                                        <input class="form-control" id="edit-profile-institution" name="instansi" type="text" value="{{ $admin->instansi }}" required>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="profile-edit-button">
                                        Simpan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    (() => {
        const input = document.getElementById('profile-photo-input');
        const preview = document.getElementById('profile-avatar-image');
        const initials = document.getElementById('profile-avatar-initials');
        const helpText = document.getElementById('profile-photo-help');
        const form = input.form;
        const maxFileSize = 2 * 1024 * 1024;
        let previewUrl = null;

        input.addEventListener('change', () => {
            const file = input.files[0];
            if (!file) return;

            if (!file.type.startsWith('image/')) {
                helpText.textContent = 'File harus berupa gambar.';
                input.value = '';
                return;
            }

            if (file.size > maxFileSize) {
                helpText.textContent = 'Ukuran foto maksimal 2 MB.';
                input.value = '';
                return;
            }

            if (previewUrl) URL.revokeObjectURL(previewUrl);
            previewUrl = URL.createObjectURL(file);
            preview.src = previewUrl;
            preview.hidden = false;
            initials.hidden = true;
            helpText.textContent = 'Menyimpan foto profil...';
            form.requestSubmit();
        });
    })();
</script>
@endsection
