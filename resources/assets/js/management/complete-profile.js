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

      // Info validation - SESUAI DENGAN REFERENSI
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
                min: 7,
                max: 8,
                message: 'NIM harus antara 7 dan 8 karakter'
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
        },
        init: instance => {
          instance.on('plugins.message.placed', function (e) {
            if (e.element.parentElement.classList.contains('input-group')) {
              e.element.parentElement.insertAdjacentElement('afterend', e.messageElement);
            }
          });
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

      // Revalidate select2 fields on change to remove error styling
      const select2Fields = stepsValidationForm.querySelectorAll('.select2');
      if (select2Fields.length > 0) {
        select2Fields.forEach(field => {
          $(field).on('change', function () {
            // Revalidate this field to clear error messages
            Steps.revalidateField(field.name);
          });
        });
      }

      // Also handle nim field input for real-time validation
      const nimField = stepsValidationForm.querySelector('#nim');
      if (nimField) {
        nimField.addEventListener('input', function () {
          Steps.revalidateField('nim');
        });
      }
    }
  })();
});
