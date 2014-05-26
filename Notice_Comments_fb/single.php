<?php
/**
 * The Template for displaying all single posts
 *
 * @package WordPress
 * @subpackage Twenty_Twelve
 * @since Twenty Twelve 1.0
 */

get_header(); ?>

	<div id="primary" class="site-content">
		<div id="content" role="main">

			<?php while ( have_posts() ) : the_post(); ?>

				<?php get_template_part( 'content', get_post_format() ); ?>

				<nav class="nav-single">
					<h3 class="assistive-text"><?php _e( 'Post navigation', 'twentytwelve' ); ?></h3>
					<span class="nav-previous"><?php previous_post_link( '%link', '<span class="meta-nav">' . _x( '&larr;', 'Previous post link', 'twentytwelve' ) . '</span> %title' ); ?></span>
					<span class="nav-next"><?php next_post_link( '%link', '%title <span class="meta-nav">' . _x( '&rarr;', 'Next post link', 'twentytwelve' ) . '</span>' ); ?></span>
				</nav><!-- .nav-single -->

				<?php comments_template( '', true ); ?>

			<?php endwhile; // end of the loop. ?>
      <div id="fb-root"></div>
      <!---Comments Facebook notice-->
      <style>
        .fb-comments, .fb-comments span, .fb-comments iframe[style] {width: 100% !important;}
      </style>
      <div id="commentsBox" style="width:100%">
         <div class="fb-comments" data-href="<?php the_permalink()?>"  data-mobile="false" data-numposts="5" data-colorscheme="light"></div>      
      </div>
     
    <script>
       var $ = jQuery;
      $(document).ready(function() {
          $.ajaxSetup({ cache: true });
          $.getScript('//connect.facebook.net/pt_BR/all.js', function(){
            FB.init({
              appId: '114775618670194',
              status     : true,
              xfbml      : true
            });            
            $('#loginbutton,#feedbutton').removeAttr('disabled');
               FB.Event.subscribe('comment.remove', function(response) {
                   callback_comment_face_remove(response); 
               }); 
              FB.Event.subscribe('comment.create', function(response) {                 
                 callback_comment_face_create(response);                
              });
          });
        });
      function callback_comment_face_create(response){
        var commentQuery = FB.Data.query("SELECT text, fromid FROM comment WHERE post_fbid='"+response.commentID+"' AND object_id IN (SELECT comments_fbid FROM link_stat WHERE url='"+response.href+"')");
        var userQuery = FB.Data.query("SELECT name, uid, pic_small, email, profile_url FROM user WHERE uid in (select fromid from {0})", commentQuery);

        FB.Data.waitOn([commentQuery, userQuery], function() {            
            var commentRow = commentQuery.value[0];
            var userRow = userQuery.value[0];
            var ajaxurl = '<?php echo admin_url()?>admin-ajax.php',
            dados = {
                action: 'FB_comment_notify',
                comentario: commentRow.text,
                comentarioID: response.commentID,
                post: '<?php echo $post->ID; ?>',
                autor: userRow.name,
                autorEmail: userRow.email,
                autorID: userRow.uid,
                autorImg: userRow.pic_small,
                autorUrl: userRow.profile_url,
            };           
            //$.get(ajaxurl, data);
            $.ajax({
              type: 'POST',
              url: ajaxurl,
              data: dados,
            });
        });
      }
      function callback_comment_face_remove(response){
        var ajaxurl = '<?php echo admin_url()?>admin-ajax.php',
        dados={
            action:'FB_comment_delete',
            comentarioID: response.commentID,
          }

          $.ajax({
            type: 'POST',
            url: ajaxurl,
            data: dados,
          });

      }
      </script>
		</div><!-- #content -->
	</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>