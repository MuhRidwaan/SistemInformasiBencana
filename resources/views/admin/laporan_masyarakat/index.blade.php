@extends('main')

@section('title', 'Laporan Masyarakat | Page')

@section('content')

    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Manajemen Laporan Masyarakat</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Laporan Masyarakat</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Daftar Laporan Masyarakat</h3>
                    <div class="card-tools">
                        {{-- Tombol Tambah Laporan Masyarakat - Mengarah ke halaman terpisah --}}
                        <a href="{{ route('laporan-masyarakat.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Tambah Laporan
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    {{-- Menampilkan pesan flash (success/error) dari controller --}}
                    @if (Session::has('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ Session::get('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                    @if (Session::has('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ Session::get('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <div class="mb-3">
                        <form action="{{ route('laporan-masyarakat.index') }}" method="GET" class="form-inline">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control float-right" placeholder="Cari..."
                                    value="{{ request('search') }}">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-default">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <table id="laporanMasyarakatTable" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th style="width: 50px;">ID</th>
                                <th>Jenis Laporan</th>
                                <th>Judul Laporan</th>
                                <th>Tanggal Laporan</th>
                                <th>Pelapor</th>
                                <th style="width: 120px;">Status</th>
                                <th style="width: 150px;">Aksi</th> {{-- Lebarkan kolom aksi untuk 3 tombol --}}
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($laporanMasyarakat as $lm)
                                <tr id="laporan-masyarakat-row-{{ $lm->laporan_id }}">
                                    <td>{{ $lm->laporan_id }}</td>
                                    <td>{{ $lm->jenis_laporan }}</td>
                                    <td>{{ $lm->judul_laporan }}</td>
                                    <td>{{ $lm->tanggal_laporan ? $lm->tanggal_laporan->format('d M Y H:i') : '-' }}</td>
                                    <td>{{ $lm->nama_pelapor ?? '-' }}</td>
                                    <td>
                                        @php
                                            $statusClass = '';
                                            switch ($lm->status_laporan) {
                                                case 'Pending':
                                                    $statusClass = 'badge-warning';
                                                    break;
                                                case 'Diterima':
                                                    $statusClass = 'badge-info';
                                                    break;
                                                case 'Diproses': // Tambahkan kasus untuk Diproses
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
                                        <span class="badge {{ $statusClass }}">{{ $lm->status_laporan }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('laporan-masyarakat.show', $lm->laporan_id) }}"
                                            class="btn btn-sm btn-primary mt-1">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('laporan-masyarakat.edit', $lm->laporan_id) }}"
                                            class="btn btn-sm btn-info mt-1"> {{-- Tambahkan mt-1 untuk jarak --}}
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button class="btn btn-sm btn-danger btn-delete mt-1"
                                            data-id="{{ $lm->laporan_id }}"> {{-- Tambahkan mt-1 --}}
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-center mt-3">
                        {{ $laporanMasyarakat->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection

@push('scripts')
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- DataTables (tidak digunakan untuk pagination, hanya untuk styling tabel) -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">

    <script>
        $(document).ready(function() {
            // DataTables hanya untuk styling, bukan pagination/searching bawaan DataTables
            $('#laporanMasyarakatTable').DataTable({
                "paging": false, // Nonaktifkan pagination DataTables
                "lengthChange": false,
                "searching": false, // Nonaktifkan searching DataTables
                "ordering": false, // Nonaktifkan ordering DataTables
                "info": false, // Nonaktifkan info DataTables
                "autoWidth": false,
                "responsive": true,
            });

            // Tangani Hapus Laporan Masyarakat (tetap via AJAX)
            $(document).on('click', '.btn-delete', function() {
                var laporanId = $(this).data('id');
                var $row = $('#laporan-masyarakat-row-' + laporanId); // Referensi ke elemen baris tabel

                var csrfToken = $('meta[name="csrf-token"]').attr('content');
                if (!csrfToken) {
                    Swal.fire('Error!',
                        'CSRF Token tidak ditemukan. Pastikan meta tag CSRF ada di layout utama Anda.',
                        'error');
                    console.error('CSRF token meta tag not found.');
                    return;
                }

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data laporan masyarakat ini akan dihapus secara permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/laporan-masyarakat/' + laporanId,
                            method: 'POST', // Laravel menggunakan POST untuk DELETE dengan _method field
                            data: {
                                _method: 'DELETE',
                            },
                            headers: {
                                'X-CSRF-TOKEN': csrfToken
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire('Dihapus!', response.message, 'success')
                                        .then(() => {
                                            $row.remove(); // Hapus baris dari DOM
                                            // table.row($row).remove().draw(); // Jika menggunakan DataTable, ini lebih baik
                                        });
                                } else {
                                    Swal.fire('Error!', response.message, 'error');
                                }
                            },
                            error: function(xhr) {
                                Swal.fire('Error!',
                                    'Terjadi kesalahan saat menghapus data.',
                                    'error');
                                console.error(xhr.responseText);
                            }
                        });
                    }
                });
            });

            // Tampilkan SweetAlert jika ada pesan success/error dari session
            @if (Session::has('success'))
                Swal.fire('Berhasil!', '{{ Session::get('success') }}', 'success');
            @endif

            @if (Session::has('error'))
                Swal.fire('Error!', '{{ Session::get('error') }}', 'error');
            @endif
        });
    </script>
@endpush
