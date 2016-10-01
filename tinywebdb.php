<?php

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

    $tinywebdb_key = explode($url_trigger.'/', $request);
    $tinywebdb_key = $tinywebdb_key[1];
    $tinywebdb_key = explode('/', $tinywebdb_key);
    $action = $tinywebdb_key[0];
    $action = $wpdb->escape($action);

    return $action;
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

  public static function getvalue($tagName) {

    $bedtag = array("id" => "0", "post_author" => "0", "post_content" => "ERROR BAD tag SUPPLIED");

    // this action enable from v 0.1.x
    // JSON_API , Post Parameters : tag
    $postid = wp_tinywebdb_api_get_postid($tagName);
    $tagValue = get_post($postid);
    if (is_null($tagValue)) $tagValue = $bedtag;	//reports a get_post failure
    // $tagName = wp_tinywebdb_api_get_tagName($postid);
    return $tagValue;
  }

  public static function storeavalue($tagName, $tagValue) {

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
    return $postid;
  }
}
