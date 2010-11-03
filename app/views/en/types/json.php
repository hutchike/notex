<?
header('Content-type: text/javascript');

$data = is_null($data) ? '{}' : json_encode($data);
?>
(<?= $data ?>)
