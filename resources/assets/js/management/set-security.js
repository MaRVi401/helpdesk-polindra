document.addEventListener('DOMContentLoaded', function (e) {
  (function () {
    const formChangePass = document.querySelector('#formAccountSettings');

    // Form validation for Change password
    if (formChangePass) {
      // Check if user has password (passed from blade)
      const hasPassword = typeof window.hasPassword !== 'undefined' ? window.hasPassword : true;

      // Dynamic validation fields based on password status
      const validationFields = {
        newPassword: {
          validators: {
            notEmpty: {
              message: 'Password baru wajib diisi'
            },
            stringLength: {
              min: 8,
              message: 'Password minimal 8 karakter'
            }
          }
        },
        confirmPassword: {
          validators: {
            notEmpty: {
              message: 'Konfirmasi password wajib diisi'
            },
            identical: {
              compare: function () {
                return formChangePass.querySelector('[name="newPassword"]').value;
              },
              message: 'Konfirmasi password tidak cocok'
            },
            stringLength: {
              min: 8,
              message: 'Password minimal 8 karakter'
            }
          }
        }
      };

      // Add currentPassword validation only if user has password
      if (hasPassword) {
        validationFields.currentPassword = {
          validators: {
            notEmpty: {
              message: 'Password saat ini wajib diisi'
            },
            stringLength: {
              min: 8,
              message: 'Password minimal 8 karakter'
            }
          }
        };
      }

      const fv = FormValidation.formValidation(formChangePass, {
        fields: validationFields,
        plugins: {
          trigger: new FormValidation.plugins.Trigger(),
          bootstrap5: new FormValidation.plugins.Bootstrap5({
            eleValidClass: '',
            rowSelector: '.form-control-validation'
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

          // Submit form on validation success
          instance.on('core.form.valid', function () {
            formChangePass.submit();
          });
        }
      });
    }

    // Display success message
    if (typeof window.passwordSuccessMessage !== 'undefined' && window.passwordSuccessMessage) {
      console.log('Showing success message:', window.passwordSuccessMessage);
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          icon: 'success',
          title: 'Berhasil!',
          text: window.passwordSuccessMessage,
          customClass: {
            confirmButton: 'btn btn-primary waves-effect waves-light'
          },
          buttonsStyling: false
        }).then(() => {
          // Clear the message after showing
          window.passwordSuccessMessage = undefined;
        });
      } else {
        alert(window.passwordSuccessMessage);
        window.passwordSuccessMessage = undefined;
      }
    }

    // Display error message
    if (typeof window.passwordErrorMessage !== 'undefined' && window.passwordErrorMessage) {
      console.log('Showing error message:', window.passwordErrorMessage);
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          icon: 'error',
          title: 'Gagal!',
          text: window.passwordErrorMessage,
          customClass: {
            confirmButton: 'btn btn-primary waves-effect waves-light'
          },
          buttonsStyling: false
        }).then(() => {
          // Clear the message after showing
          window.passwordErrorMessage = undefined;
        });
      } else {
        alert(window.passwordErrorMessage);
        window.passwordErrorMessage = undefined;
      }
    }
  })();
});
