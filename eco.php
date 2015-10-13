<?php
// host, current page, default index, data file, maximum characters
$eco_host = 'example.com';
$eco_page = $_SERVER['SCRIPT_NAME'];
$eco_indx = str_replace('index.php', '', $eco_page);
$eco_data = '/home/www/public_html' . $eco_page . '_comments.html';
$eco_cmax = 512;

// default user name, flag
$eco_user = 'anonymous';
$eco_flag = '$';

// admin prefix, suffix
$eco_apfx = 'joe';
$eco_asfx = 'root';

// init name, status
$eco_name = '';
$eco_stat = '';

// notify, mailto, from
$eco_note = 'n';
$eco_mail = 'eco@' . $eco_host;
$eco_from = 'From: eco <' . $eco_mail . '>';

// verification code
$eco_vmin = 1;
$eco_vmax = 9;
$eco_vone = mt_rand($eco_vmin, $eco_vmax);
$eco_vtwo = mt_rand($eco_vmin, $eco_vmax);


// form was posted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // filter name, text
    $eco_name = htmlspecialchars($_POST['eco_name']);
    $eco_text = htmlspecialchars($_POST['eco_text']);

    // fix escaped quotes
    $eco_text = str_replace("\'", "'", $eco_text);

    // link verification code
    $eco_vsum = $_POST['eco_vsum'];
    $eco_vone = $_POST['eco_vone'];
    $eco_vtwo = $_POST['eco_vtwo'];
    $eco_vval = $eco_vone + $eco_vtwo;

    // check missing name
    if ($eco_name == '') {
        $eco_name = $eco_user;
    }

    // check restricted name
    if (($eco_name == $eco_asfx) || 
        ($eco_name == 'admin') || 
        ($eco_name == 'administrator') || 
        ($eco_name == 'root') || 
        ($eco_name == 'webmaster')) {
        $eco_name = $eco_user;
        $eco_stat = 'That name is restricted!';
        $eco_save = 'n';
    }

    // mark admin reply
    if ($eco_name == $eco_apfx . $eco_asfx) {
        $eco_name = $eco_asfx;
        $eco_flag = '#';
    }

    // check missing text
    if ($eco_text == '') {
        $eco_stat = 'Text field cannot be empty!';
        $eco_save = 'n';
    }

    // check maximum characters
    if (strlen($eco_text) > $eco_cmax) {
        $eco_clen = strlen($eco_text);
        $eco_cfix = ($eco_clen - $eco_cmax);
        $eco_stat = 'Maximum characters allowed: ' . 
                    $eco_cmax . ' (' . $eco_cfix . 
                    ' characters have been removed!)';
        $eco_text = substr($eco_text, 0, $eco_cmax);
    }

    // check verification code
    if ($eco_vval != $eco_vsum) {
        $eco_stat = 'Invalid verification code!';
        $eco_save = 'n';
    }

    // check valid comment
    if ($eco_save != 'n') {
        $eco_post = '<div id="eco_' . gmdate('Y_m_d_H_i_s') . 
                    '_' . str_replace(' ', '_', $eco_name) . 
                    '" class="eco_item"><span>' . 
                    gmdate('Y-m-d H:i:s') . ' ' . $eco_name . ' ' . 
                    $eco_flag . '</span> ' . $eco_text . "</div>\n";

        // save comment, existing data file
        if (is_file($eco_data)) {
            $eco_post .= file_get_contents($eco_data);

        // save comment, new data file
        } else {
            file_put_contents($eco_data, $eco_post);
        }

        // check if user comment
        if ($eco_name != $eco_asfx) {

            // check whether to send notifiy
            if ($eco_note == 'y') {

                // build subject, body
                $eco_subj = $eco_host . '_Comment';
                $eco_body = $eco_name . ' regarding ' . $eco_host . 
                            $eco_indx . "\n\n" . $eco_text;

                // try sending notify -- hide status
                mail($eco_mail, $eco_subj, $eco_body, $eco_from);
            }
        }
    }
}

// check if comments disabled
if (!isset ($com)) {

    // include data file if it exists
    if (is_file ($eco_data)) {
        include ($eco_data);
    }
?>
        <form action="#Add_Comment" 
              method="POST" 
              id="Add_Comment">
<?php
    // print header depending whether data file exists or not
    if (is_file ($eco_data)) {
?>
        <p id="eco_main">
            <a href="<?php echo $eco_indx; ?>#Add_Comment" 
               title="Add new comment">Add Comment</a>
        </p>
<?php
    } else {
?>
        <p id="eco_main">
            No comments yet. Be the first to share your thoughts.
        </p>

<?php
    }
?>
            <div id="eco_stat">
                <?php echo $eco_stat; ?>
            </div>
            <p id="eco_form">
                <label for="eco_name">
                    Name
                </label>
            </p>
            <div>
                <input name="eco_name" 
                       id="eco_name" 
                       value="anonymous" 
                       title="Enter your name or post as anonymous" 
                       class="eco_input">
            </div>
            <p>
                <label for="eco_text">
                    Text (maximum <?php echo $eco_cmax; ?> characters)
                </label>
            </p>
            <div>
                <textarea name="eco_text" 
                          id="eco_text" 
                          rows="2" 
                          cols="26" 
                          title="Enter the text of your comment" 
                          class="eco_input">
                </textarea>
            </div>
            <p>
                <label for="eco_vsum">
                    Code
                </label> 
                <?php echo $eco_vone . ' + ' . $eco_vtwo . ' = '; ?>
                <input name="eco_vsum" 
                       id="eco_vsum" 
                       size="2" 
                       maxlength="2" 
                       title="Enter verification code">
                <input name="eco_vone" 
                       id="eco_vtwo" 
                       type="hidden" 
                       value="<?php echo $eco_vone; ?>">
                <input name="eco_vtwo" 
                       id="eco_vtwo" 
                       type="hidden" 
                       value="<?php echo $eco_vtwo; ?>">
            </p>
            <p>
                <input type="submit" 
                       value="Add Comment" 
                       title="Click to post your comment" 
                       class="eco_input">
            </p>
            <p>
                <small>
                    <a href="http://phclaus.eu.org/?eco" 
                       title="Get a free copy of Easy Comments">
                       powered by Easy Comments</a>
                </small>
                </p>
        </form>
<?php
}
?>
