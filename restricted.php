<?php
/*
 * check restricted names -- meaningful fragments are ok
 *
 * admin => admin, ADMINfoo, AdminisTraitor...
 * webma => webma, myWebMama, yourWebMASTER...
 */

$eco_aray = array (
                  "admin",
                  "local",
                  "moder",
                  "root",
                  "test",
                  "webma",
                 );

foreach ($eco_aray as $eco_nono) {

  if (preg_match("/$eco_nono/i", $eco_name)) {

    //** check if admin post
    if ($eco_name === $eco_apfx . $eco_asfx) {
      $eco_name = $eco_apfx . $eco_asfx;
    } else {
      $eco_name = $eco_anon;
      $eco_stat = "Part or all of the selected name is restricted!";
    }
  }
}
