<?
class Note extends Model
{
    public function setup()
    {
        $this->set_timestamp('created_at', 'updated_at');
    }

    public function filter() // to remove deleted notes
    {
        $filtered = array();
        $notes = $this->to_array();
        foreach ($notes as $id => $note) {
            if (array_key($note, 'deleted')) continue;
            $filtered[$id] = $note;
        }
        return $filtered ? $filtered : NULL;
    }

    public function to_text()
    {
        $notes = $this->to_array();
        $list = array();
        foreach ($notes as $id => $note)
        {
            if (array_key($note, 'deleted')) continue;
            $x = array_key($note, 'x', 0);
            $line = sprintf('%02d', intval(array_key($note, 'y', 0) / 41));
            $text = array_key($note, 'text', '');
            $list[$line.($x/1000)] = $text;
        }
        ksort($list);
        $note = '';
        $last_pos = 0;
        foreach ($list as $pos => $text)
        {
            $nl = str_repeat("\n", intval($pos/10) - intval($last_pos/10));
            if ($note) $note .= (intval($pos) == intval($last_pos) ? ' ' : $nl);
            $note .= $text;
            $last_pos = $pos;
        }
        return $note;
    }

    public function to_array()
    {
        $notes_json = str_replace('<', '&lt;', $this->notes);
        $arry = json_decode($notes_json, TRUE);
        return (is_array($arry) ? $arry : array());
    }

    public static function set_database_for($username)
    {
        if ($username == '' || $username == 'www') return;

        $data_file = "app/data/users/$username.db";
        if (!file_exists($data_file))
        {
            $user_file = 'app/data/new_user.db';
            copy($user_file, $data_file);
        }
        parent::connect($data_file, TRUE);
    }
}

// End of Note.php
