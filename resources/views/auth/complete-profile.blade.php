<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lengkapi Profil Anda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4>Lengkapi Profil Mahasiswa</h4>
                        <p>Selamat datang! Silakan lengkapi data Anda untuk melanjutkan.</p>
                    </div>
                    <div class="card-body">
                        {{-- Tampilkan error validasi --}}
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('profile.completion.save') }}">
                            @csrf

                            {{-- Field NIM --}}
                            <div class="mb-3">
                                <label for="nim" class="form-label">NIM (Nomor Induk Mahasiswa)</label>
                                <input type="text" class="form-control" id="nim" name="nim" value="{{ old('nim') }}" required>
                            </div>

                            {{-- Field Program Studi --}}
                            <div class="mb-3">
                                <label for="program_studi_id" class="form-label">Program Studi</label>
                                <select class="form-select" id="program_studi_id" name="program_studi_id" required>
                                    <option selected disabled>-- Pilih Program Studi --</option>
                                    @foreach ($programStudi as $prodi)
                                        <option value="{{ $prodi->id }}">{{ $prodi->program_studi }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Field Tahun Masuk --}}
                            <div class="mb-3">
                                <label for="tahun_masuk" class="form-label">Tahun Masuk</label>
                                <input type="number" class="form-control" id="tahun_masuk" name="tahun_masuk" placeholder="Contoh: 2023" value="{{ old('tahun_masuk') }}" required>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Simpan dan Lanjutkan</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>