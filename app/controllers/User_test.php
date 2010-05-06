<?
load_controller('User');

class User_test_controller extends User_controller
{
    private $test_user;

    public function setup()
    {
        $this->teardown(); // just in case

        // Create a test user

        $this->test_user = new User(array(
                                    'name' => 'Kevin Hutchinson',
                                    'email' => 'kevin@guanoo.com',
                                    'password' => 'gherkin',
                                    ));
        $this->test_user->insert();
    }

    public function teardown()
    {
        $user = new User();
        $user->delete_all();
    }

    public function login_test()
    {
        // Test that our test user can login

        $this->set_params(array(
                        'user->email'       => $this->test_user->email,
                        'user->password'    => $this->test_user->password,
                        ));
        list($user) = $this->login();
        $this->should('find a matching user', $user && $user->email === $this->test_user->email, $user);
        $this->should('start a user session', $user->get_id() > 0 && $user->get_id() === $this->session->user_id, $user);

        // Test that we cannot login with the wrong details

        $this->set_params(array(
                        'user->email'       => $this->test_user->email,
                        'user->password'    => 'WRONG',
                        ));
        list($user) = $this->login();
        $this->should_not('login with the wrong details', $user->get_id(), $user);
    }

    public function logout_test()
    {
        list($user) = $this->logout();
        $this->should('find the user to logout', $user && $user->email === $this->test_user->email, $user);
        $this->should_not('be any user session left over', $this->session->user_id, $user);
    }
}

// End of User_test.php
