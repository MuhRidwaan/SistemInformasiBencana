@extends('main')

@section('title', 'Permission Modules | Page')

@section('content')

    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Manajemen Modul Izin</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Permission Modules</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Daftar Modul Izin</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary btn-sm" id="btn-add-module">
                            <i class="fas fa-plus"></i> Tambah Modul
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <table id="permissionModulesTable" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nama Modul</th>
                                <th>Deskripsi</th>
                                <th>Dibuat Pada</th>
                                <th>Diperbarui Pada</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($permissionModules as $module)
                                <tr id="module-row-{{ $module->module_id }}">
                                    <td>{{ $module->module_id }}</td>
                                    <td>{{ $module->name }}</td>
                                    <td>{{ $module->description ?? '-' }}</td>
                                    <td>{{ $module->created_at ? $module->created_at->format('d M Y H:i') : '-' }}</td>
                                    <td>{{ $module->updated_at ? $module->updated_at->format('d M Y H:i') : '-' }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-info btn-edit" data-id="{{ $module->module_id }}">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <button class="btn btn-sm btn-danger btn-delete" data-id="{{ $module->module_id }}">
                                            <i class="fas fa-trash"></i> Hapus
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal untuk Tambah/Edit Modul Izin -->
    <div class="modal fade" id="permissionModuleModal" tabindex="-1" role="dialog"
        aria-labelledby="permissionModuleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="permissionModuleModalLabel">Tambah Modul Izin Baru</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="permissionModuleForm">
                    <div class="modal-body">
                        @csrf
                        <input type="hidden" id="module_id" name="module_id">
                        <div class="form-group">
                            <label for="name">Nama Modul</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                            <div class="invalid-feedback" id="name-error"></div>
                        </div>
                        <div class="form-group">
                            <label for="description">Deskripsi Modul</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                            <div class="invalid-feedback" id="description-error"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary" id="saveModuleBtn">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Inisialisasi DataTables
            var table = $('#permissionModulesTable').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
            });

            // Fungsi untuk mereset form dan menghapus pesan validasi
            function resetForm() {
                $('#permissionModuleForm')[0].reset();
                $('#module_id').val('');
                $('#permissionModuleModalLabel').text('Tambah Modul Izin Baru');
                $('#saveModuleBtn').text('Simpan');
                $('.form-control').removeClass('is-invalid');
                $('.invalid-feedback').text('');
            }

            // Buka Modal Tambah Modul Izin
            $('#btn-add-module').on('click', function() {
                resetForm();
                $('#permissionModuleModal').modal('show');
            });

            // Buka Modal Edit Modul Izin
            $(document).on('click', '.btn-edit', function() {
                resetForm(); // Reset form terlebih dahulu
                var moduleId = $(this).data('id');
                $('#permissionModuleModalLabel').text('Edit Modul Izin');
                $('#saveModuleBtn').text('Update');
                $('#module_id').val(moduleId);

                $.ajax({
                    url: '/permission-modules/' + moduleId +
                        '/edit', // Sesuai dengan route resource Laravel
                    method: 'GET',
                    success: function(response) {
                        if (response.success) {
                            var module = response.data;
                            $('#name').val(module.name);
                            $('#description').val(module.description);
                            $('#permissionModuleModal').modal('show');
                        } else {
                            Swal.fire('Error!', response.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Error!', 'Gagal mengambil data modul izin.', 'error');
                        console.error(xhr.responseText);
                    }
                });
            });

            // Tangani Pengiriman Form (Tambah/Edit)
            $('#permissionModuleForm').on('submit', function(e) {
                e.preventDefault();

                // Bersihkan pesan validasi sebelumnya
                $('.form-control').removeClass('is-invalid');
                $('.invalid-feedback').text('');

                var moduleId = $('#module_id').val();
                var url = moduleId ? '/permission-modules/' + moduleId : '/permission-modules';
                var method = moduleId ? 'PUT' :
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
                                $('#permissionModuleModal').modal('hide');
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

            // Tangani Hapus Modul Izin
            $(document).on('click', '.btn-delete', function() {
                var moduleId = $(this).data('id');
                var $moduleRow = $('#module-row-' + moduleId); // Referensi ke elemen baris tabel

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
                    text: "Data modul izin ini akan dihapus secara permanen! Pastikan tidak ada izin yang terhubung.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/permission-modules/' + moduleId,
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
                                            // Hapus baris dari DataTables
                                            table.row($moduleRow)
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
