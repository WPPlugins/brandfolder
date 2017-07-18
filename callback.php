<?php
  $parse_uri = explode( 'wp-content', $_SERVER['SCRIPT_FILENAME'] );
  $abspath = $parse_uri[0];
  require_once($abspath . 'wp-load.php');
  require_once($abspath . 'wp-admin/includes/media.php');
  require_once($abspath . 'wp-admin/includes/file.php');
  require_once($abspath . 'wp-admin/includes/image.php');
  require_once($abspath . 'wp-admin/includes/post.php');

  $url = $_REQUEST['attachment_url'];
  if (false === strpos($url, '://')) {
    $url = 'http:' . $url;
  }

  $desc = urldecode($_REQUEST['desc']);

  $attid = "";
  $html = "";
  function new_attachment($att_id){
      global $attid, $image, $html;
      $attid = $att_id;
      // Automatically add as header image:
      if ( isset($_REQUEST['header_image']) ) {
        $p = get_post($att_id);
        update_post_meta($p->post_parent,'_thumbnail_id',$att_id);
        $html = _wp_post_thumbnail_html( $att_id, $_REQUEST['post_id'] );
      }
  }

  add_action('add_attachment','new_attachment');

  add_filter( 'wp_check_filetype_and_ext', 'bf_filepicker_bypass' );

  function bf_filepicker_bypass( $filearray ) {
      $filearray['type'] = 'image/jpeg';
      $filearray['ext'] = 1;
      return $filearray;
  }

  $image = media_sideload_image($url, $_REQUEST['post_id'], $desc);
  $image = str_replace("src=", "class='size-full wp-image-".$attid."' src=", $image);
  if ( isset($_REQUEST['header_image']) ) {
    $image = "";
  }
  remove_action('add_attachment','new_attachment');

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Brandfolder Callback</title>
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script>
      //send it to the editor. Putting it in a timeout, for some reason prevents IE from throwing an ACCESS DENIED error:
      setTimeout(function(){
        parent.parent.wp.media.editor.insert("<? echo $image; ?>");
        jQuery('.media-modal-close').click();
        var html = "<?php echo urlencode($html); ?>";
        if (html) {
          var feature_image = parent.parent.document.getElementById("postimagediv");
          var inner_feature = feature_image.getElementsByClassName("inside")[0];
          inner_feature.innerHTML = decodeURIComponent(html.replace(/\+/g,  " "));
        }
        window.history.back();
      },0);
    </script>
  </head>
  <body>
  </body>
</html>