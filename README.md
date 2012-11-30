# OneAPI4SMS-Two-Factor-Auth

This is a sample implementation of two-factor authentication using the OneAPI4SMS API.

## How does it work?

This example presents you with a login form which takes any username and password, along with a phone number in the form of 5550123456. Upon logging in, you will receive a text message to your mobile number. 

Obviously, you will need to put together the nuts and bolts for grabbing the username, user id and the user's phone number from the database and check to make sure that they've entered a correct password.

We set up a session variable called `tf_token` which stores a 4 character token we've randomly generated; This token is also sent to the user's phone number via the OneAPI4SMS API, and the user is expected to provide it on the verification form which appears after successfully logging in.

Assuming the user has entered a correct token, we clear the `tf_token` cookie and create another cookie called `tf_remember_auth` which expires after 7 days, and we log the user in.

The reason for creating the `tf_remember_auth` cookie is quite simple: A user may log in several times a week from the same computer, so there's no reason to keep pestering him with messages every time he logs in, instead we store the cookie value in the database along with the user's user ID.

A suggested schema for the authorized devices table can be found in `trusted_devices.sql`.

## Configuration

In `post.php` you will need to configure your OneAPI4SMS credentials.
```php
$sms_api = new OneApi4Sms('your-customer-id', 'your-password');
```
If you're having trouble sending a message, you can debug the message sending procedure by using `print_r($data)`.

You'll also want to add your own logic where needed for database queries, or adapt the existing queries, which are provided using the PDO library.

The `tf_remember_auth` cookie should be given a secure flag to be used only over SSL, if possible.