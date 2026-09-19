@extends('layouts.mainLayout')

@section('title', 'Jadwal Ujian | Sistem Uji Kompetensi')

@section('hero')
<div class="page-header">
    <div>
      <h1 class="page-title">Jadwal Ujian Kompetensi Teknis</h1>
      <p class="page-subtitle">Penetapan sesi ujian, alokasi paket soal, waktu pengerjaan, dan penugasan penguji teknis</p>
    </div>
    <button class="btn-custom btn-custom-primary" type="button" data-bs-toggle="modal" data-bs-target="#tambahJadwalUjianModal">
      <i class="bi bi-plus-lg me-1"></i>Tambah Jadwal
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
    <table id="pesertaTable" class="table-custom w-100" data-datatable data-datatable-page-length="10">
      <thead>
        <tr>
          <th>No</th>
          <th>Peserta & NIP</th>
          <th>Tujuan Ujian</th>
          <th>Tanggal & Waktu Ujian</th>
          <th>Penilai Teknis</th>
          <th>Lokasi</th>
          <th>Status</th>
          <th class="text-center">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php $no = 1; ?>
        @foreach($jadwalUjian as $j)
          <tr>
            <td>{{ $no++ }}</td>
            <td><strong>{{ $j->peserta->nama }}</strong><br><small>{{ $j->peserta->nip }}</small></td>
            <td>{{ ucfirst($j->tujuan_ujian?->value) }}</td>
            <td>
              <strong>{{ \Carbon\Carbon::parse($j->tanggal_ujian)->format('d M Y') }}</strong>
              <br>
              <small class="text-muted">
                {{ \Carbon\Carbon::parse($j->waktu_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($j->waktu_selesai)->format('H:i') }}
              </small>
            </td>
            <td>{{ $j->penguji->nama }}</td>
            <td>{{ ucfirst($j->lokasi) }}</td>
            <td>
              @php
              $statusValue = array_search($j->status, $listStatus, true);
              $statusClass = match ($statusValue) {
                'terjadwal' => 'pending',
                'sedang berlangsung' => 'success',
                'menunggu hasil' => 'primary',
                default => 'primary',
              };
              @endphp
              <span class="badge-table {{ $statusClass }}">{{ ucfirst($j->status) }}</span>
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
               
                <form action="{{ route('jadwal-ujian.destroy', ['id' => $j->id]) }}" method="POST" style="display: inline;">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="table-btn-action delete" title="Delete row" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')"><i class="bi bi-trash"></i></button>
                </form>
              </div>
            </td>
          </tr>

          <!-- Modal Edit Penguji -->
        <div class="modal fade modal-standard" id="editJadwalUjianModal-{{ $j->id }}" tabindex="-1" aria-labelledby="editJadwalUjianModalLabel-{{ $j->id }}" aria-hidden="true">
          <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content bg-white">
              <div class="modal-header px-6 py-4 ">
                <h2 class="modal-title fs-5" id="editJadwalUjianModalLabel-{{ $j->id }}">Edit Data Jadwal Ujian</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
              </div>
              <form action="{{ route('jadwal-ujian.update', $j->id) }}" method="POST">
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
              
                    <!-- Tujuan Ujian -->
                    <div class="col-md-6">
                      <label for="tujuan_ujian_{{ $j->id }}" class="form-label">Tujuan Ujian*</label>
                      
                      <select class="form-control form-select-custom select-tujuan-ujian" 
                              id="tujuan_ujian_{{ $j->id }}" 
                              name="tujuan_ujian" 
                              data-target-suffix="{{ $j->id }}" 
                              required>
                        <option value="" 
                                disabled 
                                {{ old('tujuan_ujian', $j->tujuan_ujian) ? '' : 'selected' }}>Pilih Tujuan Ujian...</option>
                        @foreach($tujuan as $key => $value)
                          <option value="{{ $key }}" {{ old('tujuan_ujian', $j->tujuan_ujian) == $key ? 'selected' : '' }}>
                            {{ $value }}
                          </option>
                        @endforeach
                      </select>
                    </div>
                    
                    <!-- Jenjang Tujuan -->
                    <div class="col-md-12">
                      <label class="form-label" for="jenjang_tujuan_{{ $j->id }}">Jenjang Tujuan*</label>
                      <select class="form-control form-select-custom" 
                              id="jenjang_tujuan_{{ $j->id }}" 
                              name="jenjang_tujuan">
                        <option value="" 
                                disabled 
                                {{ old('jenjang_tujuan', $j->jenjang_tujuan) ? '' : 'selected' }}>Pilih Jenjang Tujuan...</option>
                        @foreach(['ahli pertama', 'ahli muda', 'ahli madya', 'ahli utama'] as $jenjang)
                          <option value="{{ $jenjang }}" {{ old('jenjang_tujuan', $j->jenjang_tujuan) == $jenjang ? 'selected' : '' }}>
                            {{ $jenjang }}
                          </option>
                        @endforeach
                      </select>
                    </div>
              
                    <!-- Penilai Teknis / Penguji -->
                    <div class="col-md-12">
                      <label for="penguji_id_{{ $j->id }}" class="form-label">Penilai Teknis*</label>
                      <select class="form-control form-select-custom" id="penguji_id_{{ $j->id }}" name="penguji_id" required>
                        <option value="" disabled {{ old('penguji_id', $j->penguji_id) ? '' : 'selected' }}>Pilih Penguji...</option>
                        @foreach($penguji as $p)
                          <option value="{{ $p->id }}" {{ old('penguji_id', $j->penguji_id) == $p->id ? 'selected' : '' }}>
                            {{ $p->nama }} | {{ $p->nip }} | {{ $p->instansi }}
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
              
                    <!-- Lokasi -->
                    <div class="col-md-12">
                      <label for="lokasi_{{ $j->id }}" class="form-label">Lokasi*</label>
                      <input type="text" class="form-control" id="lokasi_{{ $j->id }}" name="lokasi" 
                             value="{{ old('lokasi', $j->lokasi) }}" required>
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
                  <button type="submit" class="btn-custom btn-custom-primary">Update Jadwal</button>
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

<!-- Modal Tambah Jadwal Ujian -->
<div class="modal fade modal-standard" id="tambahJadwalUjianModal" tabindex="-1" aria-labelledby="tambahJadwalUjianModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content bg-white">
      <div class="modal-header px-6 py-4 ">
        <h2 class="modal-title fs-5" id="tambahJadwalUjianModalLabel">Tambah Jadwal Ujian</h2>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>
      <form action="{{ route('jadwal-ujian.store') }}" method="POST">
        @csrf 
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-12">
              <label for="peserta" class="form-label">Peserta*</label>
              <select class="form-control form-select-custom" id="peserta" name="peserta_id" required>
                <option selected disabled>Pilih Peserta...</option>
                @foreach($peserta as $p)
                  <option value="{{ $p->id }}">{{ $p->nama }} | {{ $p->nip }} | {{ $p->instansi }}</option>
                @endforeach
              </select>
            </div>

            <div class="col-md-6">
              <label for="tujuan" class="form-label">Tujuan Ujian*</label>
              <select class="form-control form-select-custom" id="tujuan" name="tujuan_ujian" required>
                <option selected disabled>Pilih Tujuan Ujian...</option>
                @foreach($tujuan as $key => $value)
                  <option value="{{ $key }}">{{ $value }}</option>
                @endforeach
              </select>
            </div>

            <div class="col-md-6">
              <label for="jenjang_tujuan" class="form-label">Jenjang Tujuan*</label>
              <select class="form-control form-select-custom" id="jenjang_tujuan" name="jenjang_tujuan">
                  <option selected disabled>Pilih Jenjang Tujuan...</option>
                  <option value="ahli pertama">Ahli Pertama</option>
                  <option value="ahli muda">Ahli Muda</option>
                  <option value="ahli madya">Ahli Madya</option>
                  <option value="ahli utama">Ahli Utama</option>
              </select>
            </div>
              
            <div class="col-md-12">
              <label for="penguji" class="form-label">Penilai Teknis*</label>
              <select class="form-control form-select-custom" id="penguji" name="penguji_id" required>
                <option selected disabled>Pilih Penilai Teknis...</option>
                @foreach($penguji as $p)
                  <option value="{{ $p->id }}">{{ $p->nama }} | {{ $p->nip }} | {{ $p->instansi }}</option>
                @endforeach
              </select>
            </div>

            <div class="col-md-4">
              <label for="tanggal" class="form-label">Tanggal*</label>
              <input type="date" class="form-control form-control-custom" id="tanggal" name="tanggal_ujian" required>
            </div>

            <div class="col-md-4">
              <label for="waktu_mulai" class="form-label">Waktu Mulai*</label>
              <input type="time" class="form-control form-control-custom" id="waktu_mulai" name="waktu_mulai" required> 
            </div>

            <div class="col-md-4">
              <label for="waktu_selesai" class="form-label">Waktu Selesai*</label>
              <input type="time" class="form-control form-control-custom" id="waktu_selesai" name="waktu_selesai" required> 
            </div>

            <div class="col-md-12">
              <label for="lokasi" class="form-label">Lokasi*</label>
              <input type="text" class="form-control form-control-custom" id="lokasi" name="lokasi" placeholder="Masukkan lokasi ujian..." required>
            </div>

            <div class="col-md-12">
              <label for="keterangan" class="form-label">Keterangan*</label>
              <textarea class="form-control form-control-custom" id="keterangan" name="keterangan" rows="3" placeholder="Masukkan keterangan tambahan..." required></textarea>
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
