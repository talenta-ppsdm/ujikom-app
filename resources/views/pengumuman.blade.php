@extends('layouts.mainLayout')

@section('title', 'Data Pengumuman | Sistem Uji Kompetensi')

@section('hero')
<div class="page-header">
    <div>
      <h1 class="page-title">Data Pengumuman</h1>
    </div>
    
    <button class="btn-custom btn-custom-primary" type="button" data-bs-toggle="modal" data-bs-target="#tambahPengumumanModal">
      <i class="bi bi-plus-lg me-1"></i>Tambah Pengumuman
    </button>

    @if(session('success'))
        <div class="alert alert-custom alert-custom-success mt-3 w-100" role="alert">
          <i class="bi bi-check-circle-fill alert-custom-icon" aria-hidden="true"></i>
          <div class="alert-custom-content">{{ session('success') }}</div>
          <button type="button" class="alert-custom-close" data-bs-dismiss="alert" aria-label="Tutup">
            <i class="bi bi-x-lg" aria-hidden="true"></i>
          </button>
        </div>
    @endif
  
    @if(session('error'))
        <div class="alert alert-custom alert-custom-danger mt-3 w-100" role="alert">
          <i class="bi bi-exclamation-triangle-fill alert-custom-icon" aria-hidden="true"></i>
          <div class="alert-custom-content">{{ session('error') }}</div>
          <button type="button" class="alert-custom-close" data-bs-dismiss="alert" aria-label="Tutup">
            <i class="bi bi-x-lg" aria-hidden="true"></i>
          </button>
        </div>
    @endif
</div>
@endsection

@section('content')
<div class="table-card-custom">
  <div class="table-responsive">
    <table id="dataTable" class="table-custom w-100" data-datatable data-datatable-page-length="10" data-datatable-actions="5">
      <thead>
        <tr>
          <th>No</th>
          <th>Judul</th>
          <th>Status</th>
          <th>Tanggal Terbit</th>
          <th>Tanggal Berakhir</th>
          <th class="text-center">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php $no = 1; ?>
        @foreach($pengumuman as $p)
        <tr>
          @php
          $statusBadgeClass = match(strtolower($p->status)){
            'aktif' => 'ui-badge-success',
            'nonaktif' => 'ui-badge-danger',
          } 
          @endphp
          <td class="table-order-id">{{ $no++ }}</td>
          <td>{{ ucwords($p->judul) }}</td>
          <td>
            <span class="badge-table {{ $statusBadgeClass }}">{{ ucfirst( $p->status) }}</span>
          </td>
          <td>{{ $p->tgl_terbit->translatedFormat('d F Y') }}</td>
          <td>{{ $p->tgl_berakhir }}</td>
          <td>
            <div class="d-flex justify-content-center gap-1">
              <!-- Btn Add -->
              <button class="table-btn-action" title="View Pengumuman" type="button" data-bs-toggle="modal" data-bs-target="#viewPengumumanModal-{{ $p->id }}"><i class="bi bi-eye"></i></button>

              <!-- Btn Edit -->
              <button class="table-btn-action" title="Edit Soal" type="button" data-bs-toggle="modal" data-bs-target="#editPengumumanModal-{{ $p->id }}"><i class="bi bi-pencil"></i></button>

              <!-- Btn Delete -->
              <form action="{{ route('pengumuman.destroy', $p->id) }}" method="POST" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="table-btn-action delete" title="Delete row" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')"><i class="bi bi-trash"></i></button>
              </form>
            </div>
          </td>
        </tr>

        <!-- Modal View Pengumuman -->
        <div class="modal fade modal-standard" id="viewPengumumanModal-{{ $p->id }}" tabindex="-1" aria-labelledby="viewPengumumanModalLabel-{{ $p->id }}" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered announcement-detail-dialog">
            <div class="modal-content bg-white">
              <div class="modal-header announcement-detail-header">
                <div class="announcement-detail-heading">
                  <span class="announcement-detail-heading-icon"><i class="bi bi-megaphone"></i></span>
                  <h2 class="modal-title" id="viewPengumumanModalLabel-{{ $p->id }}">Detail Pengumuman</h2>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
              </div>
              <div class="modal-body announcement-detail-body">
                <h3 class="announcement-detail-title">{{ ucwords($p->judul) }}</h3>
                <div class="announcement-detail-badges">
                  <span class="announcement-detail-badge announcement-detail-status {{ strtolower($p->status) === 'aktif' ? 'is-active' : 'is-inactive' }}">{{ ucfirst($p->status) }}</span>
                </div>

                <div class="announcement-detail-meta">
                  <div class="announcement-detail-row">
                    <span class="announcement-detail-row-icon"><i class="bi bi-calendar-event"></i></span>
                    <div>
                      <div class="announcement-detail-label">Tanggal Terbit</div>
                      <div class="announcement-detail-value">{{ $p->tgl_terbit ? \Carbon\Carbon::parse($p->tgl_terbit)->format('Y-m-d') : '-' }}</div>
                    </div>
                  </div>
                  <div class="announcement-detail-row">
                    <span class="announcement-detail-row-icon"><i class="bi bi-calendar-check"></i></span>
                    <div>
                      <div class="announcement-detail-label">Tanggal Berakhir</div>
                      <div class="announcement-detail-value">{{ $p->tgl_berakhir ? \Carbon\Carbon::parse($p->tgl_berakhir)->format('Y-m-d') : '-' }}</div>
                    </div>
                  </div>
                  <div class="announcement-detail-row">
                    <span class="announcement-detail-row-icon"><i class="bi bi-person"></i></span>
                    <div>
                      <div class="announcement-detail-label">Kepada</div>
                      <div class="announcement-detail-value">{{ $p->penerima_count }} peserta</div>
                    </div>
                  </div>
                </div>
  
                <div class="announcement-detail-content">
                  <div class="announcement-detail-content-heading">
                    <span class="announcement-detail-row-icon"><i class="bi bi-megaphone"></i></span>
                    <div class="announcement-detail-label">Konten Pengumuman</div>
                  </div>
                  <div class="announcement-detail-message">{{ $p->konten }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- END Modal View Pengumuman -->

        <!-- Modal Edit Pengumuman -->
        <div class="modal fade modal-standard" id="editPengumumanModal-{{ $p->id }}" tabindex="-1" aria-labelledby="editPengumumanModalLabel-{{ $p->id }}" aria-hidden="true">
          <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content bg-white">
              <!-- Header Modal -->
              <div class="modal-header px-6 py-4 ">
                 <h2 class="modal-title fs-5" id="editPengumumanModalLabel">Edit Pengumuman</h2>
                 <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
               </div>

               <!-- Content Modal -->
               <form action="{{ route('pengumuman.update', $p->id) }}" method="POST">
               @csrf 
               @method('PUT')
                 <div class="modal-body">
                    <div class="row g-3">
                      <div class="col-md-6">
                        <label for="tgl_terbit" class="form-label">Tanggal Terbit*</label>
                        <input type="date" class="form-control" id="tgl_terbit" name="tgl_terbit" value="{{ $p->tgl_terbit?->format('Y-m-d') }}" required>
                      </div>

                      <div class="col-md-6">
                        <label for="tgl_berakhir" class="form-label">Tanggal Berakhir*</label>
                        <input type="date" class="form-control" id="tgl_berakhir" name="tgl_berakhir" value="{{ $p->tgl_berakhir?->format('Y-m-d') }}">
                      </div>

                      <div class="col-md-12">
                        <label for="judul" class="form-label">Judul*</label>
                        <input type="text" class="form-control" id="judul" name="judul" value="{{ $p->judul }}">
                      </div>

                      <div class="col-md-12">
                        <label for="konten" class="form-label">Konten*</label>
                        <textarea class="form-control-custom" id="konten" name="konten" rows="5" required>{{$p->konten}}</textarea>
                      </div>

                      <!-- Penerima Pengumuman -->
                      <div class="col-md-12">
                        <div class="recipient-target">
                          <div class="recipient-target-label">Penerima Pengumuman</div>
                          <div class="recipient-picker">
                            <div class="recipient-picker-panel">
                              <div class="recipient-search">
                                <i class="bi bi-search" aria-hidden="true"></i>
                                <input type="search" placeholder="Cari nama, NIP, atau email peserta..." autocomplete="off">
                              </div>

                              <div class="recipient-picker-toolbar">
                                <span class="recipient-picker-count" aria-live="polite">
                                  <span>0</span> peserta dipilih
                                </span>
                                <button type="button" class="recipient-select-all select-all-btn">Pilih Semua</button>
                              </div>

                              <div class="recipient-list">
                                @forelse($allPeserta as $itemPeserta)
                                @php
                                  $selectedPesertaIds = $p->penerima->pluck('peserta_id')->toArray();
                                  $isPesertaChecked = in_array($itemPeserta->id, $selectedPesertaIds);
                                @endphp
                                  <label class="recipient-person" data-search="{{ strtolower($itemPeserta->nama . ' ' . $itemPeserta->nip . ' ' . $itemPeserta->email) }}">
                                    <input type="checkbox" name="peserta_ids[]" value="{{ $itemPeserta->id }}" {{ $isPesertaChecked ? 'checked' : '' }}>
                                    <span class="recipient-person-copy">
                                      <span class="recipient-person-name">{{ $itemPeserta->nama }}</span>
                                      <span class="recipient-person-email">NIP. {{ $itemPeserta->nip }} · {{ $itemPeserta->email }}</span>
                                    </span>
                                  </label>
                                @empty
                                  <p class="recipient-list-empty">Belum ada data peserta.</p>
                                @endforelse
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                 </div>
                 <div class="modal-footer">
                   <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                   <button type="submit" class="btn-custom btn-custom-primary">
                     <i class="bi bi-check-lg me-1"></i>Ubah
                   </button>
                 </div>
               </form>
            </div>
          </div>
        </div>
        <!-- END Modal Edit Pengumuman -->
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection

<!-- Modal Tambah Pengumuman -->
<div class="modal fade modal-standard" id="tambahPengumumanModal" tabindex="-1" aria-labelledby="tambahPengujiModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content bg-white">
      <!-- Header Modal -->
      <div class="modal-header px-6 py-4 ">
        <h2 class="modal-title fs-5" id="tambahPengumumanModalLabel">Tambah Pengumuman</h2>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>

      <!-- Content Modal -->
      <form action="{{ route('pengumuman.store') }}" method="POST">
        @csrf 
        <div class="modal-body">
          <div class="row g-3">
            <!-- Tanggal Terbit -->
            <div class="col-md-6">
              <label for="tgl_terbit" class="form-label">Tanggal Terbit*</label>
              <input type="date" name="tgl_terbit" id="tgl_terbit" class="form-control" required>
            </div>

            <!-- Tanggal Berakhir -->
            <div class="col-md-6">
              <label for="tgl_berakhir" class="form-label">Tanggal Berakhir</label>
              <input type="date" class="form-control" id="tgl_berakhir" name="tgl_berakhir">
            </div>

            <!-- Judul -->
            <div class="col-md-12">
              <label for="judul" class="form-label">Judul*</label>
              <input type="text" class="form-control" id="judul" name="judul" placeholder="Masukkan judul pengumuman" required>
            </div>

            <!-- Konten -->
            <div class="col-md-12">
              <label for="konten" class="form-label">Konten*</label>
              <textarea class="form-control-custom" id="konten" name="konten" rows="5" required></textarea>
            </div>

            <!-- Penerima Pengumuman -->
            <div class="col-md-12">
                <div class="recipient-target">
                    <div class="recipient-target-label">Penerima Pengumuman</div>
                    <div class="recipient-picker">
                        <div class="recipient-picker-panel">
                            <div class="recipient-search">
                                <i class="bi bi-search" aria-hidden="true"></i>
                                <input type="search" placeholder="Cari nama, NIP, atau email peserta..." autocomplete="off">
                            </div>

                            <div class="recipient-picker-toolbar">
                                <span class="recipient-picker-count" aria-live="polite">
                                    <span>0</span> peserta dipilih
                                </span>
                                <button type="button" class="recipient-select-all select-all-btn">
                                    Pilih Semua
                                </button>
                            </div>

                            <div class="recipient-list">
                                @forelse($allPeserta as $peserta)
                                    <label class="recipient-person" data-search="{{ strtolower($peserta->nama . ' ' . $peserta->nip . ' ' . $peserta->email) }}">
                                        <input type="checkbox" name="peserta_ids[]" value="{{ $peserta->id }}">
                                        <span class="recipient-person-copy">
                                            <span class="recipient-person-name">{{ $peserta->nama }}</span>
                                            <span class="recipient-person-email">NIP. {{ $peserta->nip }} · {{ $peserta->email }}</span>
                                        </span>
                                    </label>
                                @empty
                                    <p class="recipient-list-empty">Belum ada data peserta.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>

                </div>
            </div>      

          </div>      
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn-custom btn-custom-primary">
            <i class="bi bi-check-lg me-1"></i>Simpan
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- END Modal Tambah Pengumuman -->

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.recipient-target').forEach((targetGroup) => {
    const pesertaSearch = targetGroup.querySelector('input[type="search"]');
    const recipientPeople = targetGroup.querySelectorAll('.recipient-person');
    const selectedPesertaCount = targetGroup.querySelector('.recipient-picker-count span');
    const selectAllBtn = targetGroup.querySelector('.select-all-btn');

    const updateSelection = () => {
      const selectedCount = targetGroup.querySelectorAll('.recipient-person input:checked').length;
      if (selectedPesertaCount) selectedPesertaCount.textContent = selectedCount;
    };

    pesertaSearch?.addEventListener('input', () => {
      const searchTerm = pesertaSearch.value.trim().toLowerCase();

      recipientPeople.forEach((person) => {
        const searchData = person.dataset.search || '';
        person.hidden = !searchData.includes(searchTerm);
      });
    });

    targetGroup.querySelectorAll('.recipient-person input').forEach((checkbox) => {
      checkbox.addEventListener('change', updateSelection);
    });

    if (selectAllBtn) {
      selectAllBtn.addEventListener('click', () => {
        const visibleCheckboxes = Array.from(targetGroup.querySelectorAll('.recipient-person:not([hidden]) input'));
        const allChecked = visibleCheckboxes.every(cb => cb.checked);

        visibleCheckboxes.forEach(cb => {
          cb.checked = !allChecked;
        });

        selectAllBtn.textContent = allChecked ? 'Pilih Semua' : 'Batalkan Semua';
        updateSelection();
      });
    }

    updateSelection();
  });
});
</script>
@endsection