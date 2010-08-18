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
        $note = new Note(array('url' => $url['path']));
        $mode = $this->session->access_token ? 'user' : 'open';
        $config = array('mode' => $mode,
                        'photo' => '',
                        'paper' => '',
                        'readers' => '',
                        'editors' => '',
                        'notes' => NULL);
        if ($note->load())
        {
            $config['photo'] = $note->photo;
            $config['paper'] = $note->paper;
            $config['readers'] = $note->readers;
            $config['editors'] = $note->editors;
            $config['notes'] = $note->filter();
        }
        $this->render->data = $config;
    }

    public function save()
    {
        // Get the old note config

        $url = parse_url($this->params->url);
        $path = $url['path'];
        $note = new Note(array('url' => $path));
        $note->load();

        // Get the new note config

        $config = json_decode($this->params->config);

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

        // Edit the note (if allowed)

        $old_notes = $note->notes;
        if ($config->notes) $note->notes = json_encode($config->notes);
        if ($config->photo) $note->photo = $config->photo;
        if ($config->paper) $note->paper = $config->paper;
        if ($config->readers) $note->readers = $config->readers;
        if ($config->editors) $note->editors = $config->editors;
        if ($can_edit) $note->save();
        $this->render->data = array(
            'diff' => $this->diff($old_notes, $note->notes),
            'paper' => $note->paper,
            'photo' => $note->photo,
            'readers' => $note->readers,
            'editors' => $note->editors,
        );

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
