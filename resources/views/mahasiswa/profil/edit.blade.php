<!DOCTYPE html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed" dir="ltr" data-theme="theme-default"
  data-assets-path="/assets/" data-template="vertical-menu-template-starter">

<head>
  <meta charset="utf-t" />
  <meta name="viewport"
    content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
  <title>Edit Profil Mahasiswa</title>
  
  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">
  
  <!-- Icons -->
  <link rel="stylesheet" href="/assets/vendor/fonts/tabler-icons.css" />
  
  <!-- Core CSS -->
  <link rel="stylesheet" href="/assets/vendor/css/core.css" />
  <link rel="stylesheet" href="/assets/vendor/css/theme-default.css" />
  <link rel="stylesheet" href="/assets/css/demo.css" />
  
  <!-- Vendors CSS -->
  <link rel="stylesheet" href="/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
  
  <!-- Helpers -->
  <script src="/assets/vendor/js/helpers.js"></script>
  <script src="/assets/js/config.js"></script>
</head>

<body>
  <!-- Layout wrapper -->
  <div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">

      <!-- !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! -->
      <!-- !!   MENU SIDEBAR DAN NAVBAR ANDA HARUS ADA DI   !! -->
      <!-- !!   SEKITAR SINI (DALAM 'layout-container')     !! -->
      <!-- !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! -->
      
      <!-- Layout container -->
      <div class="layout-page">
        <!-- Navbar -->
        <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme" id="layout-navbar">
          <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
            <!-- Ini adalah placeholder navbar. Ganti dengan navbar Anda yang sebenarnya -->
            <ul class="navbar-nav flex-row align-items-center ms-auto">
              <li class="nav-item">
                <a class="nav-link" href="#">Placeholder Navbar</a>
              </li>
            </ul>
          </div>
        </nav>
        <!-- /Navbar -->

        <!-- Content wrapper -->
        <div class="content-wrapper">
          <!-- Content -->
          <div class="container-xxl flex-grow-1 container-p-y">
            
            <h4 class="fw-bold py-3 mb-4">
              <span class="text-muted fw-light">Akun /</span> Edit Profil
            </h4>

            <div class="row">
              <div class="col-md-12">
                
                <!-- Placeholder untuk Notifikasi Sukses -->
                <!-- 
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                  Profil berhasil diperbarui!
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div> 
                -->

                <!-- Placeholder untuk Notifikasi Error -->
                <!--
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                  <h5 class="alert-heading mb-2">Terjadi Kesalahan:</h5>
                  <ul class="mb-0">
                    <li>Nama tidak boleh kosong.</li>
                    <li>Email tidak valid.</li>
                  </ul>
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                -->

                <div class="card mb-4">
                  <h5 class="card-header">Detail Profil</h5>
                  <hr class="my-0">
                  <div class="card-body">
                    
                    <!-- PENTING: Ganti [CSRF_TOKEN_HERE] dengan '{{ csrf_token() }}' saat Anda mengubah file ini menjadi .blade.php -->
                    <!-- PENTING: Ganti action URL jika perlu, tapi '/mahasiswa/profil' harusnya sudah benar -->

                    <form id="formAccountSettings" method="POST" action="/mahasiswa/profil" enctype="multipart/form-data">
                      
                      <!-- Ini adalah pengganti @csrf -->
                      <input type="hidden" name="_token" value="[CSRF_TOKEN_HERE]"> 
                      
                      <!-- Ini adalah pengganti @method('PATCH') -->
                      <input type="hidden" name="_method" value="PATCH">

                      <!-- Bagian Upload Foto -->
                      <div class="d-flex align-items-start align-items-sm-center gap-4 mb-4">
                        <img src="/assets/img/avatars/1.png" alt="user-avatar" class="d-block w-px-100 h-px-100 rounded" id="uploadedAvatar" />
                        <div class="button-wrapper">
                          <label for="foto_profil" class="btn btn-primary me-2 mb-3" tabindex="0">
                            <span class="d-none d-sm-block">Upload foto baru</span>
                            <i class="ti ti-upload d-block d-sm-none"></i>
                            <input type="file" id="foto_profil" name="foto_profil" class="account-file-input" hidden accept="image/png, image/jpeg" />
                          </label>
                          <button type="button" class="btn btn-label-secondary account-image-reset mb-3">
                            <i class="ti ti-refresh-dot d-block d-sm-none"></i>
                            <span class="d-none d-sm-block">Reset</span>
                          </button>
                          <p class="text-muted mb-0">Hanya JPG atau PNG. Ukuran maks 2MB.</p>
                        </div>
                      </div>

                      <!-- Bagian Form Fields (Gunakan data placeholder) -->
                      <div class="row">
                        <div class="mb-3 col-md-6">
                          <label for="name" class="form-label">Nama Lengkap</label>
                          <input class="form-control" type="text" id="name" name="name" value="Nama Mahasiswa Placeholder" required autofocus />
                        </div>
                        <div class="mb-3 col-md-6">
                          <label for="email" class="form-label">E-mail</label>
                          <input class="form-control" type="email" id="email" name="email" value="mahasiswa@example.com" required />
                        </div>
                        <div class="mb-3 col-md-6">
                          <label for="nim" class="form-label">NIM</label>
                          <input class="form-control" type="text" id="nim" name="nim" value="123456789" required />
                        </div>
                        <div class="mb-3 col-md-6">
                          <label for="id_program_studi" class="form-label">Program Studi</label>
                          <select id="id_program_studi" name="id_program_studi" class="form-select" required>
                            <option value="">Pilih Program Studi</option>
                            <!-- Data ini harus diisi oleh controller -->
                            <option value="1" selected>Teknik Informatika (Placeholder)</option>
                            <option value="2">Teknik Mesin (Placeholder)</option>
                          </select>
                        </div>
                        <div class="mb-3 col-md-6">
                          <label for="tahun_masuk" class="form-label">Tahun Masuk</label>
                          <select id="tahun_masuk" name="tahun_masuk" class="form-select" required>
                            <option value="">Pilih Tahun Masuk</option>
                            <!-- Data ini harus diisi oleh controller -->
                            <option value="2023">2023</option>
                            <option value="2022" selected>2022</option>
                            <option value="2021">2021</option>
                          </select>
                        </div>
                        <div class="mb-3 col-md-6">
                          <label for="no_hp" class="form-label">Nomor HP</label>
                          <input class="form-control" type="text" id="no_hp" name="no_hp" value="08123456789" placeholder="08..." />
                        </div>
                        <div class="mb-3 col-md-12">
                          <label for="alamat" class="form-label">Alamat</label>
                          <textarea class="form-control" id="alamat" name="alamat" rows="3">Alamat lengkap mahasiswa placeholder.</textarea>
                        </div>
                      </div>
                      <div class="mt-2">
                        <button type="submit" class="btn btn-primary me-2">Simpan Perubahan</button>
                        <button type="reset" class="btn btn-label-secondary">Reset</button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            </div>

          </div>
          <!-- / Content -->

          <!-- Footer -->
          <footer class="content-footer footer bg-footer-theme">
            <div class="container-xxl">
              <div class="footer-container d-flex align-items-center justify-content-between py-2 flex-md-row flex-column">
                <div>
                  © <script>document.write(new Date().getFullYear())</script>, Helpdesk Polindra
                </div>
              </div>
            </div>
          </footer>
          <!-- / Footer -->

          <div class="content-backdrop fade"></div>
        </div>
        <!-- Content wrapper -->
      </div>
      <!-- / Layout page -->
    </div>
  </div>
  <!-- / Layout wrapper -->

  <!-- Core JS -->
  <script src="/assets/vendor/libs/jquery/jquery.js"></script>
  <script src="/assets/vendor/libs/popper/popper.js"></script>
  <script src="/assets/vendor/js/bootstrap.js"></script>
  <script src="/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
  <script src="/assets/vendor/js/menu.js"></script>
  
  <!-- Main JS -->
  <script src="/assets/js/main.js"></script>

  <!-- Script untuk Preview Gambar -->
  <script>
    document.addEventListener('DOMContentLoaded', function (e) {
      (function () {
        const accountFileReset = document.querySelector('.account-image-reset');
        const accountFileInput = document.querySelector('.account-file-input');
        const accountUserAvatar = document.getElementById('uploadedAvatar');

        if (accountUserAvatar) {
          const defaultImage = accountUserAvatar.src;

          accountFileInput.onchange = () => {
            if (accountFileInput.files[0]) {
              accountUserAvatar.src = window.URL.createObjectURL(accountFileInput.files[0]);
            }
          };

          accountFileReset.onclick = () => {
            accountFileInput.value = '';
            accountUserAvatar.src = defaultImage;
          };
        }
      })();
    });
  </script>

</body>
</html>
