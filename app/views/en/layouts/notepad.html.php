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

// Users have extra features in the "notebox2"

$notebox = ($is_owner ? 'notebox2' : 'notebox1');
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
<div id="twitter">
<? if (is_null($screen_name)): ?>
  <a href="/twitter/login"><img src="/images/twitter/lighter.png" alt="twitter login"/></a>
<? else: ?>
  <div id="screen_name"><a href="http://<?= $screen_name ?>.noted.cc/" title="Go to my notes"><?= $screen_name ?></a></div>
<? endif ?>
</div>
<div id="penbox">
  <div id="selectcolor"></div>
  <div id="selectfont"></div>
  <img src="/images/penbox.png" id="penbox-img" width="200" height="82" usemap="#penmap" alt="penbox" />
  <map id="penmap" name="penmap">
    <?= map_area("penbox", 4, 29, 27, 52, "penbox.set_color('black', 4, 28)", "black") ?>
    <?= map_area("penbox", 28, 29, 51, 52, "penbox.set_color('#333', 28, 28)", "slate gray") ?>
    <?= map_area("penbox", 52, 29, 75, 52, "penbox.set_color('#666', 52, 28)", "steel gray") ?>
    <?= map_area("penbox", 76, 29, 99, 52, "penbox.set_color('#80007e', 76, 28)", "purple") ?>
    <?= map_area("penbox", 100, 29, 123, 52, "penbox.set_color('#008001', 100, 28)", "forest green") ?>
    <?= map_area("penbox", 124, 29, 147, 52, "penbox.set_color('#0003ff', 124, 28)", "aqua blue") ?>
    <?= map_area("penbox", 148, 29, 171, 52, "penbox.set_color('#ff00fc', 148, 28)", "pink kiss") ?>
    <?= map_area("penbox", 173, 29, 196, 52, "penbox.set_color('red', 173, 28)", "red alert") ?>

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
  <img src="/images/<?= $notebox ?>.png" id="notebox-img" width="200" height="139" usemap="#notemap" alt="notebox" />
  <map id="notemap" name="notemap">
    <?= map_area("notebox", 6, 112, 61, 132, "notebox.share()", "share") ?>
    <?= map_area("notebox", 66, 112, 121, 132, "notebox.rename()", "rename") ?>
    <?= map_area("notebox", 126, 112, 194, 132, "notebox.wipe(true)", "wipe") ?>
  </map>
</div>
<div id="notelist">
  <div id="listhead"></div>
  <div id="listbody">
    <a href="/new"><img id="newnote" src="/images/newnote.png" width="57" height="22" alt="new note" onmouseover="notex.fx.highlight('notelist',5,30,61,50)" onmouseout="notex.fx.highlight()" /></a>
    <form><input id="search" type="text" value="search" onfocus="this.value=''" /></form>
    <ul id="listitems">
    </ul>
  </div>
  <div id="listfoot"></div>
</div>
<div id="dialogs">
  <div id="nochange-dialog"></div>
  <div id="norename-dialog"></div>
  <div id="nowipe-dialog"></div>
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
