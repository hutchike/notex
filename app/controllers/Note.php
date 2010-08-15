<?
class Note_controller extends App_controller
{
    public function before()
    {
        parent::before();
        load_models('Note');
        Note::set_database_for($this->username);
    }

    public function load()
    {
        // Load the note

        $url = parse_url($this->params->url);
        $notes = array();
        $note = new Note(array('url' => $url['path']));
        $data = array('photo' => '',
                      'paper' => '',
                      'readers' => '',
                      'editors' => '',
                      'notes' => array());
        if ($note->load())
        {
            $data['notes'] = $note->filter();
        }
        $this->render->data = $data;
    }

    public function save()
    {
        // Get the old note

        $url = parse_url($this->params->url);
        $path = $url['path'];
        $notes = $this->params->notes;
        $note = new Note(array('url' => $path));
        $note->load();

        // Can we edit it?

        $can_edit = TRUE;
        $secret = $this->params->secret;
        $hidden = md5($path . $secret);
        if ($note->get_id())
        {
            if ($note->secret && $note->secret != $hidden) $can_edit = FALSE;
        }
        else // it's a new note and might be secret?
        {
            if ($secret) $note->secret = $hidden;
        }

        // Edit the note if allowed

        $old_notes = $note->notes;
        $note->notes = $notes;
        if ($can_edit) $note->save();
        $this->render->data = $this->diff($old_notes, $notes);

        // Log the info

        $action = $can_edit ? 'saved' : 'viewed';
        Log::info($this->host_ip . " $action a note at $path");
    }

    public function diff($old_notes, $new_notes)
    {
        $old = json_decode($old_notes, TRUE); if (!$old) $old = array();
        $new = json_decode($new_notes, TRUE); if (!$new) $new = array();
        $diff = NULL;
        foreach ($old as $id => $old_note)
        {
            $new_note = array_key($new, $id, array());
            $new_text = array_key($new_note, 'text');
            $old_text = array_key($old_note, 'text');
            $is_deleted = array_key($old_note, 'deleted');
            if ($new_note && !$is_deleted && $old_text == $new_text) continue;
            if (!$new_note && $is_deleted) continue;
            $diff[$id] = $old_note;
        }
        return $diff;
    }

    public function data_for($url)
    {
        load_models('Note');
        $url = preg_replace('/\.\w+$/', '', $url);
        $note = new Note(array('url' => $url));
        return $note->load() ? $note->to_text() : NULL;
    }
}

// End of Note.php
