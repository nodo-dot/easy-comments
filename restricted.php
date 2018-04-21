<?php
/**
 * PHP Version 5 and above
 *
 * Restricted names and fragments
 *
 * @category  PHP_Comment_Scripts
 * @package   PHP_Easy_Comments
 * @author    P H Claus <phhpro@gmail.com>
 * @copyright 2015 - 2018 P H Claus
 * @license   https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @link      https://github.com/phhpro/easy-comments
 */


/**
 * Names and fragments to be blocked as user name or part thereof
 *
 * Example #1: admin => evilADMIN => AdminisTraitor
 * Example #2: webma => myWebMama => yourWebMASTER
 */
$eco_nono = array(
    "admin",
    "cunt",
    "local",
    "moder",
    "root",
    "shit",
    "test",
    "webma"
);


/**
 ***********************************************************************
 *          YOU PROBABALY DON'T WANT TO TOUCH ANYTHING BELOW THIS LINE *
 ***********************************************************************
 */
foreach ($eco_nono as $eco_skip) {

    if (preg_match("/$eco_skip/i", $eco_name)) {

        if ($eco_name === $eco_apfx . $eco_asfx) {
            $eco_name = $eco_apfx . $eco_asfx;
        } else {
            $eco_name = $eco_user;
            $eco_stat = $eco_lang['restricted'];
        }
    }
}
