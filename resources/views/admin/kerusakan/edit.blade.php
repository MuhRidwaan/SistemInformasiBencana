@extends('main')

@section('title', 'Edit Data Kerusakan | Page')

@section('content')

    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Edit Data Kerusakan</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('kerusakan.index') }}">Data Kerusakan</a></li>
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
                    <h3 class="card-title">Form Data Kerusakan</h3>
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

                <form action="{{ route('kerusakan.update', $kerusakan->kerusakan_id) }}" method="POST">
                    @csrf
                    @method('PUT') {{-- Gunakan PUT method untuk update --}}
                    <div class="card-body">
                        <div class="form-group">
                            <label for="bencana_id">Data Bencana</label>
                            <select class="form-control" id="bencana_id" name="bencana_id" required>
                                <option value="">Pilih Data Bencana</option>
                                @foreach ($bencanaList as $bencana)
                                    <option value="{{ $bencana->bencana_id }}"
                                        {{ old('bencana_id', $kerusakan->bencana_id) == $bencana->bencana_id ? 'selected' : '' }}>
                                        {{ $bencana->nama_bencana }}
                                        ({{ $bencana->tanggal_kejadian->format('d M Y') ?? '-' }})</option>
                                @endforeach
                            </select>
                            @error('bencana_id')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="objek">Objek Kerusakan</label>
                            <input type="text" class="form-control" id="objek" name="objek"
                                value="{{ old('objek', $kerusakan->objek) }}" required>
                            @error('objek')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="tingkat_kerusakan">Tingkat Kerusakan</label>
                            <input type="text" class="form-control" id="tingkat_kerusakan" name="tingkat_kerusakan"
                                value="{{ old('tingkat_kerusakan', $kerusakan->tingkat_kerusakan) }}"
                                placeholder="Contoh: Ringan, Sedang, Berat" required>
                            @error('tingkat_kerusakan')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="jumlah">Jumlah</label>
                            <input type="number" class="form-control" id="jumlah" name="jumlah"
                                value="{{ old('jumlah', $kerusakan->jumlah) }}" required min="0">
                            @error('jumlah')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="satuan">Satuan</label>
                            <input type="text" class="form-control" id="satuan" name="satuan"
                                value="{{ old('satuan', $kerusakan->satuan) }}" placeholder="Contoh: Unit, Bangunan, Rumah"
                                required>
                            @error('satuan')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="deskripsi">Deskripsi</label>
                            <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" required>{{ old('deskripsi', $kerusakan->deskripsi) }}</textarea>
                            @error('deskripsi')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="tanggal_input">Tanggal Input</label>
                            <input type="datetime-local" class="form-control" id="tanggal_input" name="tanggal_input"
                                value="{{ old('tanggal_input', $kerusakan->tanggal_input ? $kerusakan->tanggal_input->format('Y-m-d\TH:i') : '') }}"
                                required>
                            @error('tanggal_input')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Update</button>
                        <a href="{{ route('kerusakan.index') }}" class="btn btn-secondary">Batal</a>
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
