<?php
/*
 * PHP Easy Comments is a free PHP comments script with minimal bloat.
 *
 * Please note that input fields carry class="input" which is not in
 * eco.css, assuming you already have a similar class in your default
 * style sheet. Just replace class="input" to match your own class.
 * 
 * phclaus.com/php-scripts/easy-comments
 */


/*
 ***********************************************************************
 * USER CONFIG
 ***********************************************************************
 */


/*
 * directory index
 * comments data file
 */
$eco_dirx = "index.php";
$eco_cdat = "_comments.html";

/*
 * maximum characters allowed for comments text
 * accept latin characters only
 * default anonymous user name
 */
$eco_tmax = 1024;
$eco_lato = "n";
$eco_anon = "anonymous";

/*
 * send notification for new comments
 * mail account to receive notifications
 */
$eco_note = "n";
$eco_nota = "info";

/*
 * admin prefix
 * admin suffix
 */
$eco_apfx = "youradminprefix";
$eco_asfx = "root";

//** restricted names
$eco_rest = array (
                    "admin",
                    "administrator",
                    "moderator",
                    "root",
                    "webmaster",
                  );


/*
 ***********************************************************************
 * NO NEED TO EDIT BELOW
 ***********************************************************************
 */


/*
 * host on which the script is running
 * current page for which comment is received
 * strip default index
 * comments data file
 * try to link user IP
 */
$eco_host = $_SERVER["HTTP_HOST"];
$eco_page = $_SERVER["PHP_SELF"];
$eco_indx = str_replace($eco_dirx, "", $eco_page);
$eco_data = $_SERVER["DOCUMENT_ROOT"] . $eco_page . $eco_cdat;
$eco_myip = gethostbyaddr($_SERVER["REMOTE_ADDR"]);

/*
 * mailto address
 * mail header
 */
$eco_mail = "$eco_nota@$eco_host";
$eco_head = "From: Easy Comments <$eco_mail>";

/*
 * captcha minimum
 * captcha maximum
 * captche value 1
 * captcha value 2
 */
$eco_cmin = 1;
$eco_cmax = 9;
$eco_cone = mt_rand($eco_cmin, $eco_cmax);
$eco_ctwo = mt_rand($eco_cmin, $eco_cmax);

/*
 * message text
 * user name
 * status text
 * save flag
 */
$eco_text = "";
$eco_name = "";
$eco_stat = "";
$eco_save = "";

//** script version
$eco_ver  = 20170215;

//** redirect helper
function eco_post($url) {

  if (!headers_sent()) {    
    header("Location: $url");
    exit;
  } else {
    echo "<meta http-equiv=\"refresh\" content=\"0; url=$url\">";
  }
}

//** init protocol
$van_prot = "http";

//** check https
if (isset ($_SERVER["HTTPS"]) && ("on" === $_SERVER["HTTPS"])) {
  $van_prot .= "s";
}

//** link anon user if empty name
if ($eco_name == "") {
  $eco_name = $eco_anon;
}

//** form submitted
if (isset ($_POST["eco_post"])) {
  //** filter name and text
  $eco_name = htmlspecialchars($_POST["eco_name"]);
  $eco_text = htmlspecialchars($_POST["eco_text"]);

  //** defuse http/s links
  $eco_regx = "/(?i)\b((?:https?:\/\/|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}\/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:'\".,<>?«»“”‘’]))/";
  $eco_text = preg_replace($eco_regx, "<del class=\"eco_del\">$1</del>", $eco_text);

  //** link captcha
  $eco_csum = $_POST["eco_csum"];
  $eco_cone = $_POST["eco_cone"];
  $eco_ctwo = $_POST["eco_ctwo"];
  $eco_cval = $eco_cone + $eco_ctwo;

  //** substitute missing name
  if ($eco_name == "") {
    $eco_name = $eco_anon;
  }

  //** check restricted name
  if (in_array($eco_name, $eco_rest)) {
    $eco_name = $eco_anon;
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

  //** check non-latin characters
  if ($eco_lato == "y") {

    $eco_latx = "/[^\\p{Common}\\p{Latin}]/u";

    if ((preg_match($eco_latx, $eco_text)) || 
        (preg_match($eco_latx, $eco_name))) {
      $eco_stat = "Only latin characters allowed!";
      $eco_save = "n";
    }
  }

  //** check and trim maximum characters
  if (strlen($eco_text) > $eco_tmax) {
    $eco_clen = strlen($eco_text);
    $eco_cfix = ($eco_clen - $eco_tmax);
    $eco_stat = "$eco_cfix characters have been trimmed!";
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
    $eco_post = '      <div id="eco_' . gmdate('Ymd_His_') . $eco_myip . '_' . $eco_name . '" class="eco_item"><span>' . gmdate('Y-m-d H:i:s') . " " . $eco_name . " " . $eco_ukey . "</span> " . $eco_text . "</div>\n";

    //** check existing data file
    if (is_file($eco_data)) {
      $eco_post .= file_get_contents($eco_data);
    }

    //** save comment to data file
    file_put_contents($eco_data, $eco_post);

    //** check if user post
    if ($eco_name != $eco_asfx) {

      //** try to catch re-submission
      eco_post($van_prot . "://" . $eco_host . $eco_indx . "#Comments");

      //** check whether to send notification
      if ($eco_note == "y") {

        //** prepare message
        $eco_subj = "New_Comment";
        $eco_text = $eco_name . " regarding " . $eco_host . $eco_indx . "\n\n" . $eco_text;

        // try sending -- NO VISUAL
        mail($eco_mail, $eco_subj, $eco_text, $eco_head);
      }
    }
  } else {
    //** link defaults
    $eco_name = $eco_name;
    $eco_text = $eco_text;
  }
}

//** check if comments enabled
if (!isset ($eco_this)) {
?>
    <form action="<?php echo $van_prot . "://" . $eco_host . $eco_indx; ?>#Comments" method="POST" id="Comments">
      <div id="eco_stat"><?php echo $eco_stat; ?></div>
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
      <p id="Add_Comment">
        <label for="eco_name">Name</label>
      </p>
      <div>
        <input name="eco_name" id="eco_name" value="<?php echo $eco_name; ?>" title="Please enter your name or just post anonymous" class="input">
      </div>
      <p>
        <label for="eco_text">Text (<small id="eco_ccnt"><?php echo $eco_tmax; ?></small>)</label>
      </p>
      <div>
        <textarea name="eco_text" id="eco_text" rows="4" cols="26" title="Please enter the text of your comment" class="input" onFocus="eco_tmax()" onKeyDown="eco_tmax()" onKeyUp="eco_tmax()"><?php echo $eco_text; ?></textarea>
      </div>
      <p>
        <label for="eco_csum">Code</label> 
        <?php echo $eco_cone . " + " . $eco_ctwo . " = "; ?>
        <input name="eco_csum" id="eco_csum" size="4" maxlength="2" title="Please enter the verification code">
        <input name="eco_cone" type="hidden" value="<?php echo $eco_cone; ?>">
        <input name="eco_ctwo" type="hidden" value="<?php echo $eco_ctwo; ?>">
      </p>
      <p>
        <input name="eco_post" type="submit" value="Add Comment" title="Click here to post your comment" class="input">
      </p>
      <p class="eco_by">All posts are monitored and subject to removal!</p>
      <p class="eco_by"><a href="http://phclaus.com/php-scripts/easy-comments/" title="Click here to get your own free copy of PHP Easy Comments">Powered by PHP Easy Comments v<?php echo $eco_ver; ?></a></p>
    </form>
    <script type="text/javascript">
    //** update character counter
    function eco_tmax() {
      var eco_cmax = <?php echo $eco_tmax; ?>;
      var eco_ccnt = document.getElementById("eco_ccnt").innerHTML = (eco_cmax - document.getElementById("eco_text").value.length);

      if (eco_ccnt == 0) {
        document.getElementById("eco_ccnt").innerHTML = "You have reached the maximum characters limit!";
      } else if (eco_ccnt < 0) {
        var eco_repi = document.getElementById("eco_ccnt").innerHTML.replace("-", "");
        document.getElementById("eco_ccnt").innerHTML = "You are " + eco_repi + " characters over the limit!";
      } else {
        document.getElementById("eco_ccnt").innerHTML = eco_ccnt;
      }
    }
    </script>
<?php
}
?>
