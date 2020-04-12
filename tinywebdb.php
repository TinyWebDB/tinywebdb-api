<?php

if ( !class_exists('TinyWebDB') ) {

  class TinyWebDB {

    private static $initiated = false;

  	public static function init() {
  		if ( ! self::$initiated ) {
  			self::init_hooks();
  		}
    }

    public static function init_hooks() {
      self::$initiated = true;

    }

    public function __construct() {

      global $wp_query;
      if ($wp_query->is_404) {
          $wp_query->is_404 = false;
          $wp_query->is_archive = true;
      }

    }

    public function __destruct() {
      return true;
    }

    public static function get_action() {
      global $wpdb;

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
		$tinywebdb_key = explode($url_trigger.'/', $request);
		$tinywebdb_key = $tinywebdb_key[1];
		$tinywebdb_key = explode('/', $tinywebdb_key);
		$action = $tinywebdb_key[0];
		$action = $wpdb->escape($action);
        	return $action;
 	}
	return "No match!";
    }

    function wp_tinywebdb_api_get_postid($tagName){

      $postid = NULL;
      $tagName = wp_strip_all_tags($tagName);
    	$tagtype = get_option("wp_tinywebdb_api_tag_type") or $tagtype = 'slug';

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

    	$tagtype = get_option("wp_tinywebdb_api_tag_type") or $tagtype = 'slug';

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

    public static function getvalue($tagName) {

      // this action enable from v 0.1.x
      // JSON_API , Post Parameters : tag

      if (empty($tagName)) {
      	$tagtype = get_option("wp_tinywebdb_api_tag_type") or $tagtype = 'slug';

        $posts = get_posts('numberposts=10');
        foreach ($posts as $post) {
        	if ($tagtype == 'id') {
            $tagnames[] = $post->ID;
        	} else {
            $tagnames[] = $post->post_name;
        	}
        }
        return $tagnames;
      } else {
        $postid = TinyWebDB::wp_tinywebdb_api_get_postid($tagName);
        $post = get_post($postid);
        if (is_null($post)) return "NO FOUND";   //reports a get_post failure
        else return $post->post_content;
      }
    }

    public static function storeavalue($tagName, $tagValue) {
  
      $postid = TinyWebDB::wp_tinywebdb_api_get_postid($tagName);
      $tagValue = stripslashes($tagValue);
      $tagValue = trim($tagValue, '"');

      $post = get_post( $postid );
      if (empty($post)) {

        // Create post object
        $args = array(
          'post_title'    => wp_strip_all_tags( $tagName ),
          'post_name'     => wp_strip_all_tags( $tagName ),
          'post_content'  => $tagValue,
          'post_status'   => 'publish',
        );

        // Insert the post into the database
        $postid = wp_insert_post( $args );
      } else {
        $args = array(
          'ID'		     => $postid,
          'post_content' => $tagValue,
        );
        $postid = wp_update_post( $args );
      }
      return $postid;
    }
  }
}
