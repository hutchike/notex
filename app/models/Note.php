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
        $last_pos = NULL;
        foreach ($list as $pos => $text)
        {
            if ($note) $note .= (intval($pos) == intval($last_pos) ? ' ' : "\n");
            $note .= $text;
            $last_pos = $pos;
        }
        return $note;
    }

    public function to_array()
    {
        $notes_json = str_replace('<', '&lt;', $this->notes);
        return json_decode($notes_json, TRUE);
    }
}

// End of Note.php
