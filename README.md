# Easy Comments

**Easy Comments** is a **free PHP script** to add inline comments to web pages.

Comments are stored in flat ASCII data files in the same folder with the file to which the comments apply, and thus no database is required. The script can send a notification mail whenever new comments are posted. This feature is disabled by default. You may have to change the format of `$eco_head` if you fail to receive the notification.

In addition to individual data files, a master log file keeps a complete history of all posts. You may want to change the log file name in `$eco_clog` and the query token in `$eco_list` to limit spoofing. The log file is not used with manual approval.

You can post admin replies by entering the values of `$eco_apfx` and `$eco_asfx` into the name field. If you set `eco_apfx = john` and `eco_asfx = root` you would enter `johnroot`. Change the value of `$eco_tmax` to set the maximum character count and `$eco_lato = 1` to allow latin characters only.

A delay between posts attempts to limit flooding. This can be disabled with `$eco_tdel = 0`. Set `$eco_mapp = 1` and `$eco_note = 1` to require manual approval of new posts. Add a reference like `include ('/path/to/eco/index.php);` where you want comments to appear. Basic formatting is provided via CSS. You may find it convenient to add the contents of `eco.css` to your existing style sheet.
