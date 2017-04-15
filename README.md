# Easy Comments

**Easy Comments** is a **free PHP script** to add inline comments to web pages.

Comments are stored in flat ASCII data files in the same folder with the file to which the comments apply. Hence, there is zero need for crytic database voodoo. The script can send a notification mail whenever a new comment is posted. This feature is disabled by default. You may have to change the format of `$eco_head` if you fail to receive the notification.

In addition to individual data files a master log file is stored in the script folder recording a complete history of all posts. You may want to change the log file name in `$eco_clog` and the query token in `$eco_list` to prevent spoofing. This would make the log file available only via the query token, e.g. `http://www.example.com/eco/?log` The log file is not written when using manual approval.

You can post an admin reply by entering the values of `$eco_apfx` and `$eco_asfx` into the name field. For example, if you set `eco_apfx = john` and `eco_asfx = root` you would enter `johnroot` without space. Your reply will then appear as `root # ...` rather than `john $ ...`

A default delay between posts is applied to limit flooding This can be disabled by setting `$eco_tdel = 0`. To further restrict SPAM and abuse set `$eco_mapp = "y"` to configure the script to require manual approval for new posts. If using this option notifications must be turned on by setting `$eco_note = "y"`. Please note that you will need to add all relevant new comments to the proper data files yourself. Simply copy the mark-up sent in the notification and paste it into the data file.

A typical reference to the script would look something like `include ('/path/to/eco/index.php);`and goes where you want the comments to appear. If you had comments enabled globally for all pages but then want to exclude a certain page you can do so by adding `$eco_this = 'n'` just above the reference.

Basic formatting is provided via CSS. You may find it convenient to add the contents of `eco.css` to your existing style sheet. The rules for `class="input"` used in the form are not part of `eco.css`, assuming you have a similar class in your default style sheet already.


[Script homepage](http://phclaus.com/php-scripts/easy-comments/)
