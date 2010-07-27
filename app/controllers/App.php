<?
load_helpers('Translate');

class App_controller extends Controller
{
    public function before()
    {
        $this->render->title = 'your web notepad';
        $this->render->flash = '';
        $this->render->layout = 'notepad';
    }
}

// End of App.php
