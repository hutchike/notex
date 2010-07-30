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
        if ($this->app->get_content_type() != 'html')
        {
            $path = $_SERVER['PATH_INFO'];
            $note_controller = $this->app->new_controller('Note');
            $this->render->data = $note_controller->data_for($path);
        }
    }
}

// End of App.php
