# Easy Comments

**Easy Comments** is a **free PHP script** to add inline comments to web pages.

Comments are stored in flat ASCII data files in the same folder with the file to which the comments apply. Hence, there is zero need for crytic database voodoo. The script can send a notification mail whenever a new comment is posted. This feature is disabled by default. You may have to change the format of `$eco_head` if you fail to receive the notification.

In addition to individual data files a master log file is stored in the script folder recording a complete history of all posts. You may want to change the log file name in `$eco_clog` and the query token in `$eco_list` to prevent spoofing. This would make the log file available only via the query token, e.g. `http://www.example.com/eco/?log`

Please note that `class="input"` used in the form is not part of `eco.css` - assuming you already have a similar class in your default style sheet.

A typical reference to the script would look something like `include ('/path/to/eco/index.php);`and goes where you want the comments to  appear. If you enabled comments for all pages but then want to exclude some files, you can do so by adding `$eco_this = 'n'` above the reference.

You can post an admin reply by entering the values of `$eco_apfx` and `$eco_asfx` into the name field. For example, if you set `eco_apfx = john` and `eco_asfx = root` you would enter `johnroot` without space. Your reply will then appear as `root # ...` rather than `john $ ...` A default delay of five minutes between posts is applied to limit flooding. 

Basic formatting is provided via CSS. You may find it convenient to add the contents of `eco.css` to your existing style sheet.

[Script homepage](http://phclaus.com/php-scripts/easy-comments/)
