<?
load_helpers('Translate');

class App_controller extends Controller
{
    public function before()
    {
        // Setup global variables and rendering data

        $uri = $_SERVER['REQUEST_URI'];
        $this->render->title = $uri == '/' ? 'your web notepad' : ltrim($uri, '/');
        $this->render->debug = '';
        $this->render->layout = 'notepad';
        $this->host_ip = array_key($_SERVER, 'HTTP_X_FORWARDED_FOR', $_SERVER['REMOTE_ADDR']);

        // Handle alternative content types, e.g. XML and JSON

        $this->render->callback = $this->params->callback;
        $this->render->data = NULL;
        $type = $this->app->get_content_type();
        if ($type != 'html')
        {
            $path = array_key($_SERVER, 'PATH_INFO',
                              array_key($_SERVER, 'DOCUMENT_URI', 'unknown'));
            $note_controller = $this->app->new_controller('Note');
            $data = $note_controller->data_for($path);
            if ($type == 'txt') $data = html_entity_decode($data);
            $this->render->data = $data;
        }
    }
}

// End of App.php
