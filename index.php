<?php
/**
 * PHP Version 5 and above
 *
 * Easy Comments is a trivial comments script with minimal bloat.
 *
 * @category PHP_Chat_Scripts
 * @package  PHP_Atom_Chat
 * @author   P H Claus <phhpro@gmail.com>
 * @license  https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @version  GIT: 20171220
 * @link     https://github.com/phhpro/easy-comments
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


/**
 * Script folder
 * Default directory index
 * Comments data file -- not used with manual approval
 * Comments log file  -- not used whit manual approval
 */
$eco_fold = "/easy-comments/";
$eco_dirx = "index.php";
$eco_cdat = "_comments.html";
$eco_clog = $eco_fold . "log.html";

/**
 * Maximum characters for comments text
 * Accept latin characters only -- 0 disable, 1 enable
 * Default anonymous user name
 */
$eco_tmax = 1024;
$eco_lato = 1;
$eco_anon = "anonymous";

/**
 * Send notifications for new comments -- 0 disable, 1 enable
 * Mail account to receive notifications -- make sure it exists
 * Moderator approval -- 0 disable, 1 enable (1 requires $eco_note = 1)
 */
$eco_note = 0;
$eco_mail = "info@" . $_SERVER['HTTP_HOST'];
$eco_mapp = 0;

//** Admin prefix and suffix
$eco_apfx = "YOUR_ADMIN_PREFIX";
$eco_asfx = "root";

/**
 * Query string to list log file
 * Delay between posts in seconds -- 0 to disable
 * Date and time format
 */
$eco_list = "YOUR_LIST_TOKEN";
$eco_tdel = 60;
$eco_date = gmdate('Y-m-d H:i:s');


/**
 ***********************************************************************
 *                                                     END USER CONFIG *
 ***********************************************************************
 */


/**
 * Script version
 * Host on which the script is running
 * Current page to which the comment applies
 * Strip default index
 * Comments data file
 * Restricted names data file
 * Try to link user IP
 * Mail header
 */
$eco_make = 20171230;
$eco_host = $_SERVER['HTTP_HOST'];
$eco_page = $_SERVER['SCRIPT_NAME'];
$eco_indx = str_replace($eco_dirx, "", $eco_page);
$eco_data = $_SERVER['DOCUMENT_ROOT'] . $eco_page . $eco_cdat;
$eco_rest = $_SERVER['DOCUMENT_ROOT'] . $eco_fold . "restricted.php";
$eco_myip = gethostbyaddr($_SERVER['REMOTE_ADDR']);
$eco_head = "From: Easy Comments <$eco_mail>";

//** Captcha code range and value min, max
$eco_cmin = 1;
$eco_cmax = 9;
$eco_cone = mt_rand($eco_cmin, $eco_cmax);
$eco_ctwo = mt_rand($eco_cmin, $eco_cmax);

//** Init placeholders
$eco_text = "";
$eco_name = "";
$eco_stat = "";
$eco_latb = "";

//** Check empty user name
if ($eco_name === "") {
    $eco_name = $eco_anon;
}

//** Check latin only label
if ($eco_lato === 1) {
    $eco_latb = "Latin ";
}

//** Check protocol
if (isset($_SERVER['HTTPS']) && "on" === $_SERVER['HTTPS']) {
    $eco_prot = "s";
} else {
    $eco_prot = "";
}

$eco_prot = "http" . $eco_prot . "://";

//** Check whether to list log file
if ($_SERVER['QUERY_STRING'] === $eco_list) {

    if (is_file($_SERVER['DOCUMENT_ROOT'] . $eco_clog)) {
        header("Location: $eco_prot$eco_host$eco_clog");
        exit;
    } else {
        $eco_stat = "Missing log file!";
    }
}

/**
 * Function eco_post
 *
 * @param string $eco_goto redirect
 *
 * @return string url
 */
function ecoPost($eco_goto) 
{

    if (!headers_sent()) {    
        header("Location: $eco_goto");
        exit;
    } else {
        echo "    <meta http-equiv=\"refresh\" content=\"0; url=$eco_goto\"/>";
    }
}

//** Check session with pre 5.4 fallback
if (version_compare(phpversion(), "5.4.0", ">=") !== false) {

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
} else {

    if (session_id() === "") {
        session_start();
    }
}

//** Process manual approvals -- gets values from mail link
if (isset($_GET['eco_data']) && $_GET['eco_data'] !== "" 
    && isset($_GET['eco_post']) && $_GET['eco_post'] !== "" 
    && isset($_GET['eco_link']) && $_GET['eco_link'] !== ""
) {
    $eco_data = $_GET['eco_data'];
    $eco_post = hex2bin($_GET['eco_post']);

    if (is_file($eco_data)) {
        $eco_post .= file_get_contents($eco_data);
    }

    file_put_contents($eco_data, $eco_post);
    header("Location: " . $_GET['eco_link']);
    exit;
}

//** Link session
$_SESSION['eco_tbeg'] = time();
$eco_tbeg = $_SESSION['eco_tbeg'];

//** Form submitted
if (isset($_POST['eco_post'])) {

    //** Filter name, text, and common URL protocols
    $eco_name = htmlentities($_POST['eco_name'], ENT_QUOTES, "UTF-8");
    $eco_text = htmlentities($_POST['eco_text'], ENT_QUOTES, "UTF-8");
    $eco_urls = array('http', 'https', 'ftp', 'ftps');

    foreach ($eco_urls as $eco_link) {
        $eco_text = str_replace($eco_link . "://", "", $eco_text);
    }

    //** Link captcha code
    $eco_csum = $_POST['eco_csum'];
    $eco_cone = $_POST['eco_cone'];
    $eco_ctwo = $_POST['eco_ctwo'];
    $eco_cval = ($eco_cone + $eco_ctwo);

    //** Substitute anon if name is empty or invalid
    if ($eco_name === "" || preg_match("/^\s*$/", $eco_name)) {
        $eco_name = $eco_anon;
    }

    //** Check if name is alpha only
    if (preg_match("/^[a-zA-Z]+$/", $eco_name) !== 1) {

        //** Exclude admin post
        if ($eco_name !== $eco_apfx . $eco_asfx) {
            $eco_stat = "Name contains invalid characters!";
        }
    }

    //** Link restricted names
    if (is_file($eco_rest)) {
        include $eco_rest;
    }

    //** Append user flag
    if ($eco_name === $eco_apfx . $eco_asfx) {
        $eco_name = $eco_asfx;
        $eco_ukey = "#";
        $eco_mapp = 0;
    } else {
        $eco_name = $eco_name;
        $eco_ukey = "$";
    }

    //** Check captcha code
    if ($eco_cval !== (int)$eco_csum) {
        $eco_stat = "Invalid verification code!";
    }

    //** Check missing text
    if ($eco_text === "") {
        $eco_stat = "Cannot post empty comment!";
    }

    //** Check latin only
    if ($eco_lato === 1) {

        //** Filter latin only
        $eco_latx = "/[^\\p{Common}\\p{Latin}]/u";

        //** Check name and text
        if (preg_match($eco_latx, $eco_name)
            || preg_match($eco_latx, $eco_text)
        ) {
            $eco_stat = "Only latin characters allowed!";
        }
    }

    //** Check maximum characters
    if (strlen($eco_text) >$eco_tmax) {
        $eco_clen = strlen($eco_text);
        $eco_cfix = ($eco_clen - $eco_tmax);
        $eco_stat = "$eco_cfix characters have been trimmed!";
        $eco_text = substr($eco_text, 0, $eco_tmax);
    }

    //** Save existing input and regenerate captcha
    if ($eco_stat !== "") {
        $eco_name = $eco_name;
        $eco_text = $eco_text;
        $eco_cone = mt_rand($eco_cmin, $eco_cmax);
        $eco_ctwo = mt_rand($eco_cmin, $eco_cmax);
    }

    //** Valid comment
    if ($eco_stat === "") {

        //** Build comments entry and prepare message
        $eco_post = '        <div id=eco_' . gmdate('Ymd_His_') .
                    $eco_myip . "_" . $eco_name . ' class=eco_item>' .
                    $eco_date . " " . $eco_name . " " . $eco_ukey .
                    " " . $eco_text . "</div>\n";

        $eco_subj = "New_Comment";
        $eco_text = $eco_name . " regarding " . $eco_prot . $eco_host .
                    $eco_indx . "\n\n" . $eco_post;

        //** Check moderator flag
        if ($eco_mapp !== 1) {

            //** Check existing data file
            if (is_file($eco_data)) {
                $eco_post .= file_get_contents($eco_data);
            }

            //** Update data file, link log file, and build log entry
            file_put_contents($eco_data, $eco_post);
            $eco_clog = $_SERVER['DOCUMENT_ROOT'] . $eco_clog;

            $eco_ulog = '<div>' . $eco_date . ' <a href="' .
                    $eco_prot . $eco_host . $eco_indx . '" title=' .
                    '"Click here to open the selected resource">' .
                    $eco_host . $eco_indx . "</a></div>\n";

            //** Check existing log file
            if (is_file($eco_clog)) {
                $eco_ulog .= file_get_contents($eco_clog);
            }

            //** Update log file
            file_put_contents($eco_clog, $eco_ulog);

            //** Check if user post
            if ($eco_name !== $eco_asfx) {

                //** Check notification flag and send mail
                if ($eco_note === 1) {
                    mail($eco_mail, $eco_subj, $eco_text, $eco_head);
                }
            }

            //** Link timer session and try to catch resubmission
            $_SESSION['eco_tfrm']
                = htmlentities($_POST["eco_tbeg"], ENT_QUOTES, "UTF-8");

            ecoPost($eco_prot . $eco_host . $eco_indx . "#Comments");
        } else {
            /**
           * Build manual approval link
           * Merge body and params
           * Send message
           * Link timer session
           * Try to catch resubmission
           */
            $eco_mlnk = $eco_prot . $eco_host . $eco_fold . "?eco_data=" .
                      $eco_data . "&eco_post=" . bin2hex($eco_post) .
                      "&eco_link=" .
                      str_replace($_SERVER['DOCUMENT_ROOT'], "", getcwd());

            $eco_text = $eco_text . "\n\n" . $eco_mlnk;
            mail($eco_mail, $eco_subj, $eco_text, $eco_head);

            $_SESSION['eco_tfrm']
                = htmlentities($_POST["eco_tbeg"], ENT_QUOTES, "UTF-8");

            ecoPost($eco_prot . $eco_host . $eco_indx . "#Comments");
            $eco_mtxt = "Thank you. Your message is awaiting moderation.";
        }
    }
}

//** Check conflict when moderator is on but notifications are off
if ($eco_mapp === 1 && $eco_note === 0) {
    echo "    <p id=eco_stat>Easy Comments Error</p>\n";
    echo "    <p>The moderator flag is set but notifications are disabled! " .
         "Please edit eco_note = 1 to enable notifications.</p>\n";
} else {
    echo '    <form action="' . $eco_prot . $eco_host . $eco_indx . 
         '#Comments" method=POST id=Comments accept-charset="UTF-8">' . "\n";
    echo "        <div id=eco_stat>" . $eco_stat . "</div>\n";

    //** Print header depending whether data file exists or not
    if (is_file($eco_data)) {
        echo '        <p id=eco_main><a href="' .
              $eco_prot . $eco_host . $eco_indx . '#Add_Comment" title="' .
              'Click here to add new comment">Add Comment</a></p>' . "\n";

        //** Include existing data file
        if (is_file($eco_data)) {
              include $eco_data;
        }
    } else {
        echo '        <p id=eco_main>No comments yet. ' .
             "Be the first to share your thoughts.</p>\n";
    }

    echo "        <p id=Add_Comment>\n";
    echo '          <label for=eco_name>Name</label> ' .
         "<small>(A-Z $eco_latb only)</small>\n";
    echo "        </p>\n";
    echo "        <div>\n";
    echo '          <input name=eco_name id=eco_name value="' . $eco_name . 
         '" title="Type here to enter your name or leave blank to post ' .
         'anonymous"/>' . "\n";
    echo "        </div>\n";
    echo "        <p>\n";
    echo "          <label for=eco_text>Text <small id=eco_ccnt></small></label>\n";
    echo "        </p>\n";
    echo "        <div>\n";
    echo '          <textarea name=eco_text id=eco_text rows=4 cols=26 ' .
         'maxlength=' . $eco_tmax . ' title="Type here to enter the text ' .
         'of your comment">' . $eco_text . "</textarea>\n";
    echo "        </div>\n";
    echo "        <p>\n";
    echo "          <label for=eco_csum>Code</label>\n";
    echo '        ' . $eco_cone . ' + ' . $eco_ctwo . ' = ' .
         '<input name=eco_csum id=eco_csum size=4 maxlength=2 ' .
         'title="Type here to enter the verification code"/>' . "\n";
    echo "          <input type=hidden name=eco_cone value=$eco_cone />\n";
    echo "          <input type=hidden name=eco_ctwo value=$eco_ctwo />\n";
    echo "          <input type=hidden name=eco_tbeg value=$eco_tbeg />\n";
    echo "        </p>\n";
    echo "        <p id=eco_tbtn>\n";

    //** Link timer difference and mark-up
    $eco_tdif = ($eco_tbeg-$_SESSION['eco_tfrm']);
    $eco_tbtn = '          <input type=submit name=eco_post value=Post ' .
                'title="Click here to post your comment"/>';

    //** Check timer status
    if ($eco_tdif>$eco_tdel) {
        echo $eco_tbtn . "\n";
    } else {
        echo "        Please wait <span id=eco_tdel>" . ($eco_tdel-$eco_tdif) .
             "</span> seconds before posting again.\n";
        echo "        <noscript>Refresh this page to update the timer status." .
             "</noscript>\n";
    }

    echo "        </p>\n";

    //** Check moderator flag
    if ($eco_mapp === 1) {
        $eco_mtxt = "New posts require moderator approval";
    } else {
        $eco_mtxt = "All posts are monitored and subject to removal";
    }

    echo "        <p><small>$eco_mtxt.</small></p>\n";
    echo '        <p><small><a href="https://github.com/phhpro/easy-comments" ' .
         'title="Click here to get a free copy of PHP Easy Comments">Powered ' .
         'by PHP Easy Comments v' . $eco_make . "</a></small></p>\n";
    echo "    </form>\n";
}
?>
    <script>
    // Character counter
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
        eco_ccid.innerHTML = eco_cdif + ' characters remaining';
    }

    setInterval(function() { eco_ccnt('eco_text', 'eco_ccnt') }, 55);

    // Set timer
    var eco_tend = <?php echo $eco_tdel; ?>;
    var eco_tobj = document.getElementById('eco_tdel');
    var eco_tint = setInterval(eco_tdel, 1000);

    // Timer delay counter
    function eco_tdel() {
        if (eco_tend == 0) {
            document.getElementById('eco_tbtn').innerHTML
                = '<?php echo $eco_tbtn; ?>';
            clearTimeout(eco_tint);
        } else {
            eco_tobj.innerHTML = eco_tend;
            eco_tend --;
        }
    }
    </script>
