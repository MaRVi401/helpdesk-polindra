/**
 * Setting - Profile
 */

document.addEventListener('DOMContentLoaded', function (e) {
  // Form Profile Settings
  let formAccountSettings = document.querySelector('#formAccountSettings');

  // Form validation untuk profile settings
  if (formAccountSettings) {
    const fv = FormValidation.formValidation(formAccountSettings, {
      fields: {
        name: {
          validators: {
            notEmpty: { message: 'Nama lengkap wajib diisi' },
            stringLength: {
              max: 255,
              message: 'Nama lengkap maksimal 50 karakter'
            }
          }
        },
        email_personal: {
          validators: {
            emailAddress: { message: 'Format email tidak valid' }
          }
        },
        no_wa: {
          validators: {
            stringLength: {
              max: 13,
              message: 'Nomor WhatsApp maksimal 13 karakter'
            },
            regexp: {
              regexp: /^[0-9+\-\s()]*$/,
              message: 'Nomor WhatsApp hanya boleh berisi angka'
            }
          }
        }
      },
      plugins: {
        trigger: new FormValidation.plugins.Trigger(),
        bootstrap5: new FormValidation.plugins.Bootstrap5({
          eleValidClass: '',
          rowSelector: '.col-md-6'
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

        // Tambahkan event listener untuk form submission yang berhasil
        instance.on('core.form.valid', function () {
          console.log('Form valid, attempting submit...');
          try {
            formAccountSettings.submit();
          } catch (err) {
            console.error('Form submit failed:', err);
          }
        });
      }
    });
    // Fallback: if FormValidation not loaded or fails, allow direct submit by clicking the button
    const submitBtn = formAccountSettings.querySelector('button[type="submit"]');
    if (!window.FormValidation && submitBtn) {
      submitBtn.addEventListener('click', function (e) {
        e.preventDefault();
        try {
          formAccountSettings.submit();
        } catch (err) {
          console.error('Fallback submit failed', err);
        }
      });
    }
  }

  // Log form submit and ensure hidden avatar input has the latest file before submission
  if (formAccountSettings) {
    formAccountSettings.addEventListener('submit', function (e) {
      console.log('formAccountSettings: submit event triggered');
      try {
        var hiddenAvatarInput = document.getElementById('avatarInput');
        if (fileInput && fileInput.files && fileInput.files.length > 0 && hiddenAvatarInput) {
          // copy again to ensure it exists
          const dataTransfer = new DataTransfer();
          dataTransfer.items.add(fileInput.files[0]);
          hiddenAvatarInput.files = dataTransfer.files;
          console.log('Hidden avatar input updated at submit.');
        }
      } catch (err) {
        console.error('Error while preparing avatar on submit', err);
      }
    });
  }

  // Avatar upload handling
  let uploadedAvatar = document.getElementById('uploadedAvatar');
  let fileInput = document.querySelector('.account-file-input');
  let resetButton = document.querySelector('.account-image-reset');

  if (uploadedAvatar && fileInput) {
    const defaultAvatar = uploadedAvatar.src;

    // Handle file selection dengan validasi
    fileInput.onchange = () => {
      if (fileInput.files[0]) {
        const file = fileInput.files[0];

        console.log('File selected:', file.name, 'Size:', file.size, 'Type:', file.type);

        // Validasi ukuran file (800kb = 819200 bytes)
        if (file.size > 819200) {
          Swal.fire({
            icon: 'error',
            title: 'Ukuran File Terlalu Besar',
            text: 'Ukuran avatar maksimal 800kb',
            customClass: {
              confirmButton: 'btn btn-primary waves-effect waves-light'
            },
            buttonsStyling: false
          });
          fileInput.value = '';
          return;
        }

        // Validasi tipe file
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        if (!allowedTypes.includes(file.type)) {
          Swal.fire({
            icon: 'error',
            title: 'Format File Tidak Valid',
            text: 'Hanya file JPG dan PNG yang diperbolehkan',
            customClass: {
              confirmButton: 'btn btn-primary waves-effect waves-light'
            },
            buttonsStyling: false
          });
          fileInput.value = '';
          return;
        }

        // Preview image jika validasi lolos
        uploadedAvatar.src = window.URL.createObjectURL(file);
        console.log('Avatar preview updated');

        // Salin file ke file input tersembunyi di dalam form agar file dikirim bersama form
        var hiddenAvatarInput = document.getElementById('avatarInput');
        if (hiddenAvatarInput) {
          try {
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            hiddenAvatarInput.files = dataTransfer.files;
            console.log('Copied file to hidden avatarInput');
          } catch (err) {
            console.error('Unable to copy file to hidden input', err);
          }
        }
      }
    };

    // Reset avatar to default
    resetButton.onclick = () => {
      fileInput.value = '';
      uploadedAvatar.src = defaultAvatar;
      // clear hidden avatar input inside form too
      var hiddenAvatarInput = document.getElementById('avatarInput');
      if (hiddenAvatarInput) {
        hiddenAvatarInput.value = '';
      }
      console.log('Avatar reset to default');
    };
  }

  // Display success message
  if (typeof window.profileSuccessMessage !== 'undefined' && window.profileSuccessMessage) {
    console.log('Showing success message:', window.profileSuccessMessage);
    if (typeof Swal !== 'undefined') {
      Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: window.profileSuccessMessage,
        customClass: {
          confirmButton: 'btn btn-primary waves-effect waves-light'
        },
        buttonsStyling: false
      }).then(() => {
        // Clear the message after showing
        window.profileSuccessMessage = undefined;
      });
    } else {
      alert(window.profileSuccessMessage);
      window.profileSuccessMessage = undefined;
    }
  }

  // Display error message
  if (typeof window.profileErrorMessage !== 'undefined' && window.profileErrorMessage) {
    console.log('Showing error message:', window.profileErrorMessage);
    if (typeof Swal !== 'undefined') {
      Swal.fire({
        icon: 'error',
        title: 'Gagal!',
        text: window.profileErrorMessage,
        customClass: {
          confirmButton: 'btn btn-primary waves-effect waves-light'
        },
        buttonsStyling: false
      }).then(() => {
        // Clear the message after showing
        window.profileErrorMessage = undefined;
      });
    } else {
      alert(window.profileErrorMessage);
      window.profileErrorMessage = undefined;
    }
  }

  // Phone number formatting
  var phoneNumber = document.querySelector('#no_wa');
  if (phoneNumber) {
    new Cleave(phoneNumber, {
      phone: true,
      phoneRegionCode: 'ID'
    });
  }
});


