<?php
/*
 * check restricted names or fragments thereof
 *
 * admin => admin, ADMINfoo, AdminisTraitor...
 * webma => webma, myWebMama, yourWebMASTER...
 */
$eco_nono = array ("admin", "local", "moder", "root", "test", "webma",);

foreach ($eco_nono as $eco_skip) {

  if (preg_match("/$eco_skip/i", $eco_name)) {

    //** check if admin post
    if ($eco_name === $eco_apfx . $eco_asfx) {
      $eco_name = $eco_apfx . $eco_asfx;
    } else {
      $eco_name = $eco_anon;
      $eco_stat = "Part or all of the selected name is restricted!";
    }
  }
}
