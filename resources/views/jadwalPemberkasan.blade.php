@extends('layouts.mainLayout')

@section('title', 'Jadwal Pemberkasan | Sistem Uji Kompetensi')

@section('hero')
<div class="page-header">
    <div>
      <h1 class="page-title">Jadwal Pemberkasan</h1>
      <p class="page-subtitle">Pengelolaan periode pendaftaran, batas waktu unggah dokumen, pengaturan kuota utama, dan status gelombang uji kompetensi.</p>
    </div>
    <button class="btn-custom btn-custom-primary" type="button" data-bs-toggle="modal" data-bs-target="#tambahJadwalPemberkasanModal">
      <i class="bi bi-plus-lg me-1"></i>Tambah Jadwal
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
          <th>No</th>
          <th>Gelombang/Tahun</th>
          <th>Kuota</th>
          <th>Periode</th>
          <th>Status</th>
          <th class="text-center">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php $no = 1; ?>
        @foreach($jadwalPemberkasan as $jp)
        <tr>
          <td>{{$no++}}</td>
          <td>Gelombang {{ $jp->gelombang }} Tahun {{ $jp->tahun }}</td>
          <td>{{ $jp->kuota }}</td>
          <td>{{ $jp->periode_awal->translatedFormat('d F Y') }} s/d {{ $jp->periode_akhir->translatedFormat('d F Y') }}</td>
          <td>
            @php
            $statusString = strtolower($jp->status);

            $statusClass = match ($statusString) {
              'aktif'     => 'success',
              'nonaktif'  => 'warning',
              default     => 'success',
            };
            @endphp
            <span class="badge-table {{ $statusClass }}">{{ ucfirst($jp->status) }}</span>
          </td>
          <td>
            <div class="d-flex justify-content-center gap-1">
              <button class="table-btn-action" 
                title="Edit row" 
                type="button" 
                data-bs-toggle="modal" 
                data-bs-target="#editJadwalPemberkasanModal-{{ $jp->id }}">
                <i class="bi bi-pencil"></i>
              </button>
             
              <form action="{{ route('jadwal-pemberkasan.destroy', ['id' => $jp->id]) }}" method="POST" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="table-btn-action delete" title="Delete row" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')"><i class="bi bi-trash"></i></button>
              </form>
            </div>
          </td>
        </tr>

        <!-- Modal Edit Jadwal Pemberkasan -->
        <div class="modal fade modal-standard" id="editJadwalPemberkasanModal-{{ $jp->id }}" tabindex="-1" aria-labelledby="editJadwalPemberkasanModalLabel-{{ $jp->id }}" aria-hidden="true">
          <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content bg-white">
              <div class="modal-header px-6 py-4 ">
                <h2 class="modal-title fs-5" id="editJadwalPemberkasanModalLabel-{{ $jp->id }}">Edit Data Jadwal Pemberkasan</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
              </div>
              <form action="{{ route('jadwal-pemberkasan.update', $jp->id) }}" method="POST">
                @csrf 
                @method('PUT')
                
                <div class="modal-body">
                  <div class="row g-3">
                    <!-- Gelombang -->
                    <div class="col-md-6">
                      <label for="gelombang" class="form-label">Gelombang*</label>
                      <select class="form-select form-control-custom" id="gelombang" name="gelombang" required>
                        <option value="" disabled>Pilih gelombang...</option>
                        <option value="1" {{ old('gelombang', $jp->gelombang ?? '') == '1' ? 'selected' : '' }}>Gelombang I</option>
                        <option value="2" {{ old('gelombang', $jp->gelombang ?? '') == '2' ? 'selected' : '' }}>Gelombang II</option>
                        <option value="3" {{ old('gelombang', $jp->gelombang ?? '') == '3' ? 'selected' : '' }}>Gelombang III</option>
                        <option value="4" {{ old('gelombang', $jp->gelombang ?? '') == '4' ? 'selected' : '' }}>Gelombang III</option>
                      </select>
                    </div>

                    <!-- Tahun -->
                    <div class="col-md-6">
                      <label for="tahun" class="form-label">Tahun*</label>
                      <select class="form-select form-control-custom" id="tahun" name="tahun" required>
                          <option value="" disabled>Pilih tahun periode...</option>
                          @php
                              $currentYear = (int) date('Y');
                              $selectedYear = old('tahun', $jadwal->tahun ?? $currentYear);
                          @endphp
                          
                          @for ($i = 0; $i <= 1; $i++)
                              @php $year = $currentYear + $i; @endphp
                              <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>
                                  {{ $year }}
                              </option>
                          @endfor
                      </select>
                    </div>

                    <!-- Kuota -->
                    <div class="col-md-12">
                      <label for="kuota" class="form-label">Kuota*</label>
                      <input type="number" class="form-control" id="kuota" name="kuota" 
                             value="{{ old('kuota', $jp->kuota) }}" required>
                    </div>

                    <!-- Periode Awal -->
                    <div class="col-md-6">
                      <label for="periode_awal" class="form-label">Periode Awal*</label>
                      <input type="date" class="form-control" id="periode_awal"           
                          name="periode_awal" 
                          value="{{ old('periode_awal', $jp->periode_awal ? $jp->periode_awal->format('Y-m-d') : '') }}" 
                          required>
                    </div>

                    <!-- Periode Akhir -->
                    <div class="col-md-6">
                      <label for="periode_akhir" class="form-label">Periode Akhir*</label>
                      <input type="date" class="form-control" id="periode_akhir"           
                          name="periode_akhir" 
                          value="{{ old('periode_akhir', $jp->periode_akhir ? $jp->periode_akhir->format('Y-m-d') : '') }}" 
                          required>
                    </div>

                    <!-- Status -->
                    <div class="col-md-12">
                      <label for="status" class="form-label">Status*</label>
                      <select class="form-select form-control-custom" id="status" name="status" required>
                        <option value="" disabled>Pilih status...</option>
                        <option value="aktif" {{ old('status', $jp->status ?? '') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ old('status', $jp->status ?? '') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                      </select>
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
        <!-- END Modal Edit Jadwal Pemberkasan -->
        @endforeach
      </tbody>
    </table>
  </div>
</div>

<!-- Modal Tambah Jadwal Pemberkasan -->
<div class="modal fade modal-standard" id="tambahJadwalPemberkasanModal" tabindex="-1" aria-labelledby="tambahJadwalPemberkasanModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content bg-white">
      <div class="modal-header px-6 py-4 ">
        <h2 class="modal-title fs-5" id="tambahJadwalPemberkasanModalLabel">Tambah Jadwal Pemberkasan</h2>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>
      <form action="{{ route('jadwal-pemberkasan.store') }}" method="POST">
        @csrf 
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label for="gelombang" class="form-label">Gelombang*</label>
              <select class="form-control form-select-custom" id="gelombang" name="gelombang" required>
                <option selected disabled>Pilih Gelombang...</option>
                  <option value="1">Gelombang 1</option>
                  <option value="2">Gelombang 2</option>
                  <option value="3">Gelombang 3</option>
                  <option value="4">Gelombang 4</option>
              </select>
            </div>

            <div class="col-md-6">
              <label for="tahun" class="form-label">Tahun<span>*</span></label>
              <select class="form-select form-control-custom" id="tahun" name="tahun" required>
                  <option value="" selected disabled>Pilih tahun periode...</option>
                  @php
                      $currentYear = (int) date('Y');
                  @endphp
                  @for ($i = 0; $i <= 1; $i++)
                      @php $year = $currentYear + $i; @endphp
                      <option value="{{ $year }}">{{ $year }}</option>
                  @endfor
              </select>
          </div>

            <div class="col-md-12">
              <label for="kuota" class="form-label">Kuota*</label>
              <input type="number" class="form-control form-control-custom" id="kuota" name="kuota" placeholder="Masukkan kouta tersedia..." required>
            </div>

            <div class="col-md-6">
              <label for="periode_awal" class="form-label">Periode Awal*</label>
              <input type="date" class="form-control form-control-custom" id="periode_awal" name="periode_awal" min="{{ date('Y-m-d') }}" required>
            </div>

            <div class="col-md-6">
              <label for="periode_akhir" class="form-label">Periode Akhir*</label>
              <input type="date" class="form-control form-control-custom" id="periode_akhir" name="periode_akhir" min="{{ date('Y-m-d') }}" required>
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
<!-- END Modal Tambah Jadwal Pemberkasan -->
@endsection