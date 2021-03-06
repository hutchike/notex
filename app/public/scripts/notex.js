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
  Hide_after: 5,

  // Properties
  is_updating: false,
  is_editing: false,
  is_hiding: true,
  selected: null,
  search: '',
  secret: '',
  paused: 0,
  notes: {},
  is_owner: false,
  can_read: true,
  can_edit: true,
  offset: {x: 14, y: 20},
  adjust: {x: -1, y: 5},
  origin: {x: null, y: null},
  nearby: {x: 10, y: 20},

  init: function() {
    $('body').mousemove(function(e) {
      notex.paused = 0;
      if (notex.is_hiding) notex.hide(false);
    });
    $('#page').click(notex.click);
    $('#edit').focusout(notex.write);
    with (notex) {
      load();
      set_secret();
      set_offset();
      penbox.init();
      notebox.init();
      notelist.init();
    }
    window.setInterval(notex.poll, notex.Poll_msecs);
  },
  poll: function() {
    notex.save();
    notex.paused++;
    if (notex.paused >= notex.Hide_after) {
      notex.hide(true);
    }
  },
  hide: function(is_hiding) {
    var divs = $('#penbox,#notebox,#notelist');
    is_hiding ? divs.fadeOut() : divs.fadeIn();
    notex.is_hiding = is_hiding;
  },
  perms: function(config) {
    notex.is_owner = config.is_owner;
    notex.can_read = config.can_read;
    notex.can_edit = config.can_edit;
  },
  click: function(e) {
    if (notex.is_editing || !notex.can_edit) return;
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
      notex.origin.x = e.pageX - notex.offset.x - notex.adjust.x;
      notex.origin.y = e.pageY - notex.offset.y - notex.adjust.y;
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
    $('#edit').css({'top': notex.origin.y, 'left': notex.origin.x, 'width': width, color: notex.penbox.color}).attr({value: text, 'class': notex.penbox.font}).show().select().focus();
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
    var note = {x: notex.origin.x - notex.adjust.x, y: notex.origin.y - notex.adjust.y, text: text, color: notex.penbox.color, font: notex.penbox.font};
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
    $('.note').mouseenter(function(e) {
      if (notex.selected) return;
      notex.selected = $(e.target);
      while (!notex.selected.hasClass('note')) {
        notex.selected = notex.selected.parent(); // for formetted text
      }
      if (!notex.selected.hasClass('selected')) notex.selected.addClass('selected');
    }).mouseleave(function(e) {
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
    text = text.replace(/(^|\s|\/)\*(\S.*)\*/g, '$1<b>$2</b>');
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
      var config;
      eval('config=' + (data || '{}') + ';');
      notex.perms(config);
      notex.notebox.load(config);
      notex.notelist.update(config);
      notex.notes = config.notes ? config.notes : {};
      for (id in notex.notes) {
        notex.render(id, notex.notes[id]);
      }
    });
  },
  save: function() {
    var config = notex.notebox.config();
    config.notes = notex.notes;
    $.post('/note/save.json', {url: window.location.href, config: $.toJSON(config), search: notex.search, secret: notex.secret},
    function(data) {
      if (notex.is_updating) return (notex.is_updating = false);
      var config;
      eval('config=' + (data || '{}') + ';');
      notex.perms(config);
      notex.notebox.update(config);
      notex.notelist.update(config);
      if (config.can_read == false) return notex.notebox.wipe(false, true);
      for (id in config.diff) {
        var note = notex.notes[id] = config.diff[id];
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
  debug: function(obj) { $('#debug').append('<div>['+$.toJSON(obj)+']</div>') },
  version: 0.1
};

notex.utils = {
  decode: decodeURIComponent,
  encode: encodeURIComponent,
  rand: function(lo, hi) {
    lo = lo || 1;
    hi = hi || 2;
    var time = (new Date()).getTime();
    return (time % (1 + hi - lo) + lo);
  },
  version: 0.1
};

notex.penbox = {

  // Properties
  color: '',
  font: '',

  init: function() {
    var color = notex.cookie.get('color');
    if (color) {
      color = color.split(':');
      this.set_color(color[0], parseInt(color[1]), parseInt(color[2]));
    } else {
      this.set_color('#333');
    }
    var font = notex.cookie.get('font');
    if (font) {
      font = font.split(':');
      this.set_font(font[0], parseInt(font[1]), parseInt(font[2]));
    } else {
      this.set_font('sans');
    }
  },
  set_color: function(name, x, y) {
    this.color = name;
    $('#selectcolor').css({left: x, top: y});
    notex.cookie.set('color', name+':'+x+':'+y);
  },
  set_font: function(name, x, y) {
    this.font = name;
    $('#selectfont').css({left: x, top: y});
    notex.cookie.set('font', name+':'+x+':'+y);
  },
  version: 0.1
};

notex.notebox = {

  // Constants
  Defaults: {
    photo: 'photo4',
    paper: 'paper1',
    readers: 'all',
    editors: 'me'
  },

  // Properties
  photo: null,
  paper: null,
  readers: null,
  editors: null,
  has_changed: {},

  init: function() {
    var dialog_images = ['dialogs/nochange.png', 'dialogs/norename.png', 'dialogs/nowipe.png'];
    notex.fx.preload(dialog_images);
  },
  load: function(config) {
    config.photo = config.photo || notex.cookie.get('photo') || this.Defaults.photo;
    config.paper = config.paper || notex.cookie.get('paper') || this.Defaults.paper;
    config.readers = config.readers || notex.cookie.get('readers') || this.Defaults.readers;
    config.editors = config.editors || notex.cookie.get('editors') || this.Defaults.editors;
    this.update(config);
  },
  update: function(config) {
    if (config.photo) this.photo = config.photo;
    if (config.paper) this.paper = config.paper;
    if (config.readers) this.readers = config.readers;
    if (config.editors) this.editors = config.editors;
    this.display();
  },
  config: function() {
    var changes = {
      photo: this.has_changed.photo ? this.photo : null,
      paper: this.has_changed.paper ? this.paper : null,
      readers: this.has_changed.readers ? this.readers : null,
      editors: this.has_changed.editors ? this.editors : null
    };
    this.has_changed = {};
    return changes;
  },
  display: function() {
    var images = '/images/';
    $('body').css('background', 'url(' + images + 'photos/' + this.photo + '.jpg)');
    $('#page').css('background', 'url(' + images + 'papers/' + this.paper + '.jpg)');
    $('#notebox #photo img').attr('src', images + 'thumbs/' + this.photo + '.jpg');
    $('#notebox #paper').css('background', 'url(' + images + 'thumbs/' + this.paper + '.jpg) no-repeat -27px -47px');
    var canread = (notex.is_owner ? this.readers == 'all' : notex.can_read) ?  'check' : 'cross';
    $('#canread').css('background', 'url(' + images + canread + '.png)');
    var canedit = (notex.is_owner ? this.editors == 'all' : notex.can_edit) ?  'check' : 'cross';
    $('#canedit').css('background', 'url(' + images + canedit + '.png)');
  },
  select: function(photo_or_paper, selected) {
    var dialog = $('#dialogs #' + photo_or_paper + '-dialog');
    if (selected) {
      dialog.fadeOut();
      this[photo_or_paper] = selected;
      this.display();
      notex.cookie.set(photo_or_paper, selected);
      this.has_changed[photo_or_paper] = true;
      notex.is_updating = true;
    } else {
      dialog.fadeIn();
    }
  },
  toggle: function(readers_or_editors) {
    if (!notex.is_owner) return notex.fx.flash("nochange-dialog");
    this[readers_or_editors] = (this[readers_or_editors] == 'all' ? 'me' : 'all');
    if (readers_or_editors == 'readers' && this['readers'] == 'me') this['editors'] = 'me';
    if (readers_or_editors == 'editors' && this['editors'] == 'all') this['readers'] = 'all';
    this.display();
    this.has_changed['readers'] = this.has_changed['editors'] = true;
    notex.cookie.set('readers', this.readers);
    notex.cookie.set('editors', this.editors);
    notex.is_updating = true;
  },
  share: function() {
    var dialog = $('#dialogs #share-dialog');
    dialog.fadeIn().mouseleave(function(){ dialog.fadeOut() });
  },
  rename: function() {
    if (!notex.can_edit) return notex.fx.flash('norename-dialog');
    var re = /(http:\/\/[^/]+\/)([^\?#]*)/i;
    match = re.exec(location.href);
    var from = notex.utils.decode(match[2]);
    var name = prompt('New name?', from.spacify());
    if (typeof name != 'string') return;
    var to = notex.utils.encode(name.scorify());
    if (to != 'null' && to != from) location.href = match[1] + 'note/rename?from=' + from + '&to=' + to;
  },
  wipe: function(with_confirm, then_erase) {
    if (with_confirm && !notex.can_edit) return notex.fx.flash('nowipe-dialog');
    var confirmed = with_confirm ? confirm('Wipe this note clean?') : true;
    if (confirmed) {
      for (id in notex.notes) {
        var note = notex.notes[id];
        note.deleted = true;
        $('#'+id).remove();
      }
    }
    if (then_erase) notex.notes = {};
  },
  version: 0.1
};

notex.notelist = {
  html: '',
  init: function() {
    $('#search').keyup(function() {
      var text = $(this).val();
      notex.search = (text == 'search' ? '' : text);
      notex.paused = -10;
    }).keyup();
  },
  update: function(config) {
    if (!config.notelist) return;
    var html = '';
    var list = config.notelist;
    for (i in list) {
      var note = list[i];
      var show_url = note.url ? note.url.spacify() : 'Home page';
      var words = note.words ? note.words.quotify() + '&hellip;' : '';
      html += '<li><div><a href="/' + note.url + '" title="' + words + '">' + show_url + '</a></div><div class="timestamp">' + this.elapsed(config.now - note.time) + '</div></li>';
    }
    if (this.html != html) {
      $('#notelist #listitems').html(html ? html : 'no matching notes');
      this.html = html;
    }
  },
  ago: function(n, unit) {
    return '' + n + ' ' + unit + (n == 1 ? '' : 's') + ' ago';
  },
  elapsed: function(secs) {
    if (secs < 60) return this.ago(secs, 'second');
    if (secs < 3600) return this.ago(Math.floor(secs / 60), 'minute');
    if (secs < 3600 * 24) return this.ago(Math.floor(secs / 3600), 'hour');
    if (secs < 3600 * 24 * 30) return this.ago(Math.floor(secs / 3600 / 24), 'day');
    return '';
  },
  version: 0.1
};

notex.cookie = {

  // Constants
  Hours: 24*90,
  
  set: function(name, value, hours) {
    hours = hours || this.Hours;
    var cookie = 'notex_' + name + '=' + notex.utils.encode(value) + '; path=/'
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
    return notex.utils.decode(cookie.substring(begin + prefix.length, end));
  },
  version: 0.1
};

notex.fx = {
  cache: {},
  highlight: function(element, x1, y1, x2, y2) {
    if (element) {
      var w = (x2 - x1) + 'px';
      var h = (y2 - y1) + 'px';
      var pos = $('#'+element).position();
      x1 = (x1 + pos.left) + 'px';
      y1 = (y1 + pos.top) + 'px';
      $('#highlight').css({left:x1, top:y1, width:w, height:h}).show();
    } else {
      $('#highlight').hide();
    }
  },
  flash: function(element) {
    var e = $('#'+element);
    if ($.browser.msie) {
      e.show();
      setTimeout(function() { e.hide() }, 3000);
    } else {
      e.fadeIn(500, function() { setTimeout(function() { e.fadeOut(1000) }, 2500) });
    }
  },
  preload: function(images) {
    for (i in images) {
      var img = new Image();
      var src = images[i];
      img.src = '/images/' + src;
      this.cache[src] = img;
    }
  },
  version: 0.1
};

// Finally a bit of monkey patching ;-)

String.prototype.spacify = function() {
  return this.replace(/_/g, ' ');
};

String.prototype.scorify = function() {
  return this.replace(/ /g, '_');
};

String.prototype.quotify = function() {
  return this.replace(/"/g, '&quot;');
};

// End of notex.js
