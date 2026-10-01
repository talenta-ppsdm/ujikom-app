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
    <table id="pengumumanTable" class="table-custom w-100" data-datatable data-datatable-page-length="10" data-datatable-actions="5">
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
          <td>{{ $p->tgl_terbit }}</td>
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
                  <span class="announcement-detail-badge announcement-detail-target">
                    <i class="bi bi-people" aria-hidden="true"></i>
                    {{ $p->tipe_target === 'semua' ? 'Semua Peserta' : 'Peserta Terpilih' }}
                  </span>
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
                      <div class="announcement-detail-value">{{ $p->penerima_count }} peserta{{ $p->tipe_target === 'semua' ? '' : ' terpilih' }}</div>
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
                        <input type="date" class="form-control" id="tgl_terbit" name="tgl_terbit" value="{{ $p->tgl_terbit }}" required>
                      </div>

                      <div class="col-md-6">
                        <label for="tgl_berakhir" class="form-label">Tanggal Berakhir*</label>
                        <input type="date" class="form-control" id="tgl_berakhir" name="tgl_berakhir" value="{{ $p->berakhir }}">
                      </div>

                      <div class="col-md-12">
                        <label for="judul" class="form-label">Judul*</label>
                        <input type="text" class="form-control" id="judul" name="judul" value="{{ $p->judul }}">
                      </div>

                      <div class="col-md-12">
                        <label for="konten" class="form-label">Konten*</label>
                        <textarea class="form-control-custom" id="konten" name="konten" rows="5" required>{{$p->konten}}</textarea>
                      </div>

                      <!-- Target Penerima -->
                      <div class="col-md-12">
                        <div class="recipient-target">
                          <input type="hidden" name="tipe_target" value="{{ $p->tipe_target }}">
                          <div class="recipient-target-label">Target Penerima</div>
                          <div class="row g-3" role="group" aria-label="Pilih target penerima">
                            
                            <!-- Opsi Semua Peserta -->
                            @php
                              $isAll = $p->tipe_target === \App\Enums\TargetPengumumanEnum::SEMUA->value;
                            @endphp
                            <div class="col-md-6">
                              <button type="button" 
                                      class="recipient-target-option {{ $isAll ? 'is-selected' : '' }}" 
                                      data-recipient-target="all" 
                                      data-target-value="semua" 
                                      aria-pressed="{{ $isAll ? 'true' : 'false' }}">
                                <span class="recipient-target-icon"><i class="bi bi-people"></i></span>
                                <span class="recipient-target-copy">
                                  <span class="recipient-target-title">Semua Peserta</span>
                                  <span class="recipient-target-description">Kirim ke seluruh peserta</span>
                                </span>
                              </button>
                            </div>
                      
                            <!-- Opsi Peserta Terpilih -->
                            <div class="col-md-6">
                              <button type="button" 
                                      class="recipient-target-option {{ !$isAll ? 'is-selected' : '' }}" 
                                      data-recipient-target="selected" 
                                       data-target-value="peserta terpilih" 
                                      aria-pressed="{{ !$isAll ? 'true' : 'false' }}">
                                <span class="recipient-target-icon"><i class="bi bi-person-check"></i></span>
                                <span class="recipient-target-copy">
                                  <span class="recipient-target-title">Peserta Terpilih</span>
                                  <span class="recipient-target-description">Pilih peserta tertentu</span>
                                </span>
                              </button>
                            </div>
                          </div>
                      
                          <!-- Panel Picker Peserta -->
                          <div class="recipient-picker" id="recipientPicker" {{ !$isAll ? '' : 'hidden' }}>
                            <label class="recipient-target-label" for="pesertaSearch">Pilih Peserta</label>
                            <div class="recipient-picker-panel">
                              <div class="recipient-search">
                                <i class="bi bi-search" aria-hidden="true"></i>
                                <input type="search" id="pesertaSearch" placeholder="Cari nama atau email peserta..." autocomplete="off">
                              </div>

                              <div class="recipient-picker-count" aria-live="polite">
                                <span id="selectedPesertaCount">0</span> dari <span id="totalPesertaCount">{{ $peserta->count() }}</span> peserta dipilih
                              </div>

                              <div class="recipient-list" id="recipientList">
                                @forelse($peserta as $itemPeserta)
                                @php
                                  $selectedPesertaIds = $p->penerima->pluck('peserta_id')->toArray();
                                  $isPesertaChecked = in_array($itemPeserta->id, $selectedPesertaIds);
                                @endphp
                                  <label class="recipient-person" data-search="{{ strtolower($itemPeserta->nama . ' ' . $itemPeserta->email) }}">
                                    <input type="checkbox" name="peserta_ids[]" value="{{ $itemPeserta->id }}" {{ $isPesertaChecked ? 'checked' : '' }}>
                                    <span class="recipient-person-copy">
                                      <span class="recipient-person-name">{{ $itemPeserta->nama }}</span>
                                      <span class="recipient-person-email">{{ $itemPeserta->email }}</span>
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
                     <i class="bi bi-check-lg me-1"></i>Ubah Pengumuman
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
        <input type="hidden" name="tipe_target" id="tipeTargetInput" value="semua">
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label for="tgl_terbit" class="form-label">Tanggal Terbit*</label>
              <input type="date" name="tgl_terbit" id="tgl_terbit" class="form-control" required>
            </div>

            <div class="col-md-6">
              <label for="tgl_berakhir" class="form-label">Tanggal Berakhir</label>
                       <input type="date" class="form-control" id="tgl_berakhir" name="tgl_berakhir" value="{{ $p->tgl_berakhir }}">
            </div>

            <div class="col-md-12">
              <label for="judul" class="form-label">Judul*</label>
              <input type="text" class="form-control" id="judul" name="judul" placeholder="Masukkan judul pengumuman" required>
            </div>

            <div class="col-md-12">
              <label for="konten" class="form-label">Konten*</label>
              <textarea class="form-control-custom" id="konten" name="konten" rows="5" required></textarea>
            </div>
            
            <input type="hidden" name="tipe_target" id="tipeTargetInput" value="{{ $p->tipe_target->value ?? $p->tipe_target }}">
            
            <!-- Target Penerima -->
            <div class="col-md-12">
              <div class="recipient-target">
                <div class="recipient-target-label">Target Penerima</div>
                <div class="row g-3" role="group" aria-label="Pilih target penerima">
                  
                  @php
                    $isAll = ($p->tipe_target->value ?? $p->tipe_target) === \App\Enums\TargetPengumumanEnum::SEMUA->value;
                  @endphp
            
                  <!-- Opsi Semua Peserta -->
                  <div class="col-md-6">
                    <button type="button" 
                            class="recipient-target-option {{ $isAll ? 'is-selected' : '' }}" 
                            data-recipient-target="all" 
                            data-target-value="semua" 
                            aria-pressed="{{ $isAll ? 'true' : 'false' }}">
                      <span class="recipient-target-icon"><i class="bi bi-people"></i></span>
                      <span class="recipient-target-copy">
                        <span class="recipient-target-title">Semua Peserta</span>
                        <span class="recipient-target-description">Kirim ke seluruh peserta</span>
                      </span>
                    </button>
                  </div>
            
                  <!-- Opsi Peserta Terpilih -->
                  <div class="col-md-6">
                    <button type="button" 
                            class="recipient-target-option {{ !$isAll ? 'is-selected' : '' }}" 
                            data-recipient-target="selected" 
                            data-target-value="peserta_terpilih" 
                            aria-pressed="{{ !$isAll ? 'true' : 'false' }}">
                      <span class="recipient-target-icon"><i class="bi bi-person-check"></i></span>
                      <span class="recipient-target-copy">
                        <span class="recipient-target-title">Peserta Terpilih</span>
                        <span class="recipient-target-description">Pilih peserta tertentu</span>
                      </span>
                    </button>
                  </div>
                </div>
            
                <!-- Panel Picker Peserta -->
                <div class="recipient-picker" id="recipientPicker" {{ !$isAll ? '' : 'hidden' }}>
                  <label class="recipient-target-label" for="pesertaSearch">Pilih Peserta</label>
                  <div class="recipient-picker-panel">
                    <div class="recipient-search">
                      <i class="bi bi-search" aria-hidden="true"></i>
                      <input type="search" id="pesertaSearch" placeholder="Cari nama atau email peserta..." autocomplete="off">
                    </div>
            
                    <div class="recipient-picker-count" aria-live="polite">
                      <span id="selectedPesertaCount">0</span> dari <span id="totalPesertaCount">{{ $peserta->count() }}</span> peserta dipilih
                    </div>
            
                    <div class="recipient-list" id="recipientList">
                      @php
                        $selectedPesertaIds = $p->penerima->pluck('peserta_id')->toArray();
                      @endphp
            
                      @forelse($peserta as $itemPeserta)
                        @php
                          $isPesertaChecked = in_array($itemPeserta->id, $selectedPesertaIds);
                        @endphp
                        <label class="recipient-person" data-search="{{ strtolower($itemPeserta->nama . ' ' . $itemPeserta->email) }}">
                          <input type="checkbox" name="peserta_ids[]" value="{{ $itemPeserta->id }}" {{ $isPesertaChecked ? 'checked' : '' }}>
                          <span class="recipient-person-copy">
                            <span class="recipient-person-name">{{ $itemPeserta->nama }}</span>
                            <span class="recipient-person-email">{{ $itemPeserta->email }}</span>
                          </span>
                        </label>
                      @empty
                        <p class="recipient-list-empty">Belum ada data peserta.</p>
                      @endforelse
                    </div>
                  </div>
                  <div class="recipient-validation-message" id="recipientValidationMessage" role="status" hidden>Pilih minimal satu peserta.</div>
                </div>
              </div>
            </div>      

          </div>      
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn-custom btn-custom-primary">Simpan</button>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn-custom btn-custom-primary">
            <i class="bi bi-check-lg me-1"></i>Simpan pengumuman
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
      const targetButtons = targetGroup.querySelectorAll('[data-recipient-target]');
      const recipientPicker = targetGroup.querySelector('.recipient-picker');
      const tipeTargetInput = targetGroup.querySelector('[name="tipe_target"]');
      const pesertaSearch = targetGroup.querySelector('input[type="search"]');
      const recipientPeople = targetGroup.querySelectorAll('.recipient-person');
      const selectedPesertaCount = targetGroup.querySelector('.recipient-picker-count span');
      const recipientValidationMessage = targetGroup.querySelector('.recipient-validation-message');

      const updateSelection = () => {
        const selectedCount = targetGroup.querySelectorAll('.recipient-person input:checked').length;
        if (selectedPesertaCount) selectedPesertaCount.textContent = selectedCount;

        if (recipientValidationMessage) {
          const isSelectedTargetMode = recipientPicker && !recipientPicker.hidden;
          recipientValidationMessage.hidden = !isSelectedTargetMode || selectedCount > 0;
        }
      };

      targetButtons.forEach((button) => {
        button.addEventListener('click', () => {
          const isSelectedTarget = button.dataset.recipientTarget === 'selected';
          if (tipeTargetInput) tipeTargetInput.value = button.dataset.targetValue;

          targetButtons.forEach((targetButton) => {
            const isActive = targetButton === button;
            targetButton.classList.toggle('is-selected', isActive);
            targetButton.setAttribute('aria-pressed', String(isActive));
          });

          if (recipientPicker) recipientPicker.hidden = !isSelectedTarget;
          updateSelection();
        });
      });

      pesertaSearch?.addEventListener('input', () => {
        const searchTerm = pesertaSearch.value.trim().toLocaleLowerCase();

        recipientPeople.forEach((person) => {
          person.hidden = !person.dataset.search.includes(searchTerm);
        });
      });

      targetGroup.querySelectorAll('.recipient-person input').forEach((checkbox) => {
        checkbox.addEventListener('change', updateSelection);
      });

      updateSelection();
    });
  });
</script>
@endsection