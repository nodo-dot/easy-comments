<?php
/**
 * PHP Version 5 and above
 *
 * PHP Easy Comments is a trivial comments script with minimal bloat.
 *
 * @category PHP_Chat_Scripts
 * @package  PHP_Atom_Chat
 * @author   P H Claus <phhpro@gmail.com>
 * @license  https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @version  GIT: 20180102.5
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
 * Document root -- change if default returns wrong value
 * Script folder
 * Default directory index
 */
$eco_path = $_SERVER['DOCUMENT_ROOT'];
$eco_fold = "/demo/easy-comments/";
$eco_dirx = "index.php";

/**
 * Comments data file
 * Comments log file
 * These are not used in moderator mode
 */
$eco_cdat = "_comments.html";
$eco_clog = $eco_fold . "log.html";

/**
 * Maximum characters for comments text
 * Accept latin text only -- 0 = no, 1 = yes
 * Default anonymous user name
 */
$eco_tmax = 1024;
$eco_lato = 0;
$eco_anon = "anonymous";

/**
 * Send notifications for new comments -- 0 = no, 1 = yes
 * Mail account to receive notifications -- make sure it exists
 * Moderator approval -- 0 = no, 1 = yes (1 requires $eco_note = 1)
 * Control code -- 0 = no, 1 = yes
 *                 Disabling this may well open wide the gates of evil!
 */
$eco_note = 0;
$eco_mail = "info@" . $_SERVER['HTTP_HOST'];
$eco_mapp = 0;
$eco_ctrl = 1;

//** Admin prefix and suffix
$eco_apfx = "YOUR_ADMIN_PREFIX";
$eco_asfx = "root";

/**
 * Query string to list the log file
 * Delay between posts in seconds -- 0 = no delay
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
 * Current page to which the comments apply
 * Global query string
 */
$eco_make = "20180102.5";
$eco_host = $_SERVER['HTTP_HOST'];
$eco_page = $_SERVER['SCRIPT_NAME'];
$eco_qstr = $_SERVER['QUERY_STRING'];

/**
 * Strip default index
 * Comments data file
 * Restricted names data file
 */
$eco_indx = str_replace($eco_dirx, "", $eco_page);
$eco_data = $eco_path . $eco_page . $eco_cdat;
$eco_rest = $eco_path . $eco_fold . "restricted.php";

/**
 * Try to link IP
 * Set mail header
 */
$eco_myip = gethostbyaddr($_SERVER['REMOTE_ADDR']);
$eco_head = "From: PHP Easy Comments <$eco_mail>";

//** Build language reference
if (empty($eco_qstr)) {
    $eco_lref = "lang/en";
} else {
    $eco_lref = str_replace("lang_", "lang/", $eco_qstr);
}

/**
 * Link language data file and try to load it. Obviously the status text
 * for this has no translation because the file has not yet been loaded.
 */
$eco_ldat = $eco_path . $eco_fold . $eco_lref . ".php";

if (file_exists($eco_ldat)) {
    include $eco_ldat;
} else {
    echo "        <p id=eco_stat>Missing language file!</p>\n" .
         "        <p>Please check your settings to correct the " .
         "error.</p>\n" . 
         "        <p>The script will be disabled until the error " .
         "is fixed.</p>\n";
    exit;
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

//** Check permissions
if (!is_writeable($eco_path . $eco_fold)) {
    chmod($eco_path . $eco_fold, 0755);
}

if (!is_writeable($eco_data)) {
    chmod($eco_data, 0644);
}

if (!is_writeable($eco_path . $eco_clog)) {
    chmod($eco_path . $eco_clog, 0644);
}

//** Check empty name
if ($eco_name === "") {
    $eco_name = $eco_anon;
}

//** Check latin only flag
if ($eco_lato === 1) {
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

//** Check whether to list the master log file
if ($_SERVER['QUERY_STRING'] === $eco_list) {

    if (is_file($eco_path . $eco_clog)) {
        header("Location: $eco_prot$eco_host$eco_clog");
        exit;
    } else {
        $eco_stat = $eco_lang['miss_log'];
    }
}

//** Check conflict when moderator mode is enabled without notifications
if ($eco_mapp === 1 && $eco_note === 0) {
    echo "        <p id=eco_stat>" .$eco_lang['mod_flag'] . "</p>\n" .
         "        <p>" . $eco_lang['chk_settings'] . "</p>\n" . 
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

    if (is_file($eco_data)) {
        $eco_post .= file_get_contents($eco_data);
    }

    file_put_contents($eco_data, $eco_post);
    header("Location: " . $_GET['eco_link']);
    exit;
}

//** Link timer session
$_SESSION['eco_tbeg'] = time();
$eco_tbeg             = $_SESSION['eco_tbeg'];

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
    $eco_cval = ($eco_cone+$eco_ctwo);

    //** Substitute anonymous if name is missing or else invalid
    if ($eco_name === "" || preg_match("/^\s*$/", $eco_name)) {
        $eco_name = $eco_anon;
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

    //** Check control code
    if ($eco_ctrl === 1) {

        if ($eco_cval !== (int)$eco_csum) {
            $eco_stat = $eco_lang['invalid_code'];
        }
    }

    //** Check missing text
    if ($eco_text === "") {
        $eco_stat = $eco_lang['miss_text'];
    }

    //** Check latin only
    if ($eco_lato === 1) {

        //** Filter latin only
        $eco_latx = "/[^\\p{Common}\\p{Latin}]/u";

        //** Check name and text
        if (preg_match($eco_latx, $eco_name)
            || preg_match($eco_latx, $eco_text)
        ) {
            $eco_stat = $eco_lang['only_latin'];
        }
    }

    //** Save existing input
    if ($eco_stat !== "") {
        $eco_name = $eco_name;
        $eco_text = $eco_text;
    }

    //** Valid comment
    if ($eco_stat === "") {
        /**
         * Build entry and prepare mail
         * Skip translation for better compatability
         */
        $eco_post = '            <div id=eco_' . gmdate('Ymd_His_') .
                    $eco_myip . '_' . $eco_name . ' class=eco_item>' .
                    $eco_date . ' ' . $eco_name . ' ' . $eco_ukey .
                    ' ' . $eco_text . "</div>\n";
        $eco_subj = "PHP_Easy_Comments_NEW";
        $eco_body = $eco_name . " on " . $eco_prot . $eco_host .
                    $eco_indx . "\n\n" . $eco_post;

        //** Check moderator flag
        if ($eco_mapp !== 1) {

            //** Check existing data file
            if (is_file($eco_data)) {
                $eco_post .= file_get_contents($eco_data);
            }

            //** Update data and log file
            file_put_contents($eco_data, $eco_post);
            $eco_clog = $eco_path . $eco_clog;
            $eco_ulog = '<div>' . $eco_date . ' <a href="' . $eco_indx .
                        '" title="' . $eco_lang['view_item'] . '">' .
                        $eco_indx . "</a></div>\n";

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
                    mail($eco_mail, $eco_subj, $eco_body, $eco_head);
                }
            }

            //** Update timer session
            $_SESSION['eco_tfrm']
                = htmlentities($_POST['eco_tbeg'], ENT_QUOTES, "UTF-8");
        } else {
            //** Build moderator approval link and message body
            $eco_mlnk = $eco_prot . $eco_host . $eco_fold . "?eco_data=" .
                      $eco_data . "&eco_post=" . bin2hex($eco_post) .
                      "&eco_link=" . str_replace($eco_path, "", getcwd());
            $eco_text = $eco_text . "\n\n" . $eco_mlnk;

            //** Send mail and update timer session
            mail($eco_mail, $eco_subj, $eco_body, $eco_head);
            $_SESSION['eco_tfrm']
                = htmlentities($_POST['eco_tbeg'], ENT_QUOTES, "UTF-8");
            $eco_mtxt = $eco_lang['mod_wait'];
        }

        //** Reset control code
        $eco_cone = mt_rand($eco_cmin, $eco_cmax);
        $eco_ctwo = mt_rand($eco_cmin, $eco_cmax);

        //** Return to referrer at current comment position
        header("Location: " . $_SERVER['HTTP_REFERER'] . "#Comments");
        ob_end_flush();
    }
}

//** Language selector
echo "        <div id=eco_lang><small>\n" .
     '            <a href="?lang_en" lang="en-GB" ' .
     'title="Click here to switch to English">EN</a>' . " \n" .
     '            <a href="?lang_de" lang="de-DE" ' .
     'title="Klicken Sie hier um nach Deutsch zu wechseln">DE</a>' . " \n" .
     '            <a href="?lang_es" lang="es-ES" ' .
     'title="Haga clic aquí para cambiar a Español">ES</a>' . " \n" .
     '            <a href="?lang_ar" lang="ar-AE" ' .
     'title="انقر هنا للتبديل إلى اللغة العربية">AR</a>' . " \n" .
     "        </small></div>\n";

//** Form
echo '        <form action="' . $eco_indx . '" ' .
     'method=POST id=Comments accept-charset="UTF-8">' . "\n";
echo "            <div id=eco_stat>$eco_stat</div>\n";

//** Print header depending whether data file exists or not
if (is_file($eco_data)) {
    echo '            <p><a href="' . $eco_indx . '#Add_Comment" ' .
         'title="' . $eco_lang['add_new'] . '">' .
         $eco_lang['add_comment'] . "</a></p>\n";

    //** Include existing data file
    if (is_file($eco_data)) {
          include $eco_data;
    }
} else {
    echo "            <p>" . $eco_lang['be_first'] . "</p>\n";
}

//** Name
echo "            <p id=Add_Comment>\n";
echo '                <label for=eco_name>' . $eco_lang['name'] . "</label>\n";
echo "            </p>\n";
echo "            <div>\n";
echo '                <input name=eco_name id=eco_name value="' .
     $eco_name . '" title="' . $eco_lang['type_name'] . '"/>' . "\n";
echo "            </div>\n";

//** Text
echo "            <p>\n";
echo '                <label for=eco_text>' . $eco_lang['text'] . ' ' .
     "<small id=eco_ccnt></small></label>\n";
echo "            </p>\n";
echo "            <div>\n";
echo '                <textarea name=eco_text id=eco_text rows=4 ' .
     'cols=26 maxlength=' . $eco_tmax . ' title="' .
     $eco_lang['type_text'] . '">' . $eco_text . "</textarea>\n";
echo "            </div>\n";

//** Code
if ($eco_ctrl === 1) {
    echo "            <p>\n";
    echo "                <label for=eco_csum>" . $eco_lang['code'] . "</label>\n";
    echo '                ' . $eco_cone . ' + ' . $eco_ctwo . ' = ' .
         '<input name=eco_csum id=eco_csum size=4 maxlength=2 ' .
         'title="' . $eco_lang['type_code'] . '"/>' . "\n";
    echo "                <input type=hidden name=eco_cone value=$eco_cone />\n";
    echo "                <input type=hidden name=eco_ctwo value=$eco_ctwo />\n";
    echo "                <input type=hidden name=eco_tbeg value=$eco_tbeg />\n";
    echo "            </p>\n";
}
//** Post
echo "            <p id=eco_tbtn>\n";
//** Link timer difference and submit button
$eco_tdif = ($eco_tbeg-$_SESSION['eco_tfrm']);
$eco_tbtn = '                <input type=submit name=eco_post value="' .
            $eco_lang['post'] . '" title="' . $eco_lang['type_post'] . '"/>';

//** Check timer status
if ($eco_tdif >$eco_tdel) {
    echo $eco_tbtn . "\n";
} else {
    echo '            ' . $eco_lang['post_again'] . ' <span id=eco_tdel>' . 
         ($eco_tdel-$eco_tdif) . "</span> " . $eco_lang['seconds'] . ".\n";
    echo '            <noscript>' . $eco_lang['man_refresh'] . "</noscript>\n";
}

echo "            </p>\n";

//** Check moderator flag
if ($eco_mapp === 1) {
    $eco_mtxt = $eco_lang['mod_require'];
} else {
    $eco_mtxt = $eco_lang['be_polite'];
}

echo "            <p><small>$eco_mtxt.</small></p>\n";
echo '            <p><small><a href="https://github.com/phhpro/easy-comments" ' .
     'title="' . $eco_lang['get_copy'] .' PHP Easy Comments">' .
     $eco_lang['powered_by'] . ' PHP Easy Comments v' . $eco_make .
     "</a></small></p>\n";
echo "        </form>\n";
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
            var eco_crem       = " <?php echo $eco_lang['char_rem']; ?>";
            eco_ccid.innerHTML = '(' + eco_cdif + eco_crem + ')';
        }

        setInterval(function() {eco_ccnt('eco_text', 'eco_ccnt')}, 55);

        // Set timer
        var eco_tobj = document.getElementById('eco_tdel');
        var eco_tend = <?php echo $eco_tdel; ?>;
        var eco_tint = setInterval(eco_tdel, 1000);

        // Timer delay
        function eco_tdel() {
            if (eco_tend === 0) {
                document.getElementById('eco_tbtn').innerHTML
                    = '<?php echo $eco_tbtn; ?>';
                clearTimeout(eco_tint);
            } else {
                eco_tobj.innerHTML = eco_tend;
                eco_tend --;
            }
        }
        </script>
