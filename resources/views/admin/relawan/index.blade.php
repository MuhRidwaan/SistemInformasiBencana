@extends('main')

@section('title', 'Relawan | Page')

@section('content')

    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Manajemen Relawan</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Relawan</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Daftar Relawan</h3>
                    <div class="card-tools">
                        <a href="{{ route('relawan.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Lengkapi Profil Relawan
                        </a>
                    </div>
                </div>
                <div class="card-body">
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
                        <form action="{{ route('relawan.index') }}" method="GET" class="form-inline">
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
                    <table id="relawanTable" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>ID Relawan</th>
                                <th>Nama Lengkap</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Kontak</th>
                                <th>Peran</th>
                                <th>Status User</th>
                                <th>Keahlian</th>
                                <th>Organisasi</th>
                                <th>Dibuat Oleh</th>
                                <th>Tanggal Dibuat</th>
                                <th>Diubah Oleh</th>
                                <th>Tanggal Diubah</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($relawan as $r)
                                <tr id="relawan-row-{{ $r->relawan_id }}">
                                    <td>{{ $r->relawan_id }}</td>
                                    <td>{{ $r->user->nama_lengkap ?? '-' }}</td>
                                    <td>{{ $r->user->username ?? '-' }}</td>
                                    <td>{{ $r->user->email ?? '-' }}</td>
                                    <td>{{ $r->user->kontak ?? '-' }}</td>
                                    <td>{{ $r->user->role->nama_role ?? '-' }}</td>
                                    <td>
                                        @if ($r->user->is_active ?? false)
                                            <span class="badge badge-success">Aktif</span>
                                        @else
                                            <span class="badge badge-danger">Tidak Aktif</span>
                                        @endif
                                    </td>
                                    <td>{{ $r->keahlian ?? '-' }}</td>
                                    <td>{{ $r->organisasi ?? '-' }}</td>
                                    <td>{{ $r->creator->nama_lengkap ?? '-' }}</td>
                                    <td>{{ $r->create_date ? $r->create_date->format('d M Y H:i') : '-' }}</td>
                                    <td>{{ $r->changer->nama_lengkap ?? '-' }}</td>
                                    <td>{{ $r->change_date ? $r->change_date->format('d M Y H:i') : '-' }}</td>
                                    <td>
                                        <a href="{{ route('relawan.edit', $r->relawan_id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <button class="btn btn-sm btn-danger btn-delete" data-id="{{ $r->relawan_id }}">
                                            <i class="fas fa-trash"></i> Hapus
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-center mt-3">
                        {{ $relawan->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">

    <script>
        $(document).ready(function() {
            $('#relawanTable').DataTable({
                "paging": false,
                "lengthChange": false,
                "searching": false,
                "ordering": false,
                "info": false,
                "autoWidth": false,
                "responsive": true,
            });

            $(document).on('click', '.btn-delete', function() {
                var relawanId = $(this).data('id');
                var $row = $('#relawan-row-' + relawanId);

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
                    text: "Profil relawan ini akan dihapus permanen! User terkait TIDAK akan dihapus.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/relawan/' + relawanId,
                            method: 'POST',
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
                                            $row.remove();
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

            @if (Session::has('success'))
                Swal.fire('Berhasil!', '{{ Session::get('success') }}', 'success');
            @endif

            @if (Session::has('error'))
                Swal.fire('Error!', '{{ Session::get('error') }}', 'error');
            @endif
        });
    </script>
@endpush
