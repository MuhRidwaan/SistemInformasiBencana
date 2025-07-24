@extends('main')

@section('title', 'Tambah Kebutuhan Logistik | Page')

@section('content')

    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Tambah Kebutuhan Logistik</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('kebutuhan-logistik.index') }}">Kebutuhan Logistik</a>
                        </li>
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
                    <h3 class="card-title">Form Kebutuhan Logistik</h3>
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

                <form action="{{ route('kebutuhan-logistik.store') }}" method="POST">
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
                            <label for="jenis_kebutuhan">Jenis Kebutuhan</label>
                            <input type="text" class="form-control" id="jenis_kebutuhan" name="jenis_kebutuhan"
                                value="{{ old('jenis_kebutuhan') }}" required>
                            @error('jenis_kebutuhan')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="jumlah_dibutuhkan">Jumlah Dibutuhkan</label>
                            <input type="number" class="form-control" id="jumlah_dibutuhkan" name="jumlah_dibutuhkan"
                                value="{{ old('jumlah_dibutuhkan') }}" required min="0">
                            @error('jumlah_dibutuhkan')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="satuan">Satuan</label>
                            <input type="text" class="form-control" id="satuan" name="satuan"
                                value="{{ old('satuan') }}" required>
                            @error('satuan')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="jumlah_tersedia">Jumlah Tersedia</label>
                            <input type="number" class="form-control" id="jumlah_tersedia" name="jumlah_tersedia"
                                value="{{ old('jumlah_tersedia') }}" required min="0">
                            @error('jumlah_tersedia')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="tanggal_update">Tanggal Update</label>
                            <input type="datetime-local" class="form-control" id="tanggal_update" name="tanggal_update"
                                value="{{ old('tanggal_update') }}" required>
                            @error('tanggal_update')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="deskripsi">Deskripsi</label>
                            <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3">{{ old('deskripsi') }}</textarea>
                            @error('deskripsi')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="{{ route('kebutuhan-logistik.index') }}" class="btn btn-secondary">Batal</a>
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
