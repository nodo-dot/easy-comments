# Easy Comments

**Easy Comments** is a **free PHP script** to add inline comments to web pages.

Comments are stored in flat ASCII data files in the same folder with the file to which the comments apply. No database required. The script can send notification mails whenever new comments are posted. This feature is disabled by default. You may have to change the format of `$eco_head` if you fail to receive the notification.

In addition to individual data files, a master log file keeps a complete history of all posts. You can change the log file name in `$eco_clog` and the query token to view the list in `$eco_list`. The log file is not used when manual approval is enabled.

You can post admin replies by entering the values of `$eco_apfx` and `$eco_asfx` into the name field. If you set `eco_apfx = john` and `eco_asfx = root` you would enter `johnroot`. Change the value of `$eco_tmax` to set the maximum characters allowed per post and `$eco_lato = 1` to allow latin characters only.

A delay between posts attempts to limit flooding. This can be disabled with `$eco_tdel = 0`. Set `$eco_mapp = 1` and `$eco_note = 1` to require manual approval of new posts. Add a reference like `include ('/path/to/easy-comments/index.php);` where you want the comments section to appear. Basic formatting is provided via CSS. You may find it convenient to add the contents of `eco.css` to your existing style sheet.

For simple testing you can just call the script directly. This is *not* recommended for public use.
