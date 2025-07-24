@extends('main')

@section('title', 'Tambah Data Korban | Page')

@section('content')

    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Tambah Data Korban</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('korban.index') }}">Data Korban</a></li>
                        <li class="breadcrumb-item active">Tambah</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Form Data Korban</h3>
                </div>
                <!-- Menampilkan pesan error dari validasi Laravel -->
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <!-- Menampilkan pesan flash (error) dari controller -->
                @if (Session::has('error'))
                    <div class="alert alert-danger">
                        {{ Session::get('error') }}
                    </div>
                @endif

                <form action="{{ route('korban.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label for="bencana_id">Data Bencana</label>
                            <select class="form-control" id="bencana_id" name="bencana_id" required>
                                <option value="">Pilih Data Bencana</option>
                                @foreach ($bencanaList as $bencana)
                                    <option value="{{ $bencana->bencana_id }}"
                                        {{ old('bencana_id') == $bencana->bencana_id ? 'selected' : '' }}>
                                        {{ $bencana->nama_bencana }}
                                        ({{ $bencana->tanggal_kejadian->format('d M Y') ?? '-' }})</option>
                                @endforeach
                            </select>
                            @error('bencana_id')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="meninggal">Meninggal</label>
                            <input type="number" class="form-control" id="meninggal" name="meninggal"
                                value="{{ old('meninggal', 0) }}" required min="0">
                            @error('meninggal')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="luka_berat">Luka Berat</label>
                            <input type="number" class="form-control" id="luka_berat" name="luka_berat"
                                value="{{ old('luka_berat', 0) }}" required min="0">
                            @error('luka_berat')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="luka_ringan">Luka Ringan</label>
                            <input type="number" class="form-control" id="luka_ringan" name="luka_ringan"
                                value="{{ old('luka_ringan', 0) }}" required min="0">
                            @error('luka_ringan')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="hilang">Hilang</label>
                            <input type="number" class="form-control" id="hilang" name="hilang"
                                value="{{ old('hilang', 0) }}" required min="0">
                            @error('hilang')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="mengungsi">Mengungsi</label>
                            <input type="number" class="form-control" id="mengungsi" name="mengungsi"
                                value="{{ old('mengungsi', 0) }}" required min="0">
                            @error('mengungsi')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="terdampak">Terdampak</label>
                            <input type="number" class="form-control" id="terdampak" name="terdampak"
                                value="{{ old('terdampak', 0) }}" required min="0">
                            @error('terdampak')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="tanggal_input">Tanggal Input</label>
                            <input type="datetime-local" class="form-control" id="tanggal_input" name="tanggal_input"
                                value="{{ old('tanggal_input') }}" required>
                            @error('tanggal_input')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="{{ route('korban.index') }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </section>

@endsection

@push('scripts')
    <!-- SweetAlert2 (opsional, jika ingin notifikasi setelah redirect) -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // Tampilkan SweetAlert jika ada pesan success/error dari session
            @if (Session::has('success'))
                Swal.fire('Berhasil!', '{{ Session::get('success') }}', 'success');
            @endif

            @if (Session::has('error'))
                Swal.fire('Error!', '{{ Session::get('error') }}', 'error');
            @endif
        });
    </script>
@endpush
