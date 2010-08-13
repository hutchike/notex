/*
 * noteX - Copyright (c) 2010 Kevin Hutchinson <hutchike@gmail.com>
 *         All Rights Reserved. Please contact me if you'd like to
 *         own a copy of this system or have it installed locally.
 */
var notex = {

  // Constants
  Line_height: 41,
  Page_height: 764,
  Page_width: 556,
  Poll_msecs: 2000,

  // Properties
  is_editing: false,
  selected: null,
  secret: '',
  color: '',
  font: '',
  notes: {},
  cursor: {x: null, y: null},
  offset: {x: 2, y: 8},
  adjust: {x: -1, y: 5},
  origin: {x: null, y: null},
  nearby: {x: 10, y: 20},

  init: function() {
    $('#page').mousemove(function(e) {
      notex.cursor.x = e.pageX;
      notex.cursor.y = e.pageY;
    }).click(notex.click);
    $('#edit').focusout(notex.write);
    notex.load();
    notex.set_secret();
    notex.set_offset();
    notex.stylebox.init();
    window.setInterval(notex.poll, notex.Poll_msecs);
  },
  poll: function() {
    notex.save();
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
      notex.origin.x = notex.cursor.x - notex.offset.x - notex.adjust.x;
      notex.origin.y = notex.cursor.y - notex.offset.y - notex.adjust.y;
    }
    if (notex.selected) {
      var id = notex.selected.attr('id');
      var note = notex.notes[id];
      if (note) {
        text = notex.selected.text(); notex.selected.text('');
        notex.origin.x = note.x + notex.adjust.x;
        notex.origin.y = note.y + notex.adjust.y;
        notex.notes[id].deleted = true;
      }
    }
    var width = notex.Page_width - notex.origin.x;
    $('#edit').css({'top': notex.origin.y, 'left': notex.origin.x, 'width': width, color: notex.color}).attr({value: text, 'class': notex.font}).show().select().focus();
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
    text = text.replace(/"/g, '&quot;'); // for JSON
    text = text.replace(/</g, '&lt;');  // for XML
    text = text.replace(/>/g, '&gt;'); // for XML
    var note = {x: notex.origin.x - notex.adjust.x, y: notex.origin.y - notex.adjust.y, text: text, color: notex.color, font: notex.font};
    notex.notes[id] = note;
    notex.render(id, note);
  },
  embed: function(it) {
    var id = 'embed' + (new Date()).getTime();
    return '<span id="'+id+'_embed"></span><script type="text/javascript">try{$("#'+id+'_embed").text('+it+')}catch(e){};</script>='+it;
  },
  render: function(id, note) {
    if (note.deleted) return;
    $('#content').append('<div id="'+id+'" class="note '+note.font+'" style="top:'+note.y+'px;left:'+note.x+'px;color:'+note.color+'">'+notex.markup(note.text)+'</div>');
    $('.note').mouseover(function(e) {
      if (notex.selected) return;
      notex.selected = $(e.target);
      while (!notex.selected.hasClass('note')) {
        notex.selected = notex.selected.parent(); // for formetted text
      }
      if (!notex.selected.hasClass('selected')) notex.selected.addClass('selected');
    }).mouseout(function(e) {
      if (notex.selected) notex.selected.removeClass('selected');
      notex.selected = null;
    });
  },
  markup: function(text) {
    text = text.replace(/(noted?):(\S+)/ig, '$1:<a href="/$2">$2</a>');
    text = text.replace(/(https?):\/\/(\S+)/ig, '$1://<a href="$1://$2" target="_blank">$2</a>');
    text = text.replace(/(images?):(.+)/i, '$1:<a href="http://www.google.com/images?hl=en&q=$2" target="_blank">$2</a>');
    text = text.replace(/(maps?):(.+)/i, '$1:<a href="http://maps.google.com/?ie=UTF&near=$2" target="_blank">$2</a>');
    text = text.replace(/(weather):(.+)/i, '$1:<a href="http://www.wunderground.com/cgi-bin/findweather/getForecast?query=$2" target="_blank">$2</a>');
    text = text.replace(/(music):(.+)/i, '$1:<a href="http://www.emusic.com/search.html?mode=x&QT=$2" target="_blank">$2</a>');
    text = text.replace(/(photos?):(.+)/i, '$1:<a href="http://www.flickr.com/search/?q=$2" target="_blank">$2</a>');
    text = text.replace(/(search):(.+)/i, '$1:<a href="http://www.google.com/search?ie=UTF-8&q=$2" target="_blank">$2</a>');
    text = text.replace(/(videos?):(.+)/i, '$1:<a href="http://www.youtube.com/results?search_query=$2" target="_blank">$2</a>');
    text = text.replace(/(wiki|wikipedia):(.+)/i, '$1:<a href="http://en.wikipedia.org/wiki/$2" target="_blank">$2</a>');
    text = text.replace(/(^|\s|\/)_(\S.*)_/g, '$1<b>$2</b>');
    text = text.replace(/(^|\s|>)\/(\S.*[^\s<])\//g, '$1<i>$2</i>');
    text = text.replace(/(\S+@\S+)/g, '<a href="mailto:$1">$1</a>');
    text = text.replace(/(^|\s)@(\w+)/g, '$1@<a href="http://twitter.com/$2" target="_blank">$2</a>');
    text = text.replace(/(^|\s)#(\w+)/g, '$1#<a href="http://twitter.com/#search?q=%23$2" target="_blank">$2</a>');
    text = text.replace(/^=(.+)$/, notex.embed('$1'));
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
  set_color: function(color) {
    if (color) {
      notex.color = color;
    } else { // use a named anchor from the URL
      var url = new String(window.location.href);
      var found = url.match(/#(\w+)/);
      if (found) notex.color = found[1];
    }
  },
  set_font: function(font) {
    notex.font = font;
  },
  set_secret: function(secret) {
    if (secret) {
      notex.secret = secret;
    } else { // use a query string from the URL
      var url = new String(window.location.href);
      var found = url.match(/\?(\w+)/);
      if (found) notex.secret = found[1];
    }
  },
  set_offset: function() {
    var notepad = $('#notepad');
    notex.offset.x += parseInt(notepad.css('left'));
    notex.offset.y += parseInt(notepad.css('top'));
  },
  debug: function(obj) { $('#debug').text('['+$.toJSON(obj)+']') }
};

notex.stylebox = {
  Cookie_hours: 24*90,
  init: function() {
    var color = notex.cookie.get('color');
    if (color) {
      color = color.split(':');
      this.set_color(color[0], parseInt(color[1]), parseInt(color[2]));
    } else {
      notex.set_color('#333');
    }
    var font = notex.cookie.get('font');
    if (font) {
      font = font.split(':');
      this.set_font(font[0], parseInt(font[1]), parseInt(font[2]));
    } else {
      notex.set_font('sans');
    }
  },
  set_color: function(name, x, y) {
    notex.set_color(name);
    $('#selectcolor').css({left: x, top: y});
    notex.cookie.set('color', name+':'+x+':'+y, this.Cookie_hours);
  },
  set_font: function(name, x, y) {
    notex.set_font(name);
    $('#selectfont').css({left: x, top: y});
    notex.cookie.set('font', name+':'+x+':'+y, this.Cookie_hours);
  },
  version: 0.1
};

notex.cookie = {
  decode: decodeURIComponent,
  encode: encodeURIComponent,
  set: function(name, value, hours) {
    var cookie = 'notex_' + name + '=' + this.encode(value) + '; path=/'
    if (hours) {
      var expires = new Date();
      expires.setTime(expires.getTime() + hours*60*60*1000);
      cookie += '; expires=' + expires.toGMTString();
    }
    document.cookie = cookie;
    return value;
  },
  get: function(name)
  {
    var cookie = document.cookie;
    var prefix = 'notex_' + name + '=';
    var begin = cookie.indexOf('; ' + prefix);
    if (begin == -1) {
      begin = cookie.indexOf(prefix);
      if (begin != 0) return '';
    }
    else begin += 2;
    var end = document.cookie.indexOf(';', begin);
    if (end == -1) end = cookie.length;
    return this.decode(cookie.substring(begin + prefix.length, end));
  }
};
