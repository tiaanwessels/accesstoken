# accesstoken
Generate and verify access tokens for security complexes with no budget. Hire the cheapest hosting package you can and put this one there.
Make a directory under your document root e.g. /var/www/html/sentry
Place files in directory and access <yourip>/sentry
This creates a SQLite3 database called sentry.db
You need to protect downloading of this file by your preferred means which could be:
- apache configuration
- .htaccess
- edit the PHP files and set a path to sentry.db where it is referenced to a safer place
You need to edit forgot.php and set $headers From to a real sender address
The system assumes user names are in fact e-mail addresses for 'Forget Password' to work as it will interpret the username as an e-mail address to send a temporary password to
Passwords are saved hashed in the database so the next step to protect un-hashed passwords in flight is to setup https on your web server
The first time you login as the admin user, the password you entered will be set as that of the admin user so do this immediately after installation.
You need to style everything yourself if you don't like the minimalist appearance.
NOTE: this was an experiment for me. The s/w was 90% generated by AI and i simply fixed some of the more stupid obvious errors
