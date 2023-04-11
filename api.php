<?php 
/** Start Insert Posts */
add_action('rest_api_init', function () {
	register_rest_route( 'api/v1', '/posts', array(
		'methods' => 'POST',
		'callback' => 'insert_post_from_data'
	));
});
function insert_post_from_data($req) {
	
	if(!empty($req['post_title'])) {
		$my_post = array(
			'post_title'    => wp_strip_all_tags( $req['post_title'] ),
			'post_content'  => $req['post_content'],
			'post_status'   => 'publish',
		  );
		   
		  // Insert the post into the database
		$post_id = wp_insert_post( $my_post );
		echo '<pre>'; print_r($post_id); echo '</pre>';
		
	}
}
/** End Insert Posts */

/** Post method api */
add_action( 'wp_ajax_nopriv_insert_post_api', 'insert_post_api' );
add_action( 'wp_ajax_insert_post_api', 'insert_post_api' );

function insert_post_api() {
		
	$post_title = (!empty($_POST['post_title'])) ? $_POST['post_title'] : '';
	$post_content = (!empty($_POST['post_content'])) ? $_POST['post_content'] : '';

	
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL,"http://localhost/training/login_register/wp-json/api/v1/posts");
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, "post_title=".$post_title."&post_content=".$post_content."");
	
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	
	$server_output = curl_exec($ch);
	
	curl_close ($ch);
	echo $server_output;
	
	die;
}

/** Start Get Posts From api */
add_action( 'rest_api_init', 'get_posts_api');
 
function get_posts_api(){
    register_rest_route( 'api/v1', '/posts', array(
        'methods' => 'GET',
        'callback' => 'get_posts_data',
    ));
}

function get_posts_data($req) {
	$defaults = array(
        'post_type'        => 'post',
        'suppress_filters' => true,
    );
	$posts = get_posts($defaults);
	$json_posts = json_encode($posts);
	echo $json_posts;
	
	die;
}
/** End Get Posts From api */

/** Get method api */
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL,"http://localhost/training/login_register/wp-json/api/v1/posts");

curl_setopt($ch, CURLOPT_HTTPGET, 1);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$server_output = curl_exec($ch);

curl_close ($ch);

$posts = json_decode($server_output);
// echo '<pre>'; print_r($posts); echo '</pre>';