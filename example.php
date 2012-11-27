<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>OpenAPI4SMS Two Factor Authentication Demo</title>
    <style>
        .container {
            margin: 0 auto;
            width: 960px;
        }

        form {
            width: 250px;
        }

    </style>
</head>
<body>
<div class="container">
    <p style="color: red;"><?php if (isset($_SESSION['error'])) echo $_SESSION['error']; unset($_SESSION['error']); ?></p>

    <?php if (!isset($_SESSION['tf_token']) && !isset($_SESSION['user_id'])): ?>
    <p>This demonstration will allow you to enter any username and password into the login form below in order to test
        the two factor authentication system.</p>

    <form action="post.php" method="POST" class="center">
        <input type="hidden" name="action" value="login">

        <label for="username">Username</label>
        <input type="text" name="username" id="username" value=""/>

        <label for="password">Password</label>
        <input type="password" name="password" id="password" value=""/>

        <p><input type="submit" name="Login" value="Login"/></p>
    </form>
    <?php endif; ?>

    <?php if (isset($_SESSION['tf_token'])): ?>
    <p>In a few moments you should receive a text message on your mobile phone with the verification code.</p>
    <form action="post.php" method="POST" class="center">
        <input type="hidden" name="action" value="verify_tf_auth">

        <label for="username">Verification Code</label>
        <input type="text" name="token" id="token" value=""/>

        <p><input type="submit" name="Verify" value="Verify"/></p>
    </form>
    <?php endif; ?>

    <?php if (isset($_SESSION['user_id'])): ?>
    <p>You are logged in as <?php echo $_SESSION['username']; ?>.</p>
    <p>Click <a href="logout.php">here</a> to logout.</p>
    <?php endif; ?>
</div>
</body>
</html>