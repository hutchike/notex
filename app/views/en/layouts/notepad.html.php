<a href="/"><div id="logo"></div></a>
<div id="copy"><?= $copy ?></div>
<div id="notepad">
  <div id="page">
    <div id="content"><?= $content ?></div>
    <form id="pen" onsubmit="notex.write({newline: true}); return false">
      <input type="text" id="edit" />
    </form>
  </div>
</div>
<div id="debug"><?= $debug ?></div>
<? if (is_null($access_token)): ?>
<div id="twitter">
  <a href="/twitter/login"><img src="/images/twitter/lighter.png" alt="twitter login"/></a>
</div>
<div id="penbox">
  <div id="selectcolor"></div>
  <div id="selectfont"></div>
  <img src="/images/penbox.png" id="styleimg" width="200" height="90" usemap="#stylemap" alt="penbox" />
  <map name="stylemap">
    <area shape="rect" coords="4,28,27,52" onclick="notex.penbox.set_color('black', 4, 28)" alt="black" />
    <area shape="rect" coords="28,28,51,52" onclick="notex.penbox.set_color('#333', 28, 28)" alt="slate gray" />
    <area shape="rect" coords="52,28,75,52" onclick="notex.penbox.set_color('#666', 52, 28)" alt="steel gray" />
    <area shape="rect" coords="76,28,99,52" onclick="notex.penbox.set_color('#80007e', 76, 28)" alt="deep purple" />
    <area shape="rect" coords="100,28,123,52" onclick="notex.penbox.set_color('#008001', 100, 28)" alt="forest green" />
    <area shape="rect" coords="123,28,146,52" onclick="notex.penbox.set_color('#0003ff', 123, 28)" alt="aqua blue" />
    <area shape="rect" coords="147,28,170,52" onclick="notex.penbox.set_color('#ff00fc', 147, 28)" alt="pink kiss" />
    <area shape="rect" coords="171,28,194,52" onclick="notex.penbox.set_color('red', 171, 28)" alt="red alert" />

    <area shape="rect" coords="4,56,66,85" onclick="notex.penbox.set_font('sans', 4, 56)" alt="sans serif" />
    <area shape="rect" coords="68,56,130,85" onclick="notex.penbox.set_font('serif', 68, 56)" alt="serif" />
    <area shape="rect" coords="133,56,195,85" onclick="notex.penbox.set_font('mono', 133, 56)" alt="mono" />
  </map>
</div>
<div id="notelist">
  <div id="listhead"></div>
  <div id="listbody">
    <ul id="listitems">
      <li>first note</li>
      <li>second note</li>
    </ul>
  </div>
  <div id="listfoot"></div>
</div>
<div id="legal">Copyright &copy;<?= strftime('%Y') ?> <a href="mailto:kevin.hutchinson@guanoo.com">Kevin Hutchinson</a>. All Rights Reserved.</div>
<? endif ?>
