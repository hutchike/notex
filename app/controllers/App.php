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
    }
}

// End of App.php
