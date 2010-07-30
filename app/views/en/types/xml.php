<?
header('Content-type: text/xml');

if (is_null($data)) $data = '';
?>
<note><?= $data ?></note>
