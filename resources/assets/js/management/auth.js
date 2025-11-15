/**
 *  Pages Authentication
 */
'use strict';

document.addEventListener('DOMContentLoaded', function () {
  (() => {
    const formAuthentication = document.querySelector('#formAuthentication');

    // Form validation for Add new record
    if (formAuthentication && typeof FormValidation !== 'undefined') {
      FormValidation.formValidation(formAuthentication, {
        fields: {
          username: {
            validators: {
              notEmpty: {
                message: 'Silakan masukkan nama pengguna'
              },
              stringLength: {
                min: 6,
                message: 'Nama pengguna harus lebih dari 6 karakter'
              }
            }
          },
          email: {
            validators: {
              notEmpty: {
                message: 'Silakan masukkan email Kamu'
              },
              emailAddress: {
                message: 'Silakan masukkan alamat email yang valid'
              }
            }
          },
          'email-username': {
            validators: {
              notEmpty: {
                message: 'Silakan masukkan email / nama pengguna'
              },
              stringLength: {
                min: 6,
                message: 'Nama pengguna harus lebih dari 6 karakter'
              }
            }
          },
          password: {
            validators: {
              notEmpty: {
                message: 'Silakan masukkan password Kamu'
              },
              stringLength: {
                min: 6,
                message: 'Password harus lebih dari 6 karakter'
              }
            }
          },
          'confirm-password': {
            validators: {
              notEmpty: {
                message: 'Harap konfirmasi password'
              },
              identical: {
                compare: () => formAuthentication.querySelector('[name="password"]').value,
                message: 'Password dan konfirmasinya tidak cocok'
              },
              stringLength: {
                min: 6,
                message: 'Password harus lebih dari 6 karakter'
              }
            }
          },
          terms: {
            validators: {
              notEmpty: {
                message: 'Harap setujui syarat & ketentuan'
              }
            }
          }
        },
        plugins: {
          trigger: new FormValidation.plugins.Trigger(),
          bootstrap5: new FormValidation.plugins.Bootstrap5({
            eleValidClass: '',
            rowSelector: '.form-control-validation'
          }),
          submitButton: new FormValidation.plugins.SubmitButton(),
          defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
          autoFocus: new FormValidation.plugins.AutoFocus()
        },
        init: instance => {
          instance.on('plugins.message.placed', e => {
            if (e.element.parentElement.classList.contains('input-group')) {
              e.element.parentElement.insertAdjacentElement('afterend', e.messageElement);
            }
          });
        }
      });
    }

    // Two Steps Verification for numeral input mask
    const numeralMaskElements = document.querySelectorAll('.numeral-mask');

    // Format function for numeral mask
    const formatNumeral = value => value.replace(/\D/g, ''); // Only keep digits

    if (numeralMaskElements.length > 0) {
      numeralMaskElements.forEach(numeralMaskEl => {
        numeralMaskEl.addEventListener('input', event => {
          numeralMaskEl.value = formatNumeral(event.target.value);
        });
      });
    }
  })();
});
