@extends('main')

@section('title', 'Jenis Bencana | Page')

@section('content')

    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Manajemen Jenis Bencana</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Jenis Bencana</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Daftar Jenis Bencana</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary btn-sm" id="btn-add-jenis-bencana">
                            <i class="fas fa-plus"></i> Tambah Jenis Bencana
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <table id="jenisBencanaTable" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nama Jenis</th>
                                <th>Deskripsi</th>
                                <th>Dibuat Oleh</th>
                                <th>Tanggal Dibuat</th>
                                <th>Diubah Oleh</th>
                                <th>Tanggal Diubah</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($jenisBencana as $jb)
                                <tr id="jenis-bencana-row-{{ $jb->jenis_bencana_id }}">
                                    <td>{{ $jb->jenis_bencana_id }}</td>
                                    <td>{{ $jb->nama_jenis }}</td>
                                    <td>{{ $jb->deskripsi_jenis ?? '-' }}</td>
                                    <td>{{ $jb->creator->nama_lengkap ?? '-' }}</td>
                                    <td>{{ $jb->create_date ? $jb->create_date->format('d M Y H:i') : '-' }}</td>
                                    <td>{{ $jb->changer->nama_lengkap ?? '-' }}</td>
                                    <td>{{ $jb->change_date ? $jb->change_date->format('d M Y H:i') : '-' }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-info btn-edit" data-id="{{ $jb->jenis_bencana_id }}">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <button class="btn btn-sm btn-danger btn-delete"
                                            data-id="{{ $jb->jenis_bencana_id }}">
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

    <!-- Modal untuk Tambah/Edit Jenis Bencana -->
    <div class="modal fade" id="jenisBencanaModal" tabindex="-1" role="dialog" aria-labelledby="jenisBencanaModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="jenisBencanaModalLabel">Tambah Jenis Bencana Baru</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="jenisBencanaForm">
                    <div class="modal-body">
                        @csrf
                        <input type="hidden" id="jenis_bencana_id" name="jenis_bencana_id">
                        <div class="form-group">
                            <label for="nama_jenis">Nama Jenis Bencana</label>
                            <input type="text" class="form-control" id="nama_jenis" name="nama_jenis" required>
                            <div class="invalid-feedback" id="nama_jenis-error"></div>
                        </div>
                        <div class="form-group">
                            <label for="deskripsi_jenis">Deskripsi Jenis Bencana</label>
                            <textarea class="form-control" id="deskripsi_jenis" name="deskripsi_jenis" rows="3"></textarea>
                            <div class="invalid-feedback" id="deskripsi_jenis-error"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary" id="saveJenisBencanaBtn">Simpan</button>
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
            var table = $('#jenisBencanaTable').DataTable({
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
                $('#jenisBencanaForm')[0].reset();
                $('#jenis_bencana_id').val('');
                $('#jenisBencanaModalLabel').text('Tambah Jenis Bencana Baru');
                $('#saveJenisBencanaBtn').text('Simpan');
                $('.form-control').removeClass('is-invalid');
                $('.invalid-feedback').text('');
            }

            // Buka Modal Tambah Jenis Bencana
            $('#btn-add-jenis-bencana').on('click', function() {
                resetForm();
                $('#jenisBencanaModal').modal('show');
            });

            // Buka Modal Edit Jenis Bencana
            $(document).on('click', '.btn-edit', function() {
                resetForm(); // Reset form terlebih dahulu
                var jenisBencanaId = $(this).data('id');
                $('#jenisBencanaModalLabel').text('Edit Jenis Bencana');
                $('#saveJenisBencanaBtn').text('Update');
                $('#jenis_bencana_id').val(jenisBencanaId);

                $.ajax({
                    url: '/jenis-bencana/' + jenisBencanaId +
                        '/edit', // Sesuai dengan route resource Laravel
                    method: 'GET',
                    success: function(response) {
                        if (response.success) {
                            var jenisBencana = response.data;
                            $('#nama_jenis').val(jenisBencana.nama_jenis);
                            $('#deskripsi_jenis').val(jenisBencana.deskripsi_jenis);
                            $('#jenisBencanaModal').modal('show');
                        } else {
                            Swal.fire('Error!', response.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Error!', 'Gagal mengambil data jenis bencana.', 'error');
                        console.error(xhr.responseText);
                    }
                });
            });

            // Tangani Pengiriman Form (Tambah/Edit)
            $('#jenisBencanaForm').on('submit', function(e) {
                e.preventDefault();

                // Bersihkan pesan validasi sebelumnya
                $('.form-control').removeClass('is-invalid');
                $('.invalid-feedback').text('');

                var jenisBencanaId = $('#jenis_bencana_id').val();
                var url = jenisBencanaId ? '/jenis-bencana/' + jenisBencanaId : '/jenis-bencana';
                var method = jenisBencanaId ? 'PUT' :
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
                                $('#jenisBencanaModal').modal('hide');
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

            // Tangani Hapus Jenis Bencana
            $(document).on('click', '.btn-delete', function() {
                var jenisBencanaId = $(this).data('id');
                var $row = $('#jenis-bencana-row-' + jenisBencanaId); // Referensi ke elemen baris tabel

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
                    text: "Data jenis bencana ini akan dihapus secara permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/jenis-bencana/' + jenisBencanaId,
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
