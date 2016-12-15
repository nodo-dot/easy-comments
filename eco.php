<?php
/*
 * host and domain on which the script is running
 * current page for which comment is received
 * strip default index from URL
 * comments data file
 * maximum characters allowed for comments text
 * mail account to receive notifications
 * try to link user's IP
 */
$eco_hdom = "example.com";
$eco_page = $_SERVER["SCRIPT_NAME"];
$eco_indx = str_replace("index.php", "", $eco_page);
$eco_data = $_SERVER["DOCUMENT_ROOT"] . $eco_page . "_comments.html";
$eco_tmax = 1024;
$eco_nota = "info";
$eco_myip = gethostbyaddr($_SERVER["REMOTE_ADDR"]);

/*
 * default user name
 * admin prefix
 * admin suffix
*/
$eco_user = "anonymous";
$eco_apfx = "foo";
$eco_asfx = "bar";

/*
 * send notification for new comments
 * mailto token
 * header token
 */
$eco_note = "y";
$eco_mail = "$eco_nota@$eco_hdom";
$eco_head = "From: Easy Comments <$eco_mail>";

//** init captcha
$eco_cmin = 1;
$eco_cmax = 9;
$eco_cone = mt_rand($eco_cmin, $eco_cmax);
$eco_ctwo = mt_rand($eco_cmin, $eco_cmax);

//** init internals -- must be empty!
$eco_name = "";
$eco_stat = "";
$eco_save = "";

//** script version
$eco_ver  = 20161215;

//** redirect helper
function redir($url) {

  if (!headers_sent()) {    
    header("Location: $url");
  } else {
    echo "<meta http-equiv=\"refresh\" content=\"0; url=$url\">";
  }
}

//** form submitted
if (isset ($_POST["eco_post"])) {
  //** filter name and text
  $eco_name = htmlspecialchars($_POST["eco_name"]);
  $eco_text = htmlspecialchars($_POST["eco_text"]);

  //** bust anchors
  $eco_text = preg_replace("/<a([\s\S])*a>/", "***", $_POST["eco_text"]);
  $eco_text = str_replace("\'", "'", $eco_text);

  //** link captcha
  $eco_csum = $_POST["eco_csum"];
  $eco_cone = $_POST["eco_cone"];
  $eco_ctwo = $_POST["eco_ctwo"];
  $eco_cval = $eco_cone + $eco_ctwo;

  //** substitute missing name
  if ($eco_name == "") {
    $eco_name = $eco_user;
  }

  //** check restricted name
  if (($eco_name == $eco_asfx) || 
      ($eco_name == "admin") || 
      ($eco_name == "administrator") || 
      ($eco_name == "root") || 
      ($eco_name == "webmaster")) {
    $eco_name = $eco_user;
    $eco_stat = "Sorry, that name is restricted!";
    $eco_save = "n";
  }

  //** append identifier to user post or admin reply
  if ($eco_name == $eco_apfx . $eco_asfx) {
    $eco_name = $eco_asfx;
    $eco_ukey = "#";
  } else {
    $eco_name = $eco_name;
    $eco_ukey = "$";
  }

  //** check missing text
  if ($eco_text == "") {
    $eco_stat = "Cannot post empty comment!";
    $eco_save = "n";
  }

  //** check and trim maximum characters
  if (strlen($eco_text) > $eco_tmax) {
    $eco_clen = strlen($eco_text);
    $eco_cfix = ($eco_clen - $eco_tmax);
    $eco_stat = "Maximum characters allowed: $eco_tmax ($eco_cfix characters have been removed!)";
    $eco_text = substr($eco_text, 0, $eco_tmax);
  }

  //** check captcha and re-generate
  if ($eco_cval != $eco_csum) {
    $eco_stat = "Invalid verification code!";
    $eco_save = "n";
    $eco_cone = mt_rand($eco_cmin, $eco_cmax);
    $eco_ctwo = mt_rand($eco_cmin, $eco_cmax);
  }

  //** valid comment
  if ($eco_save != "n") {
    $eco_post = '      <div id="eco_' . gmdate('Ymd_His') . '_' . $eco_myip . '_' . $eco_name . '" class="eco_item"><span>' . gmdate('Y-m-d H:i:s') . ' ' . $eco_name . ' ' . $eco_ukey . '</span> ' . $eco_text . "</div>\n";

    //** save comment to existing data file
    if (is_file($eco_data)) {
      $eco_post .= file_get_contents($eco_data);
    }

    //** save comment to new data file
    file_put_contents($eco_data, $eco_post);

    //** check if user post
    if ($eco_name != $eco_asfx) {

      //** check whether to send notification
      if ($eco_note == "y") {

        //** prepare message
        $eco_subj = "New_Comment";
        $eco_text = $eco_name . " regarding " . $eco_hdom . $eco_indx . "\n\n" . $eco_text;

        // try sending -- NO VISUAL
        mail($eco_mail, $eco_subj, $eco_text, $eco_head);

        //** try to catch re-submission
        redir($eco_indx . "#Comments");
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
      <p id="eco_main"><a href="<?php echo $eco_indx; ?>#Add_Comment" title="Click here to add new comment">Add Comment</a></p>
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
        <textarea name="eco_text" id="eco_text" rows="4" cols="26" title="Please enter the text of your comment" class="input" onFocus="eco_tmax('eco_text', 'eco_ccnt', <?php echo $eco_tmax; ?>)" onKeyDown="eco_tmax('eco_text', 'eco_ccnt', <?php echo $eco_tmax; ?>)" onKeyUp="eco_tmax('eco_text', 'eco_ccnt', <?php echo $eco_tmax; ?>)"></textarea>
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
