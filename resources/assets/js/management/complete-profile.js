/**
 *  Auth: Complete Profile
 */

'use strict';

// Select2 (jquery)
$(function () {
  var select2 = $('.select2');

  // select2
  if (select2.length) {
    select2.each(function () {
      var $this = $(this);
      $this.wrap('<div class="position-relative"></div>');
      $this.select2({
        placeholder: 'Pilih opsi',
        dropdownParent: $this.parent(),
        language: {
          noResults: function () {
            return 'Tidak ada data yang ditemukan';
          },
          searching: function () {
            return 'Mencari...';
          }
        }
      });
    });
  }
});

// Complete Profile Validation
// --------------------------------------------------------------------
document.addEventListener('DOMContentLoaded', function (e) {
  (function () {
    const stepsValidation = document.querySelector('#StepsValidation');
    if (typeof stepsValidation !== undefined && stepsValidation !== null) {
      // Complete Profile form
      const stepsValidationForm = stepsValidation.querySelector('#StepsForm');
      // Form steps
      const stepsValidationFormStep2 = stepsValidationForm.querySelector('#InfoValidation');

      let validationStepper = new Stepper(stepsValidation, {
        linear: true
      });

      // Langsung buka step Personal Info (step 2) karena step 1 hanya penanda
      try {
        validationStepper.to(2); // Langsung ke step pengguna
      } catch (e) {
        console.log('Step navigation error:', e);
      }

      // Info validation
      const Steps = FormValidation.formValidation(stepsValidationFormStep2, {
        fields: {
          nim: {
            validators: {
              notEmpty: {
                message: 'Harap masukkan NIM'
              },
              numeric: {
                message: 'NIM harus berupa angka'
              },
              stringLength: {
                min: 8,
                max: 15,
                message: 'NIM harus antara 8 dan 15 karakter'
              }
            }
          },
          program_studi_id: {
            validators: {
              notEmpty: {
                message: 'Harap pilih program studi'
              }
            }
          },
          tahun_masuk: {
            validators: {
              notEmpty: {
                message: 'Harap pilih tahun masuk'
              },
              numeric: {
                message: 'Tahun masuk harus berupa angka'
              },
              stringLength: {
                min: 4,
                max: 4,
                message: 'Tahun masuk harus 4 digit'
              }
            }
          }
        },
        plugins: {
          trigger: new FormValidation.plugins.Trigger(),
          bootstrap5: new FormValidation.plugins.Bootstrap5({
            eleValidClass: '',
            rowSelector: function (field, ele) {
              return '.form-control-validation';
            }
          }),
          autoFocus: new FormValidation.plugins.AutoFocus(),
          submitButton: new FormValidation.plugins.SubmitButton()
        }
      }).on('core.form.valid', function () {
        // Kirim form ketika langkah valid
        stepsValidationForm.submit();
      });

      // Handle submit button click
      const submitButton = stepsValidationForm.querySelector('.btn-submit');
      if (submitButton) {
        submitButton.addEventListener('click', function (e) {
          e.preventDefault();
          Steps.validate();
        });
      }
    }
  })();
});
