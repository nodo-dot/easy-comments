# PHP Easy Comments

**PHP Easy Comments** is a **free PHP comments script**

Comments are stored in flat ASCII data files in the same folder of the file to which they apply. No database required. New posts can be published instantly or require moderator approval.

The script comes with permalink and multi-language support and can send notifications for new comments. It equally works as a stand-alone discussions page or per include providing discreet inline page-specific comments.

In addition to individual comments data files, all posts are recorded in a master log. This provides a full index of all posts, which may serve as a basic navigation aid or help the admin to quickly find posts.

An basic text CAPTCHA attempts to limit SPAM. Further options are available to restrict user input to Latin characters only and the maximum of characters allowed per post.

Make sure to have `ob_start()` at the top of the page. This is required to bypass the *Headers already sent* warning after posting.
