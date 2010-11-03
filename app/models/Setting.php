<?
class Setting extends Model
{
    public function setup()
    {
        $this->set_timestamp('created_at', 'updated_at');
    }

    public static function get_for($username)
    {
        // TODO - return $settings array
    }

    public static function set_for($username, $settings)
    {
        // TODO
    }
}

// End of Setting.php
