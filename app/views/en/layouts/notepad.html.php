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
<div id="stylebox">
  <div id="selectcolor"></div>
  <div id="selectfont"></div>
  <img src="/images/stylebox.png" id="styleimg" width="200" height="90" usemap="#stylemap" alt="stylebox" />
  <map name="stylemap">
    <area shape="rect" coords="4,27,28,51" onclick="alert('clicked')" alt="black" />
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
