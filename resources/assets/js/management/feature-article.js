/**
 * Article Feature (Client-side version with pagination, filter, search)
 */
'use strict';

document.addEventListener('DOMContentLoaded', function () {
  const ITEMS_PER_PAGE = 6;
  let currentPage = 1;
  let filteredArticles = [];
  let allArticles = [];

  function initializeArticles() {
    const articleItems = document.querySelectorAll('.article-item');
    allArticles = Array.from(articleItems).map(item => ({
      element: item,
      title: item.querySelector('h5')?.textContent.toLowerCase() || '',
      description: item.querySelector('p.mt-1')?.textContent.toLowerCase() || '',
      category: item.getAttribute('data-category') || '',
      status: item.getAttribute('data-status') || ''
    }));
    filteredArticles = [...allArticles];
  }

  // Filter artikel
  function filterArticles() {
    const searchInput = document.getElementById('searchArticle');
    const categorySelect = document.getElementById('categoryFilter');
    const searchText = searchInput?.value.toLowerCase() || '';
    const selectedCategory = categorySelect?.value || '';

    filteredArticles = allArticles.filter(article => {
      const matchesSearch =
        !searchText || article.title.includes(searchText) || article.description.includes(searchText);
      const matchesCategory = !selectedCategory || article.category == selectedCategory;
      return matchesSearch && matchesCategory;
    });

    currentPage = 1;
    renderArticles();
    updatePagination();
    updateArticleCount();
  }

  function renderArticles() {
    const container = document.getElementById('articleContainer');
    if (!container) return;

    const start = (currentPage - 1) * ITEMS_PER_PAGE;
    const end = start + ITEMS_PER_PAGE;
    const articlesToShow = filteredArticles.slice(start, end);

    // Sembunyikan semua artikel
    allArticles.forEach(article => (article.element.style.display = 'none'));

    // Tampilkan artikel sesuai halaman
    articlesToShow.forEach(article => (article.element.style.display = 'block'));

    const oldEmptyState = container.querySelector('.col-12 .text-center');
    if (oldEmptyState) oldEmptyState.closest('.col-12').remove();

    // Tampilkan pesan kosong
    if (filteredArticles.length === 0) {
      container.insertAdjacentHTML(
        'beforeend',
        `
        <div class="col-12">
          <div class="card">
            <div class="card-body text-center py-5">
              <i class="icon-base ti tabler-article-off icon-30px text-danger mb-3"></i>
              <h5 class="mb-2">Tidak Ada Artikel</h5>
              <p class="text-muted">Tidak ada artikel yang sesuai dengan pencarian Anda.</p>
            </div>
          </div>
        </div>
      `
      );
    }
  }

  // Update pagination
  function updatePagination() {
    const totalPages = Math.ceil(filteredArticles.length / ITEMS_PER_PAGE);
    let paginationContainer = document.querySelector('.article-pagination-container');

    // Buat container jika belum ada
    if (!paginationContainer) {
      const cardBody = document.querySelector('#articleContainer')?.closest('.card-body');
      if (!cardBody) return;

      paginationContainer = document.createElement('div');
      paginationContainer.className = 'article-pagination-container row mx-3 mt-6 justify-content-between';

      paginationContainer.innerHTML = `
        <div class="col-sm-12 col-md-6">
          <div class="dt-info" aria-live="polite"></div>
        </div>
        <div class="col-sm-12 col-md-6">
          <nav aria-label="Page navigation">
            <ul class="pagination justify-content-end mb-0 pagination-rounded"></ul>
          </nav>
        </div>
      `;
      cardBody.appendChild(paginationContainer);
    }

    const pagination = paginationContainer.querySelector('.pagination');
    const infoSpan = paginationContainer.querySelector('.dt-info');

    // Update info text
    if (infoSpan) {
      if (filteredArticles.length === 0) {
        infoSpan.textContent = 'Menampilkan 0 sampai 0 dari 0 data';
      } else {
        const start = (currentPage - 1) * ITEMS_PER_PAGE + 1;
        const end = Math.min(currentPage * ITEMS_PER_PAGE, filteredArticles.length);
        infoSpan.textContent = `Menampilkan ${start} sampai ${end} dari total ${filteredArticles.length} data`;
      }
    }

    // Sembunyikan pagination jika hanya 1 halaman
    if (totalPages <= 1) {
      paginationContainer.style.display = 'none';
      return;
    } else {
      paginationContainer.style.display = 'flex';
    }

    // Generate pagination buttons
    pagination.innerHTML = '';

    // First button
    const firstLi = createPaginationButton(
      'first',
      currentPage === 1,
      '<i class="icon-base ti tabler-chevrons-left icon-sm scaleX-n1-rtl"></i>',
      () => goToPage(1)
    );
    pagination.appendChild(firstLi);

    // Previous button
    const prevLi = createPaginationButton(
      'prev',
      currentPage === 1,
      '<i class="icon-base ti tabler-chevron-left icon-sm scaleX-n1-rtl"></i>',
      () => goToPage(currentPage - 1)
    );
    pagination.appendChild(prevLi);

    // Page numbers
    const maxVisiblePages = 5;
    let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
    let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);

    if (endPage - startPage < maxVisiblePages - 1) {
      startPage = Math.max(1, endPage - maxVisiblePages + 1);
    }

    for (let i = startPage; i <= endPage; i++) {
      const pageLi = document.createElement('li');
      pageLi.className = `page-item ${i === currentPage ? 'active' : ''}`;
      pageLi.innerHTML = `<a class="page-link" href="javascript:void(0);">${i}</a>`;
      pageLi.addEventListener('click', () => goToPage(i));
      pagination.appendChild(pageLi);
    }

    // Next button
    const nextLi = createPaginationButton(
      'next',
      currentPage === totalPages,
      '<i class="icon-base ti tabler-chevron-right icon-xs scaleX-n1-rtl"></i>',
      () => goToPage(currentPage + 1)
    );
    pagination.appendChild(nextLi);

    // Last button
    const lastLi = createPaginationButton(
      'last',
      currentPage === totalPages,
      '<i class="icon-base ti tabler-chevrons-right icon-sm scaleX-n1-rtl"></i>',
      () => goToPage(totalPages)
    );
    pagination.appendChild(lastLi);
  }

  // Helper untuk membuat tombol pagination
  function createPaginationButton(type, disabled, icon, onClick) {
    const li = document.createElement('li');
    li.className = `page-item ${type} ${disabled ? 'disabled' : ''}`;
    li.innerHTML = `<a class="page-link" href="javascript:void(0);">${icon}</a>`;
    if (!disabled) li.addEventListener('click', onClick);
    return li;
  }

  // Navigasi ke halaman
  function goToPage(page) {
    currentPage = page;
    renderArticles();
    updatePagination();

    // Scroll ke atas artikel
    const articleCard = document.querySelector('.app-academy .card.mb-6:last-child');
    if (articleCard) articleCard.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }

  // Update jumlah artikel
  function updateArticleCount() {
    const countElement = document.querySelector('.card-title p.mb-0');
    if (countElement) {
      countElement.textContent = `Tersedia ${filteredArticles.length} artikel yang dapat kamu baca`;
    }
  }

  // Setup event listeners
  function setupEventListeners() {
    const categoryFilter = document.getElementById('categoryFilter');
    const searchInput = document.getElementById('searchArticle');
    const searchButton = document.querySelector('.btn-primary.btn-icon');

    if (categoryFilter) {
      categoryFilter.addEventListener('change', filterArticles);
    }

    if (searchInput) {
      let searchTimeout;
      searchInput.addEventListener('keyup', () => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(filterArticles, 300);
      });
      searchInput.addEventListener('keypress', e => {
        if (e.key === 'Enter') filterArticles();
      });
    }

    if (searchButton) {
      searchButton.addEventListener('click', e => {
        e.preventDefault();
        filterArticles();
      });
    }
  }

  // Tampilkan notifikasi
  function showNotifications() {
    if (window.articleSuccessMessage && Swal) {
      Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: window.articleSuccessMessage,
        customClass: { confirmButton: 'btn btn-primary waves-effect waves-light' },
        buttonsStyling: false
      });
    }

    if (window.articleErrorMessage && Swal) {
      Swal.fire({
        icon: 'error',
        title: 'Gagal!',
        text: window.articleErrorMessage,
        customClass: { confirmButton: 'btn btn-primary waves-effect waves-light' },
        buttonsStyling: false
      });
    }
  }

  function init() {
    initializeArticles();
    renderArticles();
    updatePagination();
    setupEventListeners();
    showNotifications();
  }

  init();
});
