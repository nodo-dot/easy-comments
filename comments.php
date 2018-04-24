<?php
/**
 * PHP Version 5 and above
 *
 * Main script
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
 * Script version
 * Default user
 * Page to which comments apply
 * Query string
 */
$eco_make = 20180424;
$eco_user = "anonymous";
$eco_page = $_SERVER['SCRIPT_NAME'];
$eco_qstr = $_SERVER['QUERY_STRING'];

/**
 * Comments data
 * Comments log
 */
$eco_cdat = "_comments.html";
$eco_clog = $eco_fold . "log.html";

/**
 * Strip default index
 * Link comments data
 * Restricted names data
 * Link IP -- for what it's worth anyway
 */
$eco_indx = str_replace($eco_dirx, "", $eco_page);
$eco_data = $eco_path . $eco_page . $eco_cdat;
$eco_rest = $eco_path . $eco_fold . "restricted.php";
$eco_myip = gethostbyaddr($_SERVER['REMOTE_ADDR']);

//** Link language file and session
if (isset($_POST['eco_lang_apply']) && $_POST['eco_lang'] !== "none") {
    $eco_lref = htmlentities($_POST['eco_lang'], ENT_QUOTES, "UTF-8");
} else {
    $eco_lref = "en";
}

session_start();
$_SESSION['eco_lang'] = $eco_lref;
$eco_lsrc             = "lang/" . $_SESSION['eco_lang'] . ".php";
$eco_ldat             = $eco_path . $eco_fold . $eco_lsrc;

//** Load language file -- static because translation not available yet
if (file_exists($eco_ldat)) {
    include $eco_ldat;
} else {
    echo "        <p id=eco_stat>Missing language file!</p>\n" .
         "        <p>Please check your settings.</p>\n" .
         "        <p>Script disabled until error is fixed.</p>\n";
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

//** Check Latin only
if ($eco_plat === 1) {
    $eco_latb = "Latin ";
}

//** Check and link protocol and host
if (isset($_SERVER['HTTPS']) && "on" === $_SERVER['HTTPS']) {
    $eco_prot = "s";
} else {
    $eco_prot = "";
}

$eco_prot = "http" . $eco_prot . "://";
$eco_host = $eco_prot . $_SERVER['HTTP_HOST'];

//** Check upload folder
if ($eco_up === 1) {
    $eco_up_fold = $eco_path . $eco_fold . $eco_up_fold;

    if (!is_dir($eco_up_fold)) {

        if (mkdir($eco_up_fold) === false) {
            echo $eco_lang['up_nofold'];
            exit;
        }
    }
}

//** Check whether to list the log
if ($_SERVER['QUERY_STRING'] === $eco_list) {

    if (file_exists($eco_path . $eco_clog)) {
        header("Location: $eco_host$eco_clog");
        exit;
    } else {
        $eco_stat = $eco_lang['log_err'];
    }
}

//** Check missing notifications in moderator mode
if ($eco_moda === 1 && $eco_note === 0) {
    echo "        <p id=eco_stat>" .$eco_lang['mod_err'] . "</p>\n" .
         "        <p>" . $eco_lang['stop_check'] . "</p>\n" . 
         "        <p>" . $eco_lang['stop_fix'] . "</p>\n";
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

    //** Build log entry
    $eco_clog = $eco_path . $eco_clog;
    $eco_ulog = '<div><a href="' . $eco_page . '#' . $eco_peid .
                '" title="' . $eco_lang['view'] . '">' .
                "$eco_date =&gt; $eco_page =&gt; $eco_peid</a></div>\n";

    //** Check existing log
    if (file_exists($eco_clog)) {
        $eco_ulog .= file_get_contents($eco_clog);
    }

    /**
     * Update log and load target page
     * No white-listing here because this comes from admin
     */
    file_put_contents($eco_clog, $eco_ulog);
    header("Location: " . $_GET['eco_link']);
    exit;
}

//** Check upload
if ($eco_up === 1) {

    //** Get size, trim name, and link file
    $eco_up_size = $_FILES['eco_file']['size'];
    $eco_up_base = basename($_FILES['eco_file']['name']);
    $eco_up_file = $eco_up_fold . "/" . $eco_up_base;

    //** Link type
    $eco_up_type
        = strtolower(pathinfo($eco_up_file, PATHINFO_EXTENSION));

    //** Link URL
    $eco_up_open
        = $eco_host . str_replace($eco_path, "", $eco_up_file);

    //** Init error status
    $eco_up_fail = 1;
}

//** Form submitted
if (isset($_POST['eco_post'])) {

    //** Filter name and text, and defuse common protocols
    $eco_name = htmlentities($_POST['eco_name'], ENT_QUOTES, "UTF-8");
    $eco_text = htmlentities($_POST['eco_text'], ENT_QUOTES, "UTF-8");
    $eco_urls = array("http", "https", "ftp", "ftps");

    //** Strip protocol
    foreach ($eco_urls as $eco_link) {
        $eco_text = str_replace($eco_link . "://", "", $eco_text);
    }

    //** Link control code
    $eco_csum = htmlentities($_POST['eco_csum'], ENT_QUOTES, "UTF-8");
    $eco_cone = htmlentities($_POST['eco_cone'], ENT_QUOTES, "UTF-8");
    $eco_ctwo = htmlentities($_POST['eco_ctwo'], ENT_QUOTES, "UTF-8");
    $eco_cval = ((int)$eco_cone+(int)$eco_ctwo);

    //** Substitute anonymous if name is missing or invalid
    if ($eco_name === "" || preg_match("/^\s*$/", $eco_name)) {
        $eco_name = $eco_user;
    }

    //** Link restricted names
    if (file_exists($eco_rest)) {
        include $eco_rest;
    }

    //** Flag post as user or admin
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
            $eco_code = 0;
            $eco_stat = $eco_lang['code_err'];
        }
    }

    //** Check missing text
    if ($eco_text === "" && $eco_up !== 1) {
        $eco_stat = $eco_lang['post_err'];
    }

    //** Check Latin only
    if ($eco_plat === 1) {
        $eco_latx = "/[^\\p{Common}\\p{Latin}]/u";

        if (preg_match($eco_latx, $eco_name)
            || preg_match($eco_latx, $eco_text)
        ) {
            $eco_stat = $eco_lang['latin'];
        }
    }

    //** Store existing input
    if ($eco_stat !== "") {
        $eco_name = $eco_name;
        $eco_text = $eco_text;
    }

    //** Check upload
    if ($eco_up === 1) {

        //** Check selection
        if (!empty($_FILES['eco_file']['name'])) {

            //** Link file
            $eco_up_mime
                = getimagesize($_FILES['eco_file']['tmp_name']);

            //** Check if file exists
            if (file_exists($eco_up_file)) {
                $eco_stat    = $eco_lang['up_exist'];
                $eco_up_fail = 0;
            }

            //** Check size
            if ($eco_up_size >$eco_up_max) {
                $eco_stat    = $eco_lang['up_exceed'];
                $eco_up_fail = 0;
            }

            //** Check image type
            if (in_array($eco_up_type, $eco_up_img)) {

                //** Check valid image and build entry
                if ($eco_up_mime !== false) {
                    $eco_up_link = $eco_lang['up_link'] .
                                   ' <a href="' . $eco_up_open . '" ' .
                                   'title="' . $eco_lang['up_open'] .
                                   '">' . $eco_up_base . " (" .
                                   $eco_up_size . " " .
                                   $eco_lang['up_byte'] . ")</a><br/>" . 
                                   '<a href="' . $eco_up_open . '" ' .
                                   'title="' . $eco_lang['up_open'] .
                                   '"><img src="' . $eco_up_open .
                                   '" width=' . $eco_up_tnw . ' ' .
                                   'height=' . $eco_up_tnh . ' ' .
                                   'alt=""/></a>';
                } else {
                    $eco_stat    = $eco_lang['up_noimg'];
                    $eco_up_fail = 0;
                }
            } elseif (
                //** Check non-image types and build entry
                in_array($eco_up_type, $eco_up_arc)
                || in_array($eco_up_type, $eco_up_doc)
                || in_array($eco_up_type, $eco_up_snd)
                || in_array($eco_up_type, $eco_up_vid)
            ) {
                $eco_up_link = $eco_lang['up_link'] .
                               ' <a href="' . $eco_up_open . '" ' .
                               'title="' . $eco_lang['up_open'] . '">' .
                               $eco_up_base . " (" .
                               $eco_up_size . " " .
                               $eco_lang['up_byte'] . ")</a>";
            } else {
                $eco_stat    = $eco_lang['up_notype'];
                $eco_up_fail = 0;
            }

            //** Update error status
            if ($eco_up_fail === 0) {
                $eco_stat = $eco_lang['up_fail'] . " " . $eco_stat;
            } else {

                //** Check control code
                if ($eco_code !== 0) {

                    //** Finalise upload
                    if (
                        move_uploaded_file(
                            $_FILES['eco_file']['tmp_name'],
                            $eco_up_file
                        )
                    ) {
                        //** Link reference and clear temp file
                        $eco_up_text = $eco_up_link;
                        unlink($eco_up_base);
                    } else {
                        $eco_stat = $eco_lang['up_nomove'];
                    }
                }
            }
        }
    }

    //** Valid comment
    if ($eco_stat === "") {

        //** Check upload
        if ($eco_up === 1 && $eco_up_text !== "") {
            $eco_text = "$eco_text<br/>$eco_up_text";
        } else {
            $eco_text = $eco_text;
        }

        //** Generate permalink and build entry
        $eco_peid = md5(gmdate('YmdHis') . $eco_myip . $eco_name);
        $eco_post = '            <div id="' . $eco_peid . '" ' .
                    "class=eco_item>" . $eco_date . " " . $eco_name .
                    " " . $eco_ukey . " " . $eco_text;

        //** Check whether to print permalink
        if ($eco_perm === 1) {
            $eco_post .= ' <div class=eco_perm><a href="' . $eco_host .
                         $eco_page . "#" . $eco_peid . '" ' .
                         "title=Permalink>ID: $eco_peid</a></div>";
        }

        $eco_post .= "</div>\n";

        //** Build mail body
        $eco_mbod = $eco_name . " on " . $eco_host .
                    $eco_indx . "\n\n" . $eco_post;

        //** Check moderator mode
        if ($eco_moda !== 1) {

            //** Check existing data
            if (file_exists($eco_data)) {
                $eco_post .= file_get_contents($eco_data);
            }

            //** Update data and log
            file_put_contents($eco_data, $eco_post);
            $eco_clog = $eco_path . $eco_clog;
            $eco_ulog = '<div><a href="' . $eco_page . '#' .
                        $eco_peid . '" ' .
                        'title="' . $eco_lang['view'] . '">' .
                        "$eco_date =&gt; $eco_page =&gt; " .
                        "$eco_peid</a></div>\n";

            //** Check existing log
            if (file_exists($eco_clog)) {
                $eco_ulog .= file_get_contents($eco_clog);
            }

            //** Update log
            file_put_contents($eco_clog, $eco_ulog);

            //** Check if post by user or admin
            if ($eco_name !== $eco_asfx) {

                //** Check whether to send notification
                if ($eco_note === 1) {
                    mail($eco_mato, $eco_msub, $eco_mbod, $eco_mhdr);
                }
            }
        } else {
            //** Build moderator mail link
            $eco_mlnk = $eco_host . $eco_fold .
                        "?eco_data=" . $eco_data .
                        "&eco_post=" . bin2hex($eco_post) .
                        "&eco_link=" .
                        str_replace($eco_path, "", getcwd());

            //** Build moderator mail body
            $eco_mbod = $eco_mbod . "\n\nClick the below link to " .
                        "approve the post and publish or just " .
                        "ignore this mail to dismiss the post " .
                        "without publishing.\n\n" . $eco_mlnk;

            //** Try to send mail -- SILENT -- make sure mail works!
            mail($eco_mato, $eco_msub, $eco_mbod, $eco_mhdr);
        }

        //** Reset control code
        $eco_cone = mt_rand($eco_cmin, $eco_cmax);
        $eco_ctwo = mt_rand($eco_cmin, $eco_cmax);

        //** Load page at current position
        header("Location: " . $_SERVER['HTTP_REFERER'] . "#Comments");
        exit;
    }
}

//** Check whether to list allowed file types
if ($eco_qstr === $eco_up_inf) {

    //** Summary
    echo "        <h2>" . $eco_lang['up_info'] . "</h2>\n" .
         "        <p>" . $eco_lang['up_max'] . " " .
         $eco_up_max . "</p>\n" .
         "        <h3>" . $eco_lang['up_allow'] . "</h3>\n" .

         //** Document
         "        <ul>\n" .
         "            <li><strong>" .
         $eco_lang['up_is_doc'] . "</strong>\n" .
         "                <ul>\n";

    foreach ($eco_up_doc as $eco_up_doc_item) {
        echo "                    <li>$eco_up_doc_item</li>\n";
    }

    unset($eco_up_doc_item);
    echo "                </ul>\n" .
         "            </li>\n" .
         "        </ul>\n" .

         //** Image
         "        <ul>\n" .
         "            <li><strong>" .
         $eco_lang['up_is_img'] . "</strong>\n" .
         "                <ul>\n";

    foreach ($eco_up_img as $eco_up_img_item) {
        echo "                    <li>$eco_up_img_item</li>\n";
    }

    unset($eco_up_img_item);
    echo "                </ul>\n" .
         "            </li>\n" .
         "        </ul>\n" .

         //** Sound
         "        <ul>\n" .
         "            <li><strong>" .
         $eco_lang['up_is_snd'] . "</strong>\n" .
         "                <ul>\n";

    foreach ($eco_up_snd as $eco_up_snd_item) {
        echo "                    <li>$eco_up_snd_item</li>\n";
    }

    unset($eco_up_snd_item);
    echo "                </ul>\n" .
         "            </li>\n" .
         "        </ul>\n" .

         //** Video
         "        <ul>\n" .
         "            <li><strong>" .
         $eco_lang['up_is_vid'] . "</strong>\n" .
         "                <ul>\n";

    foreach ($eco_up_vid as $eco_up_vid_item) {
        echo "                    <li>$eco_up_vid_item</li>\n";
    }

    unset($eco_up_vid_item);
    echo "                </ul>\n" .
         "            </li>\n" .
         "        </ul>\n" .

         //** Archive
         "        <ul>\n" .
         "            <li><strong>" .
         $eco_lang['up_is_arc'] . "</strong>\n" .
         "                <ul>\n";

    foreach ($eco_up_arc as $eco_up_arc_item) {
        echo "                    <li>$eco_up_arc_item</li>\n";
    }

    unset($eco_up_arc_item);
    echo "                </ul>\n" .
         "            </li>\n" .
         "        </ul>\n" .

         //** Close list
         '        <p><a href="' . $eco_indx . '" ' .
         'title="' . $eco_lang['up_close_t'] . '">' .
         $eco_lang['up_close'] . '</a></p>' . "\n";
}

//** Check whether to allow new comments
if ($eco_anew === 0) {

    //** Include existing data file
    if (file_exists($eco_data)) {
        include $eco_data;
    }

    echo "        <p><small>" . $eco_lang['closed'] . "</small></p>\n";
} else {
    //** Language selector
    echo '        <form action="' . $eco_indx . '" method=POST ' .
         'id=Comments accept-charset="UTF-8" ' .
         'enctype="multipart/form-data">' . "\n" .
         "            <div id=eco_lang>\n" .
         "                <select name=eco_lang " .
         'title="' . $eco_lang['lang_title'] . '">' . "\n" .
         "                    <option value=none " .
         'title="' . $eco_lang['lang_title'] . '">' .
         $eco_lang['lang'] . "</option>\n";

    //** Parse language folder
    $eco_lang_fold = $eco_path . $eco_fold . "lang/";
    $eco_lang_list = glob($eco_lang_fold . "*.php");
    sort($eco_lang_list);

    foreach ($eco_lang_list as $eco_lang_item) {

        //** Link source
        $eco_lang_file = file_get_contents($eco_lang_item);
        $eco_lang_line = file($eco_lang_item);

        /**
         * Line $eco_lang_line[20] => #21 $eco_lang['__name__']
         * Line $eco_lang_line[21] => #22 $eco_lang[__text__]
         *
         * Moving those lines in language file __WILL__ break things !!
         */

        //** Trim name
        $eco_lang_name = $eco_lang_line[20];
        $eco_lang_name = str_replace(
            "\$eco_lang['__name__']   = \"", "", $eco_lang_name
        );
        $eco_lang_name = str_replace("\";\n", "", $eco_lang_name);

        //** Trim text
        $eco_lang_text = $eco_lang_line[21];
        $eco_lang_text = str_replace(
            "\$eco_lang['__text__']   = \"", "", $eco_lang_text
        );
        $eco_lang_text = str_replace("\";\n", "", $eco_lang_text);

        //** Trim link
        $eco_lang_link = basename($eco_lang_item);
        $eco_lang_link = str_replace(".php", "", $eco_lang_link);

        echo "                    <option " .
             'value="' . $eco_lang_link . '" ' .
             'title="' . $eco_lang_text . '">' .
             $eco_lang_name . "</option>\n";
    }

    unset($eco_lang_item);
    echo "                </select>\n" .    
         "                <input type=submit name=eco_lang_apply " .
         'class=eco_post value="' . $eco_lang['ok'] . '" ' .
         'title="' . $eco_lang['ok_title'] . '"/>' . "\n" .
         "            </div>\n" .

    //** Status
         "            <div id=eco_stat>$eco_stat</div>\n";

    //** Print header depending if data exists or not
    if (file_exists($eco_data)) {
        echo '            <p><a href="' . $eco_indx .
             '#Add_Comment" title="' . $eco_lang['add_title'] . '">' .
             $eco_lang['add'] . "</a></p>\n";

        //** Include existing data
        if (file_exists($eco_data)) {
            include $eco_data;
        }
    } else {
        echo "            <p>" . $eco_lang['first'] . "</p>\n";
    }

    //** Name
    echo "            <p id=Add_Comment>\n" .
         "                <label for=eco_name>" .
         $eco_lang['name'] . "</label>\n" .
         "            </p>\n" .
         "            <div>\n" .
         "                <input name=eco_name id=eco_name " .
         'class=eco_text value="' . $eco_name . '" ' .
         'title="' . $eco_lang['name_title'] . '"/>' . "\n" .
         "            </div>\n";

    //** Text
    echo "            <p>\n" .
         "                <label for=eco_text>" .
         $eco_lang['text'] . " <small id=eco_ccnt></small></label>\n" .
         "            </p>\n" .
         "            <div>\n" .
         "                <textarea name=eco_text id=eco_text " .
         "class=eco_area rows=4 cols=34 maxlength=$eco_pmax " .
         'title="' . $eco_lang['text_title'] .'">' . $eco_text .
         "</textarea>\n" .
         "            </div>\n" .
         "            <p>\n";

    //** Upload
    if ($eco_up === 1) {
        echo "                <p>\n" .
             "                    <input type=file name=eco_file " .
             'title="' . $eco_lang['up_select'] . '"/>' . "\n" .
             "                    <div><small>" .
             '<a href="' . $eco_fold . '?' . $eco_up_inf . '" ' .
             'title="' . $eco_lang['up_info_t'] . '">' .
             $eco_lang['up_info'] . "</a></small></div>\n" .
             "                </p>\n";
    }

    //** Control code
    if ($eco_ctrl === 1) {
        echo "                <label for=eco_csum>" .
             $eco_lang['code'] . "</label>\n" .
             "                $eco_cone + $eco_ctwo = " .
             "<input name=eco_csum id=eco_csum size=4 maxlength=2 " .
             'title="' . $eco_lang['code_title'] . '"/>' . "\n" .
             "                <input type=hidden name=eco_cone " .
             "value=$eco_cone />\n" .
             "                <input type=hidden name=eco_ctwo " .
             "value=$eco_ctwo />\n";
    }

    //** Post comment submit button
    echo '                <input type=submit name=eco_post ' .
         'class=eco_post value="' . $eco_lang['post'] . '" ' .
         'title="' . $eco_lang['post_title'] . '"/>' . "\n" .
         "            </p>\n";

    //** Check moderator flag
    if ($eco_moda === 1) {
        $eco_mtxt = $eco_lang['mod_app'];
    } else {
        $eco_mtxt = $eco_lang['polite'];
    }

    echo "            <p><small>$eco_mtxt.</small></p>\n" .
         "        </form>\n" .

    //** Footer
         "        <p><small>" .
         '<a href="https://github.com/phhpro/easy-comments" ' .
         'title="' . $eco_lang['get'] .'">' . $eco_lang['by'] .
         " PHP Easy Comments v$eco_make</a></small></p>\n" .

    //** Javascript character counter
         "        <script>\n" .
         "        eco_char = function(eid, cid) {\n" .
         "            var eco_text\n" .
         "                = document.getElementById(eid);\n" .
         "            var eco_ccnt\n" .
         "                = document.getElementById(cid);\n\n" .
         "            if (!eco_text || !eco_ccnt) {\n" .
         "                return false\n" .
         "            };\n\n" .
         "            var eco_cmax = eco_text.maxLength;\n\n" .
         "            if (!eco_cmax) {\n" .
         "                eco_cmax\n" .
         "                    = eco_text.getAttribute('maxlength');\n" .
         "            };\n\n" .
         "            if (!eco_cmax) {\n" .
         "                return false\n" .
         "            };\n\n" .
         "            var eco_cdif\n" .
         "                = (eco_cmax-eco_text.value.length);\n" .
         "            var eco_crem\n" .
         "                = \" " . $eco_lang['remain'] . "\";\n" .
         "            eco_ccnt.innerHTML\n" .
         "                = '(' + eco_cdif + eco_crem + ')';\n" .
         "        }\n" .
         "        setInterval(function() {\n" .
         "            eco_char('eco_text', 'eco_ccnt')\n" .
         "        }, 55);\n" .
         "        </script>\n";
}
