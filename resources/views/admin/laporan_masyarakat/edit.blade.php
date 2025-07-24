@extends('main')

@section('title', 'Edit Laporan Masyarakat | Page')

@section('content')

    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Edit Laporan Masyarakat</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('laporan-masyarakat.index') }}">Laporan Masyarakat</a>
                        </li>
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
                    <h3 class="card-title">Form Laporan Masyarakat</h3>
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

                <form action="{{ route('laporan-masyarakat.update', $laporanMasyarakat->laporan_id) }}" method="POST"
                    enctype="multipart/form-data"> {{-- Tambahkan enctype untuk upload file --}}
                    @csrf
                    @method('PUT') {{-- Gunakan PUT method untuk update --}}
                    <div class="card-body">
                        <div class="form-group">
                            <label for="jenis_laporan">Jenis Laporan</label>
                            <input type="text" class="form-control" id="jenis_laporan" name="jenis_laporan"
                                value="{{ old('jenis_laporan', $laporanMasyarakat->jenis_laporan) }}" required>
                            @error('jenis_laporan')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="judul_laporan">Judul Laporan</label>
                            <input type="text" class="form-control" id="judul_laporan" name="judul_laporan"
                                value="{{ old('judul_laporan', $laporanMasyarakat->judul_laporan) }}" required>
                            @error('judul_laporan')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="deskripsi_laporan">Deskripsi Laporan</label>
                            <textarea class="form-control" id="deskripsi_laporan" name="deskripsi_laporan" rows="3" required>{{ old('deskripsi_laporan', $laporanMasyarakat->deskripsi_laporan) }}</textarea>
                            @error('deskripsi_laporan')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="tanggal_laporan">Tanggal Laporan</label>
                            <input type="datetime-local" class="form-control" id="tanggal_laporan" name="tanggal_laporan"
                                value="{{ old('tanggal_laporan', $laporanMasyarakat->tanggal_laporan ? $laporanMasyarakat->tanggal_laporan->format('Y-m-d\TH:i') : '') }}"
                                required>
                            @error('tanggal_laporan')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="nama_pelapor">Nama Pelapor</label>
                            <input type="text" class="form-control" id="nama_pelapor" name="nama_pelapor"
                                value="{{ old('nama_pelapor', $laporanMasyarakat->nama_pelapor) }}">
                            @error('nama_pelapor')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="kontak_pelapor">Kontak Pelapor</label>
                            <input type="text" class="form-control" id="kontak_pelapor" name="kontak_pelapor"
                                value="{{ old('kontak_pelapor', $laporanMasyarakat->kontak_pelapor) }}">
                            @error('kontak_pelapor')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="latitude">Latitude</label>
                                    <input type="text" class="form-control" id="latitude" name="latitude"
                                        value="{{ old('latitude', $laporanMasyarakat->latitude) }}"
                                        placeholder="Contoh: -6.200000">
                                    @error('latitude')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="longitude">Longitude</label>
                                    <input type="text" class="form-control" id="longitude" name="longitude"
                                        value="{{ old('longitude', $laporanMasyarakat->longitude) }}"
                                        placeholder="Contoh: 106.800000">
                                    @error('longitude')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="path_foto">Foto (Opsional)</label>
                            <input type="file" class="form-control-file" id="path_foto" name="path_foto">
                            @if ($laporanMasyarakat->path_foto)
                                <small class="form-text text-muted">Foto saat ini: <a
                                        href="{{ asset($laporanMasyarakat->path_foto) }}" target="_blank">Lihat
                                        Foto</a></small>
                            @endif
                            @error('path_foto')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="status_laporan">Status Laporan</label>
                            <select class="form-control" id="status_laporan" name="status_laporan" required>
                                <option value="Pending"
                                    {{ old('status_laporan', $laporanMasyarakat->status_laporan) == 'Pending' ? 'selected' : '' }}>
                                    Pending</option>
                                <option value="Diterima"
                                    {{ old('status_laporan', $laporanMasyarakat->status_laporan) == 'Diterima' ? 'selected' : '' }}>
                                    Diterima</option>
                                <option value="Ditolak"
                                    {{ old('status_laporan', $laporanMasyarakat->status_laporan) == 'Ditolak' ? 'selected' : '' }}>
                                    Ditolak</option>
                                <option value="Selesai"
                                    {{ old('status_laporan', $laporanMasyarakat->status_laporan) == 'Selesai' ? 'selected' : '' }}>
                                    Selesai</option>
                            </select>
                            @error('status_laporan')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="bencana_id">Bencana Terkait (Opsional)</label>
                            <select class="form-control" id="bencana_id" name="bencana_id">
                                <option value="">Pilih Bencana</option>
                                @foreach ($bencanaList as $bencana)
                                    <option value="{{ $bencana->bencana_id }}"
                                        {{ old('bencana_id', $laporanMasyarakat->bencana_id) == $bencana->bencana_id ? 'selected' : '' }}>
                                        {{ $bencana->nama_bencana }}
                                        ({{ $bencana->tanggal_kejadian->format('d M Y') ?? '-' }})</option>
                                @endforeach
                            </select>
                            @error('bencana_id')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Update</button>
                        <a href="{{ route('laporan-masyarakat.index') }}" class="btn btn-secondary">Batal</a>
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
