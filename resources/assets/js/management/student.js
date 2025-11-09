/**
 * Student Management
 */

'use strict';

let fv, offCanvasEl;
document.addEventListener('DOMContentLoaded', function (e) {
  let borderColor, bodyBg, headingColor;

  borderColor = config.colors.borderColor;
  bodyBg = config.colors.bodyBg;
  headingColor = config.colors.headingColor;

  const dt_student_table = document.querySelector('.datatables-basic'),
    studentAdd = baseUrl + 'student/create';

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
          (offCanvasElement.querySelector('.dt-full-name').value = ''),
            (offCanvasElement.querySelector('.dt-nim').value = ''),
            (offCanvasElement.querySelector('.dt-email').value = ''),
            (offCanvasElement.querySelector('.dt-prodi').value = ''),
            (offCanvasElement.querySelector('.dt-tahun-masuk').value = '');
          // Open offCanvas with form
          offCanvasEl.show();
        });
      }
    }, 200);

    // Form validation for Add new record
    if (formAddNewRecord) {
      fv = FormValidation.formValidation(formAddNewRecord, {
        fields: {
          name: {
            validators: {
              notEmpty: { message: 'Nama lengkap wajib diisi' }
            }
          },
          nim: {
            validators: {
              notEmpty: { message: 'NIM wajib diisi' }
            }
          },
          email: {
            validators: {
              notEmpty: { message: 'Email wajib diisi' },
              emailAddress: { message: 'Format email tidak valid' }
            }
          },
          program_studi: {
            validators: {
              notEmpty: { message: 'Program Studi wajib dipilih' }
            }
          },
          tahun_masuk: {
            validators: {
              notEmpty: { message: 'Tahun masuk wajib diisi' }
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

  // Student datatable
  if (dt_student_table) {
    var dt_student = new DataTable(dt_student_table, {
      columns: [
        { data: 'id' },
        { data: 'id', orderable: false, render: DataTable.render.select() },
        { data: null, name: 'no' },
        { data: 'full_name' },
        { data: 'nim' },
        { data: 'email' },
        { data: 'prodi' },
        { data: 'tahun_masuk' },
        { data: 'id' }
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
          // Nomor urut
          targets: 2,
          orderable: false,
          searchable: false,
          render: function (data, type, full, meta) {
            return meta.row + 1;
          }
        },
        {
          // Nama Lengkap dengan Avatar
          targets: 3,
          responsivePriority: 1,
          render: function (data, type, full, meta) {
            const name = data;
            const nim = full['nim'];

            // Generate initials for avatar
            let initials = name.match(/\b\w/g) || [];
            initials = ((initials.shift() || '') + (initials.pop() || '')).toUpperCase();

            const stateNum = Math.floor(Math.random() * 6);
            const states = ['success', 'danger', 'warning', 'info', 'dark', 'primary', 'secondary'];
            const state = states[stateNum];

            const rowOutput = `
              <div class="d-flex justify-content-start align-items-center user-name">
                <div class="avatar-wrapper">
                  <div class="avatar me-2">
                    <span class="avatar-initial rounded-circle bg-label-${state}">${initials}</span>
                  </div>
                </div>
                <div class="d-flex flex-column">
                  <span class="emp_name text-truncate text-heading fw-medium">${name}</span>
                </div>
              </div>
            `;

            return rowOutput;
          }
        },
        {
          // NIM
          targets: 4,
          responsivePriority: 1,
          render: function (data, type, full, meta) {
            return `<span class="fw-medium">${data}</span>`;
          }
        },
        {
          // Email
          targets: 5,
          responsivePriority: 1,
          render: function (data, type, full, meta) {
            return `<span>${data}</span>`;
          }
        },
        {
          // Prodi
          targets: 6,
          render: function (data, type, full, meta) {
            return `<span>${data}</span>`;
          }
        },
        {
          // Tahun Masuk
          targets: 7,
          render: function (data, type, full, meta) {
            return `<span>${data}</span>`;
          }
        },
        {
          // Actions
          targets: 8,
          title: 'Aksi',
          searchable: false,
          orderable: false,
          className: 'text-center',
          render: function (data, type, full, meta) {
            // Get all rows
            const rows = dt_student_table?.querySelectorAll('tbody tr');
            // If rows exist and the current row exists, get its ID
            const id = rows && rows[meta.row] ? rows[meta.row].querySelector('td:last-child')?.dataset?.id : '';

            return `
              <div class="d-inline-block text-nowrap">
                <button class="btn btn-text-secondary rounded-pill waves-effect btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                  <i class="icon-base ti tabler-dots-vertical icon-22px"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end m-0">
                  <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center view-student" data-id="${id}"><i class="icon-base ti tabler-eye me-2"></i> Detail</a>
                  <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center edit-student" data-id="${id}"><i class="icon-base ti tabler-pencil me-2"></i> Edit</a>
                  <div class="dropdown-divider"></div>
                  <a href="javascript:void(0);" class="dropdown-item text-danger d-flex align-items-center delete-student" data-id="${id}"><i class="icon-base ti tabler-trash me-2"></i> Hapus</a>
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
      order: [[2, 'asc']],
      displayLength: 10,
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
                menu: [10, 25, 50, 100],
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
                        columns: [2, 3, 4, 5, 6, 7],
                        format: {
                          body: function (inner, coldex, rowdex) {
                            if (inner.length <= 0) return inner;
                            if (inner.indexOf('<') > -1) {
                              const parser = new DOMParser();
                              const doc = parser.parseFromString(inner, 'text/html');
                              let text = '';
                              const userNameElements = doc.querySelectorAll('.user-name');
                              if (userNameElements.length > 0) {
                                userNameElements.forEach(el => {
                                  const nameText =
                                    el.querySelector('.fw-medium')?.textContent ||
                                    el.querySelector('.d-block')?.textContent ||
                                    el.textContent;
                                  text += nameText.trim() + ' ';
                                });
                              } else {
                                text = doc.body.textContent || doc.body.innerText;
                              }
                              return text.trim();
                            }
                            return inner;
                          }
                        }
                      },
                      customize: function (win) {
                        win.document.body.style.color = config.colors.headingColor;
                        win.document.body.style.borderColor = config.colors.borderColor;
                        win.document.body.style.backgroundColor = config.colors.bodyBg;
                        const table = win.document.body.querySelector('table');
                        table.classList.add('compact');
                        table.style.color = 'inherit';
                        table.style.borderColor = 'inherit';
                        table.style.backgroundColor = 'inherit';
                      }
                    },
                    {
                      extend: 'csv',
                      text: `<span class="d-flex align-items-center"><i class="icon-base ti tabler-file me-1"></i>CSV</span>`,
                      className: 'dropdown-item',
                      exportOptions: {
                        columns: [2, 3, 4, 5, 6, 7],
                        format: {
                          body: function (inner, coldex, rowdex) {
                            if (inner.length <= 0) return inner;
                            const parser = new DOMParser();
                            const doc = parser.parseFromString(inner, 'text/html');
                            let text = '';
                            const userNameElements = doc.querySelectorAll('.user-name');
                            if (userNameElements.length > 0) {
                              userNameElements.forEach(el => {
                                const nameText =
                                  el.querySelector('.fw-medium')?.textContent ||
                                  el.querySelector('.d-block')?.textContent ||
                                  el.textContent;
                                text += nameText.trim() + ' ';
                              });
                            } else {
                              text = doc.body.textContent || doc.body.innerText;
                            }
                            return text.trim();
                          }
                        }
                      }
                    },
                    {
                      extend: 'excel',
                      text: `<span class="d-flex align-items-center"><i class="icon-base ti tabler-upload me-1"></i>Excel</span>`,
                      className: 'dropdown-item',
                      exportOptions: {
                        columns: [2, 3, 4, 5, 6, 7],
                        format: {
                          body: function (inner, coldex, rowdex) {
                            if (inner.length <= 0) return inner;
                            const parser = new DOMParser();
                            const doc = parser.parseFromString(inner, 'text/html');
                            let text = '';
                            const userNameElements = doc.querySelectorAll('.user-name');
                            if (userNameElements.length > 0) {
                              userNameElements.forEach(el => {
                                const nameText =
                                  el.querySelector('.fw-medium')?.textContent ||
                                  el.querySelector('.d-block')?.textContent ||
                                  el.textContent;
                                text += nameText.trim() + ' ';
                              });
                            } else {
                              text = doc.body.textContent || doc.body.innerText;
                            }
                            return text.trim();
                          }
                        }
                      }
                    },
                    {
                      extend: 'pdf',
                      text: `<span class="d-flex align-items-center"><i class="icon-base ti tabler-file-text me-1"></i>PDF</span>`,
                      className: 'dropdown-item',
                      exportOptions: {
                        columns: [2, 3, 4, 5, 6, 7],
                        format: {
                          body: function (inner, coldex, rowdex) {
                            if (inner.length <= 0) return inner;
                            const parser = new DOMParser();
                            const doc = parser.parseFromString(inner, 'text/html');
                            let text = '';
                            const userNameElements = doc.querySelectorAll('.user-name');
                            if (userNameElements.length > 0) {
                              userNameElements.forEach(el => {
                                const nameText =
                                  el.querySelector('.fw-medium')?.textContent ||
                                  el.querySelector('.d-block')?.textContent ||
                                  el.textContent;
                                text += nameText.trim() + ' ';
                              });
                            } else {
                              text = doc.body.textContent || doc.body.innerText;
                            }
                            return text.trim();
                          }
                        }
                      }
                    },
                    {
                      extend: 'copy',
                      text: `<i class="icon-base ti tabler-copy me-1"></i>Copy`,
                      className: 'dropdown-item',
                      exportOptions: {
                        columns: [2, 3, 4, 5, 6, 7],
                        format: {
                          body: function (inner, coldex, rowdex) {
                            if (inner.length <= 0) return inner;
                            const parser = new DOMParser();
                            const doc = parser.parseFromString(inner, 'text/html');
                            let text = '';
                            const userNameElements = doc.querySelectorAll('.user-name');
                            if (userNameElements.length > 0) {
                              userNameElements.forEach(el => {
                                const nameText =
                                  el.querySelector('.fw-medium')?.textContent ||
                                  el.querySelector('.d-block')?.textContent ||
                                  el.textContent;
                                text += nameText.trim() + ' ';
                              });
                            } else {
                              text = doc.body.textContent || doc.body.innerText;
                            }
                            return text.trim();
                          }
                        }
                      }
                    }
                  ]
                },
                {
                  text: '<i class="icon-base ti tabler-plus me-0 me-sm-1 icon-20px"></i><span class="d-none d-sm-inline-block">Tambah Mahasiswa</span>',
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
              return 'Detail Mahasiswa';
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
      }
    });

    // Event handlers untuk tombol aksi
    document.body.addEventListener('click', function (e) {
      // View Student
      if (e.target.closest('.view-student')) {
        const id = e.target.closest('.view-student').dataset.id;
        window.location.href = baseUrl + 'student/' + id;
      }

      // Edit Student
      if (e.target.closest('.edit-student')) {
        const id = e.target.closest('.edit-student').dataset.id;
        window.location.href = baseUrl + 'student/' + id + '/edit';
      }

      // Delete Student
      if (e.target.closest('.delete-student')) {
        e.preventDefault();
        const id = e.target.closest('.delete-student').dataset.id;

        Swal.fire({
          title: 'Apakah Anda yakin?',
          text: 'Data mahasiswa yang dihapus tidak dapat dikembalikan!',
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
            form.action = baseUrl + 'student/' + id;

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

  // Tampilkan success message dari session
  if (window.studentSuccessMessage) {
    Swal.fire({
      icon: 'success',
      title: 'Berhasil!',
      text: window.studentSuccessMessage,
      customClass: {
        confirmButton: 'btn btn-primary waves-effect waves-light'
      },
      buttonsStyling: false
    });
  }

  // Tampilkan error message dari session
  if (window.studentErrorMessage) {
    Swal.fire({
      icon: 'error',
      title: 'Gagal!',
      text: window.studentErrorMessage,
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
