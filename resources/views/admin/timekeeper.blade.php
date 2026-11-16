@extends('layouts.mainLayout')

@section('title', 'Jadwal Ujian | Sistem Uji Kompetensi')

@section('hero')
<div class="page-header">
    <div>
      <h1 class="page-title">Data Timekeeper</h1>
      <p class="page-subtitle">Kelola data panitia yang berperan sebagai timekeeper</p>
    </div>
    <button class="btn-custom btn-custom-primary" type="button" data-bs-toggle="modal" data-bs-target="#tambahTimekeeperModal">
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
        
      </tbody>
    </table>
  </div>
</div>
@endsection