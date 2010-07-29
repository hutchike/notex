<?
class Note extends Model
{
    public function setup()
    {
        $this->set_timestamp('created_at', 'updated_at');
    }

    public function filter()
    {
        $notes_json = str_replace('<', '&lt;', $this->notes);
        $notes = json_decode($notes_json, TRUE);
        $filtered = array();
        foreach ($notes as $id => $note) {
            if (array_key($note, 'deleted')) continue;
            $filtered[$id] = $note;
        }
        return $filtered;
    }
}

// End of Note.php
