# Easy Comments

Free PHP script to add inline comments to web pages.

Comments are stored in flat ASCII data files. Hence, no database is required. The script can send a brief notification whenever a user adds a new comment; if the server supports sending mail. However, this feature is disabled by default.

Change `$eco_note = 'n'` to `$eco_note = 'y'` to enable and then configure `$eco_mail` and `$eco_from` to match your settings. In case you never receive the mail you may have to adjust the format of `$eco_from`.

To enable comments you need to link the script first, e.g. `include('/path/to/eco.php);` where you want the comments to appear. Adding `$eco_this = 'n'` above the line referencing `eco.php` disables comments for the given page.

You can post an admin reply by entering the values of `$eco_apfx` and `$eco_asfx` into the name field. For example, if you set `eco_apfx = joe` and `eco_asfx = root` you would enter `joeroot` (without space) and your reply appear as `root # ...` rather than `joe $ ...`

Basic formatting is supplied via CSS. You may find it convenient to add the contents of `eco.css` to your existing default style sheet.

[Script homepage](http://phclaus.eu.org/php-scripts/easy-comments)
