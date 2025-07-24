@extends('main')

@section('title', 'Provinsi | Page')

@section('content')

    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Manajemen Provinsi</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Provinsi</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Daftar Provinsi</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary btn-sm" id="btn-add-provinsi">
                            <i class="fas fa-plus"></i> Tambah Provinsi
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <form action="{{ route('provinsi.index') }}" method="GET" class="form-inline">
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
                    <table id="provinsiTable" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Kode Wilayah</th>
                                <th>Nama Provinsi</th>
                                <th>Dibuat Oleh</th>
                                <th>Tanggal Dibuat</th>
                                <th>Diubah Oleh</th>
                                <th>Tanggal Diubah</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($provinsi as $p)
                                <tr id="provinsi-row-{{ $p->provinsi_id }}">
                                    <td>{{ $p->provinsi_id }}</td>
                                    <td>{{ $p->kode_wilayah }}</td>
                                    <td>{{ $p->nama_provinsi }}</td>
                                    <td>{{ $p->creator->nama_lengkap ?? '-' }}</td>
                                    <td>{{ $p->create_date ? $p->create_date->format('d M Y H:i') : '-' }}</td>
                                    <td>{{ $p->changer->nama_lengkap ?? '-' }}</td>
                                    <td>{{ $p->change_date ? $p->change_date->format('d M Y H:i') : '-' }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-info btn-edit" data-id="{{ $p->provinsi_id }}">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <button class="btn btn-sm btn-danger btn-delete" data-id="{{ $p->provinsi_id }}">
                                            <i class="fas fa-trash"></i> Hapus
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-center mt-3">
                        {{ $provinsi->links('pagination::bootstrap-4') }} {{-- Menggunakan pagination bawaan Bootstrap 4 --}}
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal untuk Tambah/Edit Provinsi -->
    <div class="modal fade" id="provinsiModal" tabindex="-1" role="dialog" aria-labelledby="provinsiModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="provinsiModalLabel">Tambah Provinsi Baru</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="provinsiForm">
                    <div class="modal-body">
                        @csrf
                        <input type="hidden" id="provinsi_id" name="provinsi_id">
                        <div class="form-group">
                            <label for="kode_wilayah">Kode Wilayah</label>
                            <input type="text" class="form-control" id="kode_wilayah" name="kode_wilayah" required>
                            <div class="invalid-feedback" id="kode_wilayah-error"></div>
                        </div>
                        <div class="form-group">
                            <label for="nama_provinsi">Nama Provinsi</label>
                            <input type="text" class="form-control" id="nama_provinsi" name="nama_provinsi" required>
                            <div class="invalid-feedback" id="nama_provinsi-error"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary" id="saveProvinsiBtn">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

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
            $('#provinsiTable').DataTable({
                "paging": false, // Nonaktifkan pagination DataTables
                "lengthChange": false,
                "searching": false, // Nonaktifkan searching DataTables
                "ordering": false, // Nonaktifkan ordering DataTables
                "info": false, // Nonaktifkan info DataTables
                "autoWidth": false,
                "responsive": true,
            });

            // Fungsi untuk mereset form dan menghapus pesan validasi
            function resetForm() {
                $('#provinsiForm')[0].reset();
                $('#provinsi_id').val('');
                $('#provinsiModalLabel').text('Tambah Provinsi Baru');
                $('#saveProvinsiBtn').text('Simpan');
                $('.form-control').removeClass('is-invalid');
                $('.invalid-feedback').text('');
            }

            // Buka Modal Tambah Provinsi
            $('#btn-add-provinsi').on('click', function() {
                resetForm();
                $('#provinsiModal').modal('show');
            });

            // Buka Modal Edit Provinsi
            $(document).on('click', '.btn-edit', function() {
                resetForm(); // Reset form terlebih dahulu
                var provinsiId = $(this).data('id');
                $('#provinsiModalLabel').text('Edit Provinsi');
                $('#saveProvinsiBtn').text('Update');
                $('#provinsi_id').val(provinsiId);

                $.ajax({
                    url: '/provinsi/' + provinsiId +
                    '/edit', // Sesuai dengan route resource Laravel
                    method: 'GET',
                    success: function(response) {
                        if (response.success) {
                            var provinsi = response.data;
                            $('#kode_wilayah').val(provinsi.kode_wilayah);
                            $('#nama_provinsi').val(provinsi.nama_provinsi);
                            $('#provinsiModal').modal('show');
                        } else {
                            Swal.fire('Error!', response.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Error!', 'Gagal mengambil data provinsi.', 'error');
                        console.error(xhr.responseText);
                    }
                });
            });

            // Tangani Pengiriman Form (Tambah/Edit)
            $('#provinsiForm').on('submit', function(e) {
                e.preventDefault();

                // Bersihkan pesan validasi sebelumnya
                $('.form-control').removeClass('is-invalid');
                $('.invalid-feedback').text('');

                var provinsiId = $('#provinsi_id').val();
                var url = provinsiId ? '/provinsi/' + provinsiId : '/provinsi';
                var method = provinsiId ? 'PUT' :
                'POST'; // Laravel menggunakan POST dengan _method untuk PUT/PATCH

                var formData = $(this).serialize(); // Serialisasi data form

                $.ajax({
                    url: url,
                    method: 'POST', // Selalu POST untuk Laravel, dengan _method di data
                    data: formData + '&_method=' + method, // Tambahkan _method untuk PUT/PATCH
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Berhasil!', response.message, 'success').then(() => {
                                $('#provinsiModal').modal('hide');
                                // Reload halaman atau perbarui tabel secara dinamis
                                location.reload(); // Reload sederhana untuk saat ini
                            });
                        } else {
                            Swal.fire('Error!', response.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) { // Error validasi
                            var errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                $('#' + key).addClass('is-invalid');
                                $('#' + key + '-error').text(value[0]);
                            });
                            Swal.fire('Validasi Gagal!', 'Mohon periksa kembali input Anda.',
                                'warning');
                        } else {
                            Swal.fire('Error!', 'Terjadi kesalahan saat menyimpan data.',
                                'error');
                            console.error(xhr.responseText);
                        }
                    }
                });
            });

            // Tangani Hapus Provinsi
            $(document).on('click', '.btn-delete', function() {
                var provinsiId = $(this).data('id');
                var $row = $('#provinsi-row-' + provinsiId); // Referensi ke elemen baris tabel

                // Dapatkan token CSRF dari meta tag
                var csrfToken = $('meta[name="csrf-token"]').attr('content');

                // Periksa apakah token CSRF tersedia
                if (!csrfToken) {
                    Swal.fire('Error!',
                        'CSRF Token tidak ditemukan. Pastikan meta tag CSRF ada di layout utama Anda.',
                        'error');
                    console.error('CSRF token meta tag not found.');
                    return; // Hentikan eksekusi jika token tidak ada
                }

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data provinsi ini akan dihapus secara permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/provinsi/' + provinsiId,
                            method: 'POST', // Laravel menggunakan POST untuk DELETE dengan _method field
                            data: {
                                _method: 'DELETE',
                            },
                            headers: { // Pastikan bagian headers ini ada dan benar
                                'X-CSRF-TOKEN': csrfToken // Gunakan variabel csrfToken yang sudah diambil
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire('Dihapus!', response.message, 'success')
                                        .then(() => {
                                            // Hapus baris dari tabel
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
        });
    </script>
@endpush
