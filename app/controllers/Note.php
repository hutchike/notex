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

        $url = parse_url($this->params->url);
        $notes = array();
        $note = new Note(array('url' => $url['path']));
        $this->render->data = $note->load() ? json_decode(str_replace('<', '&lt;', $note->notes)) : NULL;
    }

    public function save()
    {
        // Save the note

        $url = parse_url($this->params->url);
        $path = $url['path'];
        $notes = $this->params->notes;
        $note = new Note(array('url' => $path));
        $note->load();
        $old_notes = $note->notes;
        $note->notes = $notes;
        $note->save();
        $this->render->data = $this->diff($old_notes, $notes);

        // Log the info

        Log::info($_SERVER['REMOTE_ADDR'] . " saved a note at $path");
    }

    public function diff($old_notes, $new_notes)
    {
        $old = json_decode($old_notes, TRUE); if (!$old) $old = array();
        $new = json_decode($new_notes, TRUE); if (!$new) $new = array();
        $diff = NULL;
        foreach ($old as $id => $old_note)
        {
            $new_note = array_key($new, $id);
            if ($new_note &&
                array_key($old_note, 'deleted') != TRUE &&
                array_key($old_note, 'text') == array_key($new_note, 'text')) continue;

            $diff[$id] = $old_note;
        }
        return $diff;
    }
}

// End of Note.php
