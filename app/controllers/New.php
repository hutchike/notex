<?
class New_controller extends App_controller
{
    public function before()
    {
        parent::before();
    }

    public function index()
    {
        // Redirect to a new unique notepad

        $md5 = md5($this->host_ip . time());
        $url = '/' . substr($md5, 0, 10);
        header("Location: $url");
        exit;
    }
}

// End of New.php
