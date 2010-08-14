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
<? endif ?>
<div id="penbox">
  <div id="selectcolor"></div>
  <div id="selectfont"></div>
  <img src="/images/penbox.png" id="styleimg" width="200" height="82" usemap="#penmap" alt="penbox" />
  <map name="penmap">
    <area shape="rect" coords="4,29,27,52" onclick="notex.penbox.set_color('black', 4, 28)" alt="black" />
    <area shape="rect" coords="28,29,51,52" onclick="notex.penbox.set_color('#333', 28, 28)" alt="slate gray" />
    <area shape="rect" coords="52,29,75,52" onclick="notex.penbox.set_color('#666', 52, 28)" alt="steel gray" />
    <area shape="rect" coords="76,29,99,52" onclick="notex.penbox.set_color('#80007e', 76, 28)" alt="deep purple" />
    <area shape="rect" coords="100,29,123,52" onclick="notex.penbox.set_color('#008001', 100, 28)" alt="forest green" />
    <area shape="rect" coords="123,29,146,52" onclick="notex.penbox.set_color('#0003ff', 123, 28)" alt="aqua blue" />
    <area shape="rect" coords="147,29,170,52" onclick="notex.penbox.set_color('#ff00fc', 147, 28)" alt="pink kiss" />
    <area shape="rect" coords="172,29,195,52" onclick="notex.penbox.set_color('red', 172, 28)" alt="red alert" />

    <area shape="rect" coords="4,56,51,78" onclick="notex.penbox.set_font('sans', 4, 55)" alt="sans serif" />
    <area shape="rect" coords="53,56,100,78" onclick="notex.penbox.set_font('serif', 53, 55)" alt="serif" />
    <area shape="rect" coords="101,56,147,78" onclick="notex.penbox.set_font('mono', 101, 55)" alt="mono" />
    <area shape="rect" coords="149,56,196,78" onclick="notex.penbox.set_font('script', 149, 55)" alt="script" />
  </map>
</div>
<div id="notebox">
  <div id="photo" onclick="alert('photo')"><img src="/images/nothing.gif" alt="photo" /></div>
  <div id="paper" onclick="alert('paper')"><img src="/images/nothing.gif" alt="paper" /></div>
  <div id="canread" onclick="alert('canread')"></div>
  <div id="canedit" onclick="alert('canedit')"></div>
  <img src="/images/notebox.png" id="styleimg" width="200" height="138" usemap="#notemap" alt="notebox" />
  <map name="notemap">
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
