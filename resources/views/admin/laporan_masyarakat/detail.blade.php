@extends('main')

@section('title', 'Detail Laporan Masyarakat | Page')

@section('content')

    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Detail Laporan Masyarakat</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('laporan-masyarakat.index') }}">Laporan Masyarakat</a>
                        </li>
                        <li class="breadcrumb-item active">Detail</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Informasi Lengkap Laporan #{{ $laporanMasyarakat->laporan_id }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('laporan-masyarakat.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Jenis Laporan:</strong> {{ $laporanMasyarakat->jenis_laporan }}</p>
                            <p><strong>Judul Laporan:</strong> {{ $laporanMasyarakat->judul_laporan }}</p>
                            <p><strong>Deskripsi:</strong></p>
                            <p>{!! nl2br(e($laporanMasyarakat->deskripsi_laporan)) !!}</p> {{-- Menampilkan deskripsi dengan baris baru --}}
                            <p><strong>Tanggal Laporan:</strong>
                                {{ $laporanMasyarakat->tanggal_laporan ? $laporanMasyarakat->tanggal_laporan->format('d M Y H:i') : '-' }}
                            </p>
                            <p><strong>Nama Pelapor:</strong> {{ $laporanMasyarakat->nama_pelapor ?? '-' }}</p>
                            <p><strong>Kontak Pelapor:</strong> {{ $laporanMasyarakat->kontak_pelapor ?? '-' }}</p>
                            <p><strong>Lokasi:</strong>
                                @if ($laporanMasyarakat->latitude && $laporanMasyarakat->longitude)
                                    Lat: {{ $laporanMasyarakat->latitude }}, Long: {{ $laporanMasyarakat->longitude }}
                                    <br>
                                    <a href="https://www.google.com/maps?q={{ $laporanMasyarakat->latitude }},{{ $laporanMasyarakat->longitude }}"
                                        target="_blank" class="text-primary"><i class="fas fa-map-marker-alt"></i> Lihat di
                                        Peta</a>
                                @else
                                    -
                                @endif
                            </p>
                            <p><strong>Bencana Terkait:</strong> {{ $laporanMasyarakat->bencana->nama_bencana ?? '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Status Laporan:</strong>
                                @php
                                    $statusClass = '';
                                    switch ($laporanMasyarakat->status_laporan) {
                                        case 'Pending':
                                            $statusClass = 'badge-warning';
                                            break;
                                        case 'Diterima':
                                            $statusClass = 'badge-info';
                                            break;
                                        case 'Diproses':
                                            $statusClass = 'badge-primary';
                                            break;
                                        case 'Selesai':
                                            $statusClass = 'badge-success';
                                            break;
                                        case 'Ditolak':
                                            $statusClass = 'badge-danger';
                                            break;
                                        default:
                                            $statusClass = 'badge-secondary';
                                            break;
                                    }
                                @endphp
                                <span class="badge {{ $statusClass }}">{{ $laporanMasyarakat->status_laporan }}</span>
                            </p>
                            <p><strong>Dibuat Oleh:</strong> {{ $laporanMasyarakat->creator->name ?? '-' }} pada
                                {{ $laporanMasyarakat->create_date ? $laporanMasyarakat->create_date->format('d M Y H:i') : '-' }}
                            </p>
                            <p><strong>Diubah Oleh:</strong> {{ $laporanMasyarakat->changer->name ?? '-' }} pada
                                {{ $laporanMasyarakat->change_date ? $laporanMasyarakat->change_date->format('d M Y H:i') : '-' }}
                            </p>

                            @if ($laporanMasyarakat->path_foto)
                                <div class="form-group">
                                    <label>Foto Laporan:</label><br>
                                    <a href="{{ asset($laporanMasyarakat->path_foto) }}" target="_blank">
                                        <img src="{{ asset($laporanMasyarakat->path_foto) }}" alt="Foto Laporan"
                                            class="img-fluid" style="max-width: 300px; border-radius: 8px;">
                                    </a>
                                </div>
                            @else
                                <p><strong>Foto Laporan:</strong> Tidak ada foto.</p>
                            @endif

                            <h5 class="mt-4">Ubah Status Laporan:</h5>
                            <div class="d-flex flex-wrap gap-2"> {{-- Gunakan flex-wrap dan gap untuk responsivitas --}}
                                @if ($laporanMasyarakat->status_laporan == 'Pending')
                                    <button class="btn btn-info btn-sm btn-status" data-status="Diterima"
                                        data-id="{{ $laporanMasyarakat->laporan_id }}">
                                        <i class="fas fa-check-circle"></i> Terima
                                    </button>
                                @endif
                                @if ($laporanMasyarakat->status_laporan == 'Diterima' || $laporanMasyarakat->status_laporan == 'Pending')
                                    <button class="btn btn-primary btn-sm btn-status mt-2 mt-md-0" data-status="Diproses"
                                        data-id="{{ $laporanMasyarakat->laporan_id }}">
                                        <i class="fas fa-sync-alt"></i> Proses
                                    </button>
                                @endif
                                @if ($laporanMasyarakat->status_laporan == 'Diproses')
                                    <button class="btn btn-success btn-sm btn-status mt-2 mt-md-0" data-status="Selesai"
                                        data-id="{{ $laporanMasyarakat->laporan_id }}">
                                        <i class="fas fa-flag-checkered"></i> Selesai
                                    </button>
                                @endif
                                @if ($laporanMasyarakat->status_laporan != 'Ditolak' && $laporanMasyarakat->status_laporan != 'Selesai')
                                    <button class="btn btn-danger btn-sm btn-status mt-2 mt-md-0" data-status="Ditolak"
                                        data-id="{{ $laporanMasyarakat->laporan_id }}">
                                        <i class="fas fa-times-circle"></i> Tolak
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // Tangani perubahan status
            $(document).on('click', '.btn-status', function() {
                var laporanId = $(this).data('id');
                var newStatus = $(this).data('status');
                var csrfToken = $('meta[name="csrf-token"]').attr('content');

                Swal.fire({
                    title: 'Konfirmasi Perubahan Status',
                    text: `Anda yakin ingin mengubah status laporan menjadi "${newStatus}"?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Ubah!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/laporan-masyarakat/${laporanId}/status/${newStatus}`,
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire('Berhasil!', response.message, 'success')
                                        .then(() => {
                                            location
                                        .reload(); // Reload halaman untuk melihat perubahan
                                        });
                                } else {
                                    Swal.fire('Error!', response.message, 'error');
                                }
                            },
                            error: function(xhr) {
                                Swal.fire('Error!',
                                    'Terjadi kesalahan saat memperbarui status.',
                                    'error');
                                console.error(xhr.responseText);
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
