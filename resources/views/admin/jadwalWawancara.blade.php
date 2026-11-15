@extends('layouts.mainLayout')

@section('title', 'Jadwal Ujian | Sistem Uji Kompetensi')

@section('hero')
<div class="page-header">
    <div>
      <h1 class="page-title">Jadwal Wawancara Kompetensi Teknis</h1>
      <p class="page-subtitle">Penetapan sesi ujian, penguji dan waktu pelaksanaan</p>
    </div>
    <button class="btn-custom btn-custom-primary" type="button" data-bs-toggle="modal" data-bs-target="#tambahJadwalWawancaraModal">
      <i class="bi bi-plus-lg me-1"></i>Tambah
    </button>

    <div class="row">
      @if(session('success'))
        <div class="col-12 alert alert-custom alert-custom-success mt-3" role="alert">
          <i class="bi bi-check-circle-fill alert-custom-icon" aria-hidden="true"></i>
          <div class="alert-custom-content">
            {{ session('success') }}
          </div>
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
    </div>
</div>
@endsection

@section('content')
<div class="table-card-custom">
  <div class="table-responsive">
    <table id="dataTable" class="table-custom w-100" data-datatable data-datatable-page-length="10" data-datatable-actions="5">
      <thead>
        <tr>
          <th class="text-center">No</th>
          <th class="text-center">Tanggal/Jam</th>
          <th class="text-center">Peserta</th>
          <th class="text-center">Penguji</th>
          <th class="text-center">Metode</th>
          <th class="text-center">Status</th>
          <th class="text-center">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php $no = 1; ?>
        @foreach($wawancara as $w)
        <tr>
          <td class="text-center">{{$no++}}</td>
          <td>
            {{ \Carbon\Carbon::parse($w->tanggal)->translatedFormat('d F Y') }}
            <br>
            {{ \Carbon\Carbon::parse($w->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($w->jam_selesai)->format('H:i') }} 
          </td>
          <td>{{ $w->peserta->nama }}</td>
          <td>{{ $w->penguji->nama }}</td>
          <td class="text-center">{{ ucwords($w->metode) }}</td>
          <td class="text-center">{{ ucwords($w->status) }}</td>
          <td>
            <div class="d-flex justify-content-center gap-1">
              <button class="table-btn-action" 
                title="Edit row" 
                type="button" 
                data-bs-toggle="modal" 
                data-bs-target="#editJadwalWawancaraModal-{{ $w->id }}">
                <i class="bi bi-pencil"></i>
              </button>
             
              <form action="{{ route('admin.jadwal-wawancara.destroy', Crypt::encrypt($w->id)) }}" method="POST" class="d-inline form-delete" style="display: inline;">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="table-btn-action delete btn-delete-custom" title="Hapus">
                      <i class="bi bi-trash"></i>
                  </button>
              </form>
            </div>
          </td>
        </tr>

        <!-- Modal Edit Jadwal Ujian -->
        <div class="modal fade modal-standard" id="editJadwalWawancaraModal-{{ $w->id }}" tabindex="-1" aria-labelledby="editJadwalWawancaraModalLabel-{{ $w->id }}" aria-hidden="true">
          <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content bg-white">
              <div class="modal-header px-6 py-4 ">
                <h2 class="modal-title fs-5" id="editJadwalWawancaraModal-{{ $w->id }}">Edit Data Jadwal Wawancara</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
              </div>
              <form action="{{ route('admin.jadwal-wawancara.update', Crypt::encrypt($w->id)) }}" method="POST">
                @csrf 
                @method('PUT')
                
                <div class="modal-body">
                  <div class="row g-3">
                    <!-- Peserta -->
                    <div class="col-md-12">
                      <label for="peserta" class="form-label">Peserta</label>
                      <select class="form-control form-select-custom" id="peserta" name="peserta_id" required>
                        <option disabled {{ old('peserta_id', $w->peserta_id) ? '' : 'selected' }}>Pilih Peserta...</option>
                        
                        @foreach($peserta as $p)
                          <option value="{{$p->id}}" {{ old('peserta_id', $w->peserta_id) == $p->id ? 'selected' : '' }}>
                            {{ $p->nama }} | {{ $p->nip }} | {{ ucwords($p->instansi) }}
                          </option>
                        @endforeach
                        </select>
                    </div>

                    <!-- Penguji -->
                    <div class="col-md-12">
                      <label for="penguji" class="form-label">Penguji</label>
                      <select class="form-control form-select-custom" id="penguji" name="penguji_id" required>
                        <option disabled {{ old('penguji_id', $w->penguji_id) ? '' : 'selected' }}>Pilih Penguji...</option>
                        
                        @foreach($penguji as $p)
                          <option value="{{$p->id}}" {{ old('penguji_id', $w->penguji_id) == $p->id ? 'selected' : '' }}>
                            {{ $p->nama }} | {{ $p->nip }}
                          </option>
                        @endforeach
                        </select>
                    </div>

                    <!-- Tanggal -->
                    <div class="col-md-12">
                      <label for="tanggal" class="form-label">Tanggal</label>
                      <input type="date" class="form-control form-control-custom" id="tanggal" name="tanggal" value="{{ $w->tanggal }}" required>
                    </div>

                    <!-- Jam mulai -->
                    <div class="col-md-6">
                      <label for="jam_mulai" class="form-label">Jam Mulai</label>
                      <input type="time" class="form-control form-control-custom" id="jam_mulai" name="jam_mulai" value="{{ $w->jam_mulai}}" required>
                    </div>
        
                    <!-- Jam selesai -->
                    <div class="col-md-6">
                      <label for="jam_selesai" class="form-label">Jam Selesai</label>
                      <input type="time" class="form-control form-control-custom" id="jam_selesai" name="jam_selesai" value="{{ $w->jam_selesai}}" required>
                    </div>

                    <!-- metode -->
                    <div class="col-md-4">
                      <label for="metode" class="form-label">Metode</label>
                      <select class="form-control form-select-custom" id="metode" name="metode" required>
                        <option selected disabled>Pilih Metode...</option>
                        <option value="online" {{ old('metode', $w->metode ?? '') == 'online' ? 'selected' : '' }}>Online</option>
                        <option value="offline" {{ old('metode', $w->metode ?? '') == 'offline' ? 'selected' : '' }}>Offline</option>
                        </select>
                    </div>

                    <!-- Lokasi/link -->
                    <div class="col-md-8">
                      <label for="lokasi_link" class="form-label">Lokasi/link*</label>
                      <input type="text" class="form-control form-control-custom" id="lokasi_link" name="lokasi_link" value="{{ $w->lokasi_link }}" required>
                    </div>
                  </div>
                </div>
              
                <div class="modal-footer">
                  <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                  <button type="submit" class="btn-custom btn-custom-primary">Ubah</button>
                </div>
              </form>
            </div>
          </div>
        </div>
        <!-- END Modal Edit Jadwal Ujian -->
        @endforeach
      </tbody>
    </table>
  </div>
</div>

<!-- Modal Tambah Jadwal Ujian -->
<div class="modal fade modal-standard" id="tambahJadwalWawancaraModal" tabindex="-1" aria-labelledby="tambahJadwalWawancaraModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content bg-white">
      <div class="modal-header px-6 py-4 ">
        <h2 class="modal-title fs-5" id="tambahJadwalUjianModalLabel">Tambah Jadwal Wawancara</h2>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>
      <form action="{{ route('admin.jadwal-wawancara.store') }}" method="POST">
        @csrf 
        <div class="modal-body">
          <div class="row g-3">
            <!-- Peserta -->
            <div class="col-md-12">
              <label for="peserta" class="form-label">Peserta*</label>
              <select class="form-control form-select-custom" id="peserta" name="peserta_id" required>
                <option selected disabled>Pilih Peserta...</option>
                @foreach($peserta as $p)
                  <option value="{{$p->id}}">{{ $p->nama }} | {{ $p->nip }}</option>
                @endforeach
                </select>
            </div>

            <!-- Penguji -->
            <div class="col-md-12">
              <label for="penguji" class="form-label">Penguji*</label>
              <select class="form-control form-select-custom" id="penguji" name="penguji_id" required>
                <option selected disabled>Pilih Penguji...</option>
                @foreach($penguji as $p)
                  <option value="{{$p->id}}">{{ $p->nama }} | {{ $p->nip }}</option>
                @endforeach
                </select>
            </div>

            <!-- Tanggal -->
            <div class="col-md-12">
              <label for="tanggal" class="form-label">Tanggal*</label>
              <input type="date" class="form-control form-control-custom" id="tanggal" name="tanggal" placeholder="Masukkan tanggal pelaksanaan..." required>
            </div>

            <!-- Jam mulai -->
            <div class="col-md-6">
              <label for="jam_mulai" class="form-label">Jam Mulai*</label>
              <input type="time" class="form-control form-control-custom" id="jam_mulai" name="jam_mulai" placeholder="Masukkan jam mulai..." required>
            </div>

            <!-- Jam selesai -->
            <div class="col-md-6">
              <label for="jam_selesai" class="form-label">Jam Selesai*</label>
              <input type="time" class="form-control form-control-custom" id="jam_selesai" name="jam_selesai" placeholder="Masukkan jam selesai..." required>
            </div>

            <!-- metode -->
            <div class="col-md-4">
              <label for="metode" class="form-label">Metode*</label>
              <select class="form-control form-select-custom" id="metode" name="metode" required>
                <option selected disabled>Pilih Metode...</option>
                <option value="online">Online</option>
                <option value="offline">Offline</option>
                </select>
            </div>

            <!-- Lokasi/link -->
            <div class="col-md-8">
              <label for="lokasi_link" class="form-label">Lokasi/link*</label>
              <input type="text" class="form-control form-control-custom" id="lokasi_link" name="lokasi_link" placeholder="Masukkan lokasi atau link..." required>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn-custom btn-custom-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- END Modal Tambah Jadwal Ujian -->
@endsection