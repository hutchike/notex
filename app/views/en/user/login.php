<?= HTML::form_open('login', 'user/login') ?>
Email <?= HTML::input('user->email') ?>
<br/>
Password <?= HTML::input('user->password', array('type' => 'password')) ?>
<br/>
<?= HTML::input('login', array('type' => 'submit', 'value' => 'Login')) ?>
<?= HTML::input('user->password', array('type' => 'password')) ?> remember me

