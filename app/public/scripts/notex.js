var notex = {
  line_height: 41,
  page_width: 560,
  selected: null,
  editing: false,
  notes: {},
  count: 0,
  cursor: {x: null, y: null},
  offset: {x: 42, y: 114},
  start: {x: null, y: null},
  init: function() {
    $('#page').mousemove(function(e) {
      notex.cursor.x = e.pageX;
      notex.cursor.y = e.pageY;
    }).click(notex.click);
    $('#edit').focusout(notex.write);
  },
  click: function(e) {
    if (notex.editing) return;
    var text = '';
    if (notex.selected) {
      var note = notex.notes[notex.selected.attr('id')];
      text = notex.selected.text(); notex.selected.text('');
      notex.start.x = note.x-1;
      notex.start.y = note.y-1;
    } else if (typeof e.pageX == 'undefined') {
      notex.start.y += notex.line_height;
    } else {
      notex.start.x = notex.cursor.x - notex.offset.x;
      notex.start.y = notex.cursor.y - notex.offset.y;
    }
    var width = notex.page_width - notex.start.x;
    $('#edit').css({'top': notex.start.y, 'left': notex.start.x, 'width': width}).attr('value', text).show().focus();
    notex.editing = true;
  },
  write: function(opts) {
    notex.editing = false;
    opts = opts || {};
    var text = $('#edit').attr('value');
    if (!text) return;
    $('#edit').attr('value', '').hide();
    var id = notex.count++;
    var note = {id: 'note'+id, x: notex.start.x+1, y: notex.start.y+1, text: text};
    notex.notes[note.id] = note;
    notex.render(note);
    if (opts.newline) $('#page').click();
  },
  render: function(note) {
    $('#content').append('<div id="'+note.id+'" class="note" style="top:'+note.y+'px;left:'+note.x+'px">'+note.text+'</div>');
    $('.note').mouseover(function(e) {
      notex.selected = $(e.target);
    }).mouseout(function(e) {
      notex.selected = null;
    });
  },
  save: function() {
    // TODO
  }
};
