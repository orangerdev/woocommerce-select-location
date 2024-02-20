<?php

global $post;

$product = wc_get_product($post->ID);
$variations = $product->get_available_variations();
$attributes = array_keys($product->get_attributes());
$attributes = array_filter($attributes, function ($attr) {
  return $attr === 'pa_location' ? false : true;
});

$loc_value = wcl_get_default_store_location();

$term = get_term_by('slug', $loc_value, 'pa_location');
if ($term) :
  $loc_name = $term->name;
endif;

?>
<div class="wb-no-stock">
  <p><?php _e('Maaf, produk ini tidak tersedia di lokasi yang dipilih. Silahkan pilih ukuran yang lain atau cek di lokasi toko yang lain', 'woo-chose-location'); ?></p>
</div>

<?php

if ($loc_name) :
?>
  <div class="wb-loc-store">
    <p><?= _e("Location", "woo-choose-location"); ?>: <b><span class="wb-loc-store-name"><?php echo $loc_name; ?></span></b></p>
  </div>
<?php
endif;
?>
<div class="wb-check-location-store-wrap">
  <input type="hidden" name="wb_product_id" id="wb_product_id" value="<?php echo get_the_ID(); ?>">
  <?php
  foreach ($attributes as $attr) :
    $value = isset($_GET['attribute_' . $attr]) ? sanitize_text_field($_GET['attribute_' . $attr]) : '';
  ?>
    <input type="hidden" class="wb_product_attr" id="wb_product_attr_<?= $attr; ?>" data-attr="<?= $attr; ?>" value="<?= $value; ?>">
  <?php endforeach; ?>
  <input type="hidden" class="wb_product_attr" id="wb_product_attr_pa_location" data-attr="pa_location" value="<?= $loc_value; ?>">
  <button type="button" class="wb-check-location-store-open-btn"><?php _e('Cek Di Toko Lain', 'woo-chose-location'); ?></button>
</div>
<div class="wb-popup-wrap wb-check-location-store-popup">
  <div class="wb-popup">
    <div class="wb-popup-header">
      <h3><?php _e('Cari Toko', 'woo-chose-location'); ?></h3>
      <span class="wb-check-location-store-close-btn">
        <img src="<?php echo WOO_CHOOSE_LOCATION_URI; ?>/public/img/x-icon.svg" alt="x">
      </span>
    </div>
    <div class="wb-popup-content">
      <div class="wb-popup-content-loading">
        <p><?php _e('Sedang mengambil data toko ...', 'woo-chose-location'); ?></p>
      </div>
      <div class="wb-location-stores">
      </div>
    </div>
  </div>
</div>