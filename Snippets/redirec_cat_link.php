<?php
/*
* Snippet verifica se a url digitada é uma categoria, caso verdadeiro redireciona para a categoria em questão
*/
function redirect_cat_link(){
	$request = rtrim($_SERVER['REQUEST_URI'], '/');  	
	$request	= end(explode('/', $request)); 
	$cats = get_category_by_slug($request);
	if($cats)	
		wp_redirect (get_category_link( $cats->term_id )); 
		exit;
}
add_action('template_redirect', 'redirect_cat_link', 1);