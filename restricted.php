<?php
/*
 * check restricted names -- meaningful fragments are ok
 *
 * admin => admin, ADMINfoo, AdminisTraitor...
 * webma => webma, myWebMama, yourWebMASTER...
 */
if (preg_match("/admin/i", $eco_name) ||
    preg_match("/local/i", $eco_name) ||
    preg_match("/moder/i", $eco_name) ||
    preg_match("/root/i", $eco_name) ||
    preg_match("/test/i", $eco_name) ||
    preg_match("/webma/i", $eco_name)) {

  //** check if admin post
  if ($eco_name === $eco_apfx . $eco_asfx) {
    $eco_name = $eco_apfx . $eco_asfx;
  } else {
    $eco_name = $eco_anon;
    $eco_stat = "Part or all of selected name is restricted!";
  }
}
