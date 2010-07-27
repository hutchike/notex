<?
header('Content-type: text/javascript');

// Get the callback and data, including any view content

if (is_null($callback)) $callback = '';
if (is_null($data)) $data = array();
$data['content'] = $content;

// This is cross-domain loveliness using JSONP callbacks
?>
<?= $callback ?>(<?= json_encode($data) ?>)
