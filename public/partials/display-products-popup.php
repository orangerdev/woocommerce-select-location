<?php

foreach ($product_variations as $variation_id) :

  $variation = wc_get_product($variation_id);
  $attributes = $variation->get_variation_attributes();
  $stock = $variation->get_stock_quantity();
  $stock_label = $stock > 0 ? sprintf(__('Stok: %s', 'ordv-biteship'), $stock) : __('Stok Habis', 'ordv-biteship');
  $term = get_term_by('slug', $attributes['attribute_pa_location'], 'pa_location');
  $loc_name = $term->name;
  $loc_slug = '';
  $loc_desc = $term->description;

  $display_attributes = [];

  foreach ($attributes as $attr_key => $attr_val) :
    $attr = str_replace('attribute_', '', $attr_key);
    if ($attr === 'pa_location') :
      $loc_slug = $attr_val;
      continue;
    endif;

    $term = get_term_by('slug', $attr_val, $attr);

    if ($term) :
      $display_attributes[] = $term->name . ':' . $attr_val;
    else :
      $display_attributes[] = $attr . ':' . $attr_val;
    endif;
  endforeach;

  // generate product url with attributes as parameters
  $product_url = get_permalink($product->get_id());
  $product_url = add_query_arg($attributes, $product_url);
?>

  <div class="wb-grid wb-location-store">
    <div class="wb-col-70 wb-location-store-name">
      <h4><?php echo $loc_name; ?></h4>
      <p><?php echo $loc_desc; ?></p>
      <p><small><?= implode(", ", $display_attributes); ?> | <?php echo $stock_label; ?></small></p>
    </div>
    <div class="wb-col-30 wb-location-store-action">
      <?php if ($stock === 0) : ?>
        <p>Stok habis</p>
      <?php else : ?>
        <a href="<?= $product_url; ?>" class="wb-location-store-choose-btn" type="button">
          <?php _e('Beli Di Sini', 'ordv-biteship'); ?>
        </a>
      <?php endif; ?>
    </div>
  </div>
<?php

endforeach;
