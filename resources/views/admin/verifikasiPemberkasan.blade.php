@extends('layouts.mainLayout')

@section('title', 'Rekapitulasi Berkas Pendaftaran | Sistem Uji Kompetensi')

@section('hero')
<div class="page-header">
    <div>
      <h1 class="page-title">Data Pemberkasan Peserta</h1>
      <p class="page-subtitle">Rekapitulasi berkas pendaftaran peserta.</p>
    </div>

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
    <table id="dataTable" class="table-custom w-100" data-datatable data-datatable-page-length="10">
      <thead>
        <tr>
          <th class="text-center">No</th>
          <th class="text-center">Peserta</th>
          <th class="text-center">Tanggal Daftar</th>
          <th class="text-center">Unit Kerja/Organisasi</th>
          <th class="text-center">Status</th>
          <th class="text-center">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php $no = 1; ?>
        @foreach($pemberkasan as $data)
        <tr>
          <td class="table-order-id text-center">{{ $no++ }}</td>
          <td>
            <strong>{{ $data->peserta->nama }}</strong><br>
            <small>{{ $data->peserta->nip }}</small>
          </td>
          <td>{{ \Carbon\Carbon::parse($data->tanggal_daftar)->translatedFormat('d F Y') }}</td>
          <td>
            <strong>{{ $data->peserta->unit }}</strong><br>
            <small>{{ $data->peserta->instansi }}</small>
          </td>
          <td class="text-center">
            @php
              $statusString = strtolower($data->status->value);
              $statusClass = match ($statusString) {
                'verifikasi'  => 'warning',
                'approved'    => 'success',
                'rejected'    => 'danger',
                default       => 'primary',
              };
            @endphp
            <span class="badge-table {{ $statusClass }}">{{ ucfirst($data->status->label()) }}</span>
          </td>
          <td>
            <div class="d-flex justify-content-center gap-1">
              <a href="{{ route('verifikasi-pemberkasan.edit', $data->id) }}" class="table-btn-action" title="Verifikasi Berkas"><i class="bi bi-eye"></i></a>
            </div>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection