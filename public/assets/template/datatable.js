let vh_table;

$(function () {
  vh_table = $('#layout-navbar').outerHeight(true) + $('#contain-main').outerHeight(true) - 300 + 'px';
  $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
    $($.fn.dataTable.tables(true)).DataTable().columns.adjust();
  });

  $(document).on('shown.bs.collapse', function (e) {
    $($.fn.dataTable.tables(true)).DataTable().columns.adjust();
  });
});

async function datatableTemplate(table, data, column, button = []) {
  $.fn.dataTableExt.oStdClasses.sLengthSelect = 'form-select';
  $.fn.dataTableExt.oStdClasses.sFilterInput = 'form-control';

  let DataTable = await table.DataTable({
    data: data,
    dom:
      "<'row'<'col-md-2'l><'col-md-10'<'dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column mb-3 mb-md-0'fB>>>" +
      "<'row'<'col-sm-12'tr>>" +
      "<'row'<'col-sm-12 col-md-6'i><'col-sm-12 col-md-6'p>>",
    language: {
      lengthMenu: '_MENU_',
      info: 'Trang _PAGE_ trong _PAGES_',
      search: '',
      zeroRecords: 'Không có dữ liệu',
      paginate: {
        first: 'First',
        last: 'Last',
        next: 'Tiếp theo',
        previous: 'Trang trước'
      }
    },
    // scrollX: true,
    scrollCollapse: true,
    scrollY: vh_table,
    autoWidth: true,
    buttons: button,
    lengthMenu: [50, 100],
    columns: column,
    pageLength: 50
  });

  $('[data-bs-toggle="tooltip"]').tooltip();
  return DataTable;
}

function updateDataTable(table, data) {
  let DataTable = table.DataTable();
  DataTable.clear().rows.add(data).draw();
}
