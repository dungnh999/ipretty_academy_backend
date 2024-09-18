let idCourse, kanbanInstance;
$(function () {
  loadData();
});

async function loadData() {
  let method = 'GET',
    url = '/chapters/get-data-course',
    param = null,
    data = null;
  let res = await axiosTemplate(method, url, param, data);
  drawDataTableCourse(res);
}

async function drawDataTableCourse(res) {
  let id = $('#table-course-chapter-ipretty'),
    column = [
      { data: 'DT_RowIndex', className: 'text-center', width: '5%' },
      { data: 'course_name', className: 'text-center' },
      { data: 'teacher_name', className: 'text-left' },
      { data: 'action', className: 'text-center', width: '5%' }
    ],
    button = [];
  DataTableCourse = await datatableTemplate(id, res.data[0].original.data, column, button);
}

async function saveCreateChapter() {
  let method = 'POST',
    url = '/chapters/create-chapter',
    param = null,
    data = {
      chapter_name: $('#name-create-chapter-ipretty').val(),
      course_id: idCourse
    };
  let res = await axiosTemplate(method, url, param, data);
  if (res.status == 200) {
    let title = `<span class="fw-bold">${res.data.data['chapter_name']}</span>`;
    addDataChapterCourse(kanbanInstance, title);
    closeCreateChapter();
    successSwalNotify('Thêm mới thành công');
  } else {
    errorSwalNotify('Lỗi rồi ' + res);
  }
}

async function getDataChapterforCourse(r) {
  idCourse = r.data('id');
  let method = 'GET',
    url = '/chapters/get-data-chapter-course',
    param = {
      course_id: r.data('id')
    },
    data = null;
  let res = await axiosTemplate(method, url, param, data);
  KTJKanbanDemoFixedHeight.init(res);
}
// Class definition
var KTJKanbanDemoFixedHeight = (function () {
  var element;
  var kanbanEl;
  // Private functions
  var exampleFixedHeight = function (data) {
    // Get kanban height value
    const kanbanHeight = $(kanbanEl).data('jkanban-height');

    if (kanbanInstance) {
      kanbanInstance.removeBoard('_fixed_height'); // Remove the old instance
    }

    // Init jKanban
    kanbanInstance = new jKanban({
      element: element,
      gutter: '0',
      widthBoard: '100%',
      boards: [
        {
          id: '_fixed_height',
          title: data.data[1].course_name,
          class: 'primary',
          item: data.data[0]
        }
      ],

      // Handle item scrolling
      dragEl: function (el, source) {
        $(document).on('mousemove', isDragging);
      },

      dragendEl: function (el) {
        $(document).off('mousemove', isDragging);
      }
    });

    // Set jKanban max height
    $(kanbanEl)
      .find('.kanban-drag')
      .each(function () {
        $(this).attr('style', 'max-height: ' + kanbanHeight + 'px');
      });
  };

  const isDragging = e => {
    $(kanbanEl)
      .find('.kanban-drag')
      .each(function () {
        // Get inner item element
        const $dragItem = $(this).find('.gu-transit');

        // Stop drag on inactive board
        if ($dragItem.length === 0) {
          return;
        }

        // Get jKanban drag container
        const containerRect = this.getBoundingClientRect();

        // Get inner item size
        const itemSize = $dragItem.outerHeight();

        // Get dragging element position
        const $dragMirror = $('.gu-mirror');
        const mirrorRect = $dragMirror[0].getBoundingClientRect();

        // Calculate drag element vs jKanban container
        const topDiff = mirrorRect.top - containerRect.top;
        const bottomDiff = containerRect.bottom - mirrorRect.bottom;

        // Scroll container
        if (topDiff <= itemSize) {
          // Scroll up if item at top of container
          $(this).scrollTop($(this).scrollTop() - 3);
        } else if (bottomDiff <= itemSize) {
          // Scroll down if item at bottom of container
          $(this).scrollTop($(this).scrollTop() + 3);
        } else {
          // Stop scroll if item in middle of container
          $(this).scrollTop($(this).scrollTop());
        }
      });
  };

  return {
    // Public Functions
    init: function (data) {
      element = '#kt_docs_jkanban_fixed_height';
      kanbanEl = $(element);
      exampleFixedHeight(data);
      $('.kanban-container').addClass('w-100');
    }
  };
})();

function addDataChapterCourse(el, title) {
  el.addElement('_fixed_height', {
    title: title
  });
}
