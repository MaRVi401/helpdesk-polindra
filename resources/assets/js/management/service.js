/**
 * Service (Client-side version with SweetAlert2)
 * Updated to support multiple tables per unit
 */
'use strict';

let fv, offCanvasEl;
document.addEventListener('DOMContentLoaded', function (e) {
  let borderColor, bodyBg, headingColor;

  borderColor = config.colors.borderColor;
  bodyBg = config.colors.bodyBg;
  headingColor = config.colors.headingColor;

  const dt_service_tables = document.querySelectorAll('.datatables-basic');
  const serviceAdd = baseUrl + 'service/create';

  // Tampilkan loading overlay saat halaman dimuat
  function showTableLoading(table) {
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
          <p class="text-muted fw-medium">Memuat data layanan...</p>
        </div>
      </div>
    `;

    const tableWrapper = table.closest('.card') || table.closest('.card-body') || table.parentElement;

    if (tableWrapper) {
      tableWrapper.style.position = 'relative';
      tableWrapper.style.minHeight = '400px';
      tableWrapper.insertAdjacentHTML('beforeend', loadingHtml);
    }
  }

  // Sembunyikan loading overlay
  function hideTableLoading(table) {
    const card = table.closest('.card');
    const loadingOverlay = card ? card.querySelector('.datatable-loading-overlay') : null;
    if (loadingOverlay) {
      loadingOverlay.style.opacity = '0';
      loadingOverlay.style.transition = 'opacity 0.3s ease';
      setTimeout(() => {
        loadingOverlay.remove();
      }, 300);
    }
  }

  // Form validation and offcanvas handling
  (function () {
    const formAddNewRecord = document.getElementById('form-add-new-record');

    setTimeout(() => {
      const newRecord = document.querySelector('.create-new'),
        offCanvasElement = document.querySelector('#add-new-record');

      // To open offCanvas, to add new record
      if (newRecord) {
        newRecord.addEventListener('click', function () {
          offCanvasEl = new bootstrap.Offcanvas(offCanvasElement);
          // Empty fields on offCanvas open
          (offCanvasElement.querySelector('.dt-nama').value = ''),
            (offCanvasElement.querySelector('.dt-unit').value = ''),
            (offCanvasElement.querySelector('.dt-prioritas').value = ''),
            (offCanvasElement.querySelector('.dt-penanggung-jawab').value = ''),
            (offCanvasElement.querySelector('.dt-status-arsip').value = '');
          // Open offCanvas with form
          offCanvasEl.show();
        });
      }
    }, 200);

    // Form validation for Add new record
    if (formAddNewRecord) {
      fv = FormValidation.formValidation(formAddNewRecord, {
        fields: {
          nama: {
            validators: {
              notEmpty: { message: 'Nama layanan wajib diisi' }
            }
          },
          unit_id: {
            validators: {
              notEmpty: { message: 'Unit wajib dipilih' }
            }
          },
          prioritas: {
            validators: {
              notEmpty: { message: 'Prioritas wajib dipilih' }
            }
          },
          status_arsip: {
            validators: {
              notEmpty: { message: 'Status layanan wajib dipilih' }
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
          autoFocus: new FormValidation.plugins.AutoFocus()
        },
        init: instance => {
          instance.on('plugins.message.placed', function (e) {
            if (e.element.parentElement.classList.contains('input-group')) {
              e.element.parentElement.insertAdjacentElement('afterend', e.messageElement);
            }
          });

          // Add form submit handler when validation passes
          instance.on('core.form.valid', function () {
            // Submit form when validation passes
            formAddNewRecord.submit();
            // Add event listener for form submission
            formAddNewRecord.addEventListener('submit', function () {
              // Show loading state
              Swal.fire({
                title: 'Memproses...',
                allowOutsideClick: false,
                allowEscapeKey: false,
                allowEnterKey: false,
                didOpen: () => {
                  Swal.showLoading();
                }
              });
            });
          });
        }
      });
    }
  })();

  // Initialize each table
  dt_service_tables.forEach(function (dt_service_table) {
    if (dt_service_table) {
      // Tampilkan loading sebelum inisialisasi DataTable
      showTableLoading(dt_service_table);

      // Sembunyikan tabel sementara
      dt_service_table.style.opacity = '0';
      dt_service_table.style.transition = 'opacity 0.3s ease';

      var dt_service = new DataTable(dt_service_table, {
        columns: [
          { data: 'id' },
          { data: 'id', orderable: false, render: DataTable.render.select() },
          { data: null, name: 'no' },
          { data: 'nama' },
          { data: 'unit' },
          { data: 'pic' },
          { data: 'prioritas' },
          { data: 'status' },
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
            orderable: false,
            searchable: false,
            render: function (data, type, full, meta) {
              return meta.row + 1;
            }
          },
          {
            targets: 3,
            responsivePriority: 1,
            render: function (data, type, full, meta) {
              return `<span class="fw-medium">${data}</span>`;
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
            orderable: false,
            searchable: false,
            render: function (data, type, full, meta) {
              return data ? `<span>${data}</span>` : '<span class="text-muted">-</span>';
            }
          },
          {
            targets: 6,
            render: function (data, type, full, meta) {
              const badgeClass = data === 'Tinggi' ? 'danger' : data === 'Sedang' ? 'warning' : 'info';
              return `<span class="badge bg-label-${badgeClass}">${data}</span>`;
            }
          },
          {
            targets: 7,
            render: function (data, type, full, meta) {
              const row = dt_service_table.querySelectorAll('tbody tr')[meta.row];
              const isArchived = row.dataset.archived === '1';
              return isArchived
                ? '<span class="badge bg-label-secondary">Diarsipkan</span>'
                : '<span class="badge bg-label-success">Aktif</span>';
            }
          },
          {
            targets: 8,
            title: 'Aksi',
            searchable: false,
            orderable: false,
            className: 'text-center',
            render: function (data, type, full, meta) {
              const row = dt_service_table.querySelectorAll('tbody tr')[meta.row];
              const id = row.querySelector('td:last-child').dataset.id;

              return `
                <div class="d-inline-block text-nowrap">
                  <button class="btn btn-text-secondary rounded-pill waves-effect btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                    <i class="icon-base ti tabler-dots-vertical icon-22px"></i>
                  </button>
                  <div class="dropdown-menu dropdown-menu-end m-0">
                      <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center view-service" data-id="${id}"><i class="icon-base ti tabler-details me-2"></i> Detail</a>
                      <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center edit-service" data-id="${id}"><i class="icon-base ti tabler-pencil me-2"></i> Edit</a>
                      <div class="dropdown-divider"></div>
                      <a href="javascript:void(0);" class="dropdown-item text-danger d-flex align-items-center delete-service" data-id="${id}"><i class="icon-base ti tabler-trash me-2"></i> Hapus</a>
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
        order: [],
        displayLength: 5,
        layout: {
          topStart: {
            rowClass:
              'card-header d-flex border-top rounded-0 flex-wrap py-0 flex-column flex-md-row align-items-start',
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
                          columns: [2, 3, 4, 5, 6]
                        }
                      },
                      {
                        extend: 'csv',
                        text: `<span class="d-flex align-items-center"><i class="icon-base ti tabler-file me-1"></i>Csv</span>`,
                        className: 'dropdown-item',
                        exportOptions: {
                          columns: [2, 3, 4, 5, 6]
                        }
                      },
                      {
                        extend: 'excel',
                        text: `<span class="d-flex align-items-center"><i class="icon-base ti tabler-upload me-1"></i>Excel</span>`,
                        className: 'dropdown-item',
                        exportOptions: {
                          columns: [2, 3, 4, 5, 6]
                        }
                      },
                      {
                        extend: 'pdf',
                        text: `<span class="d-flex align-items-center"><i class="icon-base ti tabler-file-text me-1"></i>Pdf</span>`,
                        className: 'dropdown-item',
                        exportOptions: {
                          columns: [2, 3, 4, 5, 6]
                        }
                      },
                      {
                        extend: 'copy',
                        text: `<i class="icon-base ti tabler-copy me-1"></i>Copy`,
                        className: 'dropdown-item',
                        exportOptions: {
                          columns: [2, 3, 4, 5, 6]
                        }
                      }
                    ]
                  },
                  {
                    text: '<i class="icon-base ti tabler-plus me-0 me-sm-1 icon-20px"></i><span class="d-none d-sm-inline-block">Tambah Layanan</span>',
                    className: 'add-new btn btn-primary create-new'
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
              _: '%d baris dipilih'
            }
          }
        },
        responsive: {
          details: {
            display: DataTable.Responsive.display.modal({
              header: function (row) {
                return 'Detail Layanan';
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
            dt_service_table.style.visibility = 'visible';
            dt_service_table.style.opacity = '1';
            setTimeout(() => hideTableLoading(dt_service_table), 300);
          }, 100);
        }
      });
    }
  });

  // Event handlers untuk tombol aksi
  document.body.addEventListener('click', function (e) {
    // View Service
    if (e.target.closest('.view-service')) {
      const id = e.target.closest('.view-service').dataset.id;
      window.location.href = baseUrl + 'service/' + id;
    }

    // Edit Service
    if (e.target.closest('.edit-service')) {
      const id = e.target.closest('.edit-service').dataset.id;
      window.location.href = baseUrl + 'service/' + id + '/edit';
    }

    // Delete Service
    if (e.target.closest('.delete-service')) {
      e.preventDefault();
      const id = e.target.closest('.delete-service').dataset.id;

      Swal.fire({
        title: 'Apakah Kamu yakin?',
        text: 'Data layanan yang dihapus tidak dapat dikembalikan!',
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
          const form = document.createElement('form');
          form.method = 'POST';
          form.action = baseUrl + 'service/' + id;

          const csrfInput = document.createElement('input');
          csrfInput.type = 'hidden';
          csrfInput.name = '_token';
          csrfInput.value = document.querySelector('meta[name="csrf-token"]').content;
          form.appendChild(csrfInput);

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

  // Tampilkan success message dari session
  if (window.serviceSuccessMessage) {
    Swal.fire({
      icon: 'success',
      title: 'Berhasil!',
      text: window.serviceSuccessMessage,
      customClass: {
        confirmButton: 'btn btn-primary waves-effect waves-light'
      },
      buttonsStyling: false
    });
  }

  // Tampilkan error message dari session
  if (window.serviceErrorMessage) {
    Swal.fire({
      icon: 'error',
      title: 'Gagal!',
      text: window.serviceErrorMessage,
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
