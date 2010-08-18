<?
load_helpers('Twitter', 'Translate');

class App_controller extends Controller
{
    public function before()
    {
        // Setup extra configuration, e.g. Twitter

        $config = Config::load('notex');
        Config::define_constants($config['twitter']);

        // Setup global variables and rendering data

        $uri = $_SERVER['REQUEST_URI'];
        $this->render->title = $uri == '/' ? 'your web notepad' : ltrim($uri, '/');
        $this->render->username = $this->username = $this->username_from_host();
        $this->render->screen_name = Twitter::screen_name($this->session->access_token);
        $this->render->copy = '';
        $this->render->debug = '';
        $this->render->layout = 'notepad';
        $this->host_ip = array_key($_SERVER, 'HTTP_X_FORWARDED_FOR', $_SERVER['REMOTE_ADDR']);

        // Handle alternative content types, e.g. XML and JSON

        $type = $this->app->get_content_type();
        if ($type != 'html') $this->respond_with_data_as($type);
    }

    public function respond_with_data_as($type)
    {
        $this->render->callback = $this->params->callback;
        $this->render->data = NULL;
        $path = preg_replace('/[#\?].*$/', '', $_SERVER['REQUEST_URI']);
        $note_controller = $this->app->new_controller('Note');
        $data = $note_controller->data_for($path);
        if ($type == 'txt') $data = html_entity_decode($data);
        $this->render->data = $data;
    }

    public function username_from_host()
    {
        $parts = explode('.', $_SERVER['HTTP_HOST']);
        array_pop($parts);  // remove "cc"
        array_pop($parts);  // remove "noted"
        return join('.', $parts);
    }
}

// End of App.php
