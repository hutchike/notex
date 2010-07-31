<?
class Twitter_controller extends App_controller
{
    public function before()
    {
        load_plugin('Twitter/OAuth');
        parent::before();
    }

    public function login()
    {
        $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET);
        $request_token = $connection->getRequestToken(OAUTH_CALLBACK);
        $this->session->oauth_token = $token = $request_token['oauth_token'];
        $this->session->oauth_token_secret = $request_token['oauth_token_secret'];
        switch ($connection->http_code) {
        case 200:
            $url = $connection->getAuthorizeURL($token);
            $this->redirect($url); 
            break;
        default:
            $this->render->debug = 'cannot authenticate with Twitter';
        }
    }

    public function clear()
    {
        $this->session->status = NULL;
        $this->session->access_token = NULL;
        $this->session->oauth_status = NULL;
        $this->session->oauth_token = NULL;
        $this->session->oauth_token_secret = NULL;
        $this->redirect('');
    }

    public function callback()
    {
        $oauth_token = $this->params->oauth_token;
        $oauth_verifier = $this->params->oauth_verifier;
        if (isset($oauth_token) && $oauth_token != $this->session->oauth_token)
        {
            $this->session->oauth_status = 'oldtoken';
            $this->redirect('twitter/clear');
        }

        $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $this->session->oauth_token, $this->session->oauth_token_secret);
        $this->session->access_token = $connection->getAccessToken($oauth_verifier);
        $this->session->oauth_token = NULL;
        $this->session->oauth_token_secret = NULL;

        if (200 == $connection->http_code) {
            $session->status = 'verified';
            $this->redirect('');
        }
        else
        {
            $this->redirect('twitter/clear');
        }
    }
}

// End of Twitter.php
