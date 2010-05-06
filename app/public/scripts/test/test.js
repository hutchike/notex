// Copyright (c) 2009 Guanoo, Inc.
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU Lesser General Public License
// as published by the Free Software Foundation; either version 3
// of the License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU Lesser General Public License for more details.

var YAWF = {
  version: '0.4',
  domain: '',    // set by the "setup()" function in this file
  prefix: '',    // set by en/views/test/browser.php (VIEW_URL_PREFIX constant)
  console: null, // set by en/views/test/console.php ("div" element)
  browser: null, // set by en/views/test/browser.php ("form" element)
  logger: null,  // set by en/views/test/logger.php  ("form" element)
  view: null,    // set by the "setupView()" function in this file
  logLines: [],
  testRunners: {},
  testActions: [],
  callbackInt: null,
  interval: 250, // msecs
  stopwatch: 0,
  duration: 0,

  // Attach an event to an object, e.g. the "load" event to a "window" object

  attachEvent: function(object, name, callback) {
    if (!object || !name || !callback) return;
    if (object.addEventListener) // Mozilla
      object.addEventListener(name, callback, false);
    else if (object.attachEvent) // IE
      object.attachEvent('on'+name, callback);
    else // old
      eval('var event = object.on'+name+'; object.on'+name+' = event ? function() { event.call(); callback.call() } : callback;');
  },

  // Setup the YAWF test system by assigning the domain name that we're using

  setup: function() {
    parts = new String(location.href).split('/');
    this.domain = parts[2];
  },

  // The "setupView()" function is called by a ".test" view after it's loaded

  setupView: function(url) {
    this.view = top.frames.view;
    this.browser.url.value = url;
    this.setupLinks();
    this.setupForms();
    this.setupTestRunners();
    this.duration = this.stopwatch;
    this.stopwatch = 0;
    this.callbackInt = setInterval(function() { YAWF.runTestActions(); }, this.interval);
  },

  // Modify all the links on the page so that they'll load more ".test" pages

  setupLinks: function(doc) {
    if (!doc) doc = this.view.document;
    for (var i = 0; i < doc.links.length; i++) {
      var link = doc.links[i];
      var href = new String(link.href);
      if (href.match(this.domain) == null) continue;
      var parts = href.split('?');
      if (/\.\w+$/.exec(parts[0]) == null)
        link.href = parts[0] + '.test' + (parts[1] ? '?' + parts[1] : '');
    }
  },

  // Modify all the forms on the page so that they'll load more ".test" pages

  setupForms: function(doc) {
    if (!doc) doc = this.view.document;
    for (var i = 0; i < doc.forms.length; i++) {
      var form = doc.forms[i];
      var action = new String(form.action);
      if (/\.\w+$/.exec(action) == null)
        form.action = action + '.test';
    }
  },

  // Run all the test runners that match the currently displayed web page URL

  setupTestRunners: function() {
    this.clearTestActions();
    for (url in this.testRunners) {
      var runner = this.testRunners[url];
      var regexp = new RegExp(url);
      if (regexp.exec(this.view.location.href)) {
        this.addTestViewHeader(url);
        runner.call(this);
      }
    }
  },

  // Remove any test actions and reset the stopwatch ready for the new runner

  clearTestActions: function() {
    this.testActions = [];
    this.stopwatch = 0;
  },

  // Look for any test actions that are scheduled to be run right now in time

  runTestActions: function() {
    for (i = 0; i < this.testActions.length; i++) {
      var action = this.testActions[i];
      if (action.func && action.msecs <= this.stopwatch) {
        if (!action.func.call(this)) action.func = null;
      }
    }
    this.stopwatch += this.interval;
    if (this.stopwatch > this.duration) this.finishActions();
  },

  // When the test actions are finished write all the results to the log file

  finishActions: function() {
    clearInterval(this.callbackInt);
    var lines = '';
    var line;
    while (line = this.logLines.shift()) lines += line + "\n";
    if (lines) {
      this.logger.lines.value = lines;
      this.logger.submit(); // poor man's AJAX
    }
  },

  // Add a console header message and a log line to say we are testing a view

  addTestViewHeader: function(url) {
    this.console.innerHTML += '<div class="test_view_header">Testing view ' + url + '</div>';
    this.logLines.push('Testing view ' + url);
  },

  // Write to the console and the log to say this test condition has been met

  addTestWhenHeader: function(text) {
    this.console.innerHTML += '<div class="test_when_header">...when ' + text + '</div>';
    this.logLines.push('...when ' + text);
  },

  // Write this test result to the console and log using an appropriate style

  addTestResult: function(test, passed) {
    var html = '';
    html += '<div class="test_case_' + (passed ? 'passed' : 'failed') + '">';
    html += 'Should ' + test + (passed ? ': passed' : ': failed');
    html += '</div>';
    this.console.innerHTML += html;
    this.logLines.push((passed ? 'passed' : 'failed') + ': Should ' + test);
  },

  // Add a test result that passed :-)

  passed: function(test) {
    this.addTestResult(test, true);
  },

  // Add a test result that failed :-/

  failed: function(test) {
    this.addTestResult(test, false);
  },

// ------------------------------------------------------------------------
// Functions below this line are available to the user for testing purposes

  cookie: function(name, value) {
    if (value) return this.set_cookie(name, value);
    var cookie = document.cookie;
    var prefix = 'YAWF_' + name + '=';
    var begin = cookie.indexOf('; ' + prefix);
    if (begin == -1) {
      begin = cookie.indexOf(prefix);
      if (begin != 0) return '';
    }
    else begin += 2;
    var end = document.cookie.indexOf(';', begin);
    if (end == -1) end = cookie.length;
    return unescape(cookie.substring(begin + prefix.length, end));
  },

  set_cookie: function(name, value) {
    var cookie = 'YAWF_' + name + '=' + escape(value) + '; path=/';
    document.cookie = cookie;
    return value;
  },

  clear_console: function() {
    this.console.innerHTML = '';
  },

  when_open: function(url, func) {
    this.testRunners[url] = func;
  },

  open: function(url) {
    if (!/\.\w+$/.exec(url)) url += '.test'; // TODO: what about query strings?
    if (!/^http/.exec(url)) {
      url = this.prefix + url;
      if (!/^\//.exec(url)) url = '/' + url;
      url = 'http://' + this.domain + url;
    }
    top.frames.view.location.href = url;
  },

  after: function(msecs, func) {
    this.stopwatch += msecs;
    this.testActions.push({msecs: this.stopwatch, func: func});
  },

  then: function(func) {
    this.after(this.interval, func);
  },

  wait: function(msecs) {
    this.after(msecs, function() {});
  },

  wait_for: function(func) {
    this.then(function() {
      if (this.interval) {
        this.original = this.interval;
        this.interval = 0; // stop the stopwatch
      }
      var ready = func.call(this);
      if (ready) this.interval = this.original;
      return (!ready); // or we may be deleted!
    });
  },

  when_found: function(text, func) {
    var regexp = new RegExp(text);
    if (regexp.exec(this.view.document.body.innerHTML)) {
      this.addTestWhenHeader('found "' + text + '"');
      func.call(this);
    }
  },

  when_not_found: function(text, func) {
    var regexp = new RegExp(text);
    if (!regexp.exec(this.view.document.body.innerHTML)) {
      this.addTestWhenHeader('not found "' + text + '"');
      func.call(this);
    }
  },

  input: function(form, field, value) {
    this.then(function() {
      this.view.document.forms[form][field].value = value;
      this.should('have a form field called "' + field + '"', this.view.document.forms[form][field]);
      this.should('hold the value "' + value + '"', this.view.document.forms[form][field].value == value);
    });
  },

  click: function(form, field) {
    this.then(function() {
      this.should('click the "' + field + '" button', this.view.document.forms[form][field].click);
      this.view.document.forms[form][field].click();
    });
  },

  click_link: function(name) {
    this.then(function() {
      link = null;
      var links = this.view.document.links;
      for (var i = 0; i < links.length; i++) {
        if (name == this.element_text(links[i])) link = links[i];
      }
      this.should('click the "' + name + '" link', link && link.href);
      return this.open(link.href);
    });
  },

  element_text: function(el) {
    return new String(el.innerText ? el.innerText : (el.textContent ? el.textContent : ''));
  },

  submit: function(form) {
    this.then(function() {
      this.should('Submit a form called "' + form + '"', this.view.document.forms[form]);
      this.view.document.forms[form].submit();
    });
  },

  should: function(test, passed) {
    this.then(function() {
      if (passed) this.passed(test);
      else this.failed(test);
    });
  },

  should_not: function(test, failed) {
    this.then(function() {
      test = 'not ' + test;
      if (failed) this.failed(test);
      else this.passed(test);
    });
  },

  should_find: function(text) {
    var test = 'find "' + text + '"';
    this.should(test, this.find(new RegExp(text)));
  },

  should_not_find: function(text) {
    var test = 'find "' + text + '"';
    this.should_not(test, this.find(new RegExp(text)));
  },

  find: function(regexp) {
    return regexp.exec(this.view.document.body.innerHTML);
  },

  find_id: function(id) {
    return this.view.document.getElementById(id);
  }
};

YAWF.setup();

// End of test.js
