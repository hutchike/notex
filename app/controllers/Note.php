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
        // Load the note

        $url = $this->short_url($this->params->url);
        $notes = array();
        $note = new Note(array('url' => $url));
        $this->render->data = $note->load() ? json_decode(str_replace('<', '&lt;', $note->notes)) : NULL;
    }

    public function save()
    {
        // Save the note

        $url = $this->short_url($this->params->url);
        $notes = $this->params->notes;
        $note = new Note(array('url' => $url));
        $note->load();
        $note->notes = $notes;
        $note->save();
        $this->render->data = json_decode($notes);

        // Log the info

        $list = array();
        foreach ($this->render->data as $id => $note) $list[] = $note->text;
        $text = join($list, ', ');
        Log::info($_SERVER['REMOTE_ADDR'] . " saved \"$text\" at $url");
    }

    protected function short_url($url)
    {
        return preg_replace('/^https?:\/\/(\w+\.)?notex.com/i', '', $url);
    }
}

// End of Note.php
