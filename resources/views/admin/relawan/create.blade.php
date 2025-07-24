@extends('main')

@section('title', 'Lengkapi Profil Relawan | Page')

@section('content')

    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Lengkapi Profil Relawan</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('relawan.index') }}">Relawan</a></li>
                        <li class="breadcrumb-item active">Lengkapi Profil</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Form Lengkapi Profil Relawan</h3>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @if (Session::has('error'))
                    <div class="alert alert-danger">
                        {{ Session::get('error') }}
                    </div>
                @endif

                <form action="{{ route('relawan.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label for="user_id">Pilih User</label>
                            <select class="form-control" id="user_id" name="user_id" required>
                                <option value="">-- Pilih User --</option>
                                @foreach ($usersWithoutRelawanProfile as $user)
                                    <option value="{{ $user->user_id }}"
                                        {{ old('user_id') == $user->user_id ? 'selected' : '' }}>
                                        {{ $user->nama_lengkap }} ({{ $user->username }})
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr>

                        <h4>Data Relawan</h4>
                        <div class="form-group">
                            <label for="keahlian">Keahlian</label>
                            <input type="text" class="form-control" id="keahlian" name="keahlian"
                                value="{{ old('keahlian') }}" placeholder="Contoh: Medis, Logistik, Komunikasi">
                            @error('keahlian')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="organisasi">Organisasi</label>
                            <input type="text" class="form-control" id="organisasi" name="organisasi"
                                value="{{ old('organisasi') }}" placeholder="Contoh: PMI, SAR, Komunitas Relawan A">
                            @error('organisasi')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Simpan Profil</button>
                        <a href="{{ route('relawan.index') }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </section>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            @if (Session::has('success'))
                Swal.fire('Berhasil!', '{{ Session::get('success') }}', 'success');
            @endif

            @if (Session::has('error'))
                Swal.fire('Error!', '{{ Session::get('error') }}', 'error');
            @endif

            // AJAX untuk mengisi form jika user yang dipilih sudah punya profil relawan
            $('#user_id').change(function() {
                var userId = $(this).val();
                if (userId) {
                    $.ajax({
                        url: '{{ route('relawan.get-data-by-user') }}', // Pastikan rute ini ada
                        type: 'GET',
                        data: {
                            user_id: userId
                        },
                        success: function(response) {
                            if (response.exists) {
                                $('#keahlian').val(response.keahlian);
                                $('#organisasi').val(response.organisasi);
                                Swal.fire('Informasi!',
                                    'User ini sudah memiliki profil relawan. Data akan diperbarui.',
                                    'info');
                            } else {
                                $('#keahlian').val(''); // Kosongkan jika belum ada profil
                                $('#organisasi').val('');
                            }
                        },
                        error: function(xhr) {
                            console.error("Error fetching relawan data: ", xhr.responseText);
                            $('#keahlian').val('');
                            $('#organisasi').val('');
                            Swal.fire('Error!', 'Gagal mengambil data relawan untuk user ini.',
                                'error');
                        }
                    });
                } else {
                    $('#keahlian').val('');
                    $('#organisasi').val('');
                }
            });
        });
    </script>
@endpush
