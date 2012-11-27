<?php

class TwoFactorAuth {

    /**
     * @param $length The length in bytes of the token you wish to generate.
     * @return string The generated token, alphanumeric.
     */
    public function generateToken($length)
    {
        if(function_exists('openssl_random_pseudo_bytes'))
        {
            $token = bin2hex(openssl_random_pseudo_bytes($length));
        }
        else
        {
            $token = substr(sha1(uniqid(mt_rand(), true)), 0, $length * 2);
        }

        return $token;
    }

    /**
     * @param $token The POSTed token which is verified against the token stored in $_SESSION['tf_token'].
     * @return bool|string Returns false if the token is incorrect or the value that needs to be placed into the tf_remember_auth cookie if the token is correct.
     */
    public function verifyToken($token)
    {
        if (strcasecmp($_SESSION['tf_token'], $token) == 0)
        {
            $remember_tf_token = $this->authorizeDevice();
            return $remember_tf_token;
        }
        else
            return false;
    }

    /**
     * @return bool Returns true if the value of $_COOKIE['tf_remember_auth'] is found in the database, or false if otherwise.
     */
    public function checkAuthorizedDevice()
    {
        if(isset($_COOKIE['tf_remember_auth']))
        {
            $dbh = new PDO('mysql:dbname=database;host=localhost', 'user', 'password');
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $dbh->prepare("SELECT id, expires FROM trusted_devices WHERE token = ?");
            $stmt->execute(array($_COOKIE['tf_remember_auth']));
            $device = $stmt->fetch();

            if($device == false)
                return false;
            else
            {
                if($device['expires'] < time())
                    return false;
                else
                    return true;
            }
        }
        else
            return false;
    }

    /**
     * @return string The value that needs to be placed into the tf_remember_auth cookie
     */
    public function authorizeDevice()
    {
        $dbh = new PDO('mysql:dbname=database;host=localhost', 'user', 'password');
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $remember_tf_token = $this->generateToken(8);

        $stmt = $dbh->prepare("INSERT INTO trusted_devices(user_id, token, expires) VALUES(?, ?, ?)");
        $stmt->execute(array($_SESSION['user_id'], $remember_tf_token, time() + 86400 * 7));

        return $remember_tf_token;
    }
}