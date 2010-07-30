<?
header('Content-type: text/javascript');

// Get the callback and data, including any view content

if (is_null($callback)) $callback = '';
$data = is_null($data) ? '{}' : json_encode($data);

// This is cross-domain loveliness using JSONP callbacks
?>
<?= $callback ?>(<?= $data ?>)
