# PHP Easy Comments

**PHP Easy Comments** is a **free PHP comments script** to add discreet inline comments to web pages.

Comments are stored in flat ASCII data files in the same folder with the file to which the comments apply. No database is required. The script can send notification mails whenever new comments are posted. This feature is disabled by default. You may have to change the format of `$eco_head` if you fail to receive the notification.

In addition to individual data files, a master log file keeps a complete history of all posts. You can change the query token to view the list in `$eco_list`. The log file is not used when moderator approval is enabled.

You can post admin replies by entering the values of `$eco_apfx` and `$eco_asfx` into the name field. If you set `eco_apfx = mySecret` and `eco_asfx = root` you would enter `mySecretroot`. Change the value of `$eco_tmax` to set the maximum characters allowed per post and `$eco_lato = 1` if you want to allow latin characters only.

A delay between posts attempts to limit flooding. This can be disabled with `$eco_tdel = 0`. Set `$eco_mapp = 1` and `$eco_note = 1` to require manual approval of new posts. Add a reference to the script where you want the comments section to appear.

Make sure to have `session_start()` and `ob_flush()` at the top of the page. This is required to get around the `headers already sent` warning after posting. Basic formatting is provided via CSS. You may want to add the provided rules to your existing style sheet.

For simple testing you can just call the script directly. However, this is *not* recommended for public use. Follow this link to try a [plain vanilla demo](http://phclaus.com/demo/easy-comments/).

