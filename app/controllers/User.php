<?
class User_controller extends App_controller
{
    public function before()
    {
        parent::before();
        load_models('User');

        $this->render->user_email = $this->cookie->user_email;
    }

    public function login()
    {
        $user = new User($this->param->user);
        $remember_me = $this->params->remember_me;
        if ($user->email && $user->password)
        {
            if ($user->load())
            {
                if ($remember_me) $this->cookie('user_email', $user->email, time()+60*60*24*30);
                $this->session->user_id = $user->get_id();
                $this->session->user_name = $user->name;
            }
            else
            {
                $this->render->flash = Translate::into($this->lang, 'FORGOT_PASSWORD_QUESTION', array('LINK' => AppView::url("user/forgot_password?email=$user->email")));
            }
        }
        else // email or password missing
        {
            // Logic goes here for missing user login details
        }

        $this->render->user = $user;
        return array($user); // for testing
    }

    public function logout()
    {
        $user = new User();
        $user = $user->find_id($this->session->user_id);
        $this->session->user_id = '';
        return array($user); // for testing
    }
}

// End of User.php
