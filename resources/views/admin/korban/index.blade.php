@extends('main')

@section('title', 'Data Korban | Page')

@section('content')

    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Manajemen Data Korban</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Data Korban</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Daftar Data Korban</h3>
                    <div class="card-tools">
                        {{-- Tombol Tambah Data Korban - Mengarah ke halaman terpisah --}}
                        <a href="{{ route('korban.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Tambah Data Korban
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
                        <form action="{{ route('korban.index') }}" method="GET" class="form-inline">
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
                    <table id="korbanTable" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Bencana</th>
                                <th>Meninggal</th>
                                <th>Luka Berat</th>
                                <th>Luka Ringan</th>
                                <th>Hilang</th>
                                <th>Mengungsi</th>
                                <th>Terdampak</th>
                                <th>Tanggal Input</th>
                                <th>Dibuat Oleh</th>
                                <th>Tanggal Dibuat</th>
                                <th>Diubah Oleh</th>
                                <th>Tanggal Diubah</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($korban as $k)
                                <tr id="korban-row-{{ $k->korban_id }}">
                                    <td>{{ $k->korban_id }}</td>
                                    <td>{{ $k->bencana->nama_bencana ?? '-' }}</td>
                                    <td>{{ number_format($k->meninggal, 0, ',', '.') }}</td>
                                    <td>{{ number_format($k->luka_berat, 0, ',', '.') }}</td>
                                    <td>{{ number_format($k->luka_ringan, 0, ',', '.') }}</td>
                                    <td>{{ number_format($k->hilang, 0, ',', '.') }}</td>
                                    <td>{{ number_format($k->mengungsi, 0, ',', '.') }}</td>
                                    <td>{{ number_format($k->terdampak, 0, ',', '.') }}</td>
                                    <td>{{ $k->tanggal_input ? $k->tanggal_input->format('d M Y H:i') : '-' }}</td>
                                    <td>{{ $k->creator->nama_lengkap ?? '-' }}</td>
                                    <td>{{ $k->create_date ? $k->create_date->format('d M Y H:i') : '-' }}</td>
                                    <td>{{ $k->changer->nama_lengkap ?? '-' }}</td>
                                    <td>{{ $k->change_date ? $k->change_date->format('d M Y H:i') : '-' }}</td>
                                    <td>
                                        <a href="{{ route('korban.edit', $k->korban_id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <button class="btn btn-sm btn-danger btn-delete" data-id="{{ $k->korban_id }}">
                                            <i class="fas fa-trash"></i> Hapus
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-center mt-3">
                        {{ $korban->links('pagination::bootstrap-4') }}
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
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.css"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">

    <script>
        $(document).ready(function() {
            // DataTables hanya untuk styling, bukan pagination/searching bawaan DataTables
            $('#korbanTable').DataTable({
                "paging": false, // Nonaktifkan pagination DataTables
                "lengthChange": false,
                "searching": false, // Nonaktifkan searching DataTables
                "ordering": false, // Nonaktifkan ordering DataTables
                "info": false, // Nonaktifkan info DataTables
                "autoWidth": false,
                "responsive": true,
            });

            // Tangani Hapus Data Korban (tetap via AJAX)
            $(document).on('click', '.btn-delete', function() {
                var korbanId = $(this).data('id');
                var $row = $('#korban-row-' + korbanId); // Referensi ke elemen baris tabel

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
                    text: "Data korban ini akan dihapus secara permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/korban/' + korbanId,
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
                                            table.row($row)
                                                .remove().draw();
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
