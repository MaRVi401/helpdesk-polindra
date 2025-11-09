/**
 * Form Basic Inputs
 */
'use strict';

document.addEventListener('DOMContentLoaded', function () {
  // Checkbox Indeterminate
  const checkbox = document.getElementById('defaultCheck2');
  if (checkbox) {
    checkbox.indeterminate = true;
  }

  const fileInput = document.querySelector('input[type="file"]');
  if (fileInput) {
    fileInput.addEventListener('change', function (e) {
      const fileName = e.target.files[0]?.name || 'Pilih file...';
      console.log('File dipilih:', fileName);
    });
  }

  const textInputs = document.querySelectorAll('input[type="text"], textarea');
  textInputs.forEach(input => {
    input.addEventListener('focus', () => input.classList.add('border-primary'));
    input.addEventListener('blur', () => input.classList.remove('border-primary'));
  });
});
