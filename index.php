<?php
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
?>
<!DOCTYPE html>
<html lang="en-GB">
    <head>
        <meta charset="UTF-8"/>
        <meta name=language content="en-GB"/>
        <meta name=viewport content="width=device-width, height=device-height, initial-scale=1"/>
        <meta name=robots content="noodp, noydir"/>
        <meta name=description content="PHP Easy Comments Demo"/>
        <meta name=keywords content="PHP Easy Comments Demo"/>
        <link rel=stylesheet href="./easy-comments.css" type="text/css"/>
        <title>PHP Easy Comments Demo</title>
    </head>
    <body>
        <h1>PHP Easy Comments Demo</h1>
<?php include './easy-comments.php'; ?>
    </body>
</html>
