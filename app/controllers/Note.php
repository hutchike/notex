<?
class Note_controller extends App_controller
{
    public function before()
    {
        parent::before();
        load_models('Note');
    }

    public function load()
    {
        $url = $this->short_url($this->params->url);
        $notes = array();
        $note = new Note(array('url' => $url));
        $this->render->data = $note->load() ? json_decode($note->notes) : NULL;
    }

    public function save()
    {
        $url = $this->short_url($this->params->url);
        $notes = $this->params->notes;
        $note = new Note(array('url' => $url));
        $note->load();
        $note->notes = $notes;
        $note->save();
        $this->render->data = json_decode($notes);
    }

    protected function short_url($url)
    {
        return preg_replace('/^https?:\/\/(\w+\.)?notex.com/i', '', $url);
    }
}

// End of Note.php
