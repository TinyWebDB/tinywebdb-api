<?php
/*
Plugin Name: Wp TinyWebDB API
Plugin URI: http://appinventor.in/side/tinywebdb-api/
Description: a AppInventor TinyWebDB API plugin, use you WordPress as a TinyWebDB web service.
    Action        URL                      Post Parameters  Response 
    Get Value     {ServiceURL}/getvalue    tag              JSON: ["VALUE","{tag}", {value}] 
    Store A Value {ServiceURL}/storeavalue tag,value        JSON: ["STORED", "{tag}", {value}] 
Author: Hong Chen
Author URI: http://digilib.net/
Version: 0.2.1
*/


define("TINYWEBDB", "tools.php?page=tinywebdb-api/tinywebdb-api.php");
define("TINYWEBDB_VER", "0.2.1");



//***** Hooks *****
register_activation_hook(__FILE__,'wp_tinywebdb_api_install'); //Install
add_action('template_redirect', 'wp_tinywebdb_api_query'); //Redirect
add_action('admin_menu', 'wp_tinywebdb_api_add_pages'); //Admin pages
//***** End Hooks *****


//***** Installer *****
if (is_admin()) {
	include "installer.php";
}

function wp_tinywebdb_api_get_postid($tagName){
	
	$tagtype = get_option("wp_tinywebdb_api_tag_type");
	if ($tagtype=='') {
		$tagtype = 'id';
	}

	if ($tagtype == 'id') {
		$postid = $tagName;
	} else {
		// get_page_by_path('slug')->ID;
		$args=array(
		  'name' => $tagName,
		  'post_type' => 'post',
		  'post_status' => 'publish',
		  'showposts' => 1,
		  'caller_get_posts'=> 1
		);
		$my_posts = get_posts($args);
		if( $my_posts ) {
		  $postid = $my_posts[0]->ID;
		}
	}
	
	return $postid;
}


function wp_tinywebdb_api_get_tagName($postid){
	
	$tagtype = get_option("wp_tinywebdb_api_tag_type");
	if ($tagtype=='') {
		$tagtype = 'id';
	}

	if ($tagtype == 'id') {
		$tagName = $postid;
	} else {
		// get_page_by_path('slug')->ID;
		$post_data = get_post($postid, ARRAY_A);
		$slug = $post_data['post_name'];
		$tagName = $slug;
	}
	
	return $tagName;
}

//***** get $request and get_post , then json_encode it *****

add_filter('query_vars', 'add_fetch');
function add_fetch($public_query_vars) {
	$public_query_vars[] = 'tag';
	$public_query_vars[] = 'value';
	$public_query_vars[] = 'apikey';
	return $public_query_vars;
}

function wp_tinywebdb_api_query() {
	global $wpdb, $table_prefix;
	$bedtag = array("id" => "0", "post_author" => "0", "post_content" => "ERROR BAD tag SUPPLIED");

	$request = $_SERVER['REQUEST_URI'];
	if (!isset($_SERVER['REQUEST_URI'])) {
		$request = substr($_SERVER['PHP_SELF'], 1);
		if (isset($_SERVER['QUERY_STRING']) AND $_SERVER['QUERY_STRING'] != '') { $request.='?'.$_SERVER['QUERY_STRING']; }
	}
	$url_trigger = get_option("wp_tinywebdb_api_url_trigger");
	if ($url_trigger=='') {
		$url_trigger = 'api';
	}

	if (isset($_POST['action'])) {
		$request = '/' . $url_trigger . '/'.$_POST['action'].'/';
	}

	if ( strpos('/'.$request, '/'.$url_trigger.'/') ) {

    global $wp_query;
    if ($wp_query->is_404) {
        $wp_query->is_404 = false;
        $wp_query->is_archive = true;
    }
    header("HTTP/1.1 200 OK");

		$tinywebdb_key = explode($url_trigger.'/', $request);
		$tinywebdb_key = $tinywebdb_key[1];
		$tinywebdb_key = explode('/', $tinywebdb_key);
		$action = $tinywebdb_key[0];
		$action = $wpdb->escape($action);
		switch ($action) {
			case "getvalue": // this action enable from v 0.1.x
				// JSON_API , Post Parameters : tag
				$tagName = get_query_var('tag');
				$postid = wp_tinywebdb_api_get_postid($tagName);
				$tagValue = get_post($postid);
				if (is_null($tagValue)) $tagValue = $bedtag;	//reports a get_post failure
				// $tagName = wp_tinywebdb_api_get_tagName($postid);

				header('Cache-Control: no-cache, must-revalidate');
				header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
				header('Content-type: application/json');
				echo json_encode(array("VALUE", $tagName, $tagValue));
				exit; // this stops WordPress entirely
				break;
			case "storeavalue": // this action will enable from v 0.2.x
				// JSON_API , Post Parameters : tag,value
				$tagName = get_query_var('tag');
				$tagValue = get_query_var('value');	             // $_REQUEST['value']; // 
				$apiKey = get_query_var('apikey');
error_log("Wp TinyWebDB API : storeavalue: " . __FILE__ . "/" . __LINE__ . " ($apiKey) $tagName -- $tagValue");
				$setting_apikey = get_option("wp_tinywebdb_api_key");
				if ($apiKey == $setting_apikey){
					
					// Create post object
					$args = array(
					  'post_title'    => wp_strip_all_tags( $tagName ),
					  'post_content'  => $tagValue,
					  'post_status'   => 'publish',
					);

					// Insert the post into the database
					$postid = wp_insert_post( $args );
					if ($postid == 0) {
						$postid = wp_tinywebdb_api_get_postid($tagName);
						$args = array(
						  'ID'		     => wp_strip_all_tags( $postid ),
						  'post_content' => $tagValue,
						);
						$postid = wp_update_post( $args );
					}
					$tagName = wp_tinywebdb_api_get_tagName($postid);

					header('Cache-Control: no-cache, must-revalidate');
					header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
					header('Content-type: application/json');
					echo json_encode(array("STORED", $tagName, $tagValue));
				} else {
					echo "check api key.";
				}
			    exit;
				break;
			default:
				break;
		}
		echo '{"status":"ok","tinywebdb_api_version":"' . TINYWEBDB_VER . '","controllers":["getvalue","storeavalue"]}' . "\n";
		exit; // this stops WordPress entirely
	}
}
//***** End get $request and call JSON_API *****



//Just a boring function to insert the menus
function wp_tinywebdb_api_add_pages() {
	add_options_page("TinyWebDB Settings", "TinyWebDB API", "manage_options", __FILE__, "wp_tinywebdb_api_optionsmenu");
}



//***** Menu *****
if (is_admin()) {
	include "menus.php";
}



//***** Text Truncation Helper Function *****
function wp_tinywebdb_api_truncate($text) {
	if ( strlen($text) > 79 ) {
		$text = $text." ";
		$text = substr($text,0,80);
		$text = $text."...";
		return $text;
	} else { return $text; }
}



//***** Get Plugin Location *****
function wp_tinywebdb_api_get_plugin_dir($type) {
	if ( !defined('WP_CONTENT_URL') )
		define( 'WP_CONTENT_URL', get_option('siteurl') . '/wp-content');
	if ( !defined('WP_CONTENT_DIR') )
		define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
	if ($type=='path') { return WP_CONTENT_DIR.'/plugins/'.plugin_basename(dirname(__FILE__)); }
	else { return WP_CONTENT_URL.'/plugins/'.plugin_basename(dirname(__FILE__)); }
}



//***** Add Item to Favorites Menu *****
function wp_tinywebdb_api_add_menu_favorite($actions) {
	$actions[TINYWEBDB] = array('TinyWebDB', 'manage_options');
	return $actions;
}
add_filter('favorite_actions', 'wp_tinywebdb_api_add_menu_favorite'); //Favorites Menu



?>
