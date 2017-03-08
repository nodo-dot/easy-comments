# Easy Comments

**Easy Comments** is a **free PHP script** to add inline comments to web pages.

Comments are stored in flat ASCII data files in the same folder with the file to which the comments apply. Hence, there is zero need for crytic database voodoo. The script can send a brief notification whenever a new comment is posted and you have access to PHP's internal `mail()` function. This feature is disabled by default. You may have to change the format of `$eco_head` if you fail to receive the mail.

In addition to individual data files in the same location as the page being commented on, a master log file is stored in the script folder recording a complete history of all comments.

You may want to change the log file name in `$eco_clog` and the query token in `$eco_list` to prevent spoofing. This would make the log file available only via the query token, e.g. `http://www.example.com/eco/?log`

Please note that the `class="input"` used in the form is not part of `eco.css` - assuming you already have a similar class in your default style sheet. Either change `"input"` to match your own class or add an alias to your default style sheet.

A typical reference to the script would look something like `include ('/path/to/eco.php);`and goes where you want the comments to  appear. If you enabled comments for all pages but then want to exclude some files, you can do so by adding `$eco_this = 'n'` above the reference.

You can post an admin reply by entering the values of `$eco_apfx` and `$eco_asfx` into the name field. For example, if you set `eco_apfx = john` and `eco_asfx = root` you would enter `johnroot` without space. Your reply will then appear as `root # ...` rather than `john $ ...`

Basic formatting is supplied via CSS. It may be more convenient to add the contents of `eco.css` to an existing style sheet.

[Script homepage](http://phclaus.com/php-scripts/easy-comments/)
