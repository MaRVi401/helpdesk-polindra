/**
 * Mahasiswa Ticket (Client-side version with SweetAlert2)
 */
'use strict';
import Swal from 'sweetalert2';
document.addEventListener('DOMContentLoaded', function () {
  const statusDropdown = document.getElementById('status-dropdown');
  const searchInput = document.getElementById('search-tiket');

  function isSelesai(status) {
    return /selesai/i.test(status || '');
  }

  function applyFilters() {
    const status = statusDropdown ? statusDropdown.value : 'semua';
    const q = searchInput ? searchInput.value.trim().toLowerCase() : '';
    const cards = document.querySelectorAll('.tiket-card');

    cards.forEach(card => {
      const rawStatus = card.dataset.status || '';
      const tiketNo = (card.dataset.tiket || '').toLowerCase();
      const judul = (card.dataset.judul || '').toLowerCase();
      const layanan = (card.dataset.layanan || '').toLowerCase();

      let statusMatch = true;
      if (status === 'selesai') statusMatch = isSelesai(rawStatus);
      else if (status === 'belum-selesai') statusMatch = !isSelesai(rawStatus);

      let textMatch = true;
      if (q) textMatch = tiketNo.includes(q) || judul.includes(q) || layanan.includes(q);

      if (statusMatch && textMatch) card.style.display = '';
      else card.style.display = 'none';
    });
  }

  if (statusDropdown) statusDropdown.addEventListener('change', applyFilters);
  if (searchInput) searchInput.addEventListener('input', applyFilters);

  // run once on load to apply default filters
  applyFilters();

  // Delete confirmation (intercepts buttons with class .btn-delete-tiket)
  document.body.addEventListener('click', function (e) {
    const btn = e.target.closest('.btn-delete-tiket');
    if (!btn) return;
    e.preventDefault();
    const form = btn.closest('form');

    Swal.fire({
      title: 'Apakah Kamu yakin?',
      text: 'Tiket akan dihapus dan tidak dapat dikembalikan!',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Ya, hapus!',
      cancelButtonText: 'Batal',
      customClass: {
        confirmButton: 'btn btn-primary me-3 waves-effect waves-light',
        cancelButton: 'btn btn-outline-secondary waves-effect'
      },
      buttonsStyling: false
    }).then(function (result) {
      if (result.isConfirmed) {
        if (form) form.submit();
      }
    });
  });

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
