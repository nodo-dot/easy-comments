<?php
/**
 * PHP Version 5 and above
 *
 * @category  PHP_Comment_Scripts
 * @package   PHP_Easy_Comments
 * @author    P H Claus <phhpro@gmail.com>
 * @copyright 2015 - 2018 P H Claus
 * @license   https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @version   GIT: Latest
 * @link      https://github.com/phhpro/easy-comments
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA 02110-1301, USA.
 */


/**
 ***********************************************************************
 *                                                   BEGIN USER CONFIG *
 ***********************************************************************
 */


//** Values of 0 mean NO -- 1 equals YES


/**
 * Document root -- "path" without slash if SERVER has wrong value
 * Script folder
 * Default directory index
 */
$eco_path = $_SERVER['DOCUMENT_ROOT'];
$eco_fold = "/easy-comments/";
$eco_dirx = "index.php";

/**
 * Maximum characters allowed per post
 * Delay between posts -- in seconds, 0 to disable
 * Accept Latin characters only
 * Enable permalinks
 * Default user name
 *
 * Beginning with v20180107 the delay management has been changed from
 * session cookie to Javascript. While this takes out the requirement
 * for session_start() it also means the delay will be ineffective on
 * devices not supporting Javascript. In this case the submit button is
 * rendered regardless of settings.
 */
$eco_pmax = 1024;
$eco_pdel = 10;
$eco_plat = 0;
$eco_perm = 1;
$eco_user = "anonymous";

/**
 * Send notifications for new comments
 * Require moderator approval -- 1 relies on $eco_note = 1
 * Require control code, aka CAPTCHA
 */
$eco_note = 0;
$eco_moda = 0;
$eco_ctrl = 1;

/**
 * Add new comments
 *
 * Setting this to 0 disables adding new comments. Existing
 * data files containing previous comments will still be shown.
 */
$eco_anew = 1;

/**
 * Mail account to send notifications
 * Mail account to receive notifications
 * Notification subject
 *
 * The mail sender must exist on the same host running the script.
 * The recipient can be any valid mail address. Default is sender.
 */
$eco_mafr = "info@" . $_SERVER['HTTP_HOST'];
$eco_mato = $eco_mafr;
$eco_msub = "PHP_Easy_Comments_NEW";

/**
 * Mail header
 *
 * The default "from" field is in standard ISO notation and should
 * be good to go. However, some hosts may require modification.
 */
$eco_mhdr = "From: PHP_Easy_Comments <$eco_mafr>";

/**
 * Admin prefix
 * Admin suffix
 *
 * Enter both values without spaces into the name field to post as
 * admin, e.g. MY_ADMIN_PREFIXroot will publish your entry as root
 */
$eco_apfx = "MY_ADMIN_PREFIX";
$eco_asfx = "root";

/**
 * Query string to list the log file
 * Date and time format
 *
 * The log records every post ever made anywhere on the site running
 * the script, including those added per moderator approval and may
 * be used as a simple navigation index.
 *
 * The value of $eco_list configures the query string required to
 * view the file, e.g. http://example.com/easy-comments/?MY_LIST_QUERY
 */
$eco_list = "MY_LIST_QUERY";
$eco_date = gmdate('Y-m-d H:i:s');


/**
 ***********************************************************************
 *                                                     END USER CONFIG *
 ***********************************************************************
 */


/**
 * Script version
 * Host on which the script is running
 * Current page to which the comments apply
 * Global query string
 */
$eco_make = "20180116";
$eco_host = $_SERVER['HTTP_HOST'];
$eco_page = $_SERVER['SCRIPT_NAME'];
$eco_qstr = $_SERVER['QUERY_STRING'];

/**
 * Comments data file
 * Comments log file
 * These are not used in moderator mode
 */
$eco_cdat = "_comments.html";
$eco_clog = $eco_fold . "log.html";

/**
 * Strip default index
 * Comments data file
 * Restricted names data file
 */
$eco_indx = str_replace($eco_dirx, "", $eco_page);
$eco_data = $eco_path . $eco_page . $eco_cdat;
$eco_rest = $eco_path . $eco_fold . "restricted.php";

//** Link the user's IP -- don't rely on this
$eco_myip = gethostbyaddr($_SERVER['REMOTE_ADDR']);

//** Build language reference
if (isset($eco_qstr) && strpos($eco_qstr, "lang_") !== false) {
    $eco_lref = str_replace("lang_", "lang/", $eco_qstr);
} else {
    //** Try fallback if selected locale is missing
    $eco_lref = "lang/en";
}

//** Link language file
$eco_ldat = $eco_path . $eco_fold . $eco_lref . ".php";

//** Load language file -- no translation because none is available
if (file_exists($eco_ldat)) {
    include $eco_ldat;
} else {
    echo "        <p id=eco_stat>Missing language file!</p>\n" .
         "        <p>Please check your settings to correct the " .
         "error.</p>\n        <p>The script will be disabled until " .
         "the error is fixed.</p>\n";
}

/**
 * Control code range min
 * Control code range max
 * Link value #1
 * Link value #2
 */
$eco_cmin = 1;
$eco_cmax = 9;
$eco_cone = mt_rand($eco_cmin, $eco_cmax);
$eco_ctwo = mt_rand($eco_cmin, $eco_cmax);

//** Init placeholders
$eco_text = "";
$eco_name = "";
$eco_stat = "";
$eco_latb = "";

//** Check empty name
if ($eco_name === "") {
    $eco_name = $eco_user;
}

//** Check if Latin only is enabled
if ($eco_plat === 1) {
    $eco_latb = "Latin ";
}

//** Check protocol
if (isset($_SERVER['HTTPS']) && "on" === $_SERVER['HTTPS']) {
    $eco_prot = "s";
} else {
    $eco_prot = "";
}

//** Link protocol
$eco_prot = "http" . $eco_prot . "://";

//** Check listing the log
if ($_SERVER['QUERY_STRING'] === $eco_list) {

    if (file_exists($eco_path . $eco_clog)) {
        header("Location: $eco_prot$eco_host$eco_clog");
        exit;
    } else {
        $eco_stat = $eco_lang['fail_log'];
    }
}

//** Check moderator mode without notifications
if ($eco_moda === 1 && $eco_note === 0) {
    echo "        <p id=eco_stat>" .$eco_lang['mod_flag'] . "</p>\n" .
         "        <p>" . $eco_lang['check_set'] . "</p>\n" . 
         "        <p>" . $eco_lang['fix_error'] . "</p>\n";
    exit;
}

//** Process moderator approval -- gets values from mail link
if (isset($_GET['eco_data']) && $_GET['eco_data'] !== "" 
    && isset($_GET['eco_post']) && $_GET['eco_post'] !== "" 
    && isset($_GET['eco_link']) && $_GET['eco_link'] !== ""
) {
    $eco_data = $_GET['eco_data'];
    $eco_post = hex2bin($_GET['eco_post']);

    if (file_exists($eco_data)) {
        $eco_post .= file_get_contents($eco_data);
    }

    file_put_contents($eco_data, $eco_post);

    //** Build log file entry
    $eco_clog = $eco_path . $eco_clog;
    $eco_ulog = '<div><a href="' . $eco_page . '#' . $eco_peid .
                '" title="' . $eco_lang['view'] . '">' .
                "$eco_date =&gt; $eco_page =&gt; $eco_peid</a></div>\n";

    //** Check existing log file
    if (file_exists($eco_clog)) {
        $eco_ulog .= file_get_contents($eco_clog);
    }

    //** Update log file
    file_put_contents($eco_clog, $eco_ulog);

    //** Check particular page to verify the post is good
    header("Location: " . $_GET['eco_link']);
    exit;
}

//** Form submitted
if (isset($_POST['eco_post'])) {

    //** Filter name and text
    $eco_name = htmlentities($_POST['eco_name'], ENT_QUOTES, "UTF-8");
    $eco_text = htmlentities($_POST['eco_text'], ENT_QUOTES, "UTF-8");

    //** Filter common protocols to defuse URL's
    $eco_urls = array('http', 'https', 'ftp', 'ftps');

    foreach ($eco_urls as $eco_link) {
        $eco_text = str_replace($eco_link . "://", "", $eco_text);
    }

    //** Link control code
    $eco_csum = $_POST['eco_csum'];
    $eco_cone = $_POST['eco_cone'];
    $eco_ctwo = $_POST['eco_ctwo'];
    $eco_cval = ((int)$eco_cone+(int)$eco_ctwo);

    //** Substitute anonymous if name is missing or invalid
    if ($eco_name === "" || preg_match("/^\s*$/", $eco_name)) {
        $eco_name = $eco_user;
    }

    //** Link restricted names
    if (file_exists($eco_rest)) {
        include $eco_rest;
    }

    //** Append user flag
    if ($eco_name === $eco_apfx . $eco_asfx) {
        $eco_name = $eco_asfx;
        $eco_ukey = "#";
        $eco_moda = 0;
    } else {
        $eco_name = $eco_name;
        $eco_ukey = "$";
    }

    //** Check control code
    if ($eco_ctrl === 1) {

        if ((int)$eco_cval !== (int)$eco_csum) {
            $eco_stat = $eco_lang['fail_code'];
        }
    }

    //** Check missing text
    if ($eco_text === "") {
        $eco_stat = $eco_lang['fail_text'];
    }

    //** Check Latin only
    if ($eco_plat === 1) {

        //** Filter Latin only
        $eco_latx = "/[^\\p{Common}\\p{Latin}]/u";

        //** Check name and text
        if (preg_match($eco_latx, $eco_name)
            || preg_match($eco_latx, $eco_text)
        ) {
            $eco_stat = $eco_lang['latin'];
        }
    }

    //** Save existing input
    if ($eco_stat !== "") {
        $eco_name = $eco_name;
        $eco_text = $eco_text;
    }

    //** Valid comment
    if ($eco_stat === "") {
        //** Generate permalink ID
        $eco_peid = md5(gmdate('YmdHis') . $eco_myip . $eco_name);

        //** Build post entry
        $eco_post = '            <div id="' . $eco_peid . '" ' .
                    "class=eco_item>" . $eco_date . " " . $eco_name .
                    " " . $eco_ukey . " " . $eco_text;

        //** Check if permalinks are enabled
        if ($eco_perm === 1) {
            $eco_post .= ' <div class=eco_perm><a href="' .
                         $eco_prot . $eco_host . $eco_page . "#" .
                         $eco_peid . '" title=Permalink>' .
                         "ID: $eco_peid</a></div>";
        }

        $eco_post .= "</div>\n";

        //** Build mail body
        $eco_mbod = $eco_name . " on " . $eco_prot . $eco_host .
                    $eco_indx . "\n\n" . $eco_post;

        //** Check if moderator mode is enabled
        if ($eco_moda !== 1) {

            //** Check existing data file
            if (file_exists($eco_data)) {
                $eco_post .= file_get_contents($eco_data);
            }

            //** Update data file and log
            file_put_contents($eco_data, $eco_post);
            $eco_clog = $eco_path . $eco_clog;
            $eco_ulog = '<div><a href="' . $eco_page . '#' . $eco_peid .
                        '" title="' . $eco_lang['view'] . '">' .
                        "$eco_date =&gt; $eco_page =&gt; $eco_peid</a></div>\n";

            //** Check existing log
            if (file_exists($eco_clog)) {
                $eco_ulog .= file_get_contents($eco_clog);
            }

            //** Update log
            file_put_contents($eco_clog, $eco_ulog);

            //** Check if user post
            if ($eco_name !== $eco_asfx) {

                //** Check if notifications are enabled
                if ($eco_note === 1) {
                    mail($eco_mato, $eco_msub, $eco_mbod, $eco_mhdr);
                }
            }
        } else {
            //** Build moderator mail link
            $eco_mlnk = $eco_prot . $eco_host . $eco_fold . "?eco_data=" .
                        $eco_data . "&eco_post=" . bin2hex($eco_post) .
                        "&eco_link=" . str_replace($eco_path, "", getcwd());

            //** Build moderator mail body
            $eco_mbod = $eco_mbod . "\n\nClick the below link to " .
                        "approve the post and publish or just " .
                        "ignore this mail to dismiss the post " .
                        "without publishing.\n\n" . $eco_mlnk;

            //** Send mail
            mail($eco_mato, $eco_msub, $eco_mbod, $eco_mhdr);
        }

        //** Reset control code
        $eco_cone = mt_rand($eco_cmin, $eco_cmax);
        $eco_ctwo = mt_rand($eco_cmin, $eco_cmax);

        //** Return to referrer at current comment position
        header("Location: " . $_SERVER['HTTP_REFERER'] . "#Comments");
        exit;
    }
}

//** Check add new comments
if ($eco_anew === 0) {
    //** Clear post delay if set
    $eco_pdel = 0;

    //** Include existing data file
    if (file_exists($eco_data)) {
        include $eco_data;
    }
} else {
    //** Load language selector
    include './lang/__config.php';

    //** Build form
    echo '        <form action="' . $eco_indx . '" method=POST ' .
         'name=eco_form id=Comments accept-charset="UTF-8">' .
         "\n            <div id=eco_stat>$eco_stat</div>\n";

    //** Print header depending data file exists or not
    if (file_exists($eco_data)) {
        echo '            <p><a href="' . $eco_indx . '#Add_Comment" ' .
             'title="' . $eco_lang['add_new'] . '">' .
             $eco_lang['add_comment'] . "</a></p>\n";

        //** Include existing data file
        if (file_exists($eco_data)) {
            include $eco_data;
        }
    } else {
        echo "            <p>" . $eco_lang['first'] . "</p>\n";
    }

    //** Name
    echo "            <p id=Add_Comment>\n                <label for=" .
         'eco_name>' . $eco_lang['name'] . "</label>\n            </p>\n" .
         "            <div>\n                <input name=eco_name " .
         'id=eco_name value="' . $eco_name . '" title="' .
         $eco_lang['text_name'] . '"/>' . "\n            </div>\n";

    //** Text
    echo "            <p>\n                <label for=eco_text>" .
         $eco_lang['text'] . " <small id=eco_ccnt></small></label>\n" .
         "            </p>\n            <div>\n                " .
         "<textarea name=eco_text id=eco_text rows=4 cols=26 " .
         "maxlength=$eco_pmax " . 'title="' . $eco_lang['text_text'] .
         '">' . $eco_text . "</textarea>\n            </div>\n";

    //** Code
    if ($eco_ctrl === 1) {
        echo "            <p>\n                <label for=eco_csum>" .
             $eco_lang['code'] . "</label>\n                $eco_cone" .
             " + $eco_ctwo  = <input name=eco_csum id=eco_csum " .
             'size=4 maxlength=2 title="' . $eco_lang['text_code'] . '"/>' .
             "\n                <input type=hidden name=eco_cone " .
             "value=$eco_cone />\n                <input type=hidden " .
             "name=eco_ctwo value=$eco_ctwo />\n            </p>\n";
    }

    //** Post
    echo "            <p id=eco_tbtn>\n";

    //** Build submit button
    $eco_tbtn = '                <input type=submit name=eco_post ' .
                'value="' . $eco_lang['post'] . '" title="' .
                $eco_lang['text_post'] . '"/>';

    //** Fallback to print submit button when Javascript is disabled
    echo "                <noscript>\n$eco_tbtn\n                " .
         "</noscript>\n            </p>\n";
}

//** Check moderator flag
if ($eco_moda === 1) {
    $eco_mtxt = $eco_lang['mod_require'];
} else {
    $eco_mtxt = $eco_lang['polite'];
}


//** Check add new comments
if ($eco_anew !== 0) {
    echo "            <p><small>$eco_mtxt.</small></p>\n";
}

//** Print footer and close form
echo '        <p><small><a href="https://github.com/phhpro/easy-comments" ' .
     'title="' . $eco_lang['get_copy'] .'">' . $eco_lang['power'] .
     " PHP Easy Comments v$eco_make</a></small></p>\n";

//** Check add new comments
if ($eco_anew !== 0) {
    echo "        </form>\n";
}

//** Javascript for character counter and post delay timer
?>
        <script>
        eco_ccnt = function(eid, cid) {
            var eco_ceid = document.getElementById(eid);
            var eco_ccid = document.getElementById(cid);

            if (!eco_ceid || !eco_ccid) {
                return false
            };

            var eco_cmax = eco_ceid.maxLengh;

            if (!eco_cmax) {
                eco_cmax = eco_ceid.getAttribute('maxlength');
            };

            if (!eco_cmax) {
                return false
            };

            var eco_cdif       = (eco_cmax-eco_ceid.value.length);
            var eco_crem       = " <?php echo $eco_lang['char_count']; ?>";
            eco_ccid.innerHTML = '(' + eco_cdif + eco_crem + ')';
        }

<?php
/**
 * Post delay timer
 * 
 * This is kept out of scope until the condition is met.
 *
 * The timer is triggered as soon as the page is loaded and reset on
 * every reload. This means, if the user manually refreshes the page
 * the timer will start again.
 *
 * If you don't want this you'll need to add a separate condition
 * to handle eco_ptin.
 *
 *
 * eco_pdel = post delay time
 * eco_pbtn = post element where to render output
 * eco_ptin = post timer interval
 */
if ($eco_pdel !== 0) {
?>
        var eco_pdel = <?php echo $eco_pdel; ?>;
        var eco_pbtn = document.getElementById('eco_tbtn');
        var eco_ptin = setInterval(ecoPdel, 1000);

        function ecoPdel() {

            if (eco_pdel == 0) {
                clearTimeout(eco_ptin);
                document.getElementById('eco_tbtn').innerHTML
                    = '            <?php echo $eco_tbtn; ?>';
            } else {
                eco_pbtn.innerHTML
                    = "            <?php echo $eco_lang['post_wait']; ?> " +
                    eco_pdel + " <?php echo $eco_lang['seconds']; ?>" + "...";
                eco_pdel--;
            }
        }
<?php
}
?>
        </script>
