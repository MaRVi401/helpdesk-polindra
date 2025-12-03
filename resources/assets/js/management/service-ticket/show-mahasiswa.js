/**
 * Show Service Ticket - Mahasiswa
 */

document.addEventListener('DOMContentLoaded', function () {
  const countdownEl = document.getElementById('mahasiswa-countdown');
  if (countdownEl) {
    const deadlineStr = countdownEl.dataset && countdownEl.dataset.deadline ? countdownEl.dataset.deadline : null;
    if (deadlineStr) {
      const isoStr = deadlineStr.replace(' ', 'T');
      const countDownDate = new Date(isoStr).getTime();

      if (!isNaN(countDownDate)) {
        const update = function () {
          const now = new Date().getTime();
          const distance = countDownDate - now;
          if (distance < 0) {
            clearInterval(timerId);
            countdownEl.innerHTML = 'WAKTU HABIS - Sedang Memproses...';
            setTimeout(function () {
              location.reload();
            }, 2000);
            return;
          }

          const days = Math.floor(distance / (1000 * 60 * 60 * 24));
          const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
          const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
          const seconds = Math.floor((distance % (1000 * 60)) / 1000);

          countdownEl.innerHTML = days + ' Hari ' + hours + ' Jam ' + minutes + ' Menit ' + seconds + ' Detik';
        };

        update();
        const timerId = setInterval(update, 1000);
      }
    }
  }

  (function initConfirmCompletion() {
    // Check if SweetAlert2 is loaded
    if (typeof Swal === 'undefined') {
      console.warn('SweetAlert2 library not loaded');
      return;
    }

    // Form SETUJU (Dinilai_Selesai_oleh_Pemohon)
    const confirmCompletion = document.getElementById('form-confirm-completion');
    if (confirmCompletion) {
      const btnConfirm = confirmCompletion.querySelector('button[type="submit"]');
      if (btnConfirm) {
        btnConfirm.addEventListener('click', function (e) {
          e.preventDefault();

          Swal.fire({
            title: 'Konfirmasi Penyelesaian',
            text: 'Apakah Anda yakin ingin menyelesaikan tiket ini?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Selesaikan',
            cancelButtonText: 'Batal',
            customClass: {
              confirmButton: 'btn btn-primary me-2',
              cancelButton: 'btn btn-outline-secondary'
            },
            buttonsStyling: false,
            reverseButtons: false,
            allowOutsideClick: false
          }).then(function (result) {
            if (result.isConfirmed) {
              // Disable button dan tampilkan loading
              btnConfirm.setAttribute('disabled', 'disabled');
              btnConfirm.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Memproses...';

              // Tampilkan loading overlay
              Swal.fire({
                title: 'Memproses...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: function () {
                  Swal.showLoading();
                }
              });

              // Submit form
              confirmCompletion.submit();
            }
          });
        });
        console.log('Button Setuju initialized');
      }
    }

    // Form BELUM SELESAI (Dinilai_Belum_Selesai_oleh_Pemohon)
    const notCompleted = document.getElementById('form-not-completed');
    if (notCompleted) {
      const btnNotCompleted = notCompleted.querySelector('button[type="submit"]');
      if (btnNotCompleted) {
        btnNotCompleted.addEventListener('click', function (e) {
          e.preventDefault();

          Swal.fire({
            title: 'Tiket Belum Selesai?',
            text: 'Status tiket akan berubah menjadi "Dinilai Belum Selesai oleh Pemohon". Lanjutkan?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Lanjutkan',
            cancelButtonText: 'Batal',
            customClass: {
              confirmButton: 'btn btn-danger me-2',
              cancelButton: 'btn btn-outline-secondary'
            },
            buttonsStyling: false,
            reverseButtons: false,
            allowOutsideClick: false
          }).then(function (result) {
            if (result.isConfirmed) {
              // Disable button dan tampilkan loading
              btnNotCompleted.setAttribute('disabled', 'disabled');
              btnNotCompleted.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Memproses...';

              // Tampilkan loading overlay
              Swal.fire({
                title: 'Memproses...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: function () {
                  Swal.showLoading();
                }
              });

              // Submit form
              notCompleted.submit();
            }
          });
        });
        console.log('Button Belum Selesai initialized');
      }
    }

    console.log('SweetAlert konfirmasi penyelesaian initialized');
  })();

  // Flash messages (support different variable names for compatibility)
  const successMsg = window.serviceTicketSuccessMessage || window.serviceSuccessMessage || window.serviceSuccess;
  const errorMsg =
    window.serviceTicketErrorMessage ||
    window.serviceErrorMessage ||
    window.serviceTickeErrorMessage ||
    window.serviceError;

  if (successMsg) {
    Swal.fire({
      icon: 'success',
      title: 'Berhasil!',
      text: successMsg,
      customClass: { confirmButton: 'btn btn-primary waves-effect waves-light' },
      buttonsStyling: false
    });
  }

  if (errorMsg) {
    Swal.fire({
      icon: 'error',
      title: 'Gagal!',
      text: errorMsg,
      customClass: { confirmButton: 'btn btn-primary waves-effect waves-light' },
      buttonsStyling: false
    });
  }
});
