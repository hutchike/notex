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
        $config = array('now' => time(),
                        'notes' => NULL,
                        'photo' => '',
                        'paper' => '',
                        'readers' => '',
                        'editors' => '',
                        'can_read' => TRUE,
                        'can_edit' => TRUE,
                        'is_owner' => $this->is_owner,
                        'notelist' => $this->recent());
        if ($note->load())
        {
            $can_read = $this->can_read($note);
            $config['photo'] = $note->photo;
            $config['paper'] = $can_read ? $note->paper : 'secret';
            $config['readers'] = $note->readers;
            $config['editors'] = $note->editors;
            $config['notes'] = $can_read ? $note->filter() : NULL;
            $config['can_read'] = $this->can_read($note);
            $config['can_edit'] = $this->can_edit($note);
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

        // Can we read or edit it?

        $can_read = $this->can_read($note);
        $can_edit = $can_read ? $this->can_edit($note) : FALSE;

        // Edit the note (if allowed)

        $old_notes = $note->notes;
        if ($config->notes)
        {
            $note->notes = json_encode($config->notes);
            $note->words = Note::words($config->notes);
        }
        if ($config->photo) $note->photo = $config->photo;
        if ($config->paper) $note->paper = $config->paper;
        if ($config->readers) $note->readers = $config->readers;
        if ($config->editors) $note->editors = $config->editors;
        if ($can_edit) $note->save();
        $this->render->data = array(
            'now' => time(),
            'diff' => $can_read ? $this->diff($old_notes, $note->notes) : NULL,
            'paper' => $can_read ? $note->paper : 'secret',
            'photo' => $note->photo,
            'readers' => $note->readers,
            'editors' => $note->editors,
            'is_owner' => $this->is_owner,
            'can_read' => $can_read,
            'can_edit' => $can_edit,
            'notelist' => $this->recent(),
        );

        // Log the info

        $action = $can_edit ? 'saved' : 'viewed';
        Log::info($this->host_ip . " $action a note at $path");
    }

    public function recent()
    {
        $search = $this->params->search;

        $list = array();
        $note = new Note();
        if ($search) $note->words = "%$search%";
        $notes = $note->set_limit(RECENT_NOTES_LIST_LENGTH)->set_order('updated_at desc')->find_all();
        foreach ($notes as $note)
        {
            if ($note->readers == 'me' && !$this->is_owner) continue;
            $words = $note->words ? substr($note->words, 0, NOTE_WORDS_SUMMARY_LENGTH) : '';
            $list[] = new Object(array('url' => ltrim($note->url, '/'),
                                       'time' => strtotime($note->updated_at),
                                       'words' => $words));
        }
        $this->render->data = $list;
        return $list;
    }

    public function rename()
    {
        $from = $this->params->from;
        $to = $this->params->to;
        if ($this->username && !$this->is_owner) return $this->redirect($from);

        $orig = new Note(array('url' => "/$from"));
        if ($orig->load())
        {
            // Check that the destination is missing or empty of words

            $dest = new Note(array('url' => "/$to"));
            if ($dest->load())
            {
                if (strlen($dest->words)) return $this->redirect($from);
                $dest->delete();
            }

            $orig->url = "/$to";
            $orig->status = STATUS_RENAMED;
            if ($orig->save()) return $this->redirect($to);
        }
    }

    public function can_read($note)
    {
        if ($this->is_owner || !$note->readers) return TRUE;

        // Check the note readers

        $readers = preg_replace('/\bme\b/', $this->username, $note->readers);
        if ($readers != 'all' && strpos($readers, $this->screen_name) === FALSE)
        {
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }

    public function can_edit($note)
    {
        if ($this->is_owner) return TRUE;
        if (!$note->editors && !$note->secret && !$this->username) return TRUE;

        // Check the note editors

        $editors = preg_replace('/\bme\b/', $this->username, $note->editors);
        if ($editors != 'all' && strpos($editors, $this->screen_name) === FALSE)
        {
            return FALSE;
        }

        // Apply any secret password

        $can_edit = TRUE;
        $secret = $this->params->secret;
        $hidden = md5($note->url . ':' . $secret);
        if ($note->get_id())
        {
            if ($note->secret && $note->secret != $hidden) $can_edit = FALSE;
        }
        else // it's a new note and might be secret?
        {
            if ($secret) $note->secret = $hidden;
        }
        return $can_edit;
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
