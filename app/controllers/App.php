<?
load_helpers('Translate');

class App_controller extends Controller
{
    public function before()
    {
        $this->render->title = 'the web notepad';
        $this->render->debug = '';
        $this->render->layout = 'notepad';

        if ($_SERVER['REQUEST_URI'] == '/')
        {
            $md5 = md5($_SERVER['REMOTE_ADDR'] . time());
            $url = '/' . substr($md5, 0, 10);
            header("Location: $url");
            exit;
        }
    }
}

// End of App.php
