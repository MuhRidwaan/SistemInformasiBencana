@extends('main')

@section('title', 'Roles | Page')

@section('content')

    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Manajemen Peran</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Roles</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Daftar Peran</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary btn-sm" id="btn-add-role">
                            <i class="fas fa-plus"></i> Tambah Peran
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <table id="rolesTable" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nama Peran</th>
                                <th>Deskripsi</th>
                                <th>Izin</th> <!-- Tambah kolom Izin -->
                                <th>Dibuat Oleh</th>
                                <th>Tanggal Dibuat</th>
                                <th>Diubah Oleh</th>
                                <th>Tanggal Diubah</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($roles as $role)
                                <tr id="role-row-{{ $role->role_id }}">
                                    <td>{{ $role->role_id }}</td>
                                    <td>{{ $role->nama_role }}</td>
                                    <td>{{ $role->deskripsi_role ?? '-' }}</td>
                                    <td>
                                        {{-- Logika untuk mengelompokkan izin berdasarkan modul secara dinamis --}}
                                        <?php
                                        $groupedPermissionsInTable = [];
                                        foreach ($role->permissions as $permission) {
                                            $moduleName = $permission->module->name ?? 'Lain-lain'; // Ambil nama modul dari relasi
                                            $groupedPermissionsInTable[$moduleName][] = $permission->name;
                                        }
                                        // Sortir berdasarkan nama modul agar tampilan konsisten
                                        ksort($groupedPermissionsInTable);
                                        ?>
                                        @forelse ($groupedPermissionsInTable as $moduleName => $perms)
                                            <strong>{{ $moduleName }}:</strong>
                                            @foreach ($perms as $permName)
                                                <span class="badge badge-secondary">{{ $permName }}</span>
                                            @endforeach
                                            <br>
                                        @empty
                                            -
                                        @endforelse
                                    </td>
                                    <td>{{ $role->creator->nama_lengkap ?? '-' }}</td>
                                    <td>{{ $role->create_date ? $role->create_date->format('d M Y H:i') : '-' }}</td>
                                    <td>{{ $role->changer->nama_lengkap ?? '-' }}</td>
                                    <td>{{ $role->change_date ? $role->change_date->format('d M Y H:i') : '-' }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-info btn-edit" data-id="{{ $role->role_id }}">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <button class="btn btn-sm btn-danger btn-delete" data-id="{{ $role->role_id }}">
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

    <!-- Modal untuk Tambah/Edit Role -->
    <div class="modal fade" id="roleModal" tabindex="-1" role="dialog" aria-labelledby="roleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="roleModalLabel">Tambah Peran Baru</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="roleForm">
                    <div class="modal-body">
                        @csrf
                        <input type="hidden" id="role_id" name="role_id">
                        <div class="form-group">
                            <label for="nama_role">Nama Peran</label>
                            <input type="text" class="form-control" id="nama_role" name="nama_role" required>
                            <div class="invalid-feedback" id="nama_role-error"></div>
                        </div>
                        <div class="form-group">
                            <label for="deskripsi_role">Deskripsi Peran</label>
                            <textarea class="form-control" id="deskripsi_role" name="deskripsi_role" rows="3"></textarea>
                            <div class="invalid-feedback" id="deskripsi_role-error"></div>
                        </div>

                        <!-- Bagian untuk memilih Izin (Permissions) - Dikelompokkan per Modul -->
                        <div class="form-group">
                            <label>Izin (Permissions)</label>
                            <div class="accordion" id="permissionsAccordion">
                                <?php
                                // Kelompokkan semua izin berdasarkan modulnya untuk tampilan accordion
                                $allPermissionsGroupedByModule = [];
                                foreach ($permissionModules as $module) {
                                    $allPermissionsGroupedByModule[$module->name] = [
                                        'module_id' => $module->module_id,
                                        'permissions' => [],
                                    ];
                                }
                                // Tambahkan kategori 'Lain-lain' untuk izin tanpa modul
                                $allPermissionsGroupedByModule['Lain-lain'] = [
                                    'module_id' => null,
                                    'permissions' => [],
                                ];
                                
                                foreach ($permissions as $permission) {
                                    if ($permission->module) {
                                        $allPermissionsGroupedByModule[$permission->module->name]['permissions'][] = $permission;
                                    } else {
                                        $allPermissionsGroupedByModule['Lain-lain']['permissions'][] = $permission;
                                    }
                                }
                                // Hapus modul yang tidak memiliki izin jika tidak ingin menampilkannya
                                $allPermissionsGroupedByModule = array_filter($allPermissionsGroupedByModule, function ($moduleData) {
                                    return !empty($moduleData['permissions']) || $moduleData['module_id'] === null;
                                });
                                ksort($allPermissionsGroupedByModule); // Urutkan berdasarkan nama modul
                                ?>
                                @foreach ($allPermissionsGroupedByModule as $moduleName => $moduleData)
                                    <div class="card card-primary card-outline">
                                        <div class="card-header">
                                            <h4 class="card-title w-100">
                                                <a class="d-block w-100" data-toggle="collapse"
                                                    href="#collapse-{{ Str::slug($moduleName) }}">
                                                    {{ $moduleName }}
                                                </a>
                                            </h4>
                                        </div>
                                        <div id="collapse-{{ Str::slug($moduleName) }}" class="collapse"
                                            data-parent="#permissionsAccordion">
                                            <div class="card-body row">
                                                @foreach ($moduleData['permissions'] as $permission)
                                                    <div class="col-md-6">
                                                        <div class="form-check">
                                                            <input class="form-check-input permission-checkbox"
                                                                type="checkbox" name="permissions[]"
                                                                value="{{ $permission->permission_id }}"
                                                                id="permission-{{ $permission->permission_id }}">
                                                            <label class="form-check-label"
                                                                for="permission-{{ $permission->permission_id }}">
                                                                {{ $permission->name }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="invalid-feedback" id="permissions-error"></div>
                        </div>
                        <!-- End Bagian Izin -->

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary" id="saveRoleBtn">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">

    <script>
        $(document).ready(function() {
            // Inisialisasi DataTables
            var table = $('#rolesTable').DataTable({
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
                $('#roleForm')[0].reset();
                $('#role_id').val('');
                $('#roleModalLabel').text('Tambah Peran Baru');
                $('#saveRoleBtn').text('Simpan');
                $('.form-control').removeClass('is-invalid');
                $('.invalid-feedback').text('');
                $('.permission-checkbox').prop('checked', false); // Pastikan semua checkbox izin tidak tercentang
                $('.collapse').collapse('hide'); // Tutup semua accordion di modal
            }

            // Buka Modal Tambah Role
            $('#btn-add-role').on('click', function() {
                resetForm();
                $('#roleModal').modal('show');
            });

            // Buka Modal Edit Role
            $(document).on('click', '.btn-edit', function() {
                resetForm(); // Reset form terlebih dahulu
                var roleId = $(this).data('id');
                $('#roleModalLabel').text('Edit Peran');
                $('#saveRoleBtn').text('Update');
                $('#role_id').val(roleId);

                $.ajax({
                    url: '/roles/' + roleId + '/edit', // Sesuai dengan route resource Laravel
                    method: 'GET',
                    success: function(response) {
                        if (response.success) {
                            var role = response.data;
                            $('#nama_role').val(role.nama_role);
                            $('#deskripsi_role').val(role.deskripsi_role);

                            // Centang checkbox izin yang dimiliki role ini
                            $('.permission-checkbox').prop('checked',
                                false); // Reset dulu semua
                            if (role.permissions && role.permissions.length > 0) {
                                role.permissions.forEach(function(permission) {
                                    $('#permission-' + permission.permission_id).prop(
                                        'checked', true);
                                });
                            }

                            $('#roleModal').modal('show');
                        } else {
                            Swal.fire('Error!', response.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Error!', 'Gagal mengambil data peran.', 'error');
                        console.error(xhr.responseText);
                    }
                });
            });

            // Tangani Pengiriman Form (Tambah/Edit)
            $('#roleForm').on('submit', function(e) {
                e.preventDefault();

                // Bersihkan pesan validasi sebelumnya
                $('.form-control').removeClass('is-invalid');
                $('.invalid-feedback').text('');

                var roleId = $('#role_id').val();
                var url = roleId ? '/roles/' + roleId : '/roles';
                var method = roleId ? 'PUT' :
                    'POST'; // Laravel menggunakan POST dengan _method untuk PUT/PATCH

                // Ambil semua data form, termasuk checkbox izin
                var formData = $(this).serializeArray();
                // Tambahkan _method secara manual ke formData array
                formData.push({
                    name: '_method',
                    value: method
                });

                // Jika tidak ada checkbox yang dicentang, pastikan 'permissions' tetap terkirim sebagai array kosong
                // Ini penting agar sync() bekerja dengan benar saat menghapus semua izin
                var selectedPermissions = $('.permission-checkbox:checked').map(function() {
                    return $(this).val();
                }).get();

                if (selectedPermissions.length === 0 && !formData.some(item => item.name ===
                        'permissions[]')) {
                    formData.push({
                        name: 'permissions[]',
                        value: ''
                    }); // Kirim array kosong jika tidak ada yang dipilih
                }


                $.ajax({
                    url: url,
                    method: 'POST', // Selalu POST untuk Laravel, dengan _method di data
                    data: $.param(
                        formData), // Gunakan $.param untuk mengkonversi array ke string query
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Berhasil!', response.message, 'success').then(() => {
                                $('#roleModal').modal('hide');
                                // Reload halaman atau perbarui tabel secara dinamis
                                location
                                    .reload(); // Reload sederhana untuk saat ini, bisa dioptimalkan dengan DataTables API
                            });
                        } else {
                            Swal.fire('Error!', response.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) { // Error validasi
                            var errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                // Penanganan error untuk permissions[]
                                if (key.startsWith('permissions.')) {
                                    $('#permissions-error').text(value[0]).addClass(
                                        'd-block');
                                } else {
                                    $('#' + key).addClass('is-invalid');
                                    $('#' + key + '-error').text(value[0]);
                                }
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

            // Tangani Hapus Role
            $(document).on('click', '.btn-delete', function() {
                var roleId = $(this).data('id');

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data peran ini akan dihapus secara permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/roles/' + roleId,
                            method: 'POST', // Laravel menggunakan POST untuk DELETE dengan _method field
                            data: {
                                _method: 'DELETE',
                                _token: $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire('Dihapus!', response.message, 'success')
                                        .then(() => {
                                            // Hapus baris dari DataTables
                                            table.row($('#role-row-' + roleId))
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
