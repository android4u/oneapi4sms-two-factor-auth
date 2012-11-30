<?php
session_start();
include 'lib/two_factor.class.php';
include 'lib/oneapi4sms.class.php';

$tf_auth = new TwoFactorAuth();

if($_POST['action'] == 'login')
{

    /*
    * Add your own logic here to query the database to find the user, verify their password and retrieve their phone number.
    */
    
    $user_phone = $_POST['phone'];
 
    if ($tf_auth->checkAuthorizedDevice() == false)
    {
        $_SESSION['tf_token'] = $tf_auth->generateToken(2);
        
        $sms_api = new OneApi4Sms('your-customer-id', 'your-password');
        
        $message = 'Your verification code is ' . $_SESSION['tf_token'];

        $data = $sms_api->sendSMS('phone-number', $message, $user_phone);
    }
    else
    {
        $_SESSION['user_id'] = 1;
        $_SESSION['username'] = 'John Smith';
        unset($_SESSION['tf_token']);
    }

    header("Location: example.php");
}
if($_POST['action'] == 'verify_tf_auth')
{
    if($tf_auth->verifyToken(trim($_POST['token'])) == true)
    {
        $remember_tf_token = $tf_auth->authorizeDevice();
        setcookie('tf_remember_auth', $remember_tf_token, time() + 86400 * 7, "", "", 0, 1);
        
        $_SESSION['user_id'] = 1;
        $_SESSION['username'] = 'John Smith';
        unset($_SESSION['tf_token']);
    }
    else
        $_SESSION['error'] = 'Invalid verification code.';
    
    header("Location: example.php");
}