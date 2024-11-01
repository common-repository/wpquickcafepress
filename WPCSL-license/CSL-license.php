<?PHP

/*
 * Currently only checks for an existing license key (PayPal
 * transaction ID).
 */
function wpCSL_check_license_key($product_key, $license_key) {
  return (file_get_contents("http://cybersprocket.com/paypal/valid_transaction.php?id=$license_key&pid=$product_key") == 'true') ? true : false;
}


function wpCSL_check_product_key($prefix) {
  // Attempt to find old versions of the license
  if (!get_option($prefix.'-purchased') && (get_option('purchased') != '') ) {
    update_option($prefix.'-purchased', get_option('purchased'));
  }
  if (!get_option($prefix.'-license_key') && (get_option('license_key') != '') ) {
    update_option($prefix.'-license_key', get_option('license_key'));
  }

  if (!get_option($prefix.'-purchased')) {
    if (get_option($prefix.'-license_key') != '') {
      update_option($prefix.'-purchased', wpCSL_check_license_key($prefix.'-product_key',get_option($prefix.'-license_key')));
    }

    if (!get_option($prefix.'-purchased')) {
      $notices['product'] =
        "You have not provided a valid license key for this plugin. Until you do so, it will only display content for Admin users.";
    }
  }

  return (isset($notices)) ? $notices : false;
}

function wpCSL_initialize_license_options($prefix) {
  register_setting($prefix.'-settings', $prefix.'-license_key');
  register_setting($prefix.'-Settings', $prefix.'-purchased');
}


?>