<?php
/**
 * PHP Version 5 and above
 *
 * @category  PHP_Comment_Scripts
 * @package   PHP_Easy_Comments
 * @author    P H Claus <phhpro@gmail.com>
 * @copyright 2015 - 2018 P H Claus
 * @license   https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @link      https://github.com/phhpro/easy-comments
 *
 * Check restricted names and/or fragments thereof
 *
 * Example #1: admin => admin => ADMINfoo => AdminisTraitor ...
 * Example #2: webma => webma => myWebMama => yourWebMASTER ...
 */
$eco_nono = array("admin",
                  "local",
                  "moder",
                  "root",
                  "test",
                  "webma");

foreach ($eco_nono as $eco_skip) {

    if (preg_match("/$eco_skip/i", $eco_name)) {

        //** Check if admin post
        if ($eco_name === $eco_apfx . $eco_asfx) {
            $eco_name = $eco_apfx . $eco_asfx;
        } else {
            $eco_name = $eco_user;
            $eco_stat = $eco_lang['restricted'];
        }
    }
}
