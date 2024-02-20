<?php

function wcl_get_default_store_location()
{
  $loc_default = carbon_get_theme_option('woo_location_default');
  if (isset($_GET['attribute_pa_location'])) :
    $loc_default = sanitize_text_field($_GET['attribute_pa_location']);
  else :
    $loc_cookie  = isset($_COOKIE['wb_loc']) ? $_COOKIE['wb_loc'] : '';
    if ($loc_cookie) :
      $loc_default = $loc_cookie;
    endif;
  endif;

  return $loc_default;
}
