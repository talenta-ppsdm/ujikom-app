@extends('layouts.mainLayout')

@section('title', 'Detail Berkas Pendaftaran | Sistem Uji Kompetensi')

@section('hero')
<div class="page-header">
  <div>
    <a href="{{ route('verifikasi-pemberkasan.index') }}" >
      <i class="bi bi-arrow-left" aria-hidden="true"></i> Kembali
    </a>
    <h1 class="page-title mt-3">Detail Berkas Pendaftaran</h1>
    <p class="page-subtitle">Periksa data peserta dan dokumen yang diunggah</p>
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
<div class="col-12">
  <section class="ui-card mb-4" aria-label="Data peserta">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-start gap-3 mb-3">
      <div class="d-flex align-items-center gap-3">
        <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary-subtle text-primary p-3 fw-bold">#1</span>
        <div>
          <h2 class="h5 mb-1">{{ $pemberkasan->peserta->nama }}</h2>
          <span class="small text-muted">NIP. {{ $pemberkasan->peserta->nip}}</span>
        </div>
      </div>
      @php
        $statusString = strtolower($pemberkasan->status->value);
        $statusClass = match ($statusString) {
          'verifikasi'  => 'warning',
          'approved'    => 'success',
          'rejected'    => 'danger',
          default       => 'primary',
        };
      @endphp
      <span class="badge-table {{ $statusClass }}">
        {{ ucwords($pemberkasan->status->label()) }}
      </span>
    </div>

    <div class="row row-cols-1 row-cols-md-2 gx-md-4">
      <div class="col">
        <div class="d-flex gap-2 py-3 border-bottom">
          <i class="bi bi-building text-muted" aria-hidden="true"></i>
          <div>
            <div class="small text-muted">Instansi</div>
            <div class="fw-semibold">{{ ucwords($pemberkasan->peserta->instansi) }}</div>
          </div>
        </div>
        <div class="d-flex gap-2 py-3 border-bottom">
          <i class="bi bi-building text-muted" aria-hidden="true"></i>
          <div>
            <div class="small text-muted">Unit Kerja</div>
            <div class="fw-semibold">{{ ucwords($pemberkasan->peserta->unit) }}</div>
          </div>
        </div>
        <div class="d-flex gap-2 py-3 border-bottom">
          <i class="bi bi-envelope text-muted" aria-hidden="true"></i>
          <div>
            <div class="small text-muted">Email</div>
            <div class="fw-semibold">{{ ucwords($pemberkasan->peserta->email) }}</div>
          </div>
        </div>
        <div class="d-flex gap-2 py-3 border-bottom">
          <i class="bi bi-hash text-muted" aria-hidden="true"></i>
          <div>
            <div class="small text-muted">Jenis Uji Kompetensi</div>
            <div class="fw-semibold">Uji Kompetensi JF Analis SDMA</div> <!-- TODO tambahin jenis uji kompetensi di awal pemberkasan -->
          </div>
        </div>
      </div>

      <div class="col">
        <div class="d-flex gap-2 py-3 border-bottom">
          <i class="bi bi-person text-muted" aria-hidden="true"></i>
          <div>
            <div class="small text-muted">Jabatan</div>
            <div class="fw-semibold">{{ ucwords($pemberkasan->peserta->jabatan) }} · {{ ucwords($pemberkasan->peserta->golongan) }}</div>
          </div>
        </div>
        <div class="d-flex gap-2 py-3 border-bottom">
          <i class="bi bi-telephone text-muted" aria-hidden="true"></i>
          <div>
            <div class="small text-muted">No. Telepon</div>
            <div class="fw-semibold">{{ $pemberkasan->peserta->telepon }}</div>
          </div>
        </div>
        <div class="d-flex gap-2 py-3 border-bottom">
          <i class="bi bi-calendar3 text-muted" aria-hidden="true"></i>
          <div>
            <div class="small text-muted">Tanggal Daftar</div>
            <div class="fw-semibold">{{ \Carbon\Carbon::parse($pemberkasan->tanggal_daftar)->translatedFormat('d F Y') }}</div>
          </div>
        </div>
        <div class="d-flex gap-2 py-3 border-bottom">
          <i class="bi bi-hash text-muted" aria-hidden="true"></i>
          <div>
            <div class="small text-muted">Nama Periode</div>
            <div class="fw-semibold">Gelombang {{ $pemberkasan->jadwalPemberkasan->gelombang }} Tahun {{ $pemberkasan->jadwalPemberkasan->tahun }}</div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="ui-card mb-4" aria-labelledby="uploaded-documents-title">
    <div class="mb-3">
      <h2 class="h5 mb-1" id="uploaded-documents-title">Dokumen yang Diunggah</h2>
      <p class="small text-muted mb-0">Klik "Lihat" untuk membuka dan memeriksa setiap berkas.</p>
    </div>

    @php
      $documents = [
        ['key' => 'sk-jabatan', 'label' => 'SK Jabatan', 'path' => $pemberkasan->file_sk_jabatan],
        ['key' => 'sk-golongan', 'label' => 'SK Golongan', 'path' => $pemberkasan->file_sk_golongan],
        ['key' => 'portofolio', 'label' => 'Portofolio', 'path' => $pemberkasan->file_portofolio],
      ];
    @endphp
    <div class="d-flex flex-column gap-2">
      @foreach ($documents as $document)
        <div class="d-flex justify-content-between align-items-center gap-3 border rounded-4 p-3 bg-light">
          <div class="d-flex align-items-center gap-2">
            <i class="bi bi-file-earmark-text text-primary" aria-hidden="true"></i>
            <div>
              <div class="fw-semibold">{{ $document['label'] }}</div>
              <div class="small text-muted">
                {{ filled($document['path']) ? basename($document['path']) : 'Belum diunggah' }}
              </div>
            </div>
          </div>
          @if (filled($document['path']))
            <a
              href="{{ route('verifikasi-pemberkasan.document', ['id' => $pemberkasan->id, 'document' => $document['key']]) }}"
              class="btn-custom btn-custom-outline-primary btn-custom-sm"
              target="_blank"
              rel="noopener"
            >
              Lihat <i class="bi bi-box-arrow-up-right" aria-hidden="true"></i>
            </a>
          @else
            <span class="small text-muted">Tidak tersedia</span>
          @endif
        </div>
      @endforeach
    </div>
  </section>

  <div class="d-flex flex-wrap gap-2 mb-3">
    <form action="{{ route('verifikasi-pemberkasan.update', $pemberkasan->id) }}" method="POST">
      @csrf
      @method('PUT')
      
      <input type="hidden" name="status" value="approved">
      
      <button type="submit" class="btn btn-success">
        <i class="bi bi-check-circle me-1" aria-hidden="true"></i> Lolos Verifikasi
      </button>
    </form>


    <form action="{{ route('verifikasi-pemberkasan.update', $pemberkasan->id) }}" method="POST">
      @csrf
      @method('PUT')
      
      <input type="hidden" name="status" value="rejected">
      
      <button type="submit" class="btn btn-danger">
        <i class="bi bi-x-circle me-1" aria-hidden="true"></i> Gagal Verifikasi
      </button>
    </form>
  </div>
</div>
@endsection
