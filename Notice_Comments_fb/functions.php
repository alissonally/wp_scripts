<?php 

add_action( 'wp_ajax_FB_comment_notify', 'FB_comment_notify_callback' );
add_action( 'wp_ajax_nopriv_FB_comment_notify', 'FB_comment_notify_callback' );

function FB_comment_notify_callback() {

	$time = current_time('mysql');

	$data = array(
	    'comment_post_ID' => (int) $_POST['post'],
	    'comment_author' => $_POST['autor'],
	    'comment_author_email' => $_POST['autorEmail'],
	    'comment_author_url' => $_POST['autorUrl'],
	    'comment_content' => $_POST['comentario'],
	    'comment_type' => '',
	    'comment_parent' => 0,
	    'user_id' => '',
	    'comment_author_IP' => $_SERVER['HTTP_X_FORWARDED_FOR'],
	    'comment_agent' => $_SERVER['HTTP_USER_AGENT'],
	    'comment_date' => $time,
	    'comment_approved' => 0,
	);
	$commentFB =  wp_insert_comment($data);
	if($commentFB){
		add_comment_meta( $commentFB, 'pic_facebook', $_POST['autorImg'] );
		add_comment_meta( $commentFB, 'comment_id', $_POST['comentarioID'] );
	}
	die();
}

function delete_comment_FB(){
	$args = array(
	'meta_key' => 'comment_id',
	'meta_value' => $_POST['comentarioID'],
	);
	$comments = get_comments($args);

	if(wp_delete_comment( $comments[0]->comment_ID, false ));
	  echo 'comentario deletado';
	die();
}
add_action( 'wp_ajax_FB_comment_delete', 'delete_comment_FB' );
add_action( 'wp_ajax_nopriv_FB_comment_delete', 'delete_comment_FB' );

function get_avatar_fb( $avatar ) {
    if (is_admin()) {
    	global $comment;
    	global $current_screen; 
    	$size = $current_screen->base == 'edit-comments' ? 32 : 50;
    	$face_avatar = get_comment_meta( $comment->comment_ID, 'pic_facebook', true );
        if($face_avatar)
        	$avatar = '<img alt="" src="'.$face_avatar.'?s='.$size.'" class="avatar avatar-'.$size.' photo avatar-default" height="'.$size.'" width="'.$size.'">';
    }
    return $avatar;
}
add_filter( 'get_avatar', 'get_avatar_fb' );

function myplugin_comment_columns( $columns ){
	return array_merge( $columns, array(
		'avatar_facebook' => __( 'User Agente' ),
	) );
}
add_filter( 'manage_edit-comments_columns', 'myplugin_comment_columns' );

function myplugin_comment_column( $column, $comment_ID ){

	switch ( $column ) {
		case 'avatar_facebook':
			if ( $coments_field =  get_comment( $comment_ID, OBJECT ) ) {
				echo $coments_field->comment_agent;
			} else {
				echo '-';
			}
		break;
	}
}
add_filter( 'manage_comments_custom_column', 'myplugin_comment_column', 10, 2 );