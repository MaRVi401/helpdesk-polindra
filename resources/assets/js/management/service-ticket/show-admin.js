/**
 * Show Service Ticket - Admin
 */

document.addEventListener('DOMContentLoaded', function () {
  const countdownEl = document.getElementById('admin-countdown');
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

  (function initStatusFormValidation() {
    // Check if FormValidation is loaded
    if (typeof FormValidation === 'undefined') {
      console.warn('FormValidation library not loaded');
      return;
    }

    if (typeof Swal === 'undefined') {
      console.warn('SweetAlert2 library not loaded');
      return;
    }

    const form = document.getElementById('form-update-status');
    if (!form) {
      console.warn('Form #form-update-status not found');
      return;
    }

    const statusField = form.querySelector('select[name="status"]');
    if (!statusField) {
      console.warn('Status field not found');
      return;
    }

    // Check if field is disabled
    if (statusField.disabled) {
      console.log('Status field is disabled, skipping validation');
      return;
    }

    try {
      const fv = FormValidation.formValidation(form, {
        fields: {
          status: {
            validators: {
              notEmpty: {
                message: 'Pilih Tindakan Selanjutnya'
              }
            }
          }
        },
        plugins: {
          trigger: new FormValidation.plugins.Trigger(),
          bootstrap5: new FormValidation.plugins.Bootstrap5({
            eleValidClass: '',
            rowSelector: '.col-12'
          }),
          submitButton: new FormValidation.plugins.SubmitButton(),
          autoFocus: new FormValidation.plugins.AutoFocus()
        }
      });

      // Handle message placement
      fv.on('plugins.message.placed', function (e) {
        if (e.element.parentElement && e.element.parentElement.classList.contains('input-group')) {
          e.element.parentElement.insertAdjacentElement('afterend', e.messageElement);
        }
      });

      // When form is valid, show confirmation
      fv.on('core.form.valid', function () {
        console.log('Form valid - showing confirmation');

        Swal.fire({
          title: 'Simpan perubahan status?',
          text: 'Status akan disimpan dan diberlakukan pada tiket ini.',
          icon: 'question',
          showCancelButton: true,
          confirmButtonText: 'Ya, Simpan',
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
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
              submitBtn.setAttribute('disabled', 'disabled');
              submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Menyimpan...';
            }

            Swal.fire({
              title: 'Memproses...',
              text: 'Mohon tunggu sebentar',
              allowOutsideClick: false,
              allowEscapeKey: false,
              didOpen: function () {
                Swal.showLoading();
              }
            });

            // Submit the form
            form.submit();
          }
        });
      });

      // When form is invalid, show error message
      fv.on('core.form.invalid', function () {
        console.log('Form invalid - validation failed');
      });

      console.log('FormValidation initialized successfully');
    } catch (e) {
      console.error('FormValidation init failed:', e);
    }
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
