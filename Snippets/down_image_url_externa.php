<?php
function set_thumb_v1_down($post){
  define('URL_V1', 'http://portalv1.com.br');
  preg_match_all('/src="([^"]*)"/', $post->post_content, $src);
  $image_url = '';
  $post_id = $post->ID;
  if (has_post_thumbnail($post->ID)) 
    $image = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'fullsize');
  if($image==true){ 
    $image_url = str_replace(get_bloginfo('url'),URL_V1,$image[0]);
  } else {
    global $wpdb, $post;
    $thumb = $wpdb->get_row("SELECT ID, post_title FROM {$wpdb->posts} WHERE post_parent = {$post->ID} AND post_mime_type LIKE 'image%' ORDER BY menu_order");
    if(!empty($thumb)){
      $p_image = image_downsize($thumb->ID, 'fullsize');
      $post_title = stripslashes($post->post_title);
      $image_url = str_replace(get_bloginfo('url'),URL_V1,$p_image[0]);
    }  else {
      if(get_post_meta($post->ID, "imagem", true) == true){
        $image_url = URL_V1.'/'.get_post_meta($post->ID, "imagem", true);     
      } else {
        $glid = (end(ngg($post->post_content,'=',']')));
        global $wpdb;
        $result = $wpdb->get_results( "SELECT filename,path FROM wp_ngg_pictures i 
        INNER JOIN wp_ngg_gallery g ON g.gid = i.galleryid
        WHERE i.galleryid=$glid LIMIT 1" ) ;              
        if(!empty($result)){  
            $image_url = URL_V1 .'/'.$result[0]->path .'/'. $result[0]->filename;
          } else {
            $image_url = get_bloginfo('template_directory').'/image/pd.jpg';
          } 
        }
      }
  }

  if($image_url == get_bloginfo('template_directory').'/image/pd.jpg'){
    $image_url = $src[1][0]; 
  }

  $result = media_sideload_image($image_url, $post_id, 'image_from_url');
    if(!$result->errors['upload_error']){
      if(has_post_thumbnail($post_id))
         {
            delete_post_thumbnail($post_id);
         }
        $attachments = get_posts(array('post_parent' => $post_id, 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'DESC'));
          if(sizeof($attachments) > 0){
            // set image as the post thumbnail
            set_post_thumbnail($post_id, $attachments[0]->ID);
          }         
    }
  
}
add_action( 'the_post','set_thumb_v1_down' ); 