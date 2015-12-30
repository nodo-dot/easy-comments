# Easy Comments

Free PHP script to add inline comments to web pages.

Comments are stored in a flat ASCII data file. Hence, no database is required. If the server supports sending mail, the script can send a brief notification whenever a user adds a new comment. This feature is disabled by default. Change `$eco_note = 'n'` to `$eco_note = 'y'` to enable and configure `$eco_mail` and `$eco_from` to match your settings. In case you never receive the mail you may have to adjust the format of `$eco_from`.

To enable comments you need to link eco to your pages first. This is done by adding a reference in the form of `include('/path/to/eco.php);` where you want the comments to appear. Just add `$eco_this = 'n'` above the line referencing eco.php to disable comments on the given page. You may find it convenient to add the contents of `eco.css` to your default 
style sheet.

You can post an admin reply by entering the values of `$eco_apfx` and `$eco_asfx` into the name field, e.g. if you set prefix to joe and suffix to root, enter joeroot (without space) as name. Your reply will appear as root # ... rather then user $ ...

[Script homepage](http://phclaus.eu.org/php-scripts/easy-comments)
