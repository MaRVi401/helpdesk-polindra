<!DOCTYPE html>
<html>

<head>
    <title>Tambah Layanan Baru</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: #f8fafc;
            color: #2d3748;
            line-height: 1.5;
            padding: 20px;
        }

        .main-container {
            background-color: white;
            padding: 32px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
        }

        h1 {
            margin-top: 0;
        }

        .button {
            display: inline-block;
            padding: 10px 16px;
            border: 1px solid transparent;
            border-radius: 5px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            font-size: 0.95rem;
        }

        .button-primary {
            background-color: #4299e1;
            color: white;
            border-color: #4299e1;
        }

        .button-danger {
            background-color: #f56565;
            color: white;
            border-color: #f56565;
            padding: 6px 12px;
        }

        .button-secondary {
            background-color: #e2e8f0;
            color: #2d3748;
            border-color: #cbd5e0;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: #4a5568;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #cbd5e0;
            border-radius: 4px;
            font-size: 0.95rem;
            box-sizing: border-box;
        }

        .form-actions {
            margin-top: 2rem;
            display: flex;
            gap: 10px;
        }

        .alert {
            padding: 1rem;
            margin-bottom: 1.5rem;
            border-radius: 4px;
        }

        .alert-error {
            color: #9b2c2c;
            background-color: #fed7d7;
        }

        .alert-error ul {
            margin: 0;
            padding-left: 20px;
        }

        /* Style untuk PIC list */
        .pic-list {
            margin-top: 1rem;
            padding-left: 0;
            list-style: none;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 0 10px;
        }

        .pic-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px dotted #e2e8f0;
        }

        .pic-item:last-child {
            border-bottom: none;
        }

        .pic-info {
            font-weight: 500;
        }

        .pic-actions {
            margin-left: 10px;
        }

        .pic-placeholder {
            color: #718096;
            font-style: italic;
            padding: 8px 0;
        }
    </style>
</head>

<body>
    <div class="main-container">
        <h1>Tambah Layanan Baru</h1>

        @if ($errors->any())
            <div class="alert alert-error">
                <strong>Oops! Ada beberapa masalah:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.layanan.store') }}" method="POST" id="layananForm">
            @csrf

            <h2>Data Dasar Layanan</h2>
            <div class="form-group">
                <label for="nama">Nama Layanan</label>
                <input type="text" name="nama" id="nama" value="{{ old('nama') }}" required>
            </div>

            <div class="form-group">
                <label for="unit_id">Unit Penanggung Jawab</label>
                <select name="unit_id" id="unit_id" required>
                    <option value="">-- Pilih Unit --</option>
                    @foreach ($units as $unit)
                        <option value="{{ $unit->id }}" {{ old('unit_id') == $unit->id ? 'selected' : '' }}>
                            {{ $unit->nama_unit }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="prioritas">Prioritas (0 = Terendah)</label>
                <input type="number" name="prioritas" id="prioritas" value="{{ old('prioritas', 0) }}" min="0">
            </div>

            <div class="form-group">
                <label for="status_arsip">Status Arsip</label>
                <select name="status_arsip" id="status_arsip" required>
                    <option value="0" {{ old('status_arsip', 0) == 0 ? 'selected' : '' }}>Tidak Diarsip (Aktif)
                    </option>
                    <option value="1" {{ old('status_arsip') == 1 ? 'selected' : '' }}>Diarsip (Non-aktif)</option>
                </select>
            </div>

            <hr style="margin: 2rem 0 1rem;">
            <h2>Kelola Penanggung Jawab (PIC)</h2>

            {{-- BAGIAN 1: TAMBAH PIC (Single Select) --}}
            <div class="form-group" style="display: flex; gap: 10px;">
                <select id="pic_to_add_select" style="flex-grow: 1;">
                    <option value="">-- Pilih Staff untuk PIC --</option>
                    @foreach ($allStaff as $staff)
                        <option value="{{ $staff->id }}" data-name="{{ $staff->user->name ?? 'N/A' }}"
                            data-nik="{{ $staff->nik ?? 'N/A' }}">
                            {{-- Format: Nama Staff (NIK: [NIK]) --}}
                            {{ $staff->user->name ?? 'N/A' }} (NIK: {{ $staff->nik ?? 'N/A' }})
                        </option>
                    @endforeach
                </select>
                <button type="button" id="addPicButton" class="button button-primary" style="width: 150px;">Tambahkan
                    PIC</button>
            </div>

            {{-- BAGIAN 2: DAFTAR PIC AKTIF (List Interaktif) --}}
            <h3 style="margin-top: 1rem;">PIC Aktif (<span id="picCount">0</span>)</h3>
            <ul class="pic-list" id="activePicList">
                <li class="pic-placeholder">Belum ada Penanggung Jawab yang ditetapkan.</li>
            </ul>

            {{-- HIDDEN INPUT: Placeholder untuk dihapus dan diganti dengan array saat submit --}}
            <input type="hidden" name="penanggung_jawab_ids[]" id="hiddenPicIds">

            <div class="form-actions">
                <button type="submit" class="button button-primary">Simpan Layanan</button>
                <a href="{{ route('admin.layanan.index') }}" class="button button-secondary">Batal</a>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectElement = document.getElementById('pic_to_add_select');
            const addButton = document.getElementById('addPicButton');
            const activeList = document.getElementById('activePicList');
            const picCountSpan = document.getElementById('picCount');

            // Untuk melacak PIC yang sudah ditambahkan: { id: { name: '...', nik: '...' } }
            let activePICs = {}; 

            function updateList() {
                activeList.innerHTML = '';
                let count = 0;

                if (Object.keys(activePICs).length === 0) {
                    activeList.innerHTML = '<li class="pic-placeholder">Belum ada Penanggung Jawab yang ditetapkan.</li>';
                } else {
                    for (const id in activePICs) {
                        const pic = activePICs[id];
                        count++;
                        
                        const listItem = document.createElement('li');
                        listItem.classList.add('pic-item');
                        listItem.dataset.id = id;
                        listItem.innerHTML = `
                            <div class="pic-info">
                                ${pic.name} (NIK: ${pic.nik})
                            </div>
                            <div class="pic-actions">
                                <button type="button" class="button button-danger remove-pic-btn" data-id="${id}">Hapus</button>
                            </div>
                        `;
                        activeList.appendChild(listItem);
                    }
                }
                picCountSpan.textContent = count;
                
                // Re-enable/disable options di select box
                const options = selectElement.options;
                for (let i = 0; i < options.length; i++) {
                    if (options[i].value !== '') {
                        options[i].disabled = activePICs.hasOwnProperty(options[i].value);
                    }
                }
                selectElement.value = ""; // Reset select
            }

            addButton.addEventListener('click', function() {
                const selectedId = selectElement.value;
                const selectedOption = selectElement.options[selectElement.selectedIndex];

                if (selectedId && !activePICs.hasOwnProperty(selectedId)) {
                    activePICs[selectedId] = {
                        name: selectedOption.dataset.name,
                        nik: selectedOption.dataset.nik
                    };
                    updateList();
                }
            });

            activeList.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-pic-btn')) {
                    const idToRemove = e.target.dataset.id;
                    if (confirm('Anda yakin ingin menghapus PIC ini?')) {
                        delete activePICs[idToRemove];
                        updateList();
                    }
                }
            });
            
            // Handle form submission: inject selected IDs into the form as multiple hidden inputs
            document.getElementById('layananForm').addEventListener('submit', function(e) {
                // Hapus placeholder hidden input
                const oldHiddenInput = document.getElementById('hiddenPicIds');
                if (oldHiddenInput) oldHiddenInput.remove();

                // Buat hidden input baru untuk setiap ID agar Laravel membacanya sebagai array
                for (const id in activePICs) {
                    const newHiddenInput = document.createElement('input');
                    newHiddenInput.type = 'hidden';
                    newHiddenInput.name = 'penanggung_jawab_ids[]';
                    newHiddenInput.value = id;
                    this.appendChild(newHiddenInput);
                }
            });
            
            updateList();
        });
    </script>
</body>

</html>