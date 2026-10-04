@extends('peserta.mainLayout')

@section('content')
<section class="col-12 announcement-history" aria-labelledby="announcement-history-title">
    <a class="exam-history-back" href="/beranda" aria-label="Kembali ke beranda">
		<i class="bi bi-arrow-left" aria-hidden="true"></i>
		Beranda
	</a>
    
	<header class="announcement-history-heading">
		<h1 id="announcement-history-title">Papan Pengumuman &amp; Broadcast Resmi</h1>
		<p>Informasi penting, jadwal verifikasi, dan pemberitahuan resmi dari Panitia Uji Kompetensi</p>
	</header>

	<div class="announcement-history-list">
        @foreach ($activePengumuman as $pengumuman)
			@php
				$penerima = $pengumuman->penerima->firstWhere('peserta_id', auth()->id());
    			$isRead = $penerima?->is_read ?? 0;
			@endphp
            <article class="announcement-history-card {{ $isRead ? 'is-read' : 'is-unread' }}">
                <div class="announcement-history-main">
                    <div class="announcement-history-icon" aria-hidden="true">
                        <i class="bi bi-megaphone"></i>
                    </div>
                    <div class="announcement-history-copy">
                        <div class="announcement-history-title-row">
                            <h2>{{ ucwords($pengumuman->judul) }}</h2>
                            @if ($isRead == 0)
                                <span class="announcement-history-badge">Belum Dibaca</span>
                            @endif
                        </div>
                        <p class="announcement-history-meta">Diterbitkan: {{ $pengumuman->tgl_terbit->translatedFormat('d F Y') }} 
							<span aria-hidden="true">•</span> 
							Oleh Panitia Uji Kompetensi
						</p>
                        <p class="announcement-history-content">{{ $pengumuman->konten }}</p>
                    </div>
                </div>
                @if (!$isRead)
                    <div class="announcement-history-footer">
                        <button class="announcement-history-read-button btn-mark-read" 
							type="button"
							data-id="{{ $pengumuman->id }}"
						>
                            <i class="bi bi-check-circle" aria-hidden="true"></i>
                            Tandai Sudah Dibaca
                        </button>
                    </div>
                @endif
            </article>
        @endforeach
	</div>
</section>
@endsection

@push('scripts')
<script>
	document.addEventListener('click', function (e) {
    const buttonRead = e.target.closest('.btn-mark-read');

    if (buttonRead) {
        const pengumumanId = buttonRead.dataset.id;
        
        const card = buttonRead.closest('.announcement-history-card');

        markAsRead(pengumumanId, card);
    }
});

function markAsRead(id, cardElement) {
    fetch(`/riwayat-pengumuman/${id}/read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && cardElement) {
            cardElement.classList.remove('is-unread');
            cardElement.classList.add('is-read');

            const badge = cardElement.querySelector('.announcement-history-badge');
            if (badge) {
                badge.remove();
            }

            const footer = cardElement.querySelector('.announcement-history-footer');
            if (footer) {
                footer.remove();
            }
        }
    })
    .catch(error => console.error('Error updating read status:', error));
}
</script>
@endpush