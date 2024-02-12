<?php

function wcl_get_default_store_location()
{
  $loc_default = carbon_get_theme_option('woo_location_default');
  $loc_cookie  = isset($_COOKIE['wb_loc']) ? $_COOKIE['wb_loc'] : '';
  if ($loc_cookie) :
    $loc_default = $loc_cookie;
  endif;

  return $loc_default;
}
