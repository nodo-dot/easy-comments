# PHP Easy Comments

**PHP Easy Comments** is a **free PHP comments script**

It can be used as a stand-alone discussions page or per include to provide inline page comments.

Comments are stored in flat ASCII data files in the same folder of the file to which they apply. Hence, no database is required. New posts can be published instantly or require moderator approval. The script comes with permalink and multi-language support and can send notifications for new comments. You may have to change the format of `$eco_head` if you fail to receive the mail.

In addition to individual data files, a master log file keeps a complete history of all posts and thus provides a full index, which may serve as a vavigation aid. However, the log file is currently not used in moderator mode and doesn't save titles. This may come in a later version.

You can post admin replies by entering the values of `$eco_apfx` and `$eco_asfx` without spaces into the name field, e.g. if `eco_apfx = HelloWorld` and `eco_asfx = root` you would enter `HelloWorldroot`. The value of `$eco_tmax` sets the maximum characters allowed per post. If you want to allow Latin characters only, set `$eco_lato = 1` Make sure to have `session_start()` and `ob_start()` at the top of the page. The script is likely to fail without.

Follow this link to try a [simple demo](http://phclaus.com/demo/easy-comments/).

