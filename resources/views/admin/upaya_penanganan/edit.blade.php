@extends('main')

@section('title', 'Edit Upaya Penanganan | Page')

@section('content')

    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Edit Upaya Penanganan</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('upaya-penanganan.index') }}">Upaya Penanganan</a></li>
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
                    <h3 class="card-title">Form Upaya Penanganan</h3>
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

                <form action="{{ route('upaya-penanganan.update', $upayaPenanganan->upaya_id) }}" method="POST">
                    @csrf
                    @method('PUT') {{-- Gunakan PUT method untuk update --}}
                    <div class="card-body">
                        <div class="form-group">
                            <label for="bencana_id">Data Bencana</label>
                            <select class="form-control" id="bencana_id" name="bencana_id" required>
                                <option value="">Pilih Data Bencana</option>
                                @foreach ($bencanaList as $bencana)
                                    <option value="{{ $bencana->bencana_id }}"
                                        {{ old('bencana_id', $upayaPenanganan->bencana_id) == $bencana->bencana_id ? 'selected' : '' }}>
                                        {{ $bencana->nama_bencana }}
                                        ({{ $bencana->tanggal_kejadian->format('d M Y') ?? '-' }})</option>
                                @endforeach
                            </select>
                            @error('bencana_id')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="instansi">Instansi</label>
                            <input type="text" class="form-control" id="instansi" name="instansi"
                                value="{{ old('instansi', $upayaPenanganan->instansi) }}" required>
                            @error('instansi')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="jenis_upaya">Jenis Upaya</label>
                            <input type="text" class="form-control" id="jenis_upaya" name="jenis_upaya"
                                value="{{ old('jenis_upaya', $upayaPenanganan->jenis_upaya) }}" required>
                            @error('jenis_upaya')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="deskripsi">Deskripsi</label>
                            <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" required>{{ old('deskripsi', $upayaPenanganan->deskripsi) }}</textarea>
                            @error('deskripsi')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="tanggal_penanganan">Tanggal Penanganan</label>
                            <input type="datetime-local" class="form-control" id="tanggal_penanganan"
                                name="tanggal_penanganan"
                                value="{{ old('tanggal_penanganan', $upayaPenanganan->tanggal_penanganan ? $upayaPenanganan->tanggal_penanganan->format('Y-m-d\TH:i') : '') }}"
                                required>
                            @error('tanggal_penanganan')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Update</button>
                        <a href="{{ route('upaya-penanganan.index') }}" class="btn btn-secondary">Batal</a>
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
