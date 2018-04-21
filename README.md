# PHP Easy Comments

**PHP Easy Comments** is a **free PHP comments script**

Comments are stored in flat ASCII data files in the same folder of the file to which they apply. No database required. New posts can be published instantly or require moderator approval.

The script supports:

- Permalinks
- Multi-language interface
- File uploads
- Notification mails
- Restricted names

It equally works as a stand-alone discussions page or per include providing discreet inline page-specific comments.

In addition to individual data files, all comments are recorded in a master log. This provides a full index of all posts and may serve as a basic navigation aid or to quickly find specific entries.

A basic text CAPTCHA attempts to limit SPAM. Further options are available to restrict user input to Latin characters only and the maximum of characters allowed per post.

You can post admin replies by typing the values of `$eco_apfx` and `$eco_asfx` into the `Name` field, e.g. `magicadmin` would post your entry as `admin #` versus `JohnDoe $`.

Make sure to have `ob_start()` at the top of the page. This is required to bypass the *Headers already sent* warning after posting.
