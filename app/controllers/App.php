<?
load_helpers('Translate');

class App_controller extends Controller
{
    public function before()
    {
        $this->render->title = 'the web notepad';
        $this->render->debug = '';
        $this->render->layout = 'notepad';

        // Redirect to a unique notepad if not chosen

        $request = $_SERVER['REQUEST_URI'];
        if ($request == '/')
        {
            $md5 = md5($_SERVER['REMOTE_ADDR'] . time());
            $url = '/' . substr($md5, 0, 10);
            header("Location: $url");
            exit;
        }
        else // good, we've chosen a personal notepad
        {
            $this->render->title = ltrim($request, '/');
        }
    }
}

// End of App.php
