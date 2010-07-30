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
            $y = array_key($note, 'y', 0);
            $text = array_key($note, 'text', '');
            $list[$y*1000+$x] = $text;
        }
        ksort($list);
        return join(' ', array_values($list));
    }

    public function to_array()
    {
        $notes_json = str_replace('<', '&lt;', $this->notes);
        return json_decode($notes_json, TRUE);
    }
}

// End of Note.php
