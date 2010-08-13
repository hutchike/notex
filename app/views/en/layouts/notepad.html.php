<div id="logo"></div>
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
<div id="legal">Copyright &copy;<?= strftime('%Y') ?> <a href="mailto:kevin.hutchinson@guanoo.com">Kevin Hutchinson</a>. All Rights Reserved.</div>
<? endif ?>
