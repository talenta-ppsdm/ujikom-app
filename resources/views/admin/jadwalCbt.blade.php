@extends('layouts.mainLayout')

@section('title', 'Jadwal Ujian | Sistem Uji Kompetensi')

@section('hero')
<div class="page-header">
    <div>
      <h1 class="page-title">Jadwal CBT Kompetensi Teknis</h1>
      <p class="page-subtitle">Penetapan sesi ujian, alokasi paket soal dan waktu pengerjaan</p>
    </div>
    <button class="btn-custom btn-custom-primary" type="button" data-bs-toggle="modal" data-bs-target="#tambahJadwalCbtModal">
      <i class="bi bi-plus-lg me-1"></i>Tambah Jadwal
    </button>

    <div class="row">
      @if(session('success'))
        <div class="col-12 alert-custom alert-custom-success mt-3" role="alert">
          <i class="bi bi-check-circle-fill alert-custom-icon" aria-hidden="true"></i>
          <div class="alert-custom-content">
            {{ session('success') }}
          </div>
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
    <table id="dataTable" class="table-custom w-100" data-datatable data-datatable-page-length="10">
      <thead>
        <tr>
          <th>No</th>
          <th>Peserta & NIP</th>
          <th>Jadwal Ujian</th>
          <th>Tanggal & Waktu Ujian</th>
          <th>Status</th>
          <th class="text-center">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php $no = 1; ?>
        @foreach($jadwalCbt as $j)
          <tr>
            <td class="text-center">{{ $no++ }}</td>
            <td><strong>{{ $j->peserta->nama }}</strong><br><small>{{ $j->peserta->nip }}</small></td>
            <td>
              <strong>Gelombang {{ $j->jadwalUjian->gelombang }}</strong>
              <br>
              <small class="text-muted">Tahun {{ $j->jadwalUjian->tahun }}</small>
            </td>
            <td>
              <strong>{{ $j->tanggal_ujian->translatedFormat('d F Y') }}</strong>
              <br>
              <small class="text-muted">
                {{ \Carbon\Carbon::parse($j->waktu_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($j->waktu_selesai)->format('H:i') }}
              </small>
            </td>
            <td>
                @php
                  $statusString = strtolower($j->status);
                  
                  $statusClass = match ($statusString) {
                    'terjadwal'          => 'primary',
                    'sedang berlangsung' => 'warning',
                    'menunggu hasil'     => 'danger',
                    'selesai'            => 'success',
                    default              => 'primary',
                  };
                @endphp
              
                <span class="badge-table {{ $statusClass }}">{{ ucfirst($j->status) }}</span>
              
                @if($statusString === 'selesai')
                  <span class="badge-table success">Poin: {{ number_format($j->total_skor, 2, ',', '.') }}</span>
                @endif
              </td>
              <td>
                <div class="d-flex justify-content-center gap-1">
                <button class="table-btn-action" 
                  title="Edit row" 
                  type="button" 
                  data-bs-toggle="modal" 
                  data-bs-target="#editJadwalUjianModal-{{ $j->id }}">
                  <i class="bi bi-pencil"></i>
                </button>
               
                <form action="{{ route('jadwal-cbt.destroy', ['id' => $j->id]) }}" method="POST" style="display: inline;">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="table-btn-action delete" title="Delete row" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')"><i class="bi bi-trash"></i></button>
                </form>
              </div>
            </td>
          </tr>

        <!-- Modal Edit Jadwal Ujian -->
        <div class="modal fade modal-standard" id="editJadwalUjianModal-{{ $j->id }}" tabindex="-1" aria-labelledby="editJadwalUjianModalLabel-{{ $j->id }}" aria-hidden="true">
          <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content bg-white">
              <div class="modal-header px-6 py-4 ">
                <h2 class="modal-title fs-5" id="editJadwalUjianModalLabel-{{ $j->id }}">Edit Data Jadwal Ujian</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
              </div>
              <form action="{{ route('jadwal-cbt.update', $j->id) }}" method="POST">
                @csrf 
                @method('PUT')
                
                <div class="modal-body">
                  <div class="row g-3">
                    <!-- Peserta -->
                    <div class="col-md-12">
                      <label for="peserta_id_{{ $j->id }}" class="form-label">Peserta*</label>
                      <select class="form-control form-select-custom" id="peserta_id_{{ $j->id }}" name="peserta_id" required>
                        <option value="" disabled {{ old('peserta_id', $j->peserta_id) ? '' : 'selected' }}>Pilih Peserta...</option>
                        @foreach($peserta as $p)
                          <option value="{{ $p->id }}" {{ old('peserta_id', $j->peserta_id) == $p->id ? 'selected' : '' }}>
                            {{ $p->nama }} | {{ $p->nip }} | {{ $p->instansi }}
                          </option>
                        @endforeach
                      </select>
                    </div>

                    <!-- Jadwal Ujian -->
                    <div class="col-md-6">
                      <label for="jadwal_ujian_id_{{ $j->id }}" class="form-label">Jadwal Ujian*</label>
                      <input type="text" 
                        class="form-control form-control-custom" 
                        id="jadwalUjian" 
                        value="Gelombang {{ $j->jadwalUjian->gelombang }} - Tahun {{ $j->jadwalUjian->tahun }}" 
                        disabled>
        
                      <input type="hidden" 
                        name="jadwal_ujian_id" 
                        value="{{ $j->jadwal_ujian_id }}">
                    </div>

                     <!-- Status Ujian -->
                    <div class="col-md-6">
                      <label for="status" class="form-label">Status*</label>
                      <select class="form-control form-select-custom" id="status" name="status" required>
                        <option value="" disabled {{ old('status', $j->status) ? '' : 'selected' }}>Pilih Status Ujian...</option>

                        @foreach($listStatus as $key => $value)
                          <option value="{{ $key }}" {{ old('status', $j->status) == $key ? 'selected' : '' }}>
                            {{ $value }}
                          </option>
                        @endforeach
                      </select>
                    </div>
              
                    <!-- Tanggal Ujian -->
                    <div class="col-md-4">
                      <label for="tanggal_ujian_{{ $j->id }}" class="form-label">Tanggal*</label>
                      <input type="date" class="form-control" id="tanggal_ujian_{{ $j->id }}" name="tanggal_ujian" 
                             value="{{ old('tanggal_ujian', $j->tanggal_ujian ? $j->tanggal_ujian->format('Y-m-d') : '') }}" required>
                    </div>
              
                    <!-- Waktu Mulai -->
                    <div class="col-md-4">
                      <label for="waktu_mulai_{{ $j->id }}" class="form-label">Waktu Mulai*</label>
                      <input type="time" class="form-control" id="waktu_mulai_{{ $j->id }}" name="waktu_mulai" 
                             value="{{ old('waktu_mulai', $j->waktu_mulai ? \Carbon\Carbon::parse($j->waktu_mulai)->format('H:i') : '') }}" required>
                    </div>
              
                    <!-- Waktu Selesai -->
                    <div class="col-md-4">
                      <label for="waktu_selesai_{{ $j->id }}" class="form-label">Waktu Selesai*</label>
                      <input type="time" class="form-control" id="waktu_selesai_{{ $j->id }}" name="waktu_selesai" 
                             value="{{ old('waktu_selesai', $j->waktu_selesai ? \Carbon\Carbon::parse($j->waktu_selesai)->format('H:i') : '') }}" required>
                    </div>
              
                    <!-- Keterangan -->
                    <div class="col-md-12">
                      <label for="keterangan_{{ $j->id }}" class="form-label">Keterangan</label>
                      <textarea class="form-control" id="keterangan_{{ $j->id }}" name="keterangan" rows="3">{{ old('keterangan', $j->keterangan) }}</textarea>
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
<div class="modal fade modal-standard" id="tambahJadwalCbtModal" tabindex="-1" aria-labelledby="tambahJadwalCbtModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content bg-white">
      <div class="modal-header px-6 py-4 ">
        <h2 class="modal-title fs-5" id="tambahJadwalCbtModalLabel">Tambah Jadwal Cbt</h2>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>
      <form action="{{ route('jadwal-cbt.store') }}" method="POST">
        @csrf 
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-12">
              <label for="peserta" class="form-label">Peserta*</label>
              <select class="form-control form-select-custom" 
                id="peserta" 
                name="peserta_id" 
                required>
                <option selected disabled>Pilih Peserta...</option>
                @foreach($peserta as $p)
                  <option value="{{ $p->id }}">{{ $p->nama }} | {{ $p->nip }} | {{ $p->instansi }}</option>
                @endforeach
              </select>
            </div>

            <div class="col-md-12">
                <label for="jadwalUjian" class="form-label">Jadwal Ujian</label>
                
                @if($jadwalUjian)
                    <input type="text" 
                        class="form-control form-control-custom" 
                        id="jadwalUjian" 
                        value="Gelombang {{ $jadwalUjian->gelombang }} - Tahun {{ $jadwalUjian->tahun }}" 
                        disabled>
            
                    <input type="hidden" 
                        name="jadwal_ujian_id" 
                        value="{{ $jadwalUjian->id }}">
                @else
                    <input type="text" 
                        class="form-control form-control-custom" 
                        id="jadwalUjian" 
                        value="" 
                        placeholder="Tidak terdapat jadwal ujian yang aktif" 
                        disabled>
                    
                    <input type="hidden" 
                        name="jadwal_ujian_id" 
                        value="">
                @endif
            </div>

            <div class="col-md-4">
              <label for="tanggal" class="form-label">Tanggal*</label>
              <input type="date" 
                class="form-control form-control-custom" 
                id="tanggal" 
                name="tanggal_ujian" 
                required>
            </div>

            <div class="col-md-4">
              <label for="waktu_mulai" class="form-label">Waktu Mulai*</label>
              <input type="time" 
                class="form-control form-control-custom" 
                id="waktu_mulai" 
                name="waktu_mulai" 
                required> 
            </div>

            <div class="col-md-4">
              <label for="waktu_selesai" class="form-label">Waktu Selesai*</label>
              <input type="time" 
                class="form-control form-control-custom" 
                id="waktu_selesai" 
                name="waktu_selesai" 
                required> 
            </div>

            <div class="col-md-12">
              <label for="keterangan" class="form-label">Keterangan</label>
              <textarea 
                class="form-control form-control-custom" 
                  id="keterangan" 
                  name="keterangan" 
                  rows="3" 
                  placeholder="Masukkan keterangan tambahan..."></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn-custom btn-custom-primary">Simpan Jadwal</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- END Modal Tambah Jadwal Ujian -->
@endsection


