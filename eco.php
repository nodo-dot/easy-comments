<?php
// host, current page, default index, data file, maximum characters
$eco_host = 'example.com';
$eco_page = $_SERVER['SCRIPT_NAME'];
$eco_indx = str_replace('index.php', '', $eco_page);
$eco_data = '/home/www/public_html' . $eco_page . '_comments.html';
$eco_cmax = 512;

// default user, admin prefix, admin suffix
$eco_user = 'anonymous';
$eco_apfx = 'joe';
$eco_asfx = 'root';

// init name and status
$eco_name = '';
$eco_stat = '';

// notify, mailto, from
$eco_note = 'n';
$eco_mail = 'eco@' . $eco_host;
$eco_from = 'From: eco <eco@' . $eco_host . '>';

// captcha
$cap_min = 1;
$cap_max = 9;
$cap_one = mt_rand($cap_min, $cap_max);
$cap_two = mt_rand($cap_min, $cap_max);


// form was posted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // filter name and text
    $eco_name = htmlspecialchars($_POST['eco_name']);
    $eco_text = htmlspecialchars($_POST['eco_text']);

    // fix escaped quotes
    $eco_text = str_replace("\'", "'", $eco_text);

    // captcha
    $cap_sum = $_POST['cap_sum'];
    $cap_one = $_POST['cap_one'];
    $cap_two = $_POST['cap_two'];
    $cap_val = $cap_one + $cap_two;

    // missing name
    if ($eco_name == '') {
        $eco_name = $eco_user;
    }

    // restricted name
    if (($eco_name == $eco_asfx) || 
        ($eco_name == 'admin') || 
        ($eco_name == 'administrator') || 
        ($eco_name == 'root') || 
        ($eco_name == 'webmaster')) {
        $eco_name = $eco_user;
        $eco_stat = 'That name is restricted!';
        $eco_save = 'n';
    }

    // admin reply
    if ($eco_name == $eco_apfx . $eco_asfx) {
        $eco_name = $eco_asfx . ' #';
    // user comment
    } else {
        $eco_name = $eco_name . ' $';
    }

    // missing text
    if ($eco_text == '') {
        $eco_stat = 'Text field cannot be empty!';
        $eco_save = 'n';
    }

    // maximum characters
    if (strlen($eco_text) > $eco_cmax) {
        $eco_clen = strlen($eco_text);
        $eco_cfix = ($eco_clen - $eco_cmax);
        $eco_stat = 'Maximum characters allowed: ' . 
                    $eco_cmax . ' (' . $eco_cfix . 
                    ' characters have been removed!)';
        $eco_text = substr($eco_text, 0, $eco_cmax);
    }

    // captcha
    if ($cap_val != $cap_sum) {
        $eco_stat = 'Invalid verification code!';
        $eco_save = 'n';
    }

    // valid comment
    if ($eco_save != 'n') {
        $eco_post = '<div id="eco_' . gmdate('Y_m_d_H_i_s') . 
                    '_' . str_replace(' ', '_', $eco_name) . 
                    '" class="eco_item"><span>' . 
                    gmdate('Y-m-d H:i:s') . ' ' . $eco_name . 
                    '</span> ' . $eco_text . "</div>\n";

        // save comment, existing data file
        if (is_file($eco_data)) {
            $eco_post .= file_get_contents($eco_data);
        }

        // save comment, new data file
        file_put_contents($eco_data, $eco_post);

        // user comment
        if ($eco_name != $eco_asfx) {

            // notifiy
            if ($eco_note == 'y') {

                // subject, body
                $eco_subj = $eco_host . '_Comment';
                $eco_text = $eco_name . ' regarding ' . $eco_host . 
                            $eco_indx . "\n\n" . $eco_text;

                // try sending -- hide status
                mail($eco_mail, $eco_subj, $eco_text, $eco_from);
            }
        }
    }
}

// check if comments disabled
if (!isset ($com)) {
?>
        <form action="#Add_Comment" method="POST" id="Add_Comment">
<?php
    // print header depending whether data file exists or not
    if (is_file ($eco_data)) {
?>
        <p id="eco_main">
            <a href="<?php echo $eco_indx; ?>#Add_Comment" 
               title="Add new comment">Add Comment</a>
        </p>
<?php
        // include data file if it exists
        if (is_file ($eco_data)) {
            include ($eco_data);
        }
    } else {
?>
        <p id="eco_main">
            No comments yet. Be the first to share your thoughts.
        </p>
<?php
    }
?>
            <div id="eco_stat"><?php echo $eco_stat; ?></div>
            <p id="eco_form">
                <label for="eco_name">
                    Name
                </label>
            </p>
            <div>
                <input name="eco_name" id="eco_name" 
                       value="anonymous" 
                       title="Enter your name or post anonymous" 
                       class="eco_input">
            </div>
            <p>
                <label for="eco_text">
                    Text (maximum <?php echo $eco_cmax; ?> characters)
                </label>
            </p>
            <div>
                <textarea name="eco_text" id="eco_text" 
                          rows="2" cols="26" 
                          title="Enter the text of your comment" 
                          class=    >
                </textarea>
            </div>
            <p>
                <label for="cap_sum">
                    Code
                </label> 
                <?php echo $cap_one . ' + ' . $cap_two . ' = '; ?>
                <input name="cap_sum" id="cap_sum" 
                       size="2" maxlength="2" 
                       title="Enter verification code">
                <input name="cap_one" id="cap_code" 
                       type="hidden" 
                       value="<?php echo $cap_one; ?>">
                <input name="cap_two" id="cap_two" 
                       type="hidden" 
                       value="<?php echo $cap_two; ?>">
            </p>
            <p>
                <input type="submit" value="Add Comment" 
                       title="Click to post your comment" 
                       class="eco_input">
            </p>
            <p id="eco_by">
                <a href="http://phclaus.eu.org/?eco" 
                   title="Get a free copy of Easy Comments">
                   powered by Easy Comments</a>
            </p>
        </form>
<?php
}
?>
