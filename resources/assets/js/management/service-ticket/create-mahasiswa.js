/**
 * Create Service Ticket - Mahasiswa
 */

'use strict';

let fv;

document.addEventListener('DOMContentLoaded', function () {
  const formCreateTicket = document.getElementById('form-create-ticket');

  if (formCreateTicket) {
    fv = FormValidation.formValidation(formCreateTicket, {
      fields: {
        layanan_id: {
          validators: {
            notEmpty: { message: 'Pilih layanan wajib diisi' }
          }
        },
        deskripsi: {
          validators: {
            notEmpty: { message: 'Deskripsi permohonan wajib diisi' }
          }
        },
        // SKA Fields
        keperluan: {
          validators: {
            notEmpty: { message: 'Keperluan wajib diisi' }
          }
        },
        tahun_ajaran: {
          validators: {
            notEmpty: { message: 'Tahun ajaran wajib diisi' }
          }
        },
        semester: {
          validators: {
            notEmpty: { message: 'Semester wajib dipilih' }
          }
        },
        keperluan_lainnya: {
          validators: {}
        },
        // Reset Account Fields
        aplikasi: {
          validators: {
            notEmpty: { message: 'Aplikasi wajib dipilih' }
          }
        },
        deskripsi_detail: {
          validators: {
            notEmpty: { message: 'Detail masalah wajib diisi' }
          }
        },
        // Change Data Fields
        data_nama_lengkap: {
          validators: {
            notEmpty: { message: 'Nama lengkap wajib diisi' }
          }
        },
        data_tmp_lahir: {
          validators: {
            notEmpty: { message: 'Tempat lahir wajib diisi' }
          }
        },
        data_tgl_lhr: {
          validators: {
            notEmpty: { message: 'Tanggal lahir wajib diisi' }
          }
        },
        // Publication Fields
        judul_publikasi: {
          validators: {
            notEmpty: { message: 'Judul publikasi wajib diisi' }
          }
        },
        kategori: {
          validators: {
            notEmpty: { message: 'Kategori wajib diisi' }
          }
        },
        konten: {
          validators: {
            notEmpty: { message: 'Konten publikasi wajib diisi' }
          }
        },
        gambar: {
          validators: {
            notEmpty: { message: 'Gambar publikasi wajib di upload' }
          }
        }
      },
      plugins: {
        trigger: new FormValidation.plugins.Trigger(),
        bootstrap5: new FormValidation.plugins.Bootstrap5({
          eleValidClass: '',
          rowSelector: '.mb-3'
        }),
        submitButton: new FormValidation.plugins.SubmitButton(),
        autoFocus: new FormValidation.plugins.AutoFocus()
      },
      init: instance => {
        instance.on('plugins.message.placed', function (e) {
          if (e.element.parentElement.classList.contains('input-group')) {
            e.element.parentElement.insertAdjacentElement('afterend', e.messageElement);
          }
        });

        instance.on('core.form.valid', function () {
          Swal.fire({
            title: 'Memproses...',
            allowOutsideClick: false,
            allowEscapeKey: false,
            allowEnterKey: false,
            didOpen: () => {
              Swal.showLoading();
            }
          });
          formCreateTicket.submit();
        });
      }
    });
  }

  function toggleSpecificForm() {
    // Hide all specific forms & disable their inputs & validators
    const forms = document.querySelectorAll('.specific-form');
    forms.forEach(el => {
      el.classList.add('d-none');
      const inputs = el.querySelectorAll('input, select, textarea');
      inputs.forEach(input => {
        input.disabled = true;
        // Disable validator for hidden fields
        if (fv && input.name) {
          fv.disableValidator(input.name);
        }
      });
    });

    const select = document.getElementById('layanan_id');
    const selectedOption = select.options[select.selectedIndex];
    const namaLayanan = selectedOption.getAttribute('data-nama');

    if (!namaLayanan) return;

    let activeFormId = null;
    if (namaLayanan.includes('Surat Keterangan Aktif')) {
      activeFormId = 'form-ska';
    } else if (namaLayanan.includes('Reset Akun')) {
      activeFormId = 'form-reset';
    } else if (namaLayanan.includes('Ubah Data')) {
      activeFormId = 'form-ubah-data';
    } else if (namaLayanan.includes('Publikasi')) {
      activeFormId = 'form-publikasi';
    }

    // Show and enable the active form & its validators
    if (activeFormId) {
      const activeForm = document.getElementById(activeFormId);
      activeForm.classList.remove('d-none');
      const inputs = activeForm.querySelectorAll('input, select, textarea');
      inputs.forEach(input => {
        input.disabled = false;
        // Enable validator for visible fields
        if (fv && input.name) {
          fv.enableValidator(input.name);
        }
      });
    }

    // Revalidate form to update field status
    if (fv) {
      fv.revalidateField('layanan_id');
    }
  }

  const layananSelect = document.getElementById('layanan_id');
  if (layananSelect) {
    layananSelect.addEventListener('change', toggleSpecificForm);
  }

  const imgInp = document.getElementById('imgInp');
  const previewBox = document.getElementById('preview-box');
  const previewImg = document.getElementById('preview-img');
  const previewLink = document.getElementById('preview-link');
  const previewBtnLink = document.getElementById('preview-btn-link');

  if (imgInp) {
    imgInp.addEventListener('change', function (evt) {
      const [file] = imgInp.files;

      if (file) {
        // FILE SIZE VALIDATION (CLIENT SIDE)
        const fileSizeMB = file.size / 1024 / 1024;
        const maxFileSize = 2; // 2MB

        if (fileSizeMB > maxFileSize) {
          Swal.fire({
            icon: 'warning',
            title: 'Ukuran File Terlalu Besar!',
            text: 'Ukuran gambar maksimal adalah 2MB. File Anda berukuran ' + fileSizeMB.toFixed(2) + 'MB.',
            confirmButtonText: 'Ganti Gambar',
            confirmButtonColor: '#ff9f43',
            customClass: {
              confirmButton: 'btn btn-warning'
            },
            buttonsStyling: false
          });
          imgInp.value = '';
          previewBox.classList.add('d-none');
          return;
        }

        // Show preview
        const objectUrl = URL.createObjectURL(file);
        previewImg.src = objectUrl;
        previewLink.href = objectUrl;
        previewBtnLink.href = objectUrl;
        previewBox.classList.remove('d-none');
      } else {
        previewBox.classList.add('d-none');
      }
    });
  }

  toggleSpecificForm(); // Reset state on page load

  // Check for server-side upload error
  const urlParams = new URLSearchParams(window.location.search);
  if (urlParams.has('upload_error')) {
    Swal.fire({
      icon: 'error',
      title: 'File Terlalu Besar!',
      text: 'Ukuran file melebihi batas server. Silakan kompres file Anda.',
      confirmButtonColor: '#ea5455'
    });
    window.history.replaceState(null, null, window.location.pathname);
  }

  // Laravel validation error
  if (document.querySelector('.invalid-feedback:not(:empty)')) {
    Swal.fire({
      icon: 'error',
      title: 'Gagal Membuat Tiket!',
      text: 'Mohon periksa kembali inputan Kamu yang berwarna merah.',
      confirmButtonText: 'Periksa Inputan',
      confirmButtonColor: '#ea5455',
      customClass: {
        confirmButton: 'btn btn-danger'
      },
      buttonsStyling: false
    });
  }
});
