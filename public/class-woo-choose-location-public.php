<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://https://ridwan-arifandi.com/
 * @since      1.0.0
 *
 * @package    Woo_Choose_Location
 * @subpackage Woo_Choose_Location/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Woo_Choose_Location
 * @subpackage Woo_Choose_Location/public
 * @author     OrangerDev Team <orangerdigiart@gmail.com>
 */
class Woo_Choose_Location_Public
{

  /**
   * The ID of this plugin.
   *
   * @since    1.0.0
   * @access   private
   * @var      string    $plugin_name    The ID of this plugin.
   */
  private $plugin_name;

  /**
   * The version of this plugin.
   *
   * @since    1.0.0
   * @access   private
   * @var      string    $version    The current version of this plugin.
   */
  private $version;

  /**
   * Initialize the class and set its properties.
   *
   * @since    1.0.0
   * @param      string    $plugin_name       The name of the plugin.
   * @param      string    $version    The version of this plugin.
   */
  public function __construct($plugin_name, $version)
  {

    $this->plugin_name = $plugin_name;
    $this->version = $version;
  }

  /**
   * Register Scripts & Styles in check-awb page
   * Hooked via	action wp_enqueue_scripts
   * @since 		1.0.0
   * @return 		void
   */
  public function enqueue_scripts()
  {

    $loc_default = carbon_get_theme_option('woo_location_default');

    wp_enqueue_style($this->plugin_name . '-checkout', WOO_CHOOSE_LOCATION_URI . 'public/css/checkout.css');
    wp_enqueue_style($this->plugin_name . '-location', WOO_CHOOSE_LOCATION_URI . 'public/css/woo-choose-location-public.css');

    wp_enqueue_script($this->plugin_name . '-location', WOO_CHOOSE_LOCATION_URI . 'public/js/woo-choose-location-public.js', ['jquery'], $this->version, true);
    wp_localize_script($this->plugin_name . '-location', 'wb_loc_vars', [
      'ajax_url' => admin_url('admin-ajax.php'),
      'ajax_nonce' => [
        'get_location_store' => wp_create_nonce('wb_get_location_store')
      ],
      'loc_default' => $loc_default
    ]);
  }

  public function display_sticky_choose_location()
  {
    if (!wp_is_mobile()) :
      if (is_front_page() || is_product_category()) :

        include WOO_CHOOSE_LOCATION_PATH . '/public/partials/choose-location.php';

      endif;
    endif;
  }

  public function display_sticky_choose_location_mobile()
  {
    if (wp_is_mobile()) :
      if (is_front_page() || is_product_category()) :

        include WOO_CHOOSE_LOCATION_PATH . '/public/partials/choose-location.php';

      endif;
    endif;
  }

  public function add_body_class($classes)
  {

    if (is_front_page() || is_product_category()) :
      $classes[] = 'wb-choose-location';
    endif;

    return $classes;
  }

  public function display_check_location_store()
  {
    include WOO_CHOOSE_LOCATION_PATH . '/public/partials/check-location-store.php';
  }

  /**
   * Get location stores by product id and location
   * Hooked via	action wp_ajax_get_location_stores
   * @since 		1.0.0
   * @return 		void
   */
  public function ajax_get_location_stores()
  {

    if (wp_verify_nonce($_GET['nonce'], 'wb_get_location_store')) :

      try {
        $get = wp_parse_args($_GET, [
          'attr' => array(),
          'product_id' => '',
        ]);
        $product = wc_get_product($get['product_id']);

        $loc_stores_html = '';

        $data = [
          'loc_stores_html' => $loc_stores_html,
          'product' => null,
          'data' => []
        ];

        if (!is_a($product, 'WC_Product'))
          throw new Exception('Product not found');

        if (!$product->is_type('variable'))
          throw new Exception('Product is not variable');

        $data['product'] = $product->get_name();;

        $att_terms = wc_get_product_terms($product->get_id(), 'pa_location', array('fields' => 'all'));
        $loc_arr = [];

        foreach ($att_terms as $term) :
          $loc_arr[$term->slug] = $term->description;
        endforeach;

        $variation_ids = $product->get_children();
        $product_variations = [];

        foreach ($variation_ids as $variation_id) :

          $variation = wc_get_product($variation_id);
          $attributes = $variation->get_variation_attributes();

          $data['product_variations'][$variation_id] = [];

          $added = true;
          foreach ($attributes as $attr_key => $attr_val) :
            $attr = str_replace('attribute_', '', $attr_key);

            if ($added === false)
              break;

            if ($attr === 'pa_location' && $attr_val === $get['attr']['pa_location']) :
              $data['product_variations'][$variation_id][] = 'same-location';
              $added = false;
              break;
            endif;

            if ($attr === 'pa_location')
              continue;

            if (!array_key_exists($attr, $get['attr'])) :
              $data['product_variations'][$variation_id] = sprintf('No %s', $attr);
              $added = false;
              break;
            endif;

            if ($attr_val !== $get['attr'][$attr]) :
              $data['product_variations'][$variation_id] = sprintf('diff-attr %s, %s', $attr, $attr_val);
              $added = false;
              break;
            endif;

          endforeach;

          if ($added) :

            $product_variations[] = $variation_id;

            $data['data'][] = [
              'variation_id' => $variation_id,
              'attributes' => $attributes
            ];
          endif;

        endforeach;

        if (empty($product_variations))
          throw new Exception('No product variations found');

        ob_start();

        require_once WOO_CHOOSE_LOCATION_PATH . '/public/partials/display-products-popup.php';

        $data['loc_stores_html'] = ob_get_clean();
      } catch (Exception $e) {
        wp_send_json_error($e->getMessage());
        exit;
      } finally {
        wp_send_json($data);
        exit;
      }

    endif;
  }

  /**
   * Set location by parameter in URL
   * Hooked via	action template_redirect, priority 99
   */
  public function set_location_by_url()
  {
    if (isset($_GET['location'])) :

      $taxonomy    = 'pa_location';

      // check if location is valid
      $loc_slug = sanitize_text_field($_GET['location']);
      $loc_term = get_term_by('slug', $loc_slug, $taxonomy);

      if ($loc_term) :

        // set location to cookie
        setcookie('wb_loc', $loc_slug, time() + (86400 * 30), "/");

        // redirect to current url without location parameter
        $url = strtok($_SERVER["REQUEST_URI"], '?');
        wp_redirect($url);
        exit;
      endif;

    elseif (isset($_GET['attribute_pa_location'])) :
      // check if location is valid
      $loc_slug = sanitize_text_field($_GET['attribute_pa_location']);
      $loc_term = get_term_by('slug', $loc_slug, 'pa_location');

      if ($loc_term) :

        // set location to cookie
        setcookie('wb_loc', $loc_slug, time() + (86400 * 30), "/");
      endif;
    endif;
  }

  public function set_location_by_external()
  {

    if (is_product()) :

      $loc_default = wcl_get_default_store_location();

      $set_by_url = false;
      if (isset($_GET['attribute_pa_location']) && !empty($_GET['attribute_pa_location'])) :
        $loc_default = $_GET['attribute_pa_location'];
        $set_by_url = true;
      endif;
?>
      <script>
        jQuery(document).ready(function($) {

          if ($('#pa_location').length > 0) {

            var loc = '<?php echo $loc_default; ?>';
            $('#pa_location').val(loc);
            $('#pa_location').trigger("change");

            <?php if ($set_by_url) : ?>
              document.cookie = "wb_loc=<?php echo $loc_default; ?>; path=/";
            <?php endif; ?>

          }

        });
      </script>
<?php

    endif;
  }
}
