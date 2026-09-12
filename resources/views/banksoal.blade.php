@extends('layouts.mainLayout')

@section('title', 'Bank Soal | Sistem Uji Kompetensi')

@section('hero')
<style>
    .answer-option-key.active {
        background-color: #3b9b72 !important;
        color: #ffffff !important;
        border-color: #3b9b72 !important;
    }
    
    .answer-option-input.is-correct {
        border-color: #a3e6cd !important;
        background-color: #f6fbf8 !important;
    }
</style>

<div class="page-header">
    <div>
      <h1 class="page-title">Bank Soal</h1>
      <p class="page-subtitle">Koleksi butir soal standar kompetensi Jabatan Fungsional Penata Kelola Perumahan (0 butir terdaftar)</p>
    </div>
    <button class="btn-custom btn-custom-primary" type="button" data-bs-toggle="modal" data-bs-target="#tambahSoalModal">
      <i class="bi bi-plus-lg me-1"></i>Tambah Soal
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
    <table id="bankSoalTable" class="table-custom w-100" data-datatable data-datatable-page-length="10" data-datatable-actions="4">
      <thead>
        <tr>
          <th>No</th>
          <th>Pertanyaan</th>
          <th>Kategori</th>
          <th>Level & Target Jenjang</th>
          <th class="text-center">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php $no = 1; ?>
        @foreach($bankSoal as $bs)
        <tr>
          <td class="table-order-id">{{ $no++ }}</td>
          <td>
            <div class="table-user-cell">
              <div>
                  <span class="badge-table primary">{{ $bs->kode }}</span>
                <div><b>{{ ucfirst($bs->soal) }}</b></div>
              </div>
            </div>
          </td>
          <td> {{ ucwords($bs->kategori) }}</td>
          <td> {{ $bs->level_name }}</td>
          <td>
            <div class="d-flex justify-content-center gap-1">
              <button class="table-btn-action" title="View Soal" type="button" data-bs-toggle="modal" data-bs-target="#viewBankSoalModal-{{ $bs->id }}"><i class="bi bi-eye"></i></button>

              <button class="table-btn-action" title="Edit Soal" type="button" data-bs-toggle="modal" data-bs-target="#editBankSoalModal-{{ $bs->id }}"><i class="bi bi-pencil"></i></button>

              <form action="{{ route('banksoal.destroy', $bs->id) }}" method="POST" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="table-btn-action delete" title="Delete row" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')"><i class="bi bi-trash"></i></button>
              </form>
            </div>
          </td>
        </tr>

        <!-- Modal View Bank Soal -->
        <div class="modal fade modal-standard" id="viewBankSoalModal-{{ $bs->id }}" tabindex="-1" aria-labelledby="viewBankSoalModalLabel-{{ $bs->id }}" aria-hidden="true">
          <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content bg-white">
              <div class="modal-header px-6 py-4 ">
                <h2 class="modal-title fs-5" id="viewBankSoalModalLabel-{{ $bs->id }}">Detail Butir Soal {{$bs->kode}}</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
              </div>

              <div class="modal-body">
                <div class="d-flex flex-wrap gap-2 mb-4">
                  <span class="badge-table primary">{{ $bs->level_name }}</span>
                  <span class="badge-table primary">{{ ucwords($bs->kategori) }}</span>
                </div>

                <div class="alert-custom alert-custom-primary mb-4">
                  <div class="alert-custom-content">
                    <p class="mb-0">{{ ucfirst($bs->soal) }}</p>
                  </div>
                </div>

                <p><strong>Pilihan Jawaban:</strong></p>
                <div class="answer-options mb-4" role="list" aria-label="Pilihan jawaban">
                  @foreach(['a', 'b', 'c', 'd', 'e'] as $option)
                    @php($isCorrect = $bs->kunci === $option)
                    <div class="answer-option answer-option-view d-flex align-items-start {{ $isCorrect ? 'is-selected' : '' }}" role="listitem">
                      <span class="answer-option-key answer-option-key-view {{ $isCorrect ? 'active' : '' }}" aria-hidden="true">
                        {{ strtoupper($option) }}
                      </span>

                      <div class="row flex-grow-1 g-2">
                        <div class="{{ $isCorrect ? 'col-10' : 'col-12' }}">
                          <div class="alert-custom alert-custom-primary mb-0 {{ $isCorrect ? 'is-correct' : '' }}">
                            {{ $bs->{'jawaban_' . $option} }}
                          </div>
                        </div>

                        @if($isCorrect)
                          <div class="col-2">
                            <div class="alert-custom alert-custom-success mb-0">
                              Kunci Benar
                            </div>
                          </div>
                        @endif
                      </div>
                    </div>
                  @endforeach
                </div>

                <div class="alert-custom alert-custom-info mb-4">
                  <i class="bi bi-lightbulb-fill alert-custom-icon"></i>
                  <div class="alert-custom-content">
                    <p class="mb-0">{{ ucfirst($bs->pembahasan) }}</p>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
              </div>
            </div>
          </div>
        </div>
        <!-- END Modal View Bank Soal -->

        <!-- Modal Edit Bank Soal -->
        <div class="modal fade modal-standard" id="editBankSoalModal-{{ $bs->id }}" tabindex="-1" aria-labelledby="editBankSoalModalLabel-{{ $bs->id }}" aria-hidden="true">
          <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content bg-white">
              <div class="modal-header px-6 py-4 ">
                <h2 class="modal-title fs-5" id="editBankSoalModalLabel-{{ $bs->id }}">Edit Data Bank Soal</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
              </div>
              <form action="{{ route('banksoal.update', $bs->id) }}" method="POST">
                @csrf 
                @method('PUT')
                <div class="modal-body">
                  <div class="row g-3">
                    <div class="col-md-2">
                      <label for="kode" class="form-label">Kode*</label>
                      <input type="text" class="form-control" id="kode" name="kode" value="{{ $bs->kode }}" required>
                    </div>

                    <div class="col-md-5">
                      <label for="kategori" class="form-label">Kategori*</label>
                      <select class="form-control form-select-custom" id="kategori" name="kategori" required>
                        <option disabled {{ old('kategori', $bs->kategori ?? '') == '' ? 'selected' : '' }}>
                          Pilih Kategori soal...
                        </option>
        
                        @foreach($categories as $categoryValue => $categoryLabel)
                          <option value="{{ $categoryValue }}" {{ old('kategori', $bs->kategori ?? '') == $categoryValue ? 'selected' : '' }}>
                              {{ $categoryLabel }}
                            </option>
                        @endforeach
                      </select>
                    </div>

                    <div class="col-md-5">
                      <label for="level" class="form-label">Level*</label>
                      <select class="form-control form-select-custom" id="level" name="level" required>
                        <option disabled {{ old('level', $bs->level ?? '') == '' ? 'selected' : '' }}>
                          Pilih Level...
                        </option>
        
                        @foreach($levels as $levelValue => $levelLabel)
                            <option value="{{ $levelValue }}" {{ old('level', $bs->level ?? '') == $levelValue ? 'selected' : '' }}>
                                {{ $levelLabel }}
                            </option>
                        @endforeach
                      </select>
                    </div>

                    <div class="col-md-2">
                      <label for="poin" class="form-label">Poin*</label>
                      <input type="number" class="form-control" id="poin" name="poin" value="{{ old('poin', $bs->poin ?? 2) }}" min="1" required>
                    </div>

                    <div class="col-md-12">
                      <label for="soal" class="form-label">Pertanyaan*</label>
                      <textarea class="form-control-custom" id="soal" name="soal" rows="3" required>{{  $bs->soal }}</textarea>
                    </div>

                    <div class="col-md-12">
                      <label class="form-label">Pilihan Jawaban*</label>
                       
                      <input type="hidden" name="kunci" id="kunci" value="{{ old('kunci', $bs->kunci) }}" required>
                    
                      <div class="answer-options" role="group" aria-label="Pilihan jawaban">
                        @foreach(['a', 'b', 'c', 'd', 'e'] as $option)
                          <div class="answer-option d-flex align-items-center mb-2">
                            <button type="button" class="btn answer-option-key me-2 {{ old('kunci', $bs->kunci) === $option ? 'active' : '' }}" data-answer-key="{{ $option }}" aria-label="Pilih jawaban {{ strtoupper($option) }}">
                              {{ strtoupper($option) }}
                            </button>
                            
                            <input type="text" class="form-control answer-option-input me-2 {{ old('kunci', $bs->kunci) === $option ? 'is-correct' : '' }}" id="jawaban_{{ $option }}" name="jawaban_{{ $option }}" value="{{ old('jawaban_' . $option, $bs->{'jawaban_' . $option}) }}" maxlength="255" required>
                            
                            <span class="badge bg-success-subtle text-success border border-success answer-option-correct {{ old('kunci', $bs->kunci) === $option ? '' : 'd-none' }}">
                              Kunci Benar
                            </span>
                          </div>
                        @endforeach
                      </div>

                      <div class="col-md-12 mt-3">
                        <label for="pembahasan" class="form-label">Pembahasan*</label>
                        <textarea class="form-control-custom" id="pembahasan" name="pembahasan" rows="3">{{ $bs->pembahasan }}</textarea>
                      </div>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                  <button type="submit" class="btn-custom btn-custom-primary">Update Soal</button>
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


<!-- Modal Tambah Soal -->
<div class="modal fade modal-standard" id="tambahSoalModal" tabindex="-1" aria-labelledby="tambahSoalModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content bg-white">
      <div class="modal-header px-6 py-4 ">
        <h2 class="modal-title fs-5" id="tambahSoalModalLabel">Tambah Soal</h2>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>
      <form action="{{ route('banksoal.store') }}" method="POST">
        @csrf 
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-2">
                <label for="kode" class="form-label">Kode*</label>
                <input type="text" class="form-control" id="kode" name="kode" value="{{ $newCode }}" readonly required>
            </div>

            <div class="col-md-5">
              <label for="kategori" class="form-label">Kategori*</label>
              <select class="form-control form-select-custom" id="kategori" name="kategori" required>
                <option selected disabled>Pilih Kategori soal...</option>

                @foreach($categories as $categoryValue => $categoryLabel)
                  <option value="{{ $categoryValue }}">{{ $categoryLabel }}</option>
                @endforeach
              </select>
            </div>

            <div class="col-md-5">
              <label for="level" class="form-label">Level*</label>
              <select class="form-control form-select-custom" id="level" name="level" required>
                <option selected disabled>Pilih Level...</option>

                @foreach($levels as $levelValue => $levelLabel)
                    <option value="{{ $levelValue }}">{{ $levelLabel }}</option>
                @endforeach
              </select>
            </div>

            <div class="col-md-2">
              <label for="poin" class="form-label">Poin*</label>
              <input type="number" class="form-control" id="poin" name="poin" value="{{ old('poin', 2) }}" min="1" required>
            </div>

            <div class="col-md-12">
              <label for="soal" class="form-label">Pertanyaan*</label>
              <textarea class="form-control-custom" id="soal" name="soal" rows="3" placeholder="Tuliskan soal atau kasus teknis..." required></textarea>
            </div>

            <div class="col-md-12">
                <label class="form-label">Pilihan Jawaban*</label>
                
                <input type="hidden" name="kunci" id="kunci" required>
              
                <div class="answer-options" role="group" aria-label="Pilihan jawaban">
                  @foreach(['a', 'b', 'c', 'd', 'e'] as $option)
                    <div class="answer-option d-flex align-items-center mb-2">
                      <button type="button" class="btn answer-option-key me-2" data-answer-key="{{ $option }}" aria-label="Pilih jawaban {{ strtoupper($option) }}">
                        {{ strtoupper($option) }}
                      </button>
                      
                      <input type="text" class="form-control answer-option-input me-2" id="jawaban_{{ $option }}" name="jawaban_{{ $option }}" placeholder="Teks Pilihan Jawaban {{ strtoupper($option) }}..." maxlength="255" required>
                      
                      <span class="badge bg-success-subtle text-success border border-success answer-option-correct d-none">
                        Kunci Benar
                      </span>
                    </div>
                  @endforeach
                </div>
            </div>

            <div class="col-md-12">
              <label for="pembahasan" class="form-label">Pembahasan*</label>
              <textarea class="form-control-custom" id="pembahasan" name="pembahasan" rows="3" placeholder="Tuliskan pembahasan soal..."></textarea>
            </div>
          </div>      
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn-custom btn-custom-primary">Simpan Soal</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- END Modal Tambah Soal -->

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const keys = document.querySelectorAll('.answer-option-key');
    const inputKunci = document.getElementById('kunci');

    keys.forEach(button => {
        button.addEventListener('click', function () {
            keys.forEach(btn => {
                btn.classList.remove('active');
                
                const parent = btn.closest('.answer-option');
                const badge = parent.querySelector('.answer-option-correct');
                const input = parent.querySelector('.answer-option-input');

                if (badge) badge.classList.add('d-none');
                if (input) input.classList.remove('is-correct');
            });

            this.classList.add('active');

            const currentParent = this.closest('.answer-option');
            const currentBadge = currentParent.querySelector('.answer-option-correct');
            const currentInput = currentParent.querySelector('.answer-option-input');

            if (currentBadge) currentBadge.classList.remove('d-none');
            if (currentInput) currentInput.classList.add('is-correct');

            const selectedKey = this.getAttribute('data-answer-key');
            inputKunci.value = selectedKey;
        });
    });
});
</script>
@endsection