@extends('main')

@section('title', 'Permissions | Page')

@section('content')

    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Manajemen Izin</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Permissions</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Daftar Izin</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary btn-sm" id="btn-add-permission">
                            <i class="fas fa-plus"></i> Tambah Izin
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    {{-- Layout berbasis kartu untuk izin --}}
                    <div class="row">
                        <?php
                        // Kelompokkan izin berdasarkan modul_id yang diambil dari database
                        $groupedPermissions = [];
                        foreach ($permissionModules as $module) {
                            $groupedPermissions[$module->name] = [
                                'module_id' => $module->module_id,
                                'permissions' => [],
                            ];
                        }
                        // Tambahkan kategori 'Lain-lain' untuk izin tanpa modul
                        $groupedPermissions['Lain-lain'] = [
                            'module_id' => null, // Atau ID modul 'Lain-lain' jika ada di DB
                            'permissions' => [],
                        ];
                        
                        foreach ($permissions as $permission) {
                            if ($permission->module) {
                                // Jika izin memiliki modul
                                $groupedPermissions[$permission->module->name]['permissions'][] = $permission;
                            } else {
                                // Jika izin tidak memiliki modul (module_id null)
                                $groupedPermissions['Lain-lain']['permissions'][] = $permission;
                            }
                        }
                        
                        // Hapus modul yang tidak memiliki izin jika tidak ingin menampilkannya
                        $groupedPermissions = array_filter($groupedPermissions, function ($moduleData) {
                            return !empty($moduleData['permissions']) || $moduleData['module_id'] === null; // Pertahankan 'Lain-lain' meskipun kosong di awal
                        });
                        ?>

                        @forelse ($groupedPermissions as $moduleName => $moduleData)
                            <div class="col-md-4 mb-3"> {{-- Menggunakan col-md-4 agar ada tiga kolom kartu per baris --}}
                                <div class="card card-primary card-outline h-100"> {{-- h-100 untuk membuat tinggi kartu seragam --}}
                                    <div class="card-header">
                                        <h5 class="card-title">{{ $moduleName }}</h5>
                                    </div>
                                    <div class="card-body">
                                        <ul class="list-unstyled">
                                            @forelse ($moduleData['permissions'] as $permission)
                                                <li id="permission-card-{{ $permission->permission_id }}" class="mb-2">
                                                    <strong>{{ $permission->name }}</strong>
                                                    <p class="text-muted text-sm mb-0">
                                                        {{ $permission->description ?? 'Tidak ada deskripsi.' }}</p>
                                                    <div class="mt-1">
                                                        <button class="btn btn-xs btn-info btn-edit"
                                                            data-id="{{ $permission->permission_id }}">
                                                            <i class="fas fa-edit"></i> Edit
                                                        </button>
                                                        <button class="btn btn-xs btn-danger btn-delete"
                                                            data-id="{{ $permission->permission_id }}">
                                                            <i class="fas fa-trash"></i> Hapus
                                                        </button>
                                                    </div>
                                                </li>
                                                @if (!$loop->last)
                                                    <hr class="my-2"> {{-- Pemisah antar izin dalam satu kartu modul --}}
                                                @endif
                                            @empty
                                                <li>Tidak ada izin di modul ini.</li>
                                            @endforelse
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <p class="text-center">Tidak ada izin yang ditemukan.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal untuk Tambah/Edit Izin -->
    <div class="modal fade" id="permissionModal" tabindex="-1" role="dialog" aria-labelledby="permissionModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="permissionModalLabel">Tambah Izin Baru</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="permissionForm">
                    <div class="modal-body">
                        @csrf
                        <input type="hidden" id="permission_id" name="permission_id">
                        <div class="form-group">
                            <label for="name">Nama Izin</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                            <div class="invalid-feedback" id="name-error"></div>
                        </div>
                        <div class="form-group">
                            <label for="description">Deskripsi Izin</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                            <div class="invalid-feedback" id="description-error"></div>
                        </div>
                        {{-- Tambahkan dropdown untuk memilih Modul Izin --}}
                        <div class="form-group">
                            <label for="module_id">Modul Izin</label>
                            <select class="form-control" id="module_id" name="module_id">
                                <option value="">Pilih Modul (Opsional)</option>
                                @foreach ($permissionModules as $module)
                                    <option value="{{ $module->module_id }}">{{ $module->name }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" id="module_id-error"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary" id="savePermissionBtn">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <!-- SweetAlert2 -->

    <script>
        $(document).ready(function() {
            // DataTables tidak lagi diinisialisasi di sini karena tampilan sudah diubah
            // var table = $('#permissionsTable').DataTable({ /* ... */ });

            // Fungsi untuk mereset form dan menghapus pesan validasi
            function resetForm() {
                $('#permissionForm')[0].reset();
                $('#permission_id').val('');
                $('#permissionModalLabel').text('Tambah Izin Baru');
                $('#savePermissionBtn').text('Simpan');
                $('.form-control').removeClass('is-invalid');
                $('.invalid-feedback').text('');
                $('#module_id').val(''); // Reset dropdown modul
            }

            // Buka Modal Tambah Izin
            $('#btn-add-permission').on('click', function() {
                resetForm();
                $('#permissionModal').modal('show');
            });

            // Buka Modal Edit Izin
            $(document).on('click', '.btn-edit', function() {
                resetForm(); // Reset form terlebih dahulu
                var permissionId = $(this).data('id');
                $('#permissionModalLabel').text('Edit Izin');
                $('#savePermissionBtn').text('Update');
                $('#permission_id').val(permissionId);

                $.ajax({
                    url: '/permissions/' + permissionId +
                        '/edit', // Sesuai dengan route resource Laravel
                    method: 'GET',
                    success: function(response) {
                        if (response.success) {
                            var permission = response.data;
                            $('#name').val(permission.name);
                            $('#description').val(permission.description);
                            $('#module_id').val(permission
                                .module_id); // Set nilai dropdown modul
                            $('#permissionModal').modal('show');
                        } else {
                            Swal.fire('Error!', response.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Error!', 'Gagal mengambil data izin.', 'error');
                        console.error(xhr.responseText);
                    }
                });
            });

            // Tangani Pengiriman Form (Tambah/Edit)
            $('#permissionForm').on('submit', function(e) {
                e.preventDefault();

                // Bersihkan pesan validasi sebelumnya
                $('.form-control').removeClass('is-invalid');
                $('.invalid-feedback').text('');

                var permissionId = $('#permission_id').val();
                var url = permissionId ? '/permissions/' + permissionId : '/permissions';
                var method = permissionId ? 'PUT' :
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
                                $('#permissionModal').modal('hide');
                                // Reload halaman atau perbarui tampilan secara dinamis
                                location
                                    .reload(); // Reload sederhana untuk saat ini
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

            // Tangani Hapus Izin
            $(document).on('click', '.btn-delete', function() {
                var permissionId = $(this).data('id');
                var $permissionCard = $('#permission-card-' +
                    permissionId); // Referensi ke elemen card izin

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
                    text: "Data izin ini akan dihapus secara permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/permissions/' + permissionId,
                            method: 'POST', // Laravel menggunakan POST untuk DELETE dengan _method field
                            data: {
                                _method: 'DELETE',
                                // Baris _token di sini dihapus atau dibiarkan, karena headers lebih diutamakan
                                // _token: csrfToken // Ini bisa dihapus
                            },
                            headers: { // Pastikan bagian headers ini ada dan benar
                                'X-CSRF-TOKEN': csrfToken // Gunakan variabel csrfToken yang sudah diambil
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire('Dihapus!', response.message, 'success')
                                        .then(() => {
                                            // Hapus card izin dari tampilan
                                            $permissionCard.remove();
                                            // Jika ada elemen hr setelahnya, hapus juga
                                            if ($permissionCard.next('hr').length) {
                                                $permissionCard.next('hr').remove();
                                            } else if ($permissionCard.prev('hr')
                                                .length) {
                                                $permissionCard.prev('hr').remove();
                                            }
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
