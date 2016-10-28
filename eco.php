<?php
//** host, page, index, data, maximum chars, IP
$eco_host = 'example.com';
$eco_page = $_SERVER['SCRIPT_NAME'];
$eco_indx = str_replace('index.php', '', $eco_page);
$eco_data = '/home/www/public_html' . $eco_page . '_comments.html';
$eco_tmax = 1024;
$eco_myip = gethostbyaddr($_SERVER['REMOTE_ADDR']);

//** default user name, admin prefix and suffix
$eco_user = 'anonymous';
$eco_apfx = 'secret_';
$eco_asfx = 'root';

//** init name, status, and save flag
$eco_name = '';
$eco_stat = '';
$eco_save = '';

//** notify flag, mailto and from
$eco_note = 'n';
$eco_mail = 'info@example.com';
$eco_from = 'From: Comment ' . $eco_host . ' <info@' . $eco_host . '>';

//** captcha
$eco_cmin = 1;
$eco_cmax = 9;
$eco_cone = mt_rand($eco_cmin, $eco_cmax);
$eco_ctwo = mt_rand($eco_cmin, $eco_cmax);

//** version
$eco_ver  = 20161027;

//** redirect helper
function redir($url) {

  if (!headers_sent()) {    
    header('Location: ' . $url);
  } else {
    echo '<meta http-equiv="refresh" content="0; url=' . $url . '">';
  }
}

//** form submitted
if (isset ($_POST['eco_post'])) {
  //** filter name, text, and hard links, fix escaped quotes
  $eco_name = htmlspecialchars($_POST['eco_name']);
  $eco_text = htmlspecialchars($_POST['eco_text']);

  //** strip anchors
  $eco_text = preg_replace('/<a([\s\S])*a>/', '***', $_POST['eco_text']);
  $eco_text = str_replace("\'", "'", $eco_text);

  //** link captcha
  $eco_csum = $_POST['eco_csum'];
  $eco_cone = $_POST['eco_cone'];
  $eco_ctwo = $_POST['eco_ctwo'];
  $eco_cval = $eco_cone + $eco_ctwo;

  //** substitute missing name
  if ($eco_name == '') {
    $eco_name = $eco_user;
  }

  //** check restricted name
  if (($eco_name == $eco_asfx) || 
      ($eco_name == 'admin') || 
      ($eco_name == 'administrator') || 
      ($eco_name == 'root') || 
      ($eco_name == 'webmaster')) {
    $eco_name = $eco_user;
    $eco_stat = 'That name is restricted!';
    $eco_save = 'n';
  }

  //** append user key to user post or admin reply
  if ($eco_name == $eco_apfx . $eco_asfx) {
    $eco_name = $eco_asfx;
    $eco_ukey = '#';
  } else {
    $eco_name = $eco_name;
    $eco_ukey = '$';
  }

  //** check missing text
  if ($eco_text == '') {
    $eco_stat = 'Text field cannot be empty!';
    $eco_save = 'n';
  }

  //** check maximum characters
  if (strlen($eco_text) > $eco_tmax) {
    $eco_clen = strlen($eco_text);
    $eco_cfix = ($eco_clen - $eco_tmax);
    $eco_stat = 'Maximum characters allowed: ' . $eco_tmax . ' (' . $eco_cfix . ' characters have been removed!)';
    $eco_text = substr($eco_text, 0, $eco_tmax);
  }

  //** check captcha and re-generate
  if ($eco_cval != $eco_csum) {
    $eco_stat = 'Invalid verification code!';
    $eco_save = 'n';
    $eco_cone = mt_rand($eco_cmin, $eco_cmax);
    $eco_ctwo = mt_rand($eco_cmin, $eco_cmax);
  }

  //** valid comment
  if ($eco_save != 'n') {
    $eco_post = '      <div id="eco_' . gmdate('Y_m_d_H_i_s') . '_' . $eco_myip . '_' . $eco_name . '" class="eco_item"><span>' . gmdate('Y-m-d H:i:s') . ' ' . $eco_name . ' ' . $eco_ukey . '</span> ' . $eco_text . "</div>\n";

    //** save comment to existing data file
    if (is_file($eco_data)) {
      $eco_post .= file_get_contents($eco_data);
    }

    //** save comment to new data file
    file_put_contents($eco_data, $eco_post);

    //** check if user post
    if ($eco_name != $eco_asfx) {

      //** check whether to send notification -- NO VISUAL
      if ($eco_note == 'y') {

        //** prepare message
        $eco_subj = $eco_host . '_Comment';
        $eco_text = $eco_name . ' regarding ' . $eco_host . $eco_indx . "\n\n" . $eco_text;

        // try sending
        if (mail($eco_mail, $eco_subj, $eco_text, $eco_from)) {
          $eco_info = 'Thank you, your message has been sent.';
        } else {
          $eco_info = 'There was a problem sending your message!';
        }

        //** try to prevent re-submission
        redir($eco_indx . '#Comments');
      }
    }
  }
}

//** check if comments enabled
if (!isset ($eco_this)) {
?>
    <form action="<?php echo $eco_indx; ?>#Comments" method="POST" id="Comments">
<?php
  //** print header depending whether data file exists or not
  if (is_file ($eco_data)) {
?>
      <p id="eco_main"><a href="<?php echo $eco_indx; ?>#Add_Comment" title="Add new comment">Add Comment</a></p>
<?php
    //** include existing data file
    if (is_file ($eco_data)) {
      include ($eco_data);
    }
  } else {
?>
      <p id="eco_main">No comments yet. Be the first to share your thoughts.</p>
<?php
  }
?>
      <div id="eco_stat"><span id="Add_Comment"><?php echo $eco_stat; ?></span></div>
      <p>
        <label for="eco_name">Name</label>
      </p>
      <div>
        <input name="eco_name" id="eco_name" value="anonymous" title="Please enter your name or just post anonymous" class="input">
      </div>
      <p>
        <label for="eco_text">Text (<small>maximum <span id="eco_ccnt"><?php echo $eco_tmax; ?></span> characters</small>)</label>
      </p>
      <div>
        <textarea name="eco_text" id="eco_text" rows="2" cols="26" title="Please enter the text of your comment" class="input" onFocus="eco_tmax('eco_text', 'eco_ccnt', <?php echo $eco_tmax; ?>)" onKeyDown="eco_tmax('eco_text', 'eco_ccnt', <?php echo $eco_tmax; ?>)" onKeyUp="eco_tmax('eco_text', 'eco_ccnt', <?php echo $eco_tmax; ?>)"></textarea>
      </div>
      <p>
        <label for="eco_csum">Code</label> 
        <?php echo $eco_cone . ' + ' . $eco_ctwo . ' = '; ?>
        <input name="eco_csum" id="eco_csum" size="4" maxlength="2" title="Please enter the verification code">
        <input name="eco_cone" type="hidden" value="<?php echo $eco_cone; ?>">
        <input name="eco_ctwo" type="hidden" value="<?php echo $eco_ctwo; ?>">
      </p>
      <p>
        <input name="eco_post" type="submit" value="Add Comment" title="Click here to post your comment" class="input">
      </p>
      <p class="eco_by">All posts are monitored and subject for removal!</p>
      <p class="eco_by"><a href="http://phclaus.com/php-scripts/easy-comments/" title="Click here to get your own free copy of PHP Easy Comments">Powered by PHP Easy Comments v<?php echo $eco_ver; ?></a></p>
    </form>
    <script type="text/javascript">
    //** update max character counter
    function eco_tmax(eco_text, eco_ccnt, eco_tmax) {
      var eco_ccur = (eco_tmax - document.getElementById("eco_text").value.length);

      if (eco_ccur < 0) {
        document.getElementById("eco_ccnt").innerHTML = eco_ccur;
      } else {
        document.getElementById("eco_ccnt").innerHTML = eco_ccur;
      }
    }
    </script>
<?php
}
?>
