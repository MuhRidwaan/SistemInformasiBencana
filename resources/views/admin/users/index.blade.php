@extends('main')

@section('title', 'Users | Page')

@section('content')

    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Manajemen User</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Users</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Daftar Pengguna</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary btn-sm" id="btn-add-user">
                            <i class="fas fa-plus"></i> Tambah User
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <table id="example2" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Nama Lengkap</th>
                                <th>Email</th>
                                <th>Kontak</th>
                                <th>Peran</th> <!-- Tambah kolom Peran -->
                                <th>Status Aktif</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr id="user-row-{{ $user->user_id }}">
                                    <td>{{ $user->user_id }}</td>
                                    <td>{{ $user->username }}</td>
                                    <td>{{ $user->nama_lengkap }}</td>
                                    <td>{{ $user->email ?? '-' }}</td>
                                    <td>{{ $user->kontak ?? '-' }}</td>
                                    <td>{{ $user->role->nama_role ?? '-' }}</td> <!-- Tampilkan nama peran -->
                                    <td>
                                        @if ($user->is_active)
                                            <span class="badge badge-success">Aktif</span>
                                        @else
                                            <span class="badge badge-danger">Tidak Aktif</span>
                                        @endif
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-info btn-edit" data-id="{{ $user->user_id }}">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <button class="btn btn-sm btn-danger btn-delete" data-id="{{ $user->user_id }}">
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

    <div class="modal fade" id="userModal" tabindex="-1" role="dialog" aria-labelledby="userModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="userModalLabel">Tambah User Baru</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="userForm">
                    <div class="modal-body">
                        @csrf <input type="hidden" id="user_id" name="user_id">
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                            <div class="invalid-feedback" id="username-error"></div>
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" id="password" name="password"
                                placeholder="Isi untuk tambah, kosongkan untuk tidak mengubah saat edit">
                            <div class="invalid-feedback" id="password-error"></div>
                        </div>
                        <div class="form-group">
                            <label for="nama_lengkap">Nama Lengkap</label>
                            <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" required>
                            <div class="invalid-feedback" id="nama_lengkap-error"></div>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email">
                            <div class="invalid-feedback" id="email-error"></div>
                        </div>
                        <div class="form-group">
                            <label for="kontak">Kontak</label>
                            <input type="text" class="form-control" id="kontak" name="kontak">
                            <div class="invalid-feedback" id="kontak-error"></div>
                        </div>
                        <div class="form-group">
                            <label for="role_id">Peran</label>
                            <select class="form-control" id="role_id" name="role_id" required>
                                <option value="">Pilih Peran</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->role_id }}">{{ $role->nama_role }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" id="role_id-error"></div>
                        </div>
                        <div class="form-group form-check">
                            <input type="checkbox" class="form-check-input" id="is_active" name="is_active"
                                value="1" checked>
                            <label class="form-check-label" for="is_active">Aktif</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary" id="saveUserBtn">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Initialize DataTables
            var table = $('#example2').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
            });


            // Function to reset form and clear validation errors
            function resetForm() {
                $('#userForm')[0].reset();
                $('#user_id').val('');
                $('#userModalLabel').text('Tambah User Baru');
                $('#saveUserBtn').text('Simpan');
                $('#password').attr('required', true).attr('placeholder',
                    'Isi untuk tambah, kosongkan untuk tidak mengubah saat edit');
                $('.form-control').removeClass('is-invalid');
                $('.invalid-feedback').text('');
                $('#is_active').prop('checked', true); // Default checked for add
                $('#role_id').val(''); // Reset dropdown peran
            }

            // Open Add User Modal
            $('#btn-add-user').on('click', function() {
                resetForm();
                $('#userModal').modal('show');
            });

            // Open Edit User Modal
            $(document).on('click', '.btn-edit', function() {
                resetForm(); // Reset form first
                var userId = $(this).data('id');
                $('#userModalLabel').text('Edit User');
                $('#saveUserBtn').text('Update');
                $('#user_id').val(userId);
                $('#password').removeAttr('required').attr('placeholder',
                    'Kosongkan jika tidak ingin mengubah password');

                $.ajax({
                    url: '/users/' + userId + '/edit',
                    method: 'GET',
                    success: function(response) {
                        if (response.success) {
                            var user = response.data;
                            $('#username').val(user.username);
                            // Password field is intentionally left empty for security reasons when editing
                            $('#nama_lengkap').val(user.nama_lengkap);
                            $('#email').val(user.email);
                            $('#kontak').val(user.kontak);
                            $('#role_id').val(user.role_id); // Set nilai dropdown peran
                            $('#is_active').prop('checked', user.is_active);
                            $('#userModal').modal('show');
                        } else {
                            Swal.fire('Error!', response.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Error!', 'Gagal mengambil data user.', 'error');
                        console.error(xhr.responseText);
                    }
                });
            });

            // Handle Form Submission (Add/Edit)
            $('#userForm').on('submit', function(e) {
                e.preventDefault();

                // Clear previous validation errors
                $('.form-control').removeClass('is-invalid');
                $('.invalid-feedback').text('');

                var userId = $('#user_id').val();
                var url = userId ? '/users/' + userId : '/users';
                var method = userId ? 'PUT' : 'POST';

                var formData = $(this).serialize(); // Serialize form data

                $.ajax({
                    url: url,
                    method: 'POST', // Laravel uses POST for PUT/PATCH with _method field
                    data: formData + '&_method=' + method, // Append _method for PUT/PATCH
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Berhasil!', response.message, 'success').then(() => {
                                $('#userModal').modal('hide');
                                // Reload page or update table dynamically
                                location
                                    .reload(); // Simple reload for now, can be optimized with DataTables API
                            });
                        } else {
                            Swal.fire('Error!', response.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) { // Validation errors
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

            // Handle Delete User
            $(document).on('click', '.btn-delete', function() {
                var userId = $(this).data('id');

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data user ini akan dihapus secara permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/users/' + userId,
                            method: 'POST', // Laravel uses POST for DELETE with _method field
                            data: {
                                _method: 'DELETE',
                                _token: $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire('Dihapus!', response.message, 'success')
                                        .then(() => {
                                            // Remove row from DataTables
                                            table.row($('#user-row-' + userId))
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
