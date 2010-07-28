var notex = {
  line_height: 41,
  page_height: 777,
  page_width: 560,
  selected: null,
  editing: false,
  notes: {},
  cursor: {x: null, y: null},
  offset: {x: 42, y: 114},
  adjust: {x: 0, y: 7},
  origin: {x: null, y: null},
  nearby: {x: 10, y: 20},
  init: function() {
    $('#page').mousemove(function(e) {
      notex.cursor.x = e.pageX;
      notex.cursor.y = e.pageY;
    }).click(notex.click);
    $('#edit').focusout(notex.write);
    notex.load();
  },
  click: function(e) {
    if (notex.editing) return;
    var text = '';
    if (typeof e.pageX == 'undefined') {
      notex.origin.y += notex.line_height;
      if (notex.origin.y > notex.page_height - notex.line_height) return;
      notex.selected = null;
      for (id in notex.notes) {
        var note = notex.notes[id];
        if (note && notex.is_near(note, notex.origin)) {
          notex.selected = $('#'+note.id);
        }
      }
    } else {
      notex.origin.x = notex.cursor.x - notex.offset.x;
      notex.origin.y = notex.cursor.y - notex.offset.y - notex.adjust.y;
    }
    if (notex.selected) {
      var id = notex.selected.attr('id');
      var note = notex.notes[id];
      if (note) {
        text = notex.selected.text(); notex.selected.text('');
        notex.origin.x = note.x-1;
        notex.origin.y = note.y-1;
        delete notex.notes[id];
      }
    }
    var width = notex.page_width - notex.origin.x;
    $('#edit').css({'top': notex.origin.y, 'left': notex.origin.x, 'width': width}).attr('value', text).show().select().focus();
    notex.editing = true;
  },
  write: function(opts) {
    notex.editing = false;
    opts = opts || {};
    var text = $('#edit').attr('value');
    if (text) notex.create(text);
    notex.save();
    if (opts.newline) $('#page').click();
  },
  create: function(text) {
    $('#edit').attr('value', '').hide();
    var id = (new Date()).getTime();
    var note = {id: 'note'+id, x: notex.origin.x+1, y: notex.origin.y+1, text: text};
    notex.notes[note.id] = note;
    notex.render(note);
  },
  render: function(note) {
    $('#content').append('<div id="'+note.id+'" class="note" style="top:'+note.y+'px;left:'+note.x+'px">'+note.text+'</div>');
    $('.note').mouseover(function(e) {
      notex.selected = $(e.target);
    }).mouseout(function(e) {
      notex.selected = null;
    });
  },
  load: function() {
    $.get('/note/load.json', {url: window.location.href},
    function(data) {
      eval('notex.notes='+data+';');
      for (id in notex.notes) {
        var note = notex.notes[id];
        if (note) notex.render(note);
      }
    });
  },
  save: function() {
    $.post('/note/save.json', {url: window.location.href, notes: $.toJSON(notex.notes)},
    function(data) {
      // callback
    });
  },
  is_near: function(pos1, pos2) {
    var x_diff = Math.abs(pos1.x - pos2.x);
    var y_diff = Math.abs(pos1.y - pos2.y);
    return (x_diff <= notex.nearby.x && y_diff <= notex.nearby.y ? true : false);
  },
  debug: function(obj) { $('#debug').text('['+$.toJSON(obj)+']') }
};
