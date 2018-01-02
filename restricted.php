<?php
/**
 * PHP Version 5 and above
 *
 * Easy Comments is a trivial comments script with minimal bloat.
 *
 * @category PHP_Chat_Scripts
 * @package  PHP_Atom_Chat
 * @author   P H Claus <phhpro@gmail.com>
 * @license  https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @link     https://github.com/phhpro/easy-comments
 *
 * Check restricted names or fragments thereof
 *
 * Example 1: admin => admin => ADMINfoo => AdminisTraitor ...
 * Example 2: webma => webma => myWebMama => yourWebMASTER ...
 */
$eco_nono = array("admin", "local", "moder", "root", "test", "webma");

foreach ($eco_nono as $eco_skip) {

    if (preg_match("/$eco_skip/i", $eco_name)) {

        //** check if admin post
        if ($eco_name === $eco_apfx . $eco_asfx) {
            $eco_name = $eco_apfx . $eco_asfx;
        } else {
            $eco_name = $eco_anon;
            $eco_stat = $eco_lang['name_rest'];
        }
    }
}
