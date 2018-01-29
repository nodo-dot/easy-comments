# PHP Easy Comments

**PHP Easy Comments** is a **free PHP comments script**

Comments are stored in flat ASCII data files in the same folder of the file to which they apply. Hence, no database is required. New posts can be published instantly or require moderator approval.

The script comes with permalink and multi-language support and can send notifications for new comments. It can be used as a stand-alone discussions page or per include to provide discreet inline page specific comments.

In addition to individual comments data files, all posts are recorded in a master log. This provides a full index of all posts, which may serve as a basic navigation aid or to help the admin to quickly find posts.

An optional delay before and between posts plus a basic text CAPTCHA try to limit SPAM. Further options are available to restrict user input to Latin characters only and the maximum of characters allowed per post.

Make sure to have `ob_start()` at the top of the page. This is required to bypass the *headers already send* warning after posting. The script may well break without this.

Follow this link to try a [simple demo](http://phclaus.com/demo/easy-comments/).
