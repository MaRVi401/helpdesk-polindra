/**
 * FAQ (Client-side version with SweetAlert2)
 */
// REFERENSI NYA 
'use strict';

document.addEventListener('DOMContentLoaded', function (e) {
  let borderColor, bodyBg, headingColor;

  borderColor = config.colors.borderColor;
  bodyBg = config.colors.bodyBg;
  headingColor = config.colors.headingColor;

  const dt_faq_table = document.querySelector('.datatables-faq'),
    faqAdd = baseUrl + 'faq/create',
    statusObj = {
      Post: { title: 'Post', class: 'bg-label-success' },
      Draft: { title: 'Draft', class: 'bg-label-warning' }
    };

  // FAQ datatable
  if (dt_faq_table) {
    var dt_faq = new DataTable(dt_faq_table, {
      columns: [
        { data: 'id' },
        { data: 'id', orderable: false, render: DataTable.render.select() },
        { data: null, name: 'no' },
        { data: 'judul' },
        { data: 'layanan_id' },
        { data: 'status' },
        { data: 'user_id' },
        { data: 'created_at' },
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
          render: function (data, type, full, meta) {
            const status = data;
            return (
              '<span class="badge ' +
              statusObj[status].class +
              '" text-capitalized>' +
              statusObj[status].title +
              '</span>'
            );
          }
        },
        {
          targets: 6,
          render: function (data, type, full, meta) {
            return `<span>${data}</span>`;
          }
        },
        {
          targets: 7,
          render: function (data, type, full, meta) {
            if (!data || data === '-') return '<span class="text-muted">-</span>';

            const date = new Date(data);
            const formattedDate = date.toLocaleDateString('id-ID', {
              day: '2-digit',
              month: '2-digit',
              year: 'numeric'
            });
            const formattedTime = date.toLocaleTimeString('id-ID', {
              hour: '2-digit',
              minute: '2-digit'
            });

            return `
              <div class="d-flex flex-column">
                <span class="fw-medium">${formattedDate}</span>
                <small class="text-muted">${formattedTime}</small>
              </div>
            `;
          }
        },
        {
          targets: 8,
          title: 'Aksi',
          searchable: false,
          orderable: false,
          className: 'text-center',
          render: function (data, type, full, meta) {
            const row = dt_faq_table.querySelectorAll('tbody tr')[meta.row];
            const id = row.querySelector('td:last-child').dataset.id;

            return `
              <div class="d-inline-block text-nowrap">
                <button class="btn btn-text-secondary rounded-pill waves-effect btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                  <i class="icon-base ti tabler-dots-vertical icon-22px"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end m-0">
                  <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center view-faq" data-id="${id}"><i class="icon-base ti tabler-details me-2"></i> Detail</a>
                  <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center edit-faq" data-id="${id}"><i class="icon-base ti tabler-pencil me-2"></i> Edit</a>
                  <div class="dropdown-divider"></div>
                  <a href="javascript:void(0);" class="dropdown-item text-danger d-flex align-items-center delete-faq" data-id="${id}"><i class="icon-base ti tabler-trash me-2"></i> Hapus</a>
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
                        columns: [2, 3, 4, 5, 6, 7]
                      }
                    },
                    {
                      extend: 'csv',
                      text: `<span class="d-flex align-items-center"><i class="icon-base ti tabler-file me-1"></i>Csv</span>`,
                      className: 'dropdown-item',
                      exportOptions: {
                        columns: [2, 3, 4, 5, 6, 7]
                      }
                    },
                    {
                      extend: 'excel',
                      text: `<span class="d-flex align-items-center"><i class="icon-base ti tabler-upload me-1"></i>Excel</span>`,
                      className: 'dropdown-item',
                      exportOptions: {
                        columns: [2, 3, 4, 5, 6, 7]
                      }
                    },
                    {
                      extend: 'pdf',
                      text: `<span class="d-flex align-items-center"><i class="icon-base ti tabler-file-text me-1"></i>Pdf</span>`,
                      className: 'dropdown-item',
                      exportOptions: {
                        columns: [2, 3, 4, 5, 6, 7]
                      }
                    },
                    {
                      extend: 'copy',
                      text: `<i class="icon-base ti tabler-copy me-1"></i>Copy`,
                      className: 'dropdown-item',
                      exportOptions: {
                        columns: [2, 3, 4, 5, 6, 7]
                      }
                    }
                  ]
                },
                {
                  text: '<i class="icon-base ti tabler-plus me-0 me-sm-1 icon-20px"></i><span class="d-none d-sm-inline-block">Tambah FAQ</span>',
                  className: 'add-new btn btn-primary',
                  action: function () {
                    window.location.href = faqAdd;
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
        // Untuk row selection
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
              const data = row.data();
              return 'Frequently Asked Questions (FAQ)';
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
      initComplete: function () {
        const api = this.api();

        api.columns(5).every(function () {
          const column = this;
          const select = document.createElement('select');
          select.id = 'FaqStatus';
          select.className = 'form-select text-capitalize';
          select.innerHTML = '<option value="">Status</option>';

          document.querySelector('.faq_status').appendChild(select);

          select.addEventListener('change', function () {
            const val = select.value ? `^${select.value}$` : '';
            column.search(val, true, false).draw();
          });

          Object.keys(statusObj).forEach(function (key) {
            const option = document.createElement('option');
            option.value = statusObj[key].title;
            option.textContent = statusObj[key].title;
            select.appendChild(option);
          });
        });
      }
    });

    // Event handlers untuk tombol aksi (mengikuti pattern extended-ui-sweetalert2.js)
    document.body.addEventListener('click', function (e) {
      // View FAQ
      if (e.target.closest('.view-faq')) {
        const id = e.target.closest('.view-faq').dataset.id;
        window.location.href = baseUrl + 'faq/' + id;
      }

      // Edit FAQ
      if (e.target.closest('.edit-faq')) {
        const id = e.target.closest('.edit-faq').dataset.id;
        window.location.href = baseUrl + 'faq/' + id + '/edit';
      }

      // Delete FAQ
      if (e.target.closest('.delete-faq')) {
        e.preventDefault();
        const id = e.target.closest('.delete-faq').dataset.id;

        Swal.fire({
          title: 'Apakah Kamu yakin?',
          text: 'Data FAQ yang dihapus tidak dapat dikembalikan!',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Ya, hapus!',
          cancelButtonText: 'Batal',
          customClass: {
            confirmButton: 'btn btn-primary me-3 waves-effect waves-light',
            cancelButton: 'btn btn-label-secondary waves-effect'
          },
          buttonsStyling: false
        }).then(function (result) {
          if (result.value) {
            // Buat form dan submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = baseUrl + 'faq/' + id;

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
  if (window.faqSuccessMessage) {
    Swal.fire({
      icon: 'success',
      title: 'Berhasil!',
      text: window.faqSuccessMessage,
      customClass: {
        confirmButton: 'btn btn-primary waves-effect waves-light'
      },
      buttonsStyling: false
    });
  }

  // Tampilkan error message dari session
  if (window.faqErrorMessage) {
    Swal.fire({
      icon: 'error',
      title: 'Gagal!',
      text: window.faqErrorMessage,
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
