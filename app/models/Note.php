<?
class Note extends Model
{
    public function setup()
    {
        $this->set_timestamp('created_at', 'updated_at');
    }
}

// End of Note.php
