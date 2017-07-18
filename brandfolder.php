<?php
/*
Plugin Name: Brandfolder
Plugin URI: http://wordpress.org/plugins/brandfolder/
Description: Adds the ability for you to edit your Brandfolder inside Wordpress, easily embed it using our Popup Embed, and integrates with the Media Library.
Version: 3.0.2
Author: Brandfolder, Inc.
Author URI: http://brandfolder.com
License: GPLv2
*/


//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
//
// START THE BF FOR EMBEDDING
//
//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

function brandfolder_popup_link($atts)  {

  $devOptions = get_option("brandfolderWordpressPluginAdminOptions");
  if (!empty($devOptions)) {
    foreach ($devOptions as $key => $option)
      $brandfolderAdminOptions[$key] = $option;
  }

  extract( shortcode_atts( array(
    'id' => $brandfolderAdminOptions["brandfolder_url"],
    'branding' => true,
    'collection' => '',
    'query' => '',
    'text' => "&lt;button style='padding: 15px 0px;margin: 10px auto;text-align: center;width: 100%;font-size: 15px;font-weight: bold;color: #333333;background-color: #dde2e6;border: 2px solid #cccccc;border-radius: 4px;'&gt;View our Brandfolder&lt;/button&gt;",
    'classes' => ''
    ), $atts )
   );

  if ($collection != '') {
    $url = $id."/".$collection;
  } else {
    $url = $id;
  }

  $elemid = uniqid('bf');
  $output = "<a id='".$elemid."' href='https://brandfolder.com/".$url."' class='".$classes."'>".html_entity_decode($text)."</a>";
  $output .= "<script type='text/javascript'>
      jQuery('#".$elemid."').click(function(e) {
          e.stopImmediatePropagation();
          Brandfolder.showEmbed({brandfolder_id: '".$id."', branding: ".$branding.", query: '".$query."', collection_id: '".$collection."'});          
          return false;
      });
    </script>";  
  return $output;
}

add_shortcode('brandfolder', 'brandfolder_popup_link');
add_shortcode('Brandfolder', 'brandfolder_popup_link');
add_shortcode('Brandfolder-logos', 'brandfolder_popup_link');
add_shortcode('Brandfolder-images', 'brandfolder_popup_link');
add_shortcode('Brandfolder-documents', 'brandfolder_popup_link');
add_shortcode('Brandfolder-people', 'brandfolder_popup_link');
add_shortcode('Brandfolder-press', 'brandfolder_popup_link');
add_shortcode('brandfolder-logos', 'brandfolder_popup_link');
add_shortcode('brandfolder-images', 'brandfolder_popup_link');
add_shortcode('brandfolder-documents', 'brandfolder_popup_link');
add_shortcode('brandfolder-people', 'brandfolder_popup_link');
add_shortcode('brandfolder-press', 'brandfolder_popup_link');
add_filter('widget_text', 'do_shortcode');

function add_brandfolder_button() {
   // Don't bother doing this stuff if the current user lacks permissions
   if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') )
     return;
 
   // Add only in Rich Editor mode
   if ( get_user_option('rich_editing') == 'true') {
     add_filter("mce_external_plugins", "add_brandfolder_tinymce_plugin");
     add_filter('mce_buttons_2', 'register_brandfolder_button');
   }
}

if (!class_exists("brandfolderWordpressPlugin")) {
  class brandfolderWordpressPlugin {
    var $adminOptionsName = "brandfolderWordpressPluginAdminOptions";
    function brandfolderWordpressPlugin() { //constructor
      
    }
    function init() {
      $this->getAdminOptions();
    }
    //Returns an array of admin options
    function getAdminOptions() {
      $devloungeAdminOptions = array('brandfolder_url' => '');
      $devOptions = get_option($this->adminOptionsName);
      if (!empty($devOptions)) {
        foreach ($devOptions as $key => $option)
          $devloungeAdminOptions[$key] = $option;
      }
      update_option($this->adminOptionsName, $devloungeAdminOptions);
      return $devloungeAdminOptions;
    }
    //Prints out the admin page
    function printAdminPage() {
          if (!current_user_can('manage_options'))  {
            wp_die( __('You do not have sufficient permissions to access this page.') );
          }

          $devOptions = $this->getAdminOptions();

          if (isset($_POST['update_brandfolderWordpressPluginSettings'])) { 
            $devOptions['brandfolder_hidebrowser'] = apply_filters('brandfolder_hidebrowser', $_POST['brandfolder_hidebrowser']);
            update_option($this->adminOptionsName, $devOptions);
            ?>
            <div class="updated"><p><strong><?php _e("Settings Updated.", "brandfolderWordpressPlugin");?></strong></p></div>
            <?php
            } ?>
            <div class='wrap'>
              <form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
              <h2>Brandfolder Plugin Setup</h2>
              <br/>
              <h3>Post/Pages Options</h3>
              <input type='checkbox' id='brandfolder_hidebrowser' name='brandfolder_hidebrowser' value='checked' <?php echo $devOptions['brandfolder_hidebrowser']; ?>> <label for='brandfolder_hidebrowser'>Hide Media Library Option on Pages/Posts</label>

              <div class='submit'>
                <input type="submit" name="update_brandfolderWordpressPluginSettings" value="<?php _e('Update Settings', 'brandfolderWordpressPlugin') ?>" />
              </div>
              </form>
              <hr>
              <br/>
              <div>For help with using this plugin, please visit the <a href='http://help.brandfolder.com/knowledgebase/articles/238392' target='_blank'>Brandfolder Knowledge Base</a>.</div>
            </div>
          <?php
        }//End function printAdminPage()


    function Main() {
      echo '<iframe src="https://brandfolder.com/organizations" style="width: 98%; height: 95%; min-height: 730px;margin-top:10px;"></iframe>';
    }

    function ConfigureMenu() {
      add_menu_page("Edit Brandfolder", "Edit Brandfolder", 6, basename(__FILE__), array(&$dl_pluginSeries,'Main'));
      add_submenu_page( "brandfolder-menu", "Settings", "Settings", 6, basename(__FILE__),  array(&$dl_pluginSeries,'printAdminPage') );
    }     

    function add_settings_link($links, $file) {
    static $this_plugin;
    if (!$this_plugin) $this_plugin = plugin_basename(__FILE__);
     
    if ($file == $this_plugin){
      $settings_link = '<a href="admin.php?page=brandfolder-sub-menu">'.__("setup", "brandfolder").'</a>';
       array_unshift($links, $settings_link);
    }
      return $links;
     }
  
  }

} 

if (class_exists("brandfolderWordpressPlugin")) {
  $dl_pluginSeries = new brandfolderWordpressPlugin();
}

//Initialize the admin panel
if (!function_exists("brandfolderWordpressPlugin_ap")) {
  function brandfolderWordpressPlugin_ap() {
    global $dl_pluginSeries;
    if (!isset($dl_pluginSeries)) {
      return;
    }

    add_menu_page("Brandfolder", "Brandfolder", 6, "brandfolder-menu", array(&$dl_pluginSeries,'Main'), plugin_dir_url(__FILE__)."favicon.png");
    add_submenu_page( "brandfolder-menu", "Settings", "Settings", 6, "brandfolder-sub-menu",  array(&$dl_pluginSeries,'printAdminPage') );

  } 
}

//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
//
// START THE BF FOR EMBEDDING
//
//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!


/* PLACE LINK IN WORDPRESS MEDIA BUTTON */
function bf_media_tab($arr) {
  $arr['grabber'] = 'Brandfolder';
  return $arr;
}

function bf_grabber($type = 'grabber') {
  media_upload_header();
  bf_browser_manager();
}

function bf_grabber_page() {
  return wp_iframe( 'bf_grabber');
}

function bf_browser_manager() {
  $devOptions = get_option("brandfolderWordpressPluginAdminOptions");
  if (!empty($devOptions)) {
    foreach ($devOptions as $key => $option)
      $brandfolderAdminOptions[$key] = $option;
  }
  $post_id = isset($_GET['post_id'])? (int) $_GET['post_id'] : 0;
  $url = "https://brandfolder.com/organizations?wp_browser=true&wp_callback_url=".urlencode(plugin_dir_url( __FILE__ ) . 'callback.php?post_id=' . $post_id . '&wp_abspath=' . ABSPATH);
?>
  <div class="wrap" style="height:99%;margin:0px;">
  <iframe src="<?php echo $url; ?>" width="100%" height="100%"></iframe>
  </div>
<?php
}

function bf_media_buttons($context) { 
  $img = plugins_url('logo.png', __FILE__);
  ?>
  <style> .insert-brandfolder-media .wp-media-buttons-icon{ background: url('<?php echo $img ?>') no-repeat 0px 0px; background-size: 100%; } </style>  
    <a href="#" id="brandfolder-add-media" class="button insert-brandfolder-media" style="padding: 1px 0px 0px 3px;">
      <span class="wp-media-buttons-icon" style="vertical-align: text-bottom;"></span></a>
  <script type="text/javascript">
    document.addEventListener("DOMContentLoaded", function(){
      jQuery(document.body).on('click', '#brandfolder-add-media', function(e) {
        e.preventDefault();
        var media = wp.media;
        media.frames.brandfolder = wp.media.editor.open(wpActiveEditor);
        jQuery( ".media-menu-item:contains('Brandfolder')" ).click();
      });
    });
  </script>
<?php
}

function load_into_head() { 
  $devOptions = get_option("brandfolderWordpressPluginAdminOptions");
  if (!empty($devOptions)) {
    foreach ($devOptions as $key => $option)
      $brandfolderAdminOptions[$key] = $option;
  }
?>
  <style>
    <?php echo $brandfolderAdminOptions["brandfolder_style"]; ?>
  </style>
  <script type="text/javascript">
    function brandfolder_loadScript(src, callback)
    {
      var s,r,t;
      r = false;
      s = document.createElement('script');
      s.type = 'text/javascript';
      s.src = src;
      s.onload = s.onreadystatechange = function() {
        //console.log( this.readyState ); //uncomment this line to see which ready states are called.
        if ( !r && (!this.readyState || this.readyState == 'complete') )
        {
          r = true;
          callback();
        }
      };
      t = document.getElementsByTagName('script')[0];
      t.parentNode.insertBefore(s, t);
    }

    function brandfolder_null() {
    }

    jQuery(document).ready(
      function () {
        brandfolder_loadScript('//cdn.brandfolder.com/bf.min.js', brandfolder_null);
    });

  </script>
<?php
}

function brandfolder_scripts() {
  wp_enqueue_script('jquery');
}

//Actions and Filters 
if (isset($dl_pluginSeries)) {

  $devOptions = get_option("brandfolderWordpressPluginAdminOptions");
  if (!empty($devOptions)) {
    foreach ($devOptions as $key => $option)
      $brandfolderAdminOptions[$key] = $option;
  }

  //Actions
  add_action('admin_menu', 'brandfolderWordpressPlugin_ap');
  add_action('brandfolder/brandfolder.php',  array(&$dl_pluginSeries, 'init'));

  if (!isset($devOptions['brandfolder_hidebrowser']) && $devOptions['brandfolder_hidebrowser']!="checked") {
    add_filter('media_upload_tabs', 'bf_media_tab');
    add_action( 'media_buttons', 'bf_media_buttons' );
    add_action( 'media_upload_grabber', 'bf_grabber_page' );
  }

  add_action( 'wp_enqueue_scripts', 'brandfolder_scripts' );
  add_action( 'wp_head', 'load_into_head' );
}
?>