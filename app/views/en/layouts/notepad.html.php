<?
function map_area($element, $x1, $y1, $x2, $y2, $action, $alt = '')
{
    return "<area shape=\"rect\" coords=\"$x1,$y1,$x2,$y2\" onclick=\"notex.$action\" alt=\"$alt\" onmouseover=\"notex.fx.highlight('$element',$x1,$y1,$x2,$y2)\" onmouseout=\"notex.fx.highlight()\" />\n";
}

function dialog_img($photo_or_paper, $id)
{
    $name = $photo_or_paper . $id;
    return "<img src=\"/images/thumbs/$name.jpg\" alt=\"$name\" onclick=\"notex.notebox.select('$photo_or_paper', '$name')\" />\n";
}
?>
<div id="logo"><a href="/"><img id="logo-img" src="/images/nothing.gif" alt="logo"/></a></div>
<div id="copy"><?= $copy ?></div>
<div id="notepad">
  <div id="page">
    <div id="content"><?= $content ?></div>
    <form id="pen" onsubmit="notex.write({newline: true}); return false" action="get">
      <div><input type="text" id="edit" /></div>
    </form>
  </div>
</div>
<div id="debug"><?= $debug ?></div>
<div id="highlight"></div>
<? if (is_null($access_token)): ?>
<div id="twitter">
  <a href="/twitter/login"><img src="/images/twitter/lighter.png" alt="twitter login"/></a>
</div>
<? endif ?>
<div id="penbox">
  <div id="selectcolor"></div>
  <div id="selectfont"></div>
  <img src="/images/penbox.png" id="penbox-img" width="200" height="82" usemap="#penmap" alt="penbox" />
  <map id="penmap" name="penmap">
    <area shape="rect" coords="4,29,27,52" onclick="notex.penbox.set_color('black', 4, 28)" alt="black" />
    <area shape="rect" coords="28,29,51,52" onclick="notex.penbox.set_color('#333', 28, 28)" alt="slate gray" />
    <area shape="rect" coords="52,29,75,52" onclick="notex.penbox.set_color('#666', 52, 28)" alt="steel gray" />
    <area shape="rect" coords="76,29,99,52" onclick="notex.penbox.set_color('#80007e', 76, 28)" alt="deep purple" />
    <area shape="rect" coords="100,29,123,52" onclick="notex.penbox.set_color('#008001', 100, 28)" alt="forest green" />
    <area shape="rect" coords="123,29,146,52" onclick="notex.penbox.set_color('#0003ff', 123, 28)" alt="aqua blue" />
    <area shape="rect" coords="147,29,170,52" onclick="notex.penbox.set_color('#ff00fc', 147, 28)" alt="pink kiss" />
    <area shape="rect" coords="172,29,195,52" onclick="notex.penbox.set_color('red', 172, 28)" alt="red alert" />

    <?= map_area("penbox", 6, 57, 50, 75, "penbox.set_font('sans', 4, 55)", "sans") ?>
    <?= map_area("penbox", 54, 57, 98, 75, "penbox.set_font('serif', 53, 55)", "serif") ?>
    <?= map_area("penbox", 102, 57, 146, 75, "penbox.set_font('mono', 101, 55)", "mono") ?>
    <?= map_area("penbox", 150, 57, 194, 75, "penbox.set_font('script', 149, 55)", "script") ?>
  </map>
</div>
<div id="notebox">
  <div id="photo"><img src="/images/nothing.gif" alt="photo" onclick="notex.notebox.select('photo')" /></div>
  <div id="paper" onclick="notex.notebox.select('paper')"></div>
  <div id="canread" onclick="notex.notebox.toggle('readers')"></div>
  <div id="canedit" onclick="notex.notebox.toggle('editors')"></div>
  <img src="/images/notebox.png" id="notebox-img" width="200" height="139" usemap="#notemap" alt="notebox" />
  <map id="notemap" name="notemap">
    <?= map_area("notebox", 6, 112, 61, 132, "notebox.share()", "share") ?>
    <?= map_area("notebox", 66, 112, 121, 132, "notebox.rename()", "rename") ?>
    <?= map_area("notebox", 126, 112, 194, 132, "notebox.wipe()", "wipe") ?>
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
<div id="dialogs">
  <div id="photo-dialog">
    <div>
      <?= dialog_img('photo', 1) ?>
      <?= dialog_img('photo', 2) ?>
      <?= dialog_img('photo', 3) ?>
    </div>
    <div>
      <?= dialog_img('photo', 4) ?>
      <?= dialog_img('photo', 5) ?>
      <?= dialog_img('photo', 6) ?>
    </div>
    <div>
      <?= dialog_img('photo', 7) ?>
      <?= dialog_img('photo', 8) ?>
      <?= dialog_img('photo', 9) ?>
    </div>
  </div>
  <div id="paper-dialog">
    <div>
      <?= dialog_img('paper', 1) ?>
      <?= dialog_img('paper', 2) ?>
    </div>
    <div>
      <?= dialog_img('paper', 3) ?>
      <?= dialog_img('paper', 4) ?>
    </div>
  </div>
</div>
<div id="legal">Copyright &copy;<?= strftime('%Y') ?> <a href="mailto:kevin.hutchinson@guanoo.com">Kevin Hutchinson</a>. All Rights Reserved.</div>
