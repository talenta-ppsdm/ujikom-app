@extends('layouts.mainLayout')

@section('title', 'Data Peserta | Sistem Uji Kompetensi')

@section('hero')
<div class="page-header">
    <div>
      <h1 class="page-title">Data Peserta</h1>
      <p class="page-subtitle">Kelola data peserta, jenjang yang dituju (Pertama/Muda/Madya/Utama), jadwal, dan evaluasi.</p>
    </div>
    <button class="btn-custom btn-custom-primary" type="button" data-bs-toggle="modal" data-bs-target="#tambahPesertaModal">
      <i class="bi bi-plus-lg me-1"></i>Tambah Peserta
    </button>

    <div class="row">
      @if(session('success'))
        <div class="col-12 alert-custom alert-custom-success mt-3">
          <i class="bi bi-check-circle-fill alert-custom-icon"></i>
          <div class="alert-custom-content">
            {{ session('success') }}
          </div>
        </div>
      @endif
  
      @if(session('error'))
        <div class="col-12 alert-custom alert-custom-danger mt-3">
          <i class="bi bi-exclamation-triangle-fill alert-custom-icon"></i>
          <div class="alert-custom-content">
            {{ session('error') }}
          </div>
        </div>
      @endif
    </div>
</div>
@endsection

@section('content')
<div class="table-card-custom">
  <div class="table-responsive">
    <table id="pesertaTable" class="table-custom w-100" data-datatable data-datatable-page-length="10" data-datatable-actions="5">
      <thead>
        <tr>
          <th>No</th>
          <th>Nama</th>
          <th>Jabatan</th>
          <th>Unit & Instansi</th>
          <th>Kontak</th>
          <th class="text-center">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php $no = 1; ?>
        @foreach($peserta as $p)
        <tr>
          <td class="table-order-id">{{ $no++ }}</td>
          <td>
            <div class="table-user-cell">
              <div>
                <div class="table-user-name">{{ $p->nama }}</div>
                <div class="table-user-sub">{{ $p->nip }}</div>
                <span class="badge-table primary">{{ $p->golongan }}</span>
              </div>
            </div>
          </td>
          <td class="table-product-name">{{ $p->jabatan }}</td>
          <td>
            <div class="table-unit-cell">
              <div class="table-unker-name">{{ $p->unit }}</div>
              <div class="table-unor-sub">{{ $p->instansi }}</div>
            </div>
          </td>
          <td>
            <div class="table-contact-cell">
              <div class="table-contact-number">{{ $p->telepon }}</div>
              <div class="table-contact-email">{{ $p->email }}</div>
            </div>
          </td>
          <td>
            <div class="d-flex justify-content-center gap-1">
              <a href="#" class="table-btn-action" title="View details"><i class="bi bi-eye"></i></a>
              <button class="table-btn-action" title="Edit row" type="button" data-bs-toggle="modal" data-bs-target="#editPesertaModal-{{ $p->id }}"><i class="bi bi-pencil"></i></button>
              <!-- TODO Alert confirm ubah pakai sweet alert -->
              <form action="{{ route('peserta.destroy', ['id' => $p->id]) }}" method="POST" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="table-btn-action delete" title="Delete row" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')"><i class="bi bi-trash"></i></button>
              </form>
            </div>
          </td>
        </tr>

        <!-- Modal Edit Peserta -->
        <div class="modal fade modal-standard" id="editPesertaModal-{{ $p->id }}" tabindex="-1" aria-labelledby="editPesertaModalLabel-{{ $p->id }}" aria-hidden="true">
          <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content bg-white">
              <div class="modal-header px-6 py-4 ">
                <h2 class="modal-title fs-5" id="editPesertaModalLabel-{{ $p->id }}">Edit Peserta Uji Kompetensi</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
              </div>
              <form action="{{ route('peserta.update', ['id' => $p->id]) }}" method="POST">
                @csrf 
                @method('PUT')
                <div class="modal-body">
                  <div class="row g-3">
                    <div class="col-md-6">
                      <label for="nama" class="form-label">Nama Lengkap & Gelar*</label>
                      <input type="text" class="form-control" id="nama" name="nama" value="{{ $p->nama }}" required>
                    </div>
        
                    <div class="col-md-6">
                      <label for="nip" class="form-label">NIP (Nomor Induk Pegawai) *</label>
                      <input type="text" class="form-control" id="nip" name="nip" value="{{ $p->nip }}" placeholder="18 digit NIP..." maxlength="20" required>
                    </div>
        
                    <div class="col-md-6">
                      <label for="golongan" class="form-label">Golongan*</label>
                      <select class="form-control form-select-custom" id="golongan" name="golongan" required>
                        <option value="" disabled @selected(empty($p->golongan))>Pilih Golongan...</option>
                        <option value="IIIa" @selected($p->golongan === 'IIIa')>III/a</option>
                        <option value="IIIb" @selected($p->golongan === 'IIIb')>III/b</option>
                        <option value="IIIC" @selected($p->golongan === 'IIIC')>III/c</option>
                        <option value="IIId" @selected($p->golongan === 'IIId')>III/d</option>
                      </select>
                    </div>
                    
                    <div class="col-md-6">
                      <label for="jabatan" class="form-label">Nama Jabatan*</label>
                      <input type="text" class="form-control" id="jabatan" name="jabatan" value="{{ $p->jabatan }}" required>
                    </div>
        
                    <div class="col-md-6">
                      <label for="unit" class="form-label">Unit Kerja*</label>
                      <input type="text" class="form-control" id="unit" name="unit" value="{{ $p->unit }}" required>
                    </div>
        
                    <div class="col-md-6">
                      <label for="instansi" class="form-label">Instansi*</label>
                      <input type="text" class="form-control" id="instansi" name="instansi" value="{{ $p->instansi }}" required>
                    </div>
        
                    <div class="col-md-6">
                      <label for="telepon" class="form-label">Nomor Telepon*</label>
                      <input type="tel" class="form-control" id="telepon" name="telepon" value="{{ $p->telepon }}" maxlength="15" required>
                    </div>
                    <div class="col-md-6">
                      <label for="email" class="form-label">Email*</label>
                      <input type="email" class="form-control" id="email" name="email" value="{{ $p->email }}" required>
                    </div>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                  <button type="submit" class="btn-custom btn-custom-primary">Update Peserta</button>
                </div>
              </form>
            </div>
          </div>
        </div>
        <!-- END Modal Edit Peserta -->
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection

<!-- Modal Tambah Peserta -->
<div class="modal fade modal-standard" id="tambahPesertaModal" tabindex="-1" aria-labelledby="tambahPesertaModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content bg-white">
      <div class="modal-header px-6 py-4 ">
        <h2 class="modal-title fs-5" id="tambahPesertaModalLabel">Tambah Peserta Uji Kompetensi</h2>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>
      <form action="{{ route('peserta.store') }}" method="POST">
        @csrf 
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label for="nama" class="form-label">Nama Lengkap & Gelar*</label>
              <input type="text" class="form-control" id="nama" name="nama" placeholder="Contoh: Ahmad Firdaus, S.T." required>
            </div>

            <div class="col-md-6">
              <label for="nip" class="form-label">NIP (Nomor Induk Pegawai) *</label>
              <input type="text" class="form-control" id="nip" name="nip" placeholder="18 digit NIP..." maxlength="20" required>
            </div>

            <!-- <div class="alert-custom alert-custom-info">
              <div class="alert-custom-content">
                <b>Jenjang Jabatan Fungsional yang Dituju *</b>
                <p>Sistem CBT akan secara otomatis mengambil dan mengacak butir soal dari bank soal yang sesuai dengan level jenjang ini.</p>

                <div class="jenjang-options">
                  <label class="jenjang-option is-selected" for="jenjangPertama">
                    <input class="jenjang-radio" type="radio" name="jenjang" id="jenjangPertama" value="pertama" checked>
                    <span class="jenjang-name">JF Ahli Pertama</span>
                    <span class="jenjang-level">Level 1</span>
                  </label>
                  <label class="jenjang-option" for="jenjangMuda">
                    <input class="jenjang-radio" type="radio" name="jenjang" id="jenjangMuda" value="muda">
                    <span class="jenjang-name">JF Ahli Muda</span>
                    <span class="jenjang-level">Level 2 &amp; Level 3</span>
                  </label>
                  <label class="jenjang-option" for="jenjangMadya">
                    <input class="jenjang-radio" type="radio" name="jenjang" id="jenjangMadya" value="madya">
                    <span class="jenjang-name">JF Ahli Madya</span>
                    <span class="jenjang-level">Level 4</span>
                  </label>
                  <label class="jenjang-option" for="jenjangUtama">
                    <input class="jenjang-radio" type="radio" name="jenjang" id="jenjangUtama" value="utama">
                    <span class="jenjang-name">JF Ahli Utama</span>
                    <span class="jenjang-level">Level 5</span>
                  </label>
                </div>
              </div>
            </div> -->

            <div class="col-md-6">
              <label for="golongan" class="form-label">Golongan*</label>
              <select class="form-control form-select-custom" id="golongan" name="golongan" required>
                <option selected disabled>Pilih Golongan...</option>
                <option value="IIIa">III/a</option>
                <option value="IIIb">III/b</option>
                <option value="IIIC">III/c</option>
                <option value="IIId">III/d</option>
              </select>
            </div>
            
            <div class="col-md-6">
              <label for="jabatan" class="form-label">Nama Jabatan*</label>
              <input type="text" class="form-control" id="jabatan" name="jabatan" placeholder="Contoh: Penata Kelola Perumahan Ahli Pertama" required>
            </div>

            <div class="col-md-6">
              <label for="unit" class="form-label">Unit Kerja*</label>
              <input type="text" class="form-control" id="unit" name="unit" placeholder="Contoh: Pusat Pengembangan Sumber Daya Manusia" required>
            </div>

            <div class="col-md-6">
              <label for="instansi" class="form-label">Instansi*</label>
              <input type="text" class="form-control" id="instansi" name="instansi" placeholder="Contoh: Kementerian Perumahan dan Kawasan Permukiman" required>
            </div>

            <div class="col-md-6">
              <label for="telepon" class="form-label">Nomor Telepon*</label>
              <input type="tel" class="form-control" id="telepon" name="telepon" placeholder="Contoh: 081xxxxxxxxx" maxlength="15" required>
            </div>
            <div class="col-md-6">
              <label for="email" class="form-label">Email*</label>
              <input type="email" class="form-control" id="email" name="email" placeholder="Contoh: peserta@email.com" required>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn-custom btn-custom-primary">Simpan Peserta</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- END Modal Tambah Peserta -->

 