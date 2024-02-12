<?php
$taxonomy    = 'pa_location';
$terms       = get_terms($taxonomy);
$loc_default = carbon_get_theme_option('woo_location_default');
$loc_cookie  = isset($_COOKIE['wb_loc']) ? $_COOKIE['wb_loc'] : '';
if ($loc_cookie) :
  $loc_default = $loc_cookie;
endif;
?>
<div class="wb-choose-location-wrap">
  <label>Anda belanja dari</label>
  <select name="location" id="wb-choose-location">
    <option value="">- Choose Location -</option>
    <?php
    foreach ($terms as $key => $term) :
    ?>
      <option value="<?php echo esc_attr($term->slug) ?>" <?php selected($loc_default, $term->slug, true); ?>><?php echo esc_html($term->name) ?></option>';
    <?php
    endforeach;
    ?>
  </select>
</div>