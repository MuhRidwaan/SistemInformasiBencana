@extends('main')

@section('title', 'Kota/Kabupaten | Page')

@section('content')

    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Manajemen Kota/Kabupaten</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Kota/Kabupaten</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Daftar Kota/Kabupaten</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary btn-sm" id="btn-add-kota">
                            <i class="fas fa-plus"></i> Tambah Kota/Kabupaten
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <form action="{{ route('kota.index') }}" method="GET" class="form-inline">
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
                    <table id="kotaTable" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Provinsi</th>
                                <th>Kode Wilayah</th>
                                <th>Nama Kota/Kabupaten</th>
                                <th>Dibuat Oleh</th>
                                <th>Tanggal Dibuat</th>
                                <th>Diubah Oleh</th>
                                <th>Tanggal Diubah</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($kota as $k)
                                <tr id="kota-row-{{ $k->kota_id }}">
                                    <td>{{ $k->kota_id }}</td>
                                    <td>{{ $k->provinsi->nama_provinsi ?? '-' }}</td> {{-- Tampilkan nama provinsi --}}
                                    <td>{{ $k->kode_wilayah }}</td>
                                    <td>{{ $k->nama_kota }}</td>
                                    <td>{{ $k->creator->nama_lengkap ?? '-' }}</td>
                                    <td>{{ $k->create_date ? $k->create_date->format('d M Y H:i') : '-' }}</td>
                                    <td>{{ $k->changer->nama_lengkap ?? '-' }}</td>
                                    <td>{{ $k->change_date ? $k->change_date->format('d M Y H:i') : '-' }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-info btn-edit" data-id="{{ $k->kota_id }}">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <button class="btn btn-sm btn-danger btn-delete" data-id="{{ $k->kota_id }}">
                                            <i class="fas fa-trash"></i> Hapus
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-center mt-3">
                        {{ $kota->links('pagination::bootstrap-4') }} {{-- Menggunakan pagination bawaan Bootstrap 4 --}}
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal untuk Tambah/Edit Kota/Kabupaten -->
    <div class="modal fade" id="kotaModal" tabindex="-1" role="dialog" aria-labelledby="kotaModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="kotaModalLabel">Tambah Kota/Kabupaten Baru</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="kotaForm">
                    <div class="modal-body">
                        @csrf
                        <input type="hidden" id="kota_id" name="kota_id">
                        <div class="form-group">
                            <label for="provinsi_id">Provinsi</label>
                            <select class="form-control" id="provinsi_id" name="provinsi_id" required>
                                <option value="">Pilih Provinsi</option>
                                @foreach ($provinsiList as $provinsi)
                                    <option value="{{ $provinsi->provinsi_id }}">{{ $provinsi->nama_provinsi }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" id="provinsi_id-error"></div>
                        </div>
                        <div class="form-group">
                            <label for="kode_wilayah">Kode Wilayah</label>
                            <input type="text" class="form-control" id="kode_wilayah" name="kode_wilayah" required>
                            <div class="invalid-feedback" id="kode_wilayah-error"></div>
                        </div>
                        <div class="form-group">
                            <label for="nama_kota">Nama Kota/Kabupaten</label>
                            <input type="text" class="form-control" id="nama_kota" name="nama_kota" required>
                            <div class="invalid-feedback" id="nama_kota-error"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary" id="saveKotaBtn">Simpan</button>
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
            $('#kotaTable').DataTable({
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
                $('#kotaForm')[0].reset();
                $('#kota_id').val('');
                $('#kotaModalLabel').text('Tambah Kota/Kabupaten Baru');
                $('#saveKotaBtn').text('Simpan');
                $('.form-control').removeClass('is-invalid');
                $('.invalid-feedback').text('');
                $('#provinsi_id').val(''); // Reset dropdown provinsi
            }

            // Buka Modal Tambah Kota/Kabupaten
            $('#btn-add-kota').on('click', function() {
                resetForm();
                $('#kotaModal').modal('show');
            });

            // Buka Modal Edit Kota/Kabupaten
            $(document).on('click', '.btn-edit', function() {
                resetForm(); // Reset form terlebih dahulu
                var kotaId = $(this).data('id');
                $('#kotaModalLabel').text('Edit Kota/Kabupaten');
                $('#saveKotaBtn').text('Update');
                $('#kota_id').val(kotaId);

                $.ajax({
                    url: '/kota/' + kotaId + '/edit', // Sesuai dengan route resource Laravel
                    method: 'GET',
                    success: function(response) {
                        if (response.success) {
                            var kota = response.data;
                            $('#provinsi_id').val(kota
                            .provinsi_id); // Set nilai dropdown provinsi
                            $('#kode_wilayah').val(kota.kode_wilayah);
                            $('#nama_kota').val(kota.nama_kota);
                            $('#kotaModal').modal('show');
                        } else {
                            Swal.fire('Error!', response.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Error!', 'Gagal mengambil data kota/kabupaten.', 'error');
                        console.error(xhr.responseText);
                    }
                });
            });

            // Tangani Pengiriman Form (Tambah/Edit)
            $('#kotaForm').on('submit', function(e) {
                e.preventDefault();

                // Bersihkan pesan validasi sebelumnya
                $('.form-control').removeClass('is-invalid');
                $('.invalid-feedback').text('');

                var kotaId = $('#kota_id').val();
                var url = kotaId ? '/kota/' + kotaId : '/kota';
                var method = kotaId ? 'PUT' :
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
                                $('#kotaModal').modal('hide');
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

            // Tangani Hapus Kota/Kabupaten
            $(document).on('click', '.btn-delete', function() {
                var kotaId = $(this).data('id');
                var $row = $('#kota-row-' + kotaId); // Referensi ke elemen baris tabel

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
                    text: "Data kota/kabupaten ini akan dihapus secara permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/kota/' + kotaId,
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
