<?php
/*
Plugin Name: Modulates Video Commerce Integration
Plugin URI: www.modulates.com/open_source_software_integration.php
Description: Modulates advertiser integration plugin. Plugin will automatically place the Modulates integration code in your website. Works with WP-Ecommerce, WooCommerce and Cart66.
Version: 1.3
Author: Modulates Corp
Author URI: http://www.modulates.com
*/

// don't load directly
if (!function_exists('is_admin')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}

define( 'MODULATES_VERSION', '1.3' );
define( 'MODULATES_RELEASE_DATE', date_i18n( 'F j, Y', '1397937230' ) );
define( 'MODULATES_DIR', plugin_dir_path( __FILE__ ) );
define( 'MODULATES_URL', plugin_dir_url( __FILE__ ) );

add_action('wp_head','modulates_header');
// Add scripts to wp_head()
  function modulates_header() { ?>
<script type="text/javascript">
(function() {
	var md = document.createElement('script'); md.type = 'text/javascript'; md.async = true;
	md.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'modulates-resources.s3.amazonaws.com/js/leadtrck.js';
	var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(md, s);
})();
</script>
<?php  }

function modulates_footer_woocommerce( $order_id ) {
$order = new WC_Order( $order_id ); 
$realordertotal = $order->get_total();
$thisshipping = $order->get_total_shipping();
$cart_subtotal = $realordertotal - $thisshipping;
$cart_subtotal = number_format($cart_subtotal, 2);
$cart_subtotal = str_replace(',', '', $cart_subtotal);

if($_COOKIE['matoid'] != $order_id ){ ?>
<script src="http://www.modulates.com/leadtrck.php?order_id=<?php echo $order_id; ?>&offer_id=<?php echo $_COOKIE['mDofid']; ?>&publisher_key=<?php echo $_COOKIE['mDaf']; ?>&advertiser_id=<?php echo $_COOKIE['mDadv']; ?>&amount=<?php echo $cart_subtotal; ?>"></script>
<script type="text/javascript">document.cookie = "matoid=; expires=Thu, 01 Jan 1980 00:00:00 GMT"; document.cookie="matoid=<?php echo $order_id; ?>";</script>

<?php } 
}
add_action( 'woocommerce_thankyou', 'modulates_footer_woocommerce' );

add_action('wp_footer','modulates_footer_wp_ecommerce');
function modulates_footer_wp_ecommerce() { 
	
if ( isset( $_GET['sessionid'] ) ) {
$sessionid = $_GET['sessionid'];
//GET ORDER ID
$result_order = mysql_query($wpdb->prepare("SELECT * FROM ". WPSC_TABLE_PURCHASE_LOGS ." WHERE sessionid = '".$sessionid."'"));
while($row_order = mysql_fetch_array($result_order)){
$order_id = $row_order['id'];
}
$cart = mysql_query( $wpdb->prepare("SELECT * FROM " . WPSC_TABLE_CART_CONTENTS . " WHERE purchaseid = '" . $order_id . "'"));
while($row_order2 = mysql_fetch_array($cart)){
$thisquantity = $row_order2['quantity'];
$total_amount = $row_order2['price'];
$total_amount = $total_amount * $thisquantity;
$total_amount = number_format($total_amount, 2);
}

if($_COOKIE['matoid'] != $order_id ){ ?>
<script src="http://www.modulates.com/leadtrck.php?order_id=<?php echo $order_id; ?>&offer_id=<?php echo $_COOKIE['mDofid']; ?>&publisher_key=<?php echo $_COOKIE['mDaf']; ?>&advertiser_id=<?php echo $_COOKIE['mDadv']; ?>&amount=<?php echo $total_amount; ?>"></script>
<script type="text/javascript">document.cookie = "matoid=; expires=Thu, 01 Jan 1980 00:00:00 GMT"; document.cookie="matoid=<?php echo $order_id; ?>";</script>
<?php }  } 

//CART 66
if ( isset( $_GET['ouid'] ) ) {
$sessionid66 = $_GET['ouid'];
//GET ORDER ID
global $wpdb;
$thistbprefix = $wpdb->base_prefix;
$cartordertb = "cart66_orders";
$result_order66 = mysql_query($wpdb->prepare("SELECT * FROM " .  $thistbprefix . $cartordertb . " WHERE ouid = '".$sessionid66."'"));
while($row_order66 = mysql_fetch_array($result_order66)){
$order_id66 = $row_order66['id'];
$total_amount = $row_order66['subtotal'];
}

if($_COOKIE['matoid'] != $order_id66 ){ ?>
<script src="http://www.modulates.com/leadtrck.php?order_id=<?php echo $order_id66; ?>&offer_id=<?php echo $_COOKIE['mDofid']; ?>&publisher_key=<?php echo $_COOKIE['mDaf']; ?>&advertiser_id=<?php echo $_COOKIE['mDadv']; ?>&amount=<?php echo $total_amount; ?>"></script>
<script type="text/javascript">document.cookie = "matoid=; expires=Thu, 01 Jan 1980 00:00:00 GMT"; document.cookie="matoid=<?php echo $order_id66; ?>";</script>
<?php } } 

}

if (!class_exists("modulates")) :

class modulates {
	var $settings, $options_page;
	
	function __construct() {	

		if (is_admin()) {
			// Load example settings page
		//	if (!class_exists("modulates_admin_settings"))
		//		require(MODULATES_DIR . 'modulates-settings.php');
		//	$this->settings = new Modulates_Settings();	
		}
		
		add_action('init', array($this,'init') );
		add_action('admin_init', array($this,'admin_init') );
		add_action('admin_menu', array($this,'admin_menu') );
		
		register_activation_hook( __FILE__, array($this,'activate') );
		register_deactivation_hook( __FILE__, array($this,'deactivate') );
	}


/*
		Enter our plugin activation code here.
	*/
	function _activate() {}

	/*
		Enter our plugin deactivation code here.
	*/
	function _deactivate() {}
	

  function admin_init() {
	}

	function admin_menu() {
	}


} // end class
endif;

// Initialize our plugin object.
global $modulates;
if (class_exists("modulates") && !$modulates) {
$modulates = new modulates();	
}	
?>