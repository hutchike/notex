/*
 * noteX - Copyright (c) 2010 Kevin Hutchinson <hutchike@gmail.com>
 *         All Rights Reserved. Please contact me if you'd like to
 *         own a copy of this system or have it installed locally.
 */
var notex = {
  Line_height: 41,
  Page_height: 777,
  Page_width: 560,
  Poll_msecs: 2000,
  is_editing: false,
  selected: null,
  secret: '',
  color: 'black',
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
    notex.set_color();
    notex.set_secret();
    window.setInterval(notex.save, notex.Poll_msecs);
  },
  click: function(e) {
    if (notex.is_editing) return;
    var text = '';
    if (typeof e.pageX == 'undefined') {
      notex.origin.y += notex.Line_height;
      if (notex.origin.y > notex.Page_height - notex.Line_height) return;
      notex.selected = null;
      for (id in notex.notes) {
        var note = notex.notes[id];
        if (note && notex.is_near(note, notex.origin)) {
          notex.selected = $('#'+id);
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
        notex.notes[id].deleted = true;
      }
    }
    var width = notex.Page_width - notex.origin.x;
    $('#edit').css({'top': notex.origin.y, 'left': notex.origin.x, 'width': width, color: notex.color}).attr('value', text).show().select().focus();
    notex.is_editing = true;
  },
  write: function(opts) {
    notex.is_editing = false;
    opts = opts || {};
    var text = $('#edit').attr('value');
    if (text) notex.create(text);
    notex.save();
    if (opts.newline) $('#page').click();
  },
  create: function(text) {
    $('#edit').attr('value', '').hide();
    var id = 'note' + (new Date()).getTime();
    var note = {x: notex.origin.x+1, y: notex.origin.y+1, text: text, color: notex.color};
    notex.notes[id] = note;
    notex.render(id, note);
  },
  render: function(id, note) {
    if (note.deleted) return;
    $('#content').append('<div id="'+id+'" class="note" style="top:'+note.y+'px;left:'+note.x+'px;color:'+note.color+'">'+notex.markup(note.text)+'</div>');
    $('.note').mouseover(function(e) {
      notex.selected = $(e.target);
    }).mouseout(function(e) {
      notex.selected = null;
    });
  },
  markup: function(text) {
    text = text.replace(/(http:\/\/\S+)/g, '<a href="$1" target="_blank">$1</a>');
    text = text.replace(/_(\w+)_/g, '<b>$1</b>');
    text = text.replace(/\/(\w+)\//g, '<i>$1</i>');
    text = text.replace(/([^\w])@(\w+)/g, '$1<a href="http://twitter.com/$2" target="_blank">@$2</a>');
    text = text.replace(/([^\w])#(\w+)/g, '$1<a href="http://twitter.com/#search?q=%23$2" target="_blank">#$2</a>');
    return text;
  },
  load: function() {
    $.get('/note/load.json', {url: window.location.href},
    function(data) {
      eval('notex.notes='+data+';');
      for (id in notex.notes) {
        var note = notex.notes[id];
        notex.render(id, note);
      }
    });
  },
  save: function() {
    if (notex.is_editing) return;
    $.post('/note/save.json', {url: window.location.href, notes: $.toJSON(notex.notes), secret: notex.secret},
    function(data) {
      var diff;
      eval('diff='+data+';');
      for (id in diff) {
        var note = notex.notes[id] = diff[id];
        if (note.deleted) {
          $('#'+id).remove();
        } else {
          notex.render(id, note);
        }
      }
    });
  },
  is_near: function(pos1, pos2) {
    var x_diff = Math.abs(pos1.x - pos2.x);
    var y_diff = Math.abs(pos1.y - pos2.y);
    return (x_diff <= notex.nearby.x && y_diff <= notex.nearby.y);
  },
  set_color: function() {
    var url = new String(window.location.href);
    var found = url.match(/#(\w+)/);
    if (found) notex.color = found[1];
  },
  set_secret: function() {
    var url = new String(window.location.href);
    var found = url.match(/\?(\w+)/);
    if (found) notex.secret = found[1];
  },
  debug: function(obj) { $('#debug').text('['+$.toJSON(obj)+']') }
};
