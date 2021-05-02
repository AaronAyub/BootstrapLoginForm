# UserPanel

A simple login and registration form made to demonstrate PHP and MySQL. Javascript, CSS, and the Bootstrap 5 framework are used here. 

## Current Features
- Bootstrap frontend displaying multiple forms related to user management.
- Users can register for a new account, with checks to make sure fields aren't blank, passwords match, etc
- Users can authenticate, and then change their settings such as email or password.
- Checks are done both client-side, and also server-side to ensure users enter valid information.
- Passwords are hashed and salted, not stored in plain-text.

## Running this program
This website relies on a MySQL to host a database of users. To test this out, SQL must be running, with a username and password entered for an SQL user with permission to create, select, and insert to databases. Additionally, a PHP server must be running in the same directory as the included files.
