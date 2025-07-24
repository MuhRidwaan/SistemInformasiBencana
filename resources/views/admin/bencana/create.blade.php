@extends('main')

@section('title', 'Tambah Data Bencana | Page')

@section('content')

    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Tambah Data Bencana</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('bencana.index') }}">Data Bencana</a></li>
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
                    <h3 class="card-title">Form Data Bencana</h3>
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
                <!-- Menampilkan pesan flash (success/error) dari controller -->
                @if (Session::has('error'))
                    <div class="alert alert-danger">
                        {{ Session::get('error') }}
                    </div>
                @endif

                <form action="{{ route('bencana.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label for="jenis_bencana_id">Jenis Bencana</label>
                            <select class="form-control" id="jenis_bencana_id" name="jenis_bencana_id" required>
                                <option value="">Pilih Jenis Bencana</option>
                                @foreach ($jenisBencanaList as $jb)
                                    <option value="{{ $jb->jenis_bencana_id }}"
                                        {{ old('jenis_bencana_id') == $jb->jenis_bencana_id ? 'selected' : '' }}>
                                        {{ $jb->nama_jenis }}</option>
                                @endforeach
                            </select>
                            @error('jenis_bencana_id')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="nama_bencana">Nama Bencana</label>
                            <input type="text" class="form-control" id="nama_bencana" name="nama_bencana"
                                value="{{ old('nama_bencana') }}" required>
                            @error('nama_bencana')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="tanggal_kejadian">Tanggal Kejadian</label>
                            <input type="datetime-local" class="form-control" id="tanggal_kejadian" name="tanggal_kejadian"
                                value="{{ old('tanggal_kejadian') }}" required>
                            @error('tanggal_kejadian')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="kronologis">Kronologis</label>
                            <textarea class="form-control" id="kronologis" name="kronologis" rows="3" required>{{ old('kronologis') }}</textarea>
                            @error('kronologis')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="deskripsi">Deskripsi</label>
                            <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" required>{{ old('deskripsi') }}</textarea>
                            @error('deskripsi')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr>
                        <h5>Lokasi Kejadian</h5>

                        <div class="form-group">
                            <label for="provinsi_id">Provinsi</label>
                            <select class="form-control" id="provinsi_id" name="provinsi_id" required>
                                <option value="">Pilih Provinsi</option>
                                @foreach ($provinsiList as $provinsi)
                                    <option value="{{ $provinsi->provinsi_id }}"
                                        {{ old('provinsi_id') == $provinsi->provinsi_id ? 'selected' : '' }}>
                                        {{ $provinsi->nama_provinsi }}</option>
                                @endforeach
                            </select>
                            @error('provinsi_id')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="kota_id">Kota/Kabupaten</label>
                            <select class="form-control" id="kota_id" name="kota_id" required disabled>
                                <option value="">Pilih Kota/Kabupaten</option>
                            </select>
                            @error('kota_id')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="kecamatan_id">Kecamatan</label>
                            <select class="form-control" id="kecamatan_id" name="kecamatan_id" required disabled>
                                <option value="">Pilih Kecamatan</option>
                            </select>
                            @error('kecamatan_id')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="kelurahan_id">Kelurahan</label>
                            <select class="form-control" id="kelurahan_id" name="kelurahan_id" required disabled>
                                <option value="">Pilih Kelurahan</option>
                            </select>
                            @error('kelurahan_id')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="latitude">Latitude</label>
                                    <input type="text" class="form-control" id="latitude" name="latitude"
                                        value="{{ old('latitude') }}" placeholder="Contoh: -6.200000">
                                    @error('latitude')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="longitude">Longitude</label>
                                    <input type="text" class="form-control" id="longitude" name="longitude"
                                        value="{{ old('longitude') }}" placeholder="Contoh: 106.800000">
                                    @error('longitude')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="{{ route('bencana.index') }}" class="btn btn-secondary">Batal</a>
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
            // --- Cascading Dropdowns Logic ---

            // Fungsi untuk mengisi dropdown
            function populateDropdown(selector, data, selectedValue = null) {
                const dropdown = $(selector);
                dropdown.empty().append('<option value="">Pilih ' + dropdown.attr('id').replace('_id', '') +
                    '</option>');
                $.each(data, function(key, value) {
                    dropdown.append('<option value="' + value.id + '">' + value.name + '</option>');
                });
                if (selectedValue) {
                    dropdown.val(selectedValue);
                }
                dropdown.prop('disabled', data.length === 0); // Nonaktifkan jika tidak ada data
            }

            // Event listener untuk Provinsi
            $('#provinsi_id').on('change', function() {
                var provinsiId = $(this).val();
                $('#kota_id').val('').prop('disabled', true).find('option:not(:first)').remove();
                $('#kecamatan_id').val('').prop('disabled', true).find('option:not(:first)').remove();
                $('#kelurahan_id').val('').prop('disabled', true).find('option:not(:first)').remove();

                if (provinsiId) {
                    $.ajax({
                        url: '/get-kota-by-provinsi/' + provinsiId,
                        method: 'GET',
                        success: function(data) {
                            populateDropdown('#kota_id', data.map(item => ({
                                id: item.kota_id,
                                name: item.nama_kota
                            })));
                            // Jika ada old input, coba set nilai kota
                            @if (old('kota_id'))
                                $('#kota_id').val('{{ old('kota_id') }}').trigger('change');
                            @endif
                        },
                        error: function(xhr) {
                            console.error('Error fetching kota:', xhr.responseText);
                            Swal.fire('Error', 'Gagal mengambil data Kota/Kabupaten.', 'error');
                        }
                    });
                }
            });

            // Event listener untuk Kota/Kabupaten
            $('#kota_id').on('change', function() {
                var kotaId = $(this).val();
                $('#kecamatan_id').val('').prop('disabled', true).find('option:not(:first)').remove();
                $('#kelurahan_id').val('').prop('disabled', true).find('option:not(:first)').remove();

                if (kotaId) {
                    $.ajax({
                        url: '/get-kecamatan-by-kota/' + kotaId,
                        method: 'GET',
                        success: function(data) {
                            populateDropdown('#kecamatan_id', data.map(item => ({
                                id: item.kecamatan_id,
                                name: item.nama_kecamatan
                            })));
                            // Jika ada old input, coba set nilai kecamatan
                            @if (old('kecamatan_id'))
                                $('#kecamatan_id').val('{{ old('kecamatan_id') }}').trigger(
                                    'change');
                            @endif
                        },
                        error: function(xhr) {
                            console.error('Error fetching kecamatan:', xhr.responseText);
                            Swal.fire('Error', 'Gagal mengambil data Kecamatan.', 'error');
                        }
                    });
                }
            });

            // Event listener untuk Kecamatan
            $('#kecamatan_id').on('change', function() {
                var kecamatanId = $(this).val();
                $('#kelurahan_id').val('').prop('disabled', true).find('option:not(:first)').remove();

                if (kecamatanId) {
                    $.ajax({
                        url: '/get-kelurahan-by-kecamatan/' + kecamatanId,
                        method: 'GET',
                        success: function(data) {
                            populateDropdown('#kelurahan_id', data.map(item => ({
                                id: item.kelurahan_id,
                                name: item.nama_kelurahan
                            })));
                            // Jika ada old input, coba set nilai kelurahan
                            @if (old('kelurahan_id'))
                                $('#kelurahan_id').val('{{ old('kelurahan_id') }}');
                            @endif
                        },
                        error: function(xhr) {
                            console.error('Error fetching kelurahan:', xhr.responseText);
                            Swal.fire('Error', 'Gagal mengambil data Kelurahan.', 'error');
                        }
                    });
                }
            });

            // Picu perubahan saat halaman dimuat jika ada old input (misal setelah validasi gagal)
            @if (old('provinsi_id'))
                $('#provinsi_id').val('{{ old('provinsi_id') }}').trigger('change');
            @endif

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
