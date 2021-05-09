# UserPanel
A simple login and registration form made to demonstrate PHP and MySQL. Javascript, CSS, and the Bootstrap 5 framework are used here. 

## Current Features
- Bootstrap frontend displaying multiple forms related to user management.
- Users can register for a new account, with checks to make sure fields aren't blank, passwords match, etc
- A listing is of all registered users is available. Links to the profiles of users are available.
- The profiles of users can be viewed, which shows any optional, publically available information.
- Users can authenticate to view a settings panel. They can change their personal information, email, and password.
- Checks are done both client-side, and also server-side to ensure users enter valid information.
- Passwords are hashed and salted, not stored in plain-text.
- Header allows for quicker navigation to pages, changing depending on whether the user is logged in or not.
- SQL statements are prepared to prevent SQL injections. Inputs are validated and sanitized to prevent attacks.

## Running this program
This website relies on a MySQL to host a database of users. To test this out, SQL must be running, with a username and password entered for an SQL user with permission to create, select, and insert to databases.

To initialize the database, first edit some parameters in the included phpUtils script; namely the $username and $password variables to an account which can create and perform operations on databases, or at least one database named loginForm. Then run the script, i.e. "php phpUtils.php". You can also rerun this script with "reset" as an argument to reset the database to a blank state, i.e. "php phpUtils.php reset."
After that, a php server can be ran by using "php -S hostname:portnumber" in the root directory of this project. You can then access the website through a web browser.
