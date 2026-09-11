@extends('layouts.mainLayout')

@section('title', 'Data Penguji | Sistem Uji Kompetensi')

@section('hero')
<div class="page-header">
    <div>
      <h1 class="page-title">Data Penguji Teknis</h1>
      <p class="page-subtitle">Daftar asesor dan tim penilai teknis uji kompetensi Jabatan Fungsional Penata Kelola Perumahan</p>
    </div>
    <button class="btn-custom btn-custom-primary" type="button" data-bs-toggle="modal" data-bs-target="#tambahPengujiModal">
      <i class="bi bi-plus-lg me-1"></i>Tambah Penguji
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
          <th>Bidang Keahlian</th>
          <th>Jabatan</th>
          <th>Unit & Instansi</th>
          <th>Kontak</th>
          <th class="text-center">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php $no = 1; ?>
        @foreach($penguji as $p)
        <tr>
          <td class="table-order-id">{{ $no++ }}</td>
          <td>
            <div class="table-user-cell">
              <div>
                <div><b>{{ $p->nama }}</b></div>
                <div>{{ $p->nip }}</div>
                <span class="badge-table primary">{{ $p->golongan }}</span>
              </div>
            </div>
          </td>
          <td>{{ $p->bidang_keahlian }}</td>
          <td>{{ $p->jabatan }}</td>
          <td>
            <div>
              <div>{{ $p->unit }}</div>
              <div>{{ $p->instansi }}</div>
            </div>
          </td>
          <td>
            <div>
              <div>{{ $p->telepon }}</div>
              <div>{{ $p->email }}</div>
            </div>
          </td>
          <td>
            <div class="d-flex justify-content-center gap-1">
              <a href="#" class="table-btn-action" title="View details"><i class="bi bi-eye"></i></a>
              <button class="table-btn-action" title="Edit row" type="button" data-bs-toggle="modal" data-bs-target="#editPengujiModal-{{ $p->id }}"><i class="bi bi-pencil"></i></button>
             
              <form action="{{ route('penguji.destroy', ['id' => $p->id]) }}" method="POST" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="table-btn-action delete" title="Delete row" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')"><i class="bi bi-trash"></i></button>
              </form>
            </div>
          </td>
        </tr>

        <!-- Modal Edit Penguji -->
        <div class="modal fade modal-standard" id="editPengujiModal-{{ $p->id }}" tabindex="-1" aria-labelledby="editPengujiModalLabel-{{ $p->id }}" aria-hidden="true">
          <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content bg-white">
              <div class="modal-header px-6 py-4 ">
                <h2 class="modal-title fs-5" id="editPengujiModalLabel-{{ $p->id }}">Edit Data Penguji</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
              </div>
              <form action="{{ route('penguji.update', ['id' => $p->id]) }}" method="POST">
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
                    <div class="col-md-12">
                      <label for="bidang_keahlian" class="form-label">Bidang Keahlian*</label>
                      <input type="text" class="form-control" id="bidang_keahlian" name="bidang_keahlian" value="{{ $p->bidang_keahlian }}" required>
                    </div>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                  <button type="submit" class="btn-custom btn-custom-primary">Update Penguji</button>
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

<!-- Modal Tambah Penguji Teknis -->
<div class="modal fade modal-standard" id="tambahPengujiModal" tabindex="-1" aria-labelledby="tambahPengujiModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content bg-white">
      <div class="modal-header px-6 py-4 ">
        <h2 class="modal-title fs-5" id="tambahPengujiModalLabel">Tambah Penguji Teknis</h2>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>
      <form action="{{ route('penguji.store') }}" method="POST">
        @csrf 
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label for="nama" class="form-label">Nama Lengkap & Gelar*</label>
              <input type="text" class="form-control" id="nama" name="nama" placeholder="Contoh: Bambang Darsono, S.T., M.T." required>
            </div>

            <div class="col-md-6">
              <label for="nip" class="form-label">NIP (Nomor Induk Pegawai) *</label>
              <input type="text" class="form-control" id="nip" name="nip" placeholder="18 digit NIP..." maxlength="20" required>
            </div>

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

            <div class="col-md-12">
              <label for="bidang_keahlian" class="form-label">Bidang Keahlian*</label>
              <input type="text" class="form-control" id="bidang_keahlian" name="bidang_keahlian" placeholder="Contoh: Pengembangan Perumahan" required>
            </div>
          </div>      
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn-custom btn-custom-primary">Simpan Penguji</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- END Modal Tambah Penguji Teknis -->