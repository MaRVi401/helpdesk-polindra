/**
 * Unit Management (Client-side version with SweetAlert2)
 */
'use strict';

document.addEventListener('DOMContentLoaded', function (e) {
  let borderColor, bodyBg, headingColor;

  borderColor = config.colors.borderColor;
  bodyBg = config.colors.bodyBg;
  headingColor = config.colors.headingColor;

  const dt_unit_table = document.querySelector('.datatables-basic'),
    unitAdd = baseUrl + 'unit/create',
    statusObj = {
      Post: { title: 'Post', class: 'bg-label-success' },
      Draft: { title: 'Draft', class: 'bg-label-warning' }
    };

  // Tampilkan loading overlay saat halaman dimuat
  function showTableLoading() {
    const loadingHtml = `
      <div class="datatable-loading-overlay" style="
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.9);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        border-radius: 0.5rem;
        min-height: 400px;
      ">
        <div class="text-center">
          <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
            <span class="visually-hidden">Loading...</span>
          </div>
          <p class="text-muted fw-medium">Memuat data unit...</p>
        </div>
      </div>
    `;

    // Cari card atau container wrapper tabel
    const tableWrapper =
      dt_unit_table.closest('.card') || dt_unit_table.closest('.card-body') || dt_unit_table.parentElement;

    if (tableWrapper) {
      tableWrapper.style.position = 'relative';
      tableWrapper.style.minHeight = '400px';
      tableWrapper.insertAdjacentHTML('beforeend', loadingHtml);
    }
  }

  // Sembunyikan loading overlay
  function hideTableLoading() {
    const loadingOverlay = document.querySelector('.datatable-loading-overlay');
    if (loadingOverlay) {
      loadingOverlay.style.opacity = '0';
      loadingOverlay.style.transition = 'opacity 0.3s ease';
      setTimeout(() => {
        loadingOverlay.remove();
      }, 300);
    }
  }

  // Unit datatable
  if (dt_unit_table) {
    // Tampilkan loading sebelum inisialisasi DataTable
    showTableLoading();

    // Sembunyikan tabel sementara
    dt_unit_table.style.opacity = '0';
    dt_unit_table.style.transition = 'opacity 0.3s ease';
    var dt_unit = new DataTable(dt_unit_table, {
      columns: [
        { data: 'id' },
        { data: 'id', orderable: false, render: DataTable.render.select() },
        { data: 'nama_unit' },
        { data: 'slug' },
        { data: 'kepalaUnit' },
        { data: 'id' }
      ],
      columnDefs: [
        {
          className: 'control',
          searchable: false,
          orderable: false,
          responsivePriority: 2,
          targets: 0,
          render: function (data, type, full, meta) {
            return '';
          }
        },
        {
          targets: 1,
          orderable: false,
          searchable: false,
          responsivePriority: 3,
          checkboxes: true,
          render: function () {
            return '<input type="checkbox" class="dt-checkboxes form-check-input">';
          },
          checkboxes: {
            selectAllRender: '<input type="checkbox" class="form-check-input">'
          }
        },
        {
          targets: 2,
          render: function (data, type, full, meta) {
            return `<span>${data}</span>`;
          }
        },
        {
          targets: 3,
          render: function (data, type, full, meta) {
            return `<span class="fw-medium">${data}</span>`; // Tampilkan slug
          }
        },
        {
          targets: 4,
          render: function (data, type, full, meta) {
            return `<span>${data}</span>`;
          }
        },
        {
          targets: 5,
          title: 'Aksi',
          searchable: false,
          orderable: false,
          className: 'text-center',
          render: function (data, type, full, meta) {
            const rows = dt_unit_table?.querySelectorAll('tbody tr');
            const id = rows && rows[meta.row] ? rows[meta.row].querySelector('td:last-child')?.dataset?.id : '';
            return `
      <div class="d-inline-block text-nowrap">
        <button class="btn btn-text-secondary rounded-pill waves-effect btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
          <i class="icon-base ti tabler-dots-vertical icon-22px"></i>
        </button>
        <div class="dropdown-menu dropdown-menu-end m-0">
          <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center view-unit" data-id="${id}"><i class="icon-base ti tabler-details me-2"></i> Detail</a>
          <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center edit-unit" data-id="${id}"><i class="icon-base ti tabler-pencil me-2"></i> Edit</a>
          <div class="dropdown-divider"></div>
          <a href="javascript:void(0);" class="dropdown-item text-danger d-flex align-items-center delete-unit" data-id="${id}"><i class="icon-base ti tabler-trash me-2"></i> Hapus</a>
        </div>
      </div>
    `;
          }
        }
      ],
      select: {
        style: 'multi',
        selector: 'td:nth-child(2)'
      },
      order: [[1, 'asc']],
      displayLength: 5,
      layout: {
        topStart: {
          rowClass: 'card-header d-flex border-top rounded-0 flex-wrap py-0 flex-column flex-md-row align-items-start',
          features: [
            {
              search: {
                className: 'me-5 ms-n4 pe-5 mb-n6 mb-md-0',
                placeholder: 'Pencarian',
                text: '_INPUT_'
              }
            }
          ]
        },
        topEnd: {
          rowClass: 'row m-3 my-0 justify-content-between',
          features: [
            {
              pageLength: {
                menu: [5, 10, 25, 50],
                text: '_MENU_'
              },
              buttons: [
                {
                  extend: 'collection',
                  className: 'btn btn-label-secondary dropdown-toggle me-4',
                  text: '<span class="d-flex align-items-center gap-1"><i class="icon-base ti tabler-upload icon-20px"></i> <span class="d-none d-sm-inline-block">Export</span></span>',
                  buttons: [
                    {
                      extend: 'print',
                      text: `<span class="d-flex align-items-center"><i class="icon-base ti tabler-printer me-1"></i>Print</span>`,
                      className: 'dropdown-item',
                      exportOptions: {
                        columns: [2, 3, 4]
                      }
                    },
                    {
                      extend: 'csv',
                      text: `<span class="d-flex align-items-center"><i class="icon-base ti tabler-file me-1"></i>Csv</span>`,
                      className: 'dropdown-item',
                      exportOptions: {
                        columns: [2, 3, 4]
                      }
                    },
                    {
                      extend: 'excel',
                      text: `<span class="d-flex align-items-center"><i class="icon-base ti tabler-upload me-1"></i>Excel</span>`,
                      className: 'dropdown-item',
                      exportOptions: {
                        columns: [2, 3, 4]
                      }
                    },
                    {
                      extend: 'pdf',
                      text: `<span class="d-flex align-items-center"><i class="icon-base ti tabler-file-text me-1"></i>Pdf</span>`,
                      className: 'dropdown-item',
                      exportOptions: {
                        columns: [2, 3, 4]
                      }
                    },
                    {
                      extend: 'copy',
                      text: `<i class="icon-base ti tabler-copy me-1"></i>Copy`,
                      className: 'dropdown-item',
                      exportOptions: {
                        columns: [2, 3, 4]
                      }
                    }
                  ]
                },
                {
                  text: '<i class="icon-base ti tabler-plus me-0 me-sm-1 icon-20px"></i><span class="d-none d-sm-inline-block">Tambah Unit</span>',
                  className: 'add-new btn btn-primary',
                  action: function () {
                    window.location.href = unitAdd;
                  }
                }
              ]
            }
          ]
        },
        bottomStart: {
          rowClass: 'row mx-3 justify-content-between',
          features: ['info']
        },
        bottomEnd: 'paging'
      },
      language: {
        lengthMenu: 'Tampilkan _MENU_ data per halaman',
        zeroRecords: 'Tidak ada data yang ditemukan',
        info: 'Menampilkan _START_ sampai _END_ dari total _TOTAL_ data',
        infoEmpty: 'Menampilkan 0 sampai 0 dari 0 data',
        infoFiltered: '(difilter dari total _MAX_ data)',
        search: 'Pencarian:',
        emptyTable: 'Tidak ada data di tabel',
        paginate: {
          next: '<i class="icon-base ti tabler-chevron-right scaleX-n1-rtl icon-18px"></i>',
          previous: '<i class="icon-base ti tabler-chevron-left scaleX-n1-rtl icon-18px"></i>',
          first: '<i class="icon-base ti tabler-chevrons-left scaleX-n1-rtl icon-18px"></i>',
          last: '<i class="icon-base ti tabler-chevrons-right scaleX-n1-rtl icon-18px"></i>'
        },
        select: {
          rows: {
            _: '%d baris dipilih',
            1: '1 baris dipilih'
          }
        }
      },
      responsive: {
        details: {
          display: DataTable.Responsive.display.modal({
            header: function (row) {
              const data = row.data();
              return 'Detail Unit';
            }
          }),
          type: 'column',
          renderer: function (api, rowIdx, columns) {
            const data = columns
              .map(function (col) {
                return col.title !== ''
                  ? `<tr data-dt-row="${col.rowIndex}" data-dt-column="${col.columnIndex}">
                      <td>${col.title}:</td>
                      <td>${col.data}</td>
                    </tr>`
                  : '';
              })
              .join('');

            if (data) {
              const div = document.createElement('div');
              div.classList.add('table-responsive');
              const table = document.createElement('table');
              div.appendChild(table);
              table.classList.add('table');
              const tbody = document.createElement('tbody');
              tbody.innerHTML = data;
              table.appendChild(tbody);
              return div;
            }
            return false;
          }
        }
      },
      // Event callback ketika DataTable selesai di-draw
      initComplete: function () {
        setTimeout(() => {
          dt_unit_table.style.visibility = 'visible';
          dt_unit_table.style.opacity = '1';
          setTimeout(() => hideTableLoading(), 300);
        }, 100);
      }
    });

    // Event handlers untuk tombol aksi
    document.body.addEventListener('click', function (e) {
      // View Unit
      if (e.target.closest('.view-unit')) {
        const id = e.target.closest('.view-unit').dataset.id;
        window.location.href = baseUrl + 'unit/' + id;
      }

      // Edit Unit
      if (e.target.closest('.edit-unit')) {
        const id = e.target.closest('.edit-unit').dataset.id;
        window.location.href = baseUrl + 'unit/' + id + '/edit';
      }

      // Delete Unit
      if (e.target.closest('.delete-unit')) {
        e.preventDefault();
        const id = e.target.closest('.delete-unit').dataset.id;

        Swal.fire({
          title: 'Apakah Kamu yakin?',
          text: 'Data unit yang dihapus tidak dapat dikembalikan!',
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
          if (result.value) {
            // Buat form dan submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = baseUrl + 'unit/' + id;

            // CSRF Token
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = document.querySelector('meta[name="csrf-token"]').content;
            form.appendChild(csrfInput);

            // Method DELETE
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';
            form.appendChild(methodInput);

            document.body.appendChild(form);
            form.submit();
          }
        });
      }
    });
  }

  // Tampilkan success message dari session (toast notification)
  if (window.unitSuccessMessage) {
    Swal.fire({
      icon: 'success',
      title: 'Berhasil!',
      text: window.unitSuccessMessage,
      customClass: {
        confirmButton: 'btn btn-primary waves-effect waves-light'
      },
      buttonsStyling: false
    });
  }

  // Tampilkan error message dari session
  if (window.unitErrorMessage) {
    Swal.fire({
      icon: 'error',
      title: 'Gagal!',
      text: window.unitErrorMessage,
      customClass: {
        confirmButton: 'btn btn-primary waves-effect waves-light'
      },
      buttonsStyling: false
    });
  }

  // Filter form control to default size
  setTimeout(() => {
    const elementsToModify = [
      { selector: '.dt-buttons .btn', classToRemove: 'btn-secondary' },
      { selector: '.dt-buttons.btn-group', classToAdd: 'mb-md-0 mb-6' },
      { selector: '.dt-search .form-control', classToRemove: 'form-control-sm', classToAdd: 'ms-0' },
      { selector: '.dt-search', classToAdd: 'mb-0 mb-md-6' },
      { selector: '.dt-length .form-select', classToRemove: 'form-select-sm' },
      { selector: '.dt-layout-end', classToAdd: 'gap-md-2 gap-0 mt-0' },
      { selector: '.dt-layout-start', classToAdd: 'mt-0' },
      { selector: '.dt-layout-table', classToRemove: 'row mt-2' },
      { selector: '.dt-layout-full', classToRemove: 'col-md col-12', classToAdd: 'table-responsive' }
    ];

    elementsToModify.forEach(({ selector, classToRemove, classToAdd }) => {
      document.querySelectorAll(selector).forEach(element => {
        if (classToRemove) {
          classToRemove.split(' ').forEach(className => element.classList.remove(className));
        }
        if (classToAdd) {
          classToAdd.split(' ').forEach(className => element.classList.add(className));
        }
      });
    });
  }, 100);
});
