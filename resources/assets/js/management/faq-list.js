'use strict';

// Datatable (js)
document.addEventListener('DOMContentLoaded', function (e) {
  let borderColor, bodyBg, headingColor;

  borderColor = config.colors.borderColor;
  bodyBg = config.colors.bodyBg;
  headingColor = config.colors.headingColor;

  // Variable declaration for table
  const dt_faq_table = document.querySelector('.datatables-faq'),
    faqAdd = baseUrl + 'app/faq/add',
    faqGetList = baseUrl + 'faq/get-list',
    statusObj = {
      'Post': { title: 'Post', class: 'bg-label-success' },
      'Draft': { title: 'Draft', class: 'bg-label-warning' },
    };

  // FAQ datatable
  if (dt_faq_table) {
    var dt_faq = new DataTable(dt_faq_table, {
      processing: false,
      ajax: {
        url: faqGetList,
        type: 'GET',
        dataSrc: 'data',
        error: function (xhr, error, code) {
          console.error('DataTable Error:', {
            status: xhr.status,
            statusText: xhr.statusText,
            responseText: xhr.responseText,
            error: error,
            code: code
          });
          alert('Error loading data. Check console for details.');
        }
      },
      columns: [
        // columns according to database
        { data: 'id' },
        { data: 'id', orderable: false, render: DataTable.render.select() },
        { data: null, name: 'no' }, // untuk nomor urut
        { data: 'judul' },
        { data: 'status' }, // status (kolom ke-4)
        { data: 'user_id' }, // pembuat (kolom ke-5)
        { data: 'created_at' }, // dibuat pada (kolom ke-6)
        { data: 'id' } // aksi
      ],
      columnDefs: [
        {
          // For Responsive
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
          // For Checkboxes
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
          // No urut
          targets: 2,
          orderable: false,
          searchable: false,
          render: function (data, type, full, meta) {
            return meta.row + 1;
          }
        },
        {
          // Judul Pertanyaan
          targets: 3,
          responsivePriority: 1,
          render: function (data, type, full, meta) {
            return `<span class="fw-medium">${full.judul}</span>`;
          }
        },
        {
          // Status
          targets: 4,
          render: function (data, type, full, meta) {
            const status = full.status;
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
          // Pembuat
          targets: 5,
          render: function (data, type, full, meta) {
            // Cek apakah ada data relasi user
            const user = full.user && full.user.name 
              ? full.user.name 
              : (full.user_id ? `ID: ${full.user_id}` : 'Unknown');
            return `<span>${user}</span>`;
          }
        },
        {
          // Dibuat Pada
          targets: 6,
          render: function (data, type, full, meta) {
            // Format tanggal jika ada
            if (full.created_at) {
              const date = new Date(full.created_at);
              const options = { 
                year: 'numeric', 
                month: 'short', 
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
              };
              return `<span>${date.toLocaleDateString('id-ID', options)}</span>`;
            }
            return '<span>-</span>';
          }
        },
        {
          // Actions
          targets: -1,
          title: 'Aksi',
          searchable: false,
          orderable: false,
          render: function (data, type, full, meta) {
            return `
              <div class="d-inline-block text-nowrap">
                <button class="btn btn-text-secondary rounded-pill waves-effect btn-icon edit-faq" data-id="${full.id}">
                  <i class="icon-base ti tabler-edit icon-22px"></i>
                </button>
                <button class="btn btn-text-secondary rounded-pill waves-effect btn-icon delete-faq" data-id="${full.id}">
                  <i class="icon-base ti tabler-trash icon-22px"></i>
                </button>
              </div>
            `;  
          }
        }
      ],
      select: {
        style: 'multi',
        selector: 'td:nth-child(2)'
      },
      order: [2, 'asc'],
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
                menu: [5, 10, 25, 50, 100],
                text: '_MENU_'
              },
              buttons: [
                {
                  extend: 'collection',
                  className: 'btn btn-label-secondary dropdown-toggle me-4',
                  text: '<span class="d-flex align-items-center gap-1"><i class="icon-base ti tabler-upload icon-xs"></i> <span class="d-none d-sm-inline-block">Export</span></span>',
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
                  text: '<i class="icon-base ti tabler-plus me-0 me-sm-1 icon-16px"></i><span class="d-none d-sm-inline-block">Tambah Data</span>',
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
        paginate: {
          next: '<i class="icon-base ti tabler-chevron-right scaleX-n1-rtl icon-18px"></i>',
          previous: '<i class="icon-base ti tabler-chevron-left scaleX-n1-rtl icon-18px"></i>',
          first: '<i class="icon-base ti tabler-chevrons-left scaleX-n1-rtl icon-18px"></i>',
          last: '<i class="icon-base ti tabler-chevrons-right scaleX-n1-rtl icon-18px"></i>'
        }
      },
      responsive: {
        details: {
          display: DataTable.Responsive.display.modal({
            header: function (row) {
              const data = row.data();
              return 'Detail FAQ:' ;
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

        // Adding status filter
        api.columns(4).every(function () {
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

    // Event handlers untuk tombol aksi
    dt_faq_table.addEventListener('click', function (e) {
      // View FAQ
      if (e.target.closest('.view-faq')) {
        const id = e.target.closest('.view-faq').dataset.id;
        // Redirect ke halaman view atau buka modal
        window.location.href = baseUrl + 'app/faq/view/' + id;
      }

      // Edit FAQ
      if (e.target.closest('.edit-faq')) {
        const id = e.target.closest('.edit-faq').dataset.id;
        window.location.href = baseUrl + 'app/faq/edit/' + id;
      }

      // Delete FAQ
      if (e.target.closest('.delete-faq')) {
        const id = e.target.closest('.delete-faq').dataset.id;
        // Tampilkan konfirmasi sebelum menghapus
        if (confirm('Apakah Anda yakin ingin menghapus FAQ ini?')) {
          // Kirim request delete ke server
          fetch(baseUrl + 'app/faq/delete/' + id, {
            method: 'DELETE',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
          })
            .then(response => response.json())
            .then(data => {
              if (data.success) {
                dt_faq.ajax.reload();
                alert('FAQ berhasil dihapus');
              }
            });
        }
      }
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