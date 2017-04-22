<?php
/*
 * PHP Easy Comments is a free PHP comments script with minimal bloat
 *
 * phclaus.com/php-scripts/easy-comments
 */


/*
 * script folder
 * default directory index
 * comments data file -- not used with manual approval
 * comments log file -- not used whit manual approval
 */
$eco_fold = "/eco/";
$eco_dirx = "index.php";
$eco_cdat = "_comments.html";
$eco_clog = $eco_fold . "log.html";

/*
 * maximum characters allowed for comments text
 * accept latin characters only (0 1)
 * default anonymous user name
 */
$eco_tmax = 1024;
$eco_lato = 0;
$eco_anon = "anonymous";

/*
 * send notifications for new comments (0 1)
 * mail account to receive notifications
 * manual approval of new posts (0 1) -- enabled requires notifications
 */
$eco_note = 0;
$eco_mail = "info@" . $_SERVER['HTTP_HOST'];
$eco_mapp = 0;

/*
 * admin prefix
 * admin suffix
 */
$eco_apfx = "YOUR_ADMIN_PREFIX";
$eco_asfx = "root";

/*
 * query string to list log file
 * delay between posts in seconds -- 0 to disable
 * date and time format
 */
$eco_list = "YOUR_LIST_TOKEN";
$eco_tdel = 60;
$eco_date = gmdate('Y-m-d H:i:s');


/*
 ***********************************************************************
 *                                               NO NEED TO EDIT BELOW *
 ***********************************************************************
 */


/*
 * script version
 * host on which the script is running
 * current page to which the comment applies
 * strip default index
 * comments data file
 * try to link user IP
 * mail header
 */
$eco_make = 20170422;
$eco_host = $_SERVER['HTTP_HOST'];
$eco_page = $_SERVER['PHP_SELF'];
$eco_indx = str_replace($eco_dirx, "", $eco_page);
$eco_data = $_SERVER['DOCUMENT_ROOT'] . $eco_page . $eco_cdat;
$eco_myip = gethostbyaddr($_SERVER['REMOTE_ADDR']);
$eco_head = "From: Easy Comments <$eco_mail>";

/*
 * captcha range min
 * captcha range max
 * captche value min
 * captcha value max
 */
$eco_cmin = 1;
$eco_cmax = 9;
$eco_cone = mt_rand($eco_cmin, $eco_cmax);
$eco_ctwo = mt_rand($eco_cmin, $eco_cmax);

//** init message text, user name, and status text
$eco_text = "";
$eco_name = "";
$eco_stat = "";

//** init protocol
$eco_prot = "http";

//** check protocol
if (isset ($_SERVER['HTTPS']) && "on" === $_SERVER['HTTPS']) {
  $eco_prot .= "s";
}

//** set protocol
$eco_prot = $eco_prot . "://";

//** check empty user name
if ($eco_name == "") {
  $eco_name = $eco_anon;
}

//** check whether to list log file
if ($_SERVER['QUERY_STRING'] == $eco_list) {

  if (is_file($_SERVER['DOCUMENT_ROOT'] . $eco_clog)) {
    header("Location: $eco_prot$eco_host$eco_clog");
    exit;
  } else {
    $eco_stat = "Missing log file!";
  }
}

//** redirect helper
function eco_post($eco_goto) {

  if (!headers_sent()) {    
    header("Location: $eco_goto");
    exit;
  } else {
    echo "<meta http-equiv=\"refresh\" content=\"0; url=$eco_goto\">";
  }
}

// check session status helper
function is_session_started() {

  if (php_sapi_name() !== "cli") {

    if (version_compare(phpversion(), "5.4.0", ">=")) {
      return session_status() === PHP_SESSION_ACTIVE ? TRUE : FALSE;
    } else {
      return session_id() === "" ? FALSE : TRUE;
    }
  }

  return FALSE;
}

//** check session status
if (is_session_started() === FALSE) {
  session_start();
}

//** link session
$_SESSION['eco_tbeg'] = time();
$eco_tbeg = $_SESSION['eco_tbeg'];

//** form submitted
if (isset ($_POST["eco_post"])) {
  //** filter name and text
  $eco_name = htmlspecialchars($_POST['eco_name']);
  $eco_text = htmlspecialchars($_POST['eco_text']);

  //** filter links
  if (preg_match("/(?i)\b((?:https?:\/\/|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}\/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:'\".,<>?«»“”‘’]))/", $eco_text)) {
    $eco_stat = "Text must not contain links!";
  }

  //** link captcha
  $eco_csum = $_POST['eco_csum'];
  $eco_cone = $_POST['eco_cone'];
  $eco_ctwo = $_POST['eco_ctwo'];
  $eco_cval = $eco_cone + $eco_ctwo;

  //** substitute anon if name is empty or spaces only
  if ($eco_name == "" || preg_match("/^\s*$/", $eco_name)) {
    $eco_name = $eco_anon;
  }

  //** check if name is alpha
  if (preg_match("/^[a-zA-Z]+$/", $eco_name) != 1) {

    //** exclude admin post
    if ($eco_name != $eco_apfx . $eco_asfx) {
      $eco_stat = "Name contains invalid characters!";
    }
  }

  //** link restricted names
  include ($_SERVER['DOCUMENT_ROOT'] . $eco_fold . "./restricted.php");

  //** append identifier to user post or admin reply
  if ($eco_name == $eco_apfx . $eco_asfx) {
    $eco_name = $eco_asfx;
    $eco_ukey = "#";
    $eco_mapp = 0;
  } else {
    $eco_name = $eco_name;
    $eco_ukey = "$";
  }

  //** check missing text
  if ($eco_text == "") {
    $eco_stat = "Cannot post empty comment!";
  }

  //** check non-latin characters
  if ($eco_lato == 1) {

    //** regex filter
    $eco_latx = "/[^\\p{Common}\\p{Latin}]/u";

    //** check name and text
    if (preg_match($eco_latx, $eco_name) || preg_match($eco_latx, $eco_text)) {
      $eco_stat = "Only latin characters allowed!";
    }
  }

  //** check and trim maximum characters
  if (strlen($eco_text) > $eco_tmax) {
    $eco_clen = strlen($eco_text);
    $eco_cfix = ($eco_clen - $eco_tmax);
    $eco_stat = "$eco_cfix characters have been trimmed!";
    $eco_text = substr($eco_text, 0, $eco_tmax);
  }

  //** check captcha and regenerate
  if ($eco_cval != $eco_csum) {
    $eco_stat = "Invalid verification code!";
    $eco_cone = mt_rand($eco_cmin, $eco_cmax);
    $eco_ctwo = mt_rand($eco_cmin, $eco_cmax);
  }

  //** valid comment
  if ($eco_stat == "") {

    //** build comments entry
    $eco_post = '      <div id="eco_' . gmdate('Ymd_His_') . $eco_myip . '_' . $eco_name . '" class="eco_item"><span>' . $eco_date . " " . $eco_name . " " . $eco_ukey . "</span> " . $eco_text . "</div>\n";

    //** prepare message
    $eco_subj = "New_Comment";
    $eco_text = $eco_name . " regarding " . $eco_prot . $eco_host . $eco_indx . "\n\n" . $eco_post;

    //** check moderator flag
    if ($eco_mapp != 1) {

      //** check existing data file
      if (is_file($eco_data)) {
        $eco_post .= file_get_contents($eco_data);
      }

      //** update data file
      file_put_contents($eco_data, $eco_post);

      //** link log file and build log entry
      $eco_clog = $_SERVER['DOCUMENT_ROOT'] . $eco_clog;
      $eco_ulog = '<div>' . $eco_date . ' <a href="' . $eco_prot . $eco_host . $eco_indx . '" title="Click here to open">' . $eco_host . $eco_indx . "</a></div>\n";

      //** check existing log file
      if (is_file($eco_clog)) {
        $eco_ulog .= file_get_contents($eco_clog);
      }

      //** update log file
      file_put_contents($eco_clog, $eco_ulog);

      //** check if user post
      if ($eco_name != $eco_asfx) {

        //** check notification flag and send mail
        if ($eco_note == 1) {
          mail($eco_mail, $eco_subj, $eco_text, $eco_head);
        }
      }

      //** link timer session
      $_SESSION['eco_tfrm'] = htmlspecialchars($_POST['eco_tbeg']);

      //** try to catch re-submission
      eco_post($eco_prot . $eco_host . $eco_indx . "#Comments");
    } else {
      //** build link for manual approval post processing
      $eco_mlnk = $eco_prot . $eco_host . $eco_fold . "?eco_data=" . $eco_data . "&eco_post=" . base64_encode($eco_post);

      //** merge body and param
      $eco_text = $eco_text . "\n\n" . $eco_mlnk;

      //** send message
      mail($eco_mail, $eco_subj, $eco_text, $eco_head);

      //** link timer session
      $_SESSION['eco_tfrm'] = htmlspecialchars($_POST['eco_tbeg']);

      //** try to catch re-submission
      eco_post($eco_prot . $eco_host . $eco_indx . "#Comments");
    }
  } else {
    //** avoid clearing existing input
    $eco_name = $eco_name;
    $eco_text = $eco_text;
  }
}

//** check if comments enabled
if (!isset ($eco_this)) {

  //** check conflict when moderator is on but notifications are off
  if ($eco_mapp == 1 && $eco_note == 0) {
?>
    <p id="eco_stat">Easy Comments Error</p>
    <p>The moderator flag is set but notifications are disabled! Please edit the script's configuration to enable notifications.</p>
<?php
  } else {
?>
    <form action="<?php echo $eco_prot . $eco_host . $eco_indx; ?>#Comments" method="POST" id="Comments">
      <div id="eco_stat"><?php echo $eco_stat; ?></div>
<?php
    //** print header depending whether data file exists or not
    if (is_file($eco_data)) {
?>
      <p id="eco_main"><a href="<?php echo $eco_prot . $eco_host . $eco_indx; ?>#Add_Comment" title="Click here to add new comment">Add Comment</a></p>
<?php
      //** include existing data file
      if (is_file($eco_data)) {
        include ($eco_data);
      }
    } else {
?>
      <p id="eco_main">No comments yet. Be the first to share your thoughts.</p>
<?php
    }
?>
      <p id="Add_Comment">
        <label for="eco_name">Name</label> <span class="eco_by">(A-Z only)</span>
      </p>
      <div>
        <input name="eco_name" id="eco_name" value="<?php echo $eco_name; ?>" title="Please enter your name or just post anonymous" class="input">
      </div>
      <p>
        <label for="eco_text">Text (<small id="eco_ccnt"><?php echo $eco_tmax; ?></small>)</label>
      </p>
      <div>
        <div class="eco_by">Text must not contain links!</div>
        <textarea name="eco_text" id="eco_text" rows="4" cols="26" title="Please enter the text of your comment" class="input" onFocus="eco_tmax()" onKeyDown="eco_tmax()" onKeyUp="eco_tmax()"><?php echo $eco_text; ?></textarea>
      </div>
      <p>
        <label for="eco_csum">Code</label> 
        <?php echo $eco_cone . " + " . $eco_ctwo . " = "; ?>
        <input name="eco_csum" id="eco_csum" size="4" maxlength="2" title="Please enter the verification code">
        <input name="eco_cone" type="hidden" value="<?php echo $eco_cone; ?>">
        <input name="eco_ctwo" type="hidden" value="<?php echo $eco_ctwo; ?>">
        <input name="eco_tbeg" type="hidden" value="<?php echo $eco_tbeg; ?>">
      </p>
      <p id="eco_tbtn">
<?php
    //** link timer difference and mark-up
    $eco_tdif = ($eco_tbeg - $_SESSION['eco_tfrm']);
    $eco_tbtn = '        <input name="eco_post" type="submit" value="Add Comment" title="Click here to post your comment" class="input">';

    //** check timer status
    if ($eco_tdif > $eco_tdel) {
      echo $eco_tbtn . "\n";
    } else {
?>
      Please wait <span id="eco_tdel"><?php echo ($eco_tdel - $eco_tdif); ?></span> seconds before posting again!
      <noscript><br />Refresh this page to update the timer status.</noscript>
<?php
    }
?>
      </p>
<?php
    //** check moderator flag
    if ($eco_mapp == 1) {
      $eco_mtxt = "New posts will be listed after moderator approval.";
    } else {
      $eco_mtxt = "All posts are monitored and subject to removal.";
    }
?>
      <p class="eco_by"><?php echo $eco_mtxt; ?></p>
      <p class="eco_by"><a href="http://phclaus.com/php-scripts/easy-comments/" title="Click here to get your own free copy of PHP Easy Comments">Powered by PHP Easy Comments v<?php echo $eco_make; ?></a></p>
    </form>
    <script type="text/javascript">
    //** character counter
    function eco_tmax() {
      var eco_ccnt = document.getElementById("eco_ccnt").innerHTML = (<?php echo $eco_tmax; ?> - document.getElementById("eco_text").value.length);

      if (eco_ccnt == 0) {
        document.getElementById("eco_ccnt").innerHTML = "You have reached the maximum characters limit!";
      } else if (eco_ccnt < 0) {
        document.getElementById("eco_ccnt").innerHTML = "You are " + document.getElementById("eco_ccnt").innerHTML.replace("-", "") + " characters over the limit!";
      } else {
        document.getElementById("eco_ccnt").innerHTML = eco_ccnt;
      }
    }

    var t_end = <?php echo $eco_tdel; ?>;
    var t_obj = document.getElementById("eco_tdel");
    var t_int = setInterval(eco_tdel, 1000);

    //** delay counter
    function eco_tdel() {
      if (t_end == 0) {
        document.getElementById("eco_tbtn").innerHTML = '<?php echo $eco_tbtn; ?>';
        clearTimeout(t_int);
      } else {
        t_obj.innerHTML = t_end;
        t_end --;
      }
    }
    </script>
<?php
  }
}

//** process manual approvals
if (isset ($_GET['eco_data']) && $_GET['eco_data'] != "" && isset ($_GET['eco_post']) && $_GET['eco_post'] != "") {
  $eco_data = $_GET['eco_data'];
  $eco_post = base64_decode($_GET['eco_post']);

  //** check existing data file
  if (is_file($eco_data)) {
    $eco_post .= file_get_contents($eco_data);
  }

  //** update data file
  file_put_contents($eco_data, $eco_post);
}
?>
