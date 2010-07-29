<?
load_helpers('Translate');

class App_controller extends Controller
{
    public function before()
    {
        $uri = $_SERVER['REQUEST_URI'];
        $this->render->title = $uri == '/' ? 'your web notepad' : ltrim($uri, '/');
        $this->render->debug = '';
        $this->render->layout = 'notepad';
        $this->host_ip = array_key($_SERVER, 'HTTP_X_FORWARDED_FOR', $_SERVER['REMOTE_ADDR']);
    }
}

// End of App.php
