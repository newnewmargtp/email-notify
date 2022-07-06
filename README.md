### Subcribe email sending

To sep up the service, please, add to your crontab the following

1. crontab -e -u {user} to open crontab with your user.

2. Add:
   
   `0 11 * * * /usr/lib/php /home/{user}/email-nofity/validate-emails.php`

   `0 0 * * * /usr/lib/php /home/{user}/email-nofity/send-emails.php`
