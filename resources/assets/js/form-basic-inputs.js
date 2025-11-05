/**
 * Form Basic Inputs
 * Versi aman - tanpa error jika elemen tidak ditemukan
 */
'use strict';

document.addEventListener('DOMContentLoaded', function () {
  // Checkbox Indeterminate (cek dulu elemen-nya ada atau tidak)
  const checkbox = document.getElementById('defaultCheck2');
  if (checkbox) {
    checkbox.indeterminate = true;
  }

  // Contoh tambahan: jika kamu punya input file dengan preview
  const fileInput = document.querySelector('input[type="file"]');
  if (fileInput) {
    fileInput.addEventListener('change', function (e) {
      const fileName = e.target.files[0]?.name || 'Pilih file...';
      console.log('File dipilih:', fileName);
      // Bisa tambahkan logika preview gambar di sini kalau mau
    });
  }

  // Tambahan contoh untuk input teks (opsional)
  const textInputs = document.querySelectorAll('input[type="text"], textarea');
  textInputs.forEach(input => {
    input.addEventListener('focus', () => input.classList.add('border-primary'));
    input.addEventListener('blur', () => input.classList.remove('border-primary'));
  });
});
