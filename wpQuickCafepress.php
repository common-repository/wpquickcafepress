<?php
/*
  Plugin Name: MoneyPress : CafePress Edition
  Plugin URI: http://www.cybersprocket.com/products/wpquickcafepress/
  Description: MoneyPress CafePress Edition allows you to quickly and easily display products from CafePress on any page or post via a simple shortcode.
  Author: Cyber Sprocket Labs
  Version: 2.2
  Author URI: http://www.cybersprocket.com/

  Our PID: 3783719
  
  http://www.tkqlhce.com/click-PID-10467594?url=<blah>      

  
*/

/*	Copyright 2010  Cyber Sprocket Labs (info@cybersprocket.com)

        This program is free software; you can redistribute it and/or modify
        it under the terms of the GNU General Public License as published by
        the Free Software Foundation; either version 3 of the License, or
        (at your option) any later version.

        This program is distributed in the hope that it will be useful,
        but WITHOUT ANY WARRANTY; without even the implied warranty of
        MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
        GNU General Public License for more details.

        You should have received a copy of the GNU General Public License
        along with this program; if not, write to the Free Software
        Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


/* Includes */
if (!strpos(implode(get_included_files()), 'CSL-license.php')) {
  include_once('WPCSL-license/CSL-license.php');
}

/* Defines */
define('QCPPLUGINURL', plugins_url('',__FILE__));

/*--------------------------------------------------------------------------
*
* Variable Initialization
*
*/
$cpstore_my_error_handler = set_error_handler("cpstore_myErrorHandler");

if ( is_admin() ) {
  add_action('admin_init', 'wpQC_Register_Settings');
  add_action('admin_menu', 'wpQC_plugin_menu');
  add_action('admin_menu', 'wpQC_Handle_AdminMenu');
  add_filter('admin_print_scripts', 'wpQC_AdminHead');
  add_action('admin_notices', 'wpQC_admin_notices');
} else {
  wpCSL_check_product_key('qcp');
}

add_shortcode('QuickCafe', 'wpQuickCafe');

add_filter('wp_print_scripts', 'wpQC_add_js');
add_filter('wp_print_styles', 'wpQC_add_css');



function wpQC_Register_Settings() {
  /* Product Settings */
  wpCSL_initialize_license_options('qcp');

  /* Configuration Settings */
  register_setting( 'qcp-settings', 'config_cpapikey' );
  register_setting( 'qcp-settings', 'config_cjpid' );

  /* Display Settings */
  register_setting( 'qcp-settings', 'display_numtoshow' );
  register_setting( 'qcp-settings', 'display_numtopreview' );

  /* CSS Settings */
  register_setting( 'qcp-settings', 'css_viewall');
  register_setting( 'qcp-settings', 'css_category');
  register_setting( 'qcp-settings', 'css_catmenu');
  register_setting( 'qcp-settings', 'css_container');
  register_setting( 'qcp-settings', 'css_float');
  register_setting( 'qcp-settings', 'css_float_img');
  register_setting( 'qcp-settings', 'css_float_p');
  register_setting( 'qcp-settings', 'css_price_a');
  register_setting( 'qcp-settings', 'css_float_hover');
  register_setting( 'qcp-settings', 'css_float_hover_img');
  register_setting( 'qcp-settings', 'css_float_hover_p');
  register_setting( 'qcp-settings', 'css_price_hover_a');
}

function wpQC_admin_notices() {
  /* $notices[] = wpCJ_check_required_options(); */
  /* $notices[] = wpCJ_check_cache(); */
  $notices[] = wpCSL_check_product_key('qcp');

  // Generate the warning message

  foreach ($notices as $notice) {
    if ($notice) {
      $notice_output = "<div id='cscj_warning' class='updated fade' style='background-color: rgb(255, 102, 102);'>";
      $notice_output .= sprintf(__('<p><strong><a href="%1$s">MoneyPress : CafePress Edition</a> needs attention: </strong>'),"options-general.php?page=CSQC-options");

      if (isset($notice['options'])) {
        $notice_output .= 'Please provide the following on the settings page: ';
        $notice_output .= join(',', $notice['options']);
      }

      foreach( array('cache', 'product') as $item) {
        if (isset($notice[$item])) {
          $notice_output .= $notice[$item];
        }
      }

      $notice_output .= "</p></div>";

      $notices_output[] = $notice_output;
    }
  }

  if ($notices_output) {
    foreach ($notices_output as $output) echo $output;
  }
}

/*--------------------------------------------------------------------------
*
* wpQuickCafe
*
* Main processing function.  This is what parses and generates the output
* for the QuickCafe tag.
*
*/
function wpQuickCafe ($attr, $content) {
    global $thisprod;
    global $current_user;
    get_currentuserinfo();
    $UserIsAnAdmin = ($current_user->wp_capabilities['administrator'] || ($current_user->user_level == '10'));
    
    // If they don't have a license and are not admin, return blank.
    //
    #if ( ($current_user->wp_capabilities['administrator']) || ($current_user->user_level == '10') || get_option('qcp-purchased')) {
    if ( !$UserIsAnAdmin && !get_option('qcp-purchased')) {
        return '';
    }

    // Get the CafePress API Key - return if blank.
    $cpstore_content = '';
    $cpApiKey = trim(get_option('config_cpapikey'));
    if ($cpApiKey == '') {
      if ($UserIsAnAdmin) { $cpstore_content = '<div><strong>Admin Notice</strong><br/>MoneyPress : CafePress Edition is missing the CafePress Developer API key, get it from developer.cafepress.com and save it in your MoneyPress CafePress Edition settings.</div>'; } 
      return $cpstore_content;      
    }

    // Process theincoming attributes
    $attr = shortcode_atts(array('return'   => get_option('display_numtoshow'),
                                 'preview'   => get_option('display_numtopreview')), $attr);
    $cpstore_url = $content;

    // Set the display attributes
    $cpstore_preview = $attr['preview'];
    $cpstore_return = $attr['return'];
    $cpstore_permalink = get_permalink($post->ID);
    $cpstore_startPage = (isset($_GET['startpage']) && $_GET['startpage']) ? $_GET['startpage'] : "1";

    // build the file name
    $cpstoreArray1 = explode("cafepress.com/",$cpstore_url);
    list($cpstore_storeid,$cpstore_sectionid) = explode("/",$cpstoreArray1[1]);
    if ($cpstore_sectionid == '') { $cpstore_sectionid='0'; }	                    # Default store section 0 = top if not set
    $cpstore_dir = dirname(__FILE__) ;
    $cpstore_cache_dir = $cpstore_dir . "/cache" ;
    cpstore_cleancache($cpstore_cache_dir);
    $cpstore_FileName = $cpstore_cache_dir . "/" . $cpstore_storeid . "_" . $cpstore_sectionid . ".xml";
    $depth = array();
    $qcpCacheOK = true;

    // Make sure the cache directory exists with the proper permissions.
    if (file_exists($cpstore_cache_dir) === false) {
        $qcpCacheOK = mkdir($cpstore_cache_dir, 0777, true);
    }

    // No cache?  Build one...
    if ($qcpCacheOK && (!file_exists($cpstore_FileName) || !filesize($cpstore_FileName))) {
        $CafeURL = "http://open-api.cafepress.com/product.listByStoreSection.cp?appKey=$cpApiKey&storeId=$cpstore_storeid&sectionId=$cpstore_sectionid&v=3";

        // Fetch data using PHP5 built in method
        // for some reason the built-in Wordpress method
        // times out on servers with high latency
        //
        # $file_content = wp_remote_fopen($CafeURL);
        $file_content = file_get_contents($CafeURL); 
        
        // Oops - we got an error back, or the return result is an empty string!
        //
        if (
          (preg_match('/<help>\s+<exception-message>(.*?)<\/exception-message>/',$file_content,$error) > 0) ||
          (strlen($file_content)<1)
          ){
            $cpstore_content = 'No products found.<br/>' . $error[1] . '<br/>';
            if ($UserIsAnAdmin) {
                $cpstore_content .= '<br/><strong>Admin Notice</strong><br/>'
                                 .  'Try pasting this URL into your browser.  If it returns a bunch of XML then the links are working but your server is not.<br/><br/>'
                                 .  'Please ask your system administrator to try to fetch the following URL directly from the server:<br/>'
                                 .  "http://open-api.cafepress.com/product.listByStoreSection.cp?appKey=$cpApiKey&storeId=$cpstore_storeid&sectionId=$cpstore_sectionid&v=3<br/><br/>"
                                 .  "<pre>Results:\n$file_content</pre><br/>"; 
            } 
            return $cpstore_content;
            
        // Write Cache File if the response does not contain an error message.
        // And the response is not blank.
        //
        } else {
            if ($fh = fopen($cpstore_FileName, 'w')) {
                if (fwrite($fh, $file_content) === false) {
                    $qcpCacheOK = false;
                }
              fclose($fh);
            } else {
              $qcpCacheOK = false;
            }
        }

      // Read Cache
    }
    else if ($qcpCacheOK) {
        if (!$file_content = file_get_contents($cpstore_FileName)) {
            if ($UserIsAnAdmin) { $cpstore_content = '<div><strong>Admin Notice</strong><br/>MoneyPress : CafePress Edition could not open cache file '.$cpstore_FileName.'</div>'; }             
            return $cpstore_content; 
        }
    }

    // Setup for XML Parsing
    $cpstore_xml_parser = xml_parser_create();
    xml_set_element_handler($cpstore_xml_parser, "startElement", "endElement");
    if (!xml_parse($cpstore_xml_parser, $file_content, feof($fp))) {
      return sprintf("XML error: %s at line %d",
                     xml_error_string(xml_get_error_code($cpstore_xml_parser)),
                     xml_get_current_line_number($cpstore_xml_parser));
    }
    xml_parser_free($cpstore_xml_parser);

    // Display Settings...

    // Default category ordering
    $cpstore_category = array(
                              "Shirts (short)",
                              "Shirts (long)",
                              "Kids Clothing",
                              "Outerwear",
                              "Intimate Apparel",
                              "Home & Office",
                              "Fun Stuff",
                              "Cards, Prints & Calendars",
                              "Hats & Caps",
                              "Bags",
                              "Stickers",
                              "Mugs",
                              "Pets",
                              "Buttons & Magnets",
                              "Books & CDs",
                              );

    // Get CSS Settings
    $cpstore_css_container = get_option('css_container');
    $cpstore_css_category = get_option('css_category');
    $cpstore_css_float = get_option('css_float');
    $cpstore_css_float_img = get_option('css_float_img');
    $cpstore_css_float_p = get_option('css_float_p');
    $cpstore_css_price_a = get_option('css_price_a');
    $cpstore_css_float_hover = get_option('css_float_hover');
    $cpstore_css_float_hover_img = get_option('css_float_hover_img');
    $cpstore_css_float_hover_p = get_option('css_float_hover_p');
    $cpstore_css_price_hover_a = get_option('css_price_hover_a');
    $cpstore_css_viewall = get_option('css_viewall');
    $cpstore_css_catmenu = get_option('css_catmenu');


    // Build Style Sheet
    $cpstore_content .= '<style>
<!--
div.cpstore_css_category {
' . $cpstore_css_category . '
}
div.cpstore_css_container {
' . $cpstore_css_container . '
}
div.cpstore_css_spacer {
clear: both;
}
div.cpstore_css_float {
' . $cpstore_css_float . '
}
div.cpstore_css_float img{
' . $cpstore_css_float_img . '
}
div.cpstore_css_float p {
' . $cpstore_css_float_p . '
}
div.cpstore_css_float_hover {
' . $cpstore_css_float_hover . '
}
div.cpstore_css_float_hover img{
' . $cpstore_css_float_hover_img . '
}

div.cpstore_css_float_hover p {
' . $cpstore_css_float_hover_p . '
}
div.cpstore_css_price a {
' . $cpstore_css_price_a . '
}

div.cpstore_css_price a:hover {
' . $cpstore_css_price_hover_a . '
}
div.cpstore_css_viewall {
' . $cpstore_css_viewall . '
}
div.cpstore_css_viewall a {
' . $cpstore_css_viewall . '
}
div.cpstore_css_catmenu {
' . $cpstore_css_catmenu . '
}

-->
</style>
<div class="cpstore_css_container">'
    ;

    // create the category menu if this is a single post or page
    if (is_single() || is_page())  {
      foreach ($cpstore_category as $key => $cpstore_catname) {
        $cpstore_productlist = $thisprod["$cpstore_catname"];
        if (!empty($cpstore_productlist)){
          $cpstore_catlist .= "<span style=\"white-space:nowrap;\"><a href=\"#$key\">$cpstore_catname</a></span> | ";
        }
      }
      $cpstore_catlist = substr($cpstore_catlist,0,strlen($cpstore_catlist)-3);
      $cpstore_catlist = '<div class="cpstore_css_catmenu"><a name="cpstore_menu"></a>' . $cpstore_catlist . '</div>';
      $cpstore_content .= $cpstore_catlist;
    }
    $cpstore_content .= '<div class="cpstore_css_spacer"></div>';

    // now run through each category arm -f nd show the thumbs
    foreach ($cpstore_category as $key => $cpstore_catname) {
      $cpstore_productlist = $thisprod["$cpstore_catname"];
      if (!empty($cpstore_productlist)){
        $cpstore_content .= '<div class="cpstore_css_spacer"></div>';
        $cpstore_content .= "<div class=\"cpstore_css_category\"><a name=\"$key\"></a>$cpstore_catname</div>";
        foreach ($cpstore_productlist as $cpstore_id => $cpstore_attr) {
              $this_link = $cpstore_attr["link"];
              $cpstore_content .= '
<div class="cpstore_css_float" onmouseover="this.className=\'cpstore_css_float_hover\'" onmouseout="this.className=\'cpstore_css_float\'">
<a href="' . $this_link . '"><img title="' . $cpstore_attr["description"] . '" src="' . $cpstore_attr["image"] . '" alt="' . $cpstore_attr["description"] . '" width="150" height="150" /></a>
<div><a class="thickbox" href="' . str_replace("150x150","350x350",$cpstore_attr["image"]) . '">
+zoom</a></div><p>' . $cpstore_attr["name"] . '</p><div class="cpstore_css_price"><a href="' . $this_link . '">Buy Now! - $' . $cpstore_attr["price"] . '</a></div></div>
';
              $cpstore_loopcounter++;
              if (!is_single() && ($cpstore_loopcounter == $cpstore_preview)) { // exit both loops
                $cpstore_content .= '<div class="cpstore_css_spacer"></div>';
                $cpstore_content .= "<div class=\"cpstore_css_viewall\"><a href=\"" . get_permalink($post->ID) . "\">View all</a></div>";
                break 2;
    
              }
              if (is_single() && ($cpstore_loopcounter == $cpstore_return)) { // exit both loops
                break 2;
              }
            }

            // end of individual category loop
            // if this is a single post or page, show the "back to top" link
            if (is_single() || is_page())  {
              $cpstore_content .= '<div class="cpstore_css_spacer"></div><div class="cpstore_toplink"><a href="#cpstore_menu">back to menu</a></div>';
            }
        }
    }
    $cpstore_content .= '<div class="cpstore_css_spacer"></div><div style="margin-bottom:2em;"></div></div>';
    
    # Info messages
    if ($UserIsAnAdmin && !$qcpCacheOK) { 
        $cpstore_content .= '<br />MoneyPress : CafePress Edition could not create the cache file '.$cpstore_FileName.'<br />';
    }
    
    # Return
    return $cpstore_content;
}


//--------------------------------------------------------------------------
function wpQC_add_css() {
  wp_enqueue_style('thickbox');
}

//--------------------------------------------------------------------------
function wpQC_add_js() {
  wp_enqueue_script('jquery');
  wp_enqueue_script('thickbox');
}


//--------------------------------------------------------------------------
function wpQC_Handle_AdminMenu() {
  add_meta_box('cpStoreMB', 'MoneyPress CafePress Edition Entry', 'cpStoreInsertForm', 'post', 'normal');
  add_meta_box('cpStoreMB', 'MoneyPress CafePress Edition Entry', 'cpStoreInsertForm', 'page', 'normal');
}


//--------------------------------------------------------------------------
function cpStoreInsertForm() {
?>
<table class="form-table">
  <tr valign="top">
    <th align="right" scope="row"><label for="wpCPStore_url"><?php _e('Section Url:')?></label></th>
    <td>
      <input type="text" size="40" style="width:95%;" name="wpCPStore_url" id="wpCPStore_url" />
    </td>
  </tr>
  <tr valign="top">
    <th align="right" scope="row"><label for="wpCPStore_preview"><?php _e('Preview how many?:')?></label></th>
    <td>
      <input type="text" size="40" style="width:95%;" name="wpCPStore_preview" id="wpCPStore_preview" />
    </td>
  </tr>
  <tr valign="top">
    <th align="right" scope="row"><label for="wpCPStore_return"><?php _e('Show how many?:')?></label></th>
    <td>
      <input type="text" size="40" style="width:95%;" name="wpCPStore_return" id="wpCPStore_return" />
    </td>
  </tr>
</table>
<p class="submit">
  <input type="button" onclick="return this_wpCPStoreAdmin.sendToEditor(this.form);" value="<?php _e('Create MoneyPress CafePress Edition Shortcode &raquo;'); ?>" />
</p>
<?php
}


    //--------------------------------------------------------------------------
    function wpQC_AdminHead () {
      if ($GLOBALS['editing']) {
        wp_enqueue_script('wpCPStoreAdmin', WP_PLUGIN_URL .'/wpQuickCafepress/js/cpstore.js', array('jquery'), '1.0.0');
      }
    }


    //--------------------------------------------------------------------------
    function wpQC_plugin_menu() {
        add_options_page('MoneyPress : CafePress Edition Options', 'MoneyPress : CafePress Edition', 'administrator', 'CSQC-options', 'qcp_plugin_options');
    }


    //--------------------------------------------------------------------------
    function qcp_plugin_options() {
      include('include/admin/options_page.php');
    }


    //--------------------------------------------------------------------------
    function cpstore_cleancache($directory) {
      $seconds_old = 84600;
      if( !$dirhandle = @opendir($directory) )
        return;

      while( false !== ($filename = readdir($dirhandle)) ) {
        if( $filename != "." && $filename != ".." ) {
          $filename = $directory. "/". $filename;

          if( @filemtime($filename) < (time()-$seconds_old) )
            @unlink($filename);
        }
      }

    }


    //--------------------------------------------------------------------------
    function startElement($parser, $name, $attrs) {
      global $depth,$thisprod;
      if ($depth[$parser] == 1) {
        $temp_cat = $attrs['CATEGORYCAPTION'];
        $temp_id = $attrs['ID'];
        
        $temp_link = "http://www.cafepress.com/" . $attrs['STOREID'] . "." . $temp_id;
        $cjpid = trim(get_option('config_cjpid'));
        if ($cjpid != '') {
            $temp_link = sprintf('http://www.tkqlhce.com/click-%s-10467594?url=%s',$cjpid,$temp_link);
        }
                
        $temp_description = $attrs['DESCRIPTION'];
        $temp_name = $attrs['NAME'];
        $temp_price = $attrs['SELLPRICE'];
        $temp_image = str_replace("240x240","150x150",$attrs['DEFAULTPRODUCTURI']);

        $thisprod[$temp_cat][$temp_id]["name"] = $temp_name;
        $thisprod[$temp_cat][$temp_id]["link"] = $temp_link;
        $thisprod[$temp_cat][$temp_id]["description"] = $temp_description;
        $thisprod[$temp_cat][$temp_id]["price"] = $temp_price;
        $thisprod[$temp_cat][$temp_id]["image"] = $temp_image;

      }
      $depth[$parser]++;
    }



    //--------------------------------------------------------------------------
    function endElement($parser, $name) {
      global $depth;
      $depth[$parser]--;
    }



    //--------------------------------------------------------------------------
    function cpstore_myErrorHandler($errno, $errstr, $errfile, $errline) {
      switch ($errno) {
      case E_USER_ERROR:
        echo "<b>My ERROR</b> [$errno] $errstr<br />\n";
        echo "  Fatal error on line $errline in file $errfile";
        echo ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
        echo "Aborting...<br />\n";
        exit(1);
        break;

      case E_USER_WARNING:
        echo "<b>My WARNING</b> [$errno] $errstr<br />\n";
        break;

      case E_USER_NOTICE:
        echo "<b>My NOTICE</b> [$errno] $errstr<br />\n";
        break;

      default:

        //echo "Unknown error type: [$errno] $errstr<br />\n";
        break;
      }

      /* Don't execute PHP internal error handler */
      return true;
    }

    ?>