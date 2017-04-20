<?php
/*
 * check restricted names -- meaningful fragments are ok
 *
 * admin => admin, ADMINfoo, AdminisTraitor...
 * webma => webma, WebMama, webMASTER...
 */
if (
    preg_match("/admin/i", $eco_name) ||
    preg_match("/local/i", $eco_name) ||
    preg_match("/moder/i", $eco_name) ||
    preg_match("/root/i", $eco_name) ||
    preg_match("/test/i", $eco_name) ||
    preg_match("/webma/i", $eco_name)
   ) {
  $eco_name = $eco_anon;
  $eco_stat = "Sorry, at least part of the selected name is restricted!";
  $eco_save = "n";
}
