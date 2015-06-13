<?php


//***** Options Menu *****
function wp_tinywebdb_api_optionsmenu() {
	echo '<div class="wrap">';
	if (function_exists('screen_icon')) {
		screen_icon();
	}
	echo "<h2>TinyWebDB Settings</h2>";

	if ($_POST['issubmitted'] == 'yes') {
		$post_urltrigger = $_POST['urltrigger'];
		$post_apikey = $_POST['apikey'];
		$post_tagtype = $_POST['tagtype'];
		update_option("wp_tinywebdb_api_url_trigger", $post_urltrigger);
		update_option("wp_tinywebdb_api_key", $post_apikey);
		update_option("wp_tinywebdb_api_tag_type", $post_tagtype);
	}
	$setting_url_trigger = get_option("wp_tinywebdb_api_url_trigger") or $setting_url_trigger = 'api';
	$setting_tagtype = get_option("wp_tinywebdb_api_tag_type") or $setting_tagtype = 'id';
	$setting_apikey = get_option("wp_tinywebdb_api_key");

	echo '<form method="post" action="http://' . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] . '">';
	echo '<table class="form-table">';
	?>

	<h3>Controllers</h3>

        <table id="all-plugins-table" class="widefat">
      <thead>
        <tr>
          <th class="manage-column check-column" scope="col"></th>
          <th class="manage-column" scope="col">Controller</th>
          <th class="manage-column" scope="col">Description</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <th class="manage-column check-column" scope="col"></th>
          <th class="manage-column" scope="col">Controller</th>
          <th class="manage-column" scope="col">Description</th>
        </tr>
      </tfoot>
      <tbody class="plugins">
                  <tr class="active">
            <th class="check-column" scope="row">
            </th>
            <td class="plugin-title">
              <strong>Core</strong>
            </td>
            <td class="desc">
              <p>Basic introspection methods</p>
	      <p><code><a href="<?php echo get_option('home') . "/" . $setting_url_trigger ?>/info/">info</a></code></p>
            </td>
          </tr>
                  <tr class="active">
            <th class="check-column" scope="row">
            </th>
            <td class="plugin-title">
              <strong>Get Value</strong>
            </td>
            <td class="desc">
              <p>Get dara from your post by Post ID or  Slug </p>
	      <p><code><a href="<?php echo get_option('home') . "/" . $setting_url_trigger ?>/getvalue/">getvalue</a></code></p>
            </td>
          </tr>
          </tr>
                  <tr class="inactive">
            <th class="check-column" scope="row">
            </th>
            <td class="plugin-title">
              <strong>Store A Value</strong>
            </td>
            <td class="desc">
              <p>Store data on a your post by Post ID or  Slug </p>
	      <p><code><a href="<?php echo get_option('home') . "/" . $setting_url_trigger ?>/storeavalue/">storeavalue</a></code></p>
            </td>
     </tbody>
    </table>
      <div class="clear"></div>
    </div>
			  
	<h3>Address</h3>

    <table class="form-table">
        <tr valign="top">
            <th scope="row">API base</th>
		<td><input name="urltrigger" type="text" id="urltrigger" value="<?php echo $setting_url_trigger; ?>" size="50" /><br/>Specify a base URL for TinyWebDB API. For example, using api as your API base URL would enable the following <?php echo get_option('home'); ?>/api/. You can change the <em>api</em> part of your TinyWebDB APIs to something else. Enter without slashes.</td>
	</tr>

        <tr valign="top">
            <th scope="row">API Key</th>
		<td><input name="apikey" type="text" id="apikey" value="<?php echo $setting_apikey; ?>" size="50" /><br/>Set api key to protect your TinyWebDB API. Client mast set same api key to Store A Value to your site</td>
	</tr>

	<tr valign="top">
		<th scope="row">Tag type</th>
		<td>
			<input type="radio" name="tagtype" value="id" <?php if ($setting_tagtype == 'id') {	echo 'checked="checked"';} ?> /> Post ID 
			<input type="radio" name="tagtype" value="slug"  <?php if ($setting_tagtype == 'slug') { echo 'checked="checked"';	} ?> /> Slug
			<br/>Select Tag mach to type <em>post_id</em> or <em>slug</em>.</td>
	</tr>

	<?php
	echo '</table>';
	echo '<input name="issubmitted" type="hidden" value="yes" />';
	echo '<p class="submit"><input type="submit" name="Submit" value="Save settings" /></p>';
	echo '</form>';
	wp_tinywebdb_api_footer();
	echo '</div>';
}

//***** Common Elements *****
function wp_tinywebdb_api_admin_script() {
	if (function_exists('wp_enqueue_style')) {
		wp_enqueue_script('thickbox');
	}
}

function wp_tinywebdb_api_admin_style() {
	if (function_exists('wp_enqueue_style')) {
		wp_enqueue_style('thickbox');
	}
}

add_action('init', 'wp_tinywebdb_api_admin_script');
add_action('wp_head', 'wp_tinywebdb_api_admin_style');

function wp_tinywebdb_api_footer() {
	echo '<div style="margin-top:45px; font-size:0.87em;">';
	echo '<div><a href="' . wp_tinywebdb_api_get_plugin_dir('url') . '/readme.txt">Documentation</a> | <a href="http://appinventor.in/side/tinywebdb-api/">TinyWebDB Homepage</a></div>';
	echo '</div>';
}
?>
