@extends('main')

@section('title', 'Edit Profil Relawan | Page')

@section('content')

    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Edit Profil Relawan</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('relawan.index') }}">Relawan</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Form Edit Profil Relawan</h3>
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

                <form action="{{ route('relawan.update', $relawan->relawan_id) }}" method="POST">
                    @csrf
                    @method('PUT') {{-- Gunakan PUT method untuk update --}}
                    <div class="card-body">
                        <h4>Informasi Akun (User)</h4>
                        <div class="form-group">
                            <label>Nama Lengkap:</label>
                            <p class="form-control-static"><strong>{{ $user->nama_lengkap ?? '-' }}</strong></p>
                        </div>
                        <div class="form-group">
                            <label>Username:</label>
                            <p class="form-control-static">{{ $user->username ?? '-' }}</p>
                        </div>
                        <div class="form-group">
                            <label>Email:</label>
                            <p class="form-control-static">{{ $user->email ?? '-' }}</p>
                        </div>
                        <div class="form-group">
                            <label>Kontak:</label>
                            <p class="form-control-static">{{ $user->kontak ?? '-' }}</p>
                        </div>
                        <div class="form-group">
                            <label>Peran:</label>
                            <p class="form-control-static">{{ $user->role->nama_role ?? '-' }}</p>
                        </div>
                        <div class="form-group">
                            <label>Status User:</label>
                            <p class="form-control-static">
                                @if ($user->is_active ?? false)
                                    <span class="badge badge-success">Aktif</span>
                                @else
                                    <span class="badge badge-danger">Tidak Aktif</span>
                                @endif
                            </p>
                        </div>

                        <hr>

                        <h4>Data Relawan</h4>
                        <div class="form-group">
                            <label for="keahlian">Keahlian</label>
                            <input type="text" class="form-control" id="keahlian" name="keahlian"
                                value="{{ old('keahlian', $relawan->keahlian) }}"
                                placeholder="Contoh: Medis, Logistik, Komunikasi">
                            @error('keahlian')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="organisasi">Organisasi</label>
                            <input type="text" class="form-control" id="organisasi" name="organisasi"
                                value="{{ old('organisasi', $relawan->organisasi) }}"
                                placeholder="Contoh: PMI, SAR, Komunitas Relawan A">
                            @error('organisasi')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Update</button>
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
        });
    </script>
@endpush
