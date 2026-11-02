@extends('layouts.mainLayout')

@section('title', 'Jadwal Pemberkasan | Sistem Uji Kompetensi')

@section('hero')
<div class="page-header">
    <div>
      <h1 class="page-title">Jadwal Ujian</h1>
      <p class="page-subtitle">Pengelolaan periode pendaftaran, batas waktu unggah dokumen, pengaturan kuota utama, dan status gelombang uji kompetensi.</p>
    </div>
    <button class="btn-custom btn-custom-primary" type="button" data-bs-toggle="modal" data-bs-target="#tambahJadwalUjianModal">
      <i class="bi bi-plus-lg me-1"></i>Tambah Data
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
        @foreach($jadwalUjian as $ju)
        <tr>
          <td>{{$no++}}</td>
          <td>Gelombang {{ $ju->gelombang }} Tahun {{ $ju->tahun }}</td>
          <td>{{ $ju->kuota }}</td>
          <td>{{ $ju->periode_awal->translatedFormat('d F Y') }} s/d {{ $ju->periode_akhir->translatedFormat('d F Y') }}</td>
          <td>
            @php
            $statusString = strtolower($ju->status);

            $statusClass = match ($statusString) {
              'aktif'     => 'success',
              'nonaktif'  => 'warning',
              default     => 'success',
            };
            @endphp
            <span class="badge-table {{ $statusClass }}">{{ ucfirst($ju->status) }}</span>
          </td>
          <td>
            <div class="d-flex justify-content-center gap-1">
              <button class="table-btn-action" 
                title="Edit row" 
                type="button" 
                data-bs-toggle="modal" 
                data-bs-target="#editJadwalUjianModal-{{ $ju->id }}">
                <i class="bi bi-pencil"></i>
              </button>
             
              <form action="{{ route('jadwal-ujian.destroy', $ju->id) }}" method="POST" class="d-inline form-delete" style="display: inline;">
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
        <div class="modal fade modal-standard" id="editJadwalUjianModal-{{ $ju->id }}" tabindex="-1" aria-labelledby="editJadwalPemberkasanModalLabel-{{ $ju->id }}" aria-hidden="true">
          <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content bg-white">
              <div class="modal-header px-6 py-4 ">
                <h2 class="modal-title fs-5" id="editJadwalUjianModalLabel-{{ $ju->id }}">Edit Data Jadwal Pemberkasan</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
              </div>
              <form action="{{ route('jadwal-ujian.update', $ju->id) }}" method="POST">
                @csrf 
                @method('PUT')
                
                <div class="modal-body">
                  <div class="row g-3">
                    <!-- Gelombang -->
                    <div class="col-md-6">
                      <label for="gelombang" class="form-label">Gelombang*</label>
                      <select class="form-select form-control-custom" id="gelombang" name="gelombang" required>
                        <option value="" disabled>Pilih gelombang...</option>
                        <option value="1" {{ old('gelombang', $ju->gelombang ?? '') == '1' ? 'selected' : '' }}>Gelombang I</option>
                        <option value="2" {{ old('gelombang', $ju->gelombang ?? '') == '2' ? 'selected' : '' }}>Gelombang II</option>
                        <option value="3" {{ old('gelombang', $ju->gelombang ?? '') == '3' ? 'selected' : '' }}>Gelombang III</option>
                        <option value="4" {{ old('gelombang', $ju->gelombang ?? '') == '4' ? 'selected' : '' }}>Gelombang III</option>
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
                             value="{{ old('kuota', $ju->kuota) }}" required>
                    </div>

                    <!-- Periode Awal -->
                    <div class="col-md-6">
                      <label for="periode_awal" class="form-label">Periode Awal*</label>
                      <input type="date" class="form-control" id="periode_awal"           
                          name="periode_awal" 
                          value="{{ old('periode_awal', $ju->periode_awal ? $ju->periode_awal->format('Y-m-d') : '') }}" 
                          required>
                    </div>

                    <!-- Periode Akhir -->
                    <div class="col-md-6">
                      <label for="periode_akhir" class="form-label">Periode Akhir*</label>
                      <input type="date" class="form-control" id="periode_akhir"           
                          name="periode_akhir" 
                          value="{{ old('periode_akhir', $ju->periode_akhir ? $ju->periode_akhir->format('Y-m-d') : '') }}" 
                          required>
                    </div>

                    <!-- Periode Pemberkasan Awal -->
                    <div class="col-md-6">
                      <label for="periode_pemberkasan_awal" class="form-label">Periode Pemberkasan Awal*</label>
                      <input type="date" class="form-control" id="periode_pemberkasan_awal"           
                          name="periode_pemberkasan_awal" 
                          value="{{ old('periode_pemberkasan_awal', $ju->periode_pemberkasan_awal ? $ju->periode_pemberkasan_awal->format('Y-m-d') : '') }}" 
                          required>
                    </div>

                    <!-- Periode Pemberkasan Akhir -->
                    <div class="col-md-6">
                      <label for="periode_pemberkasan_akhir" class="form-label">Periode Pemberkasan Akhir*</label>
                      <input type="date" class="form-control" id="periode_pemberkasan_akhir"           
                          name="periode_pemberkasan_akhir" 
                          value="{{ old('periode_pemberkasan_akhir', $ju->periode_pemberkasan_akhir ? $ju->periode_pemberkasan_akhir->format('Y-m-d') : '') }}" 
                          required>
                    </div>

                    <!-- Periode Portofolio Awal -->
                    <div class="col-md-6">
                      <label for="periode_portofolio_awal" class="form-label">Periode Portofolio Awal*</label>
                      <input type="date" class="form-control" id="periode_portofolio_awal"           
                          name="periode_portofolio_awal" 
                          value="{{ old('periode_portofolio_awal', $ju->periode_portofolio_awal ? $ju->periode_portofolio_awal->format('Y-m-d') : '') }}" 
                          required>
                    </div>

                    <!-- Periode Portofolio Akhir -->
                    <div class="col-md-6">
                      <label for="periode_portofolio_akhir" class="form-label">Periode Portofolio Akhir*</label>
                      <input type="date" class="form-control" id="periode_portofolio_akhir"           
                          name="periode_portofolio_akhir" 
                          value="{{ old('periode_portofolio_akhir', $ju->periode_portofolio_akhir ? $ju->periode_portofolio_akhir->format('Y-m-d') : '') }}" 
                          required>
                    </div>

                    <!-- Status -->
                    <div class="col-md-12">
                      <label for="status" class="form-label">Status*</label>
                      <select class="form-select form-control-custom" id="status" name="status" required>
                        <option value="" disabled>Pilih status...</option>
                        <option value="aktif" {{ old('status', $ju->status ?? '') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ old('status', $ju->status ?? '') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
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
        <!-- END Modal Edit Jadwal Ujian -->
        @endforeach
      </tbody>
    </table>
  </div>
</div>

<!-- Modal Tambah Jadwal Ujian -->
<div class="modal fade modal-standard" id="tambahJadwalUjianModal" tabindex="-1" aria-labelledby="tambahJadwalPemberkasanModalLabel" aria-hidden="true">
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

            <!-- Periode Awal -->
            <div class="col-md-6">
              <label for="periode_awal" class="form-label">Periode Awal*</label>
              <input type="date" class="form-control form-control-custom" id="periode_awal" name="periode_awal" min="{{ date('Y-m-d') }}" required>
            </div>

            <!-- Periode Akhir -->
            <div class="col-md-6">
              <label for="periode_akhir" class="form-label">Periode Akhir*</label>
              <input type="date" 
                class="form-control form-control-custom" 
                id="periode_akhir" 
                name="periode_akhir" 
                min="{{ date('Y-m-d') }}" 
                required>
            </div>

            <!-- Periode Pemberkasan Awal -->
            <div class="col-md-6">
              <label for="periode_pemberkasan_awal" class="form-label">Periode Pemberkasan Awal*</label>
              <input type="date" 
                class="form-control form-control-custom" 
                id="periode_pemberkasan_awal" 
                name="periode_pemberkasan_awal" 
                min="{{ date('Y-m-d') }}" 
                required>
            </div>

            <!-- Periode Pemberkasan Akhir -->
            <div class="col-md-6">
              <label for="periode_pemberkasan_akhir" class="form-label">Periode Pemberkasan Akhir*</label>
              <input type="date" 
                class="form-control form-control-custom" 
                id="periode_pemberkasan_akhir" 
                name="periode_pemberkasan_akhir" 
                min="{{ date('Y-m-d') }}" 
                required>
            </div>

            <!-- Periode Portofolio Awal -->
            <div class="col-md-6">
              <label for="periode_portofolio_awal" class="form-label">Periode Portofolio Awal*</label>
              <input type="date" 
                class="form-control form-control-custom" 
                id="periode_portofolio_awal" 
                name="periode_portofolio_awal" 
                min="{{ date('Y-m-d') }}" 
                required>
            </div>

            <!-- Periode Portofolio Akhir -->
            <div class="col-md-6">
              <label for="periode_portofolio_akhir" class="form-label">Periode Portofolio Akhir*</label>
              <input type="date" 
                class="form-control form-control-custom" 
                id="periode_portofolio_akhir" 
                name="periode_portofolio_akhir" 
                min="{{ date('Y-m-d') }}" 
                required>
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

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const deleteForms = document.querySelectorAll('.form-delete');
        
        deleteForms.forEach(form => {
            form.addEventListener('submit', function (e) {
                e.preventDefault(); 
                
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data yang telah dihapusntidak dapat dipulihkan",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33', 
                    cancelButtonColor: '#6c757d', 
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit(); 
                    }
                });
            });
        });
    });
</script>
@endsection