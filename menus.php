<?php

class TinyWebDB_Admin {

}

function wp_tinywebdb_api_logtailmenu() {
	echo '<div class="wrap">';
	if (function_exists('screen_icon')) {
		screen_icon();
	}
	echo "<h2>TinyWebDB Log Tail</h2>";

    echo "<table class=\"wp-list-table widefat striped pages\">";
    echo "<thead><tr>";
    echo "<th> Log Name </th>";
    echo "<th> Size </th>";
    echo "</tr></thead>\n";

        $listDir = array();
        if($handler = opendir(WP_CONTENT_DIR)) {
            while (($sub = readdir($handler)) !== FALSE) {
                if ( substr($sub, 0, 10) == "tinywebdb_") {
		    $listDir[] = $sub;
                }
            }
            closedir($handler);
	    sort($listDir);
	    foreach ($listDir as $sub) {
	  	echo "<tr>";
      		echo "<td><a href=" . menu_page_url( 'tinywebdb_log' ,false) . "&logfile=" . $sub . ">$sub</a></td>\n";
      		echo "<td>" . filesize(WP_CONTENT_DIR. "/" . $sub) . "</td>\n";
	  	echo "</tr>";
            }
        }

    echo "</table>";
    if($_GET['logfile']) {
    	$logfile = substr($_GET['logfile'], 0, 24);
	echo "<h2>Log file : " . WP_CONTENT_DIR. "/" .$logfile . "</h2>";
	$lines = wp_tinywebdb_api_read_tail(WP_CONTENT_DIR. "/" .$logfile, 20);
	foreach ($lines as $line) {
    		echo $line . "<br>";
    	}
    }
}

function wp_tinywebdb_api_read_tail($file, $lines) {
    //global $fsize;
    $handle = fopen($file, "r");
    $linecounter = $lines;
    $pos = -2;
    $beginning = false;
    $text = array();
    while ($linecounter> 0) {
        $t = " ";
        while ($t != "\n") {
            if(fseek($handle, $pos, SEEK_END) == -1) {
                $beginning = true;
                break;
            }
            $t = fgetc($handle);
            $pos --;
        }
        $linecounter --;
        if ($beginning) {
            rewind($handle);
        }
        $text[$lines-$linecounter-1] = fgets($handle);
        if ($beginning) break;
    }
    fclose ($handle);
    return array_reverse($text);
}


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
	$setting_tagtype = get_option("wp_tinywebdb_api_tag_type") or $setting_tagtype = 'slug';
	$setting_apikey = get_option("wp_tinywebdb_api_key");

	echo '<table class="form-table">';
	?>

	<h3>Controllers</h3>

        <table id="all-plugins-table" class="widefat">
      <thead>
        <tr>
          <th class="manage-column check-column" scope="col"></th>
          <th class="manage-column" scope="col">Controller</th>
          <th class="manage-column" scope="col">Description</th>
          <th class="manage-column" scope="col">API</th>
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
            </td>
            <td class="desc">
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
            </td>
            <td class="desc">
    <form action="<?php echo get_option('home') . "/" . $setting_url_trigger ?>/getvalue" method="post" enctype=application/x-www-form-urlencoded>
       Tag:<input type="text" name="tag" />
       <input type="hidden" name="fmt" value="html">
       <input type="submit" value="Get value">
    </form>
            </td>
          </tr>
          </tr>
                  <tr class="active">
            <th class="check-column" scope="row">
            </th>
            <td class="plugin-title">
              <strong>Store A Value</strong>
            </td>
            <td class="desc">
              <p>Store data on a your post by Post ID or  Slug </p>
            </td>
            <td class="desc">
    <form action="<?php echo get_option('home') . "/" . $setting_url_trigger ?>/storeavalue" method="post" enctype=application/x-www-form-urlencoded>
	   Tag:<input type="text" name="tag"/>
	   Value:<input type="text" name="value" size="30"/>
	   <input type="hidden" name="fmt" value="html">
	   <input type="submit" value="Store a value">
    </form>
            </td>
     </tbody>
    </table>
      <div class="clear"></div>
    </div>

	<h3>Address</h3>

	<form method="post" action="">
    <table class="form-table">
        <tr valign="top">
            <th scope="row">API base</th>
		<td><input name="urltrigger" type="text" id="urltrigger" value="<?php echo $setting_url_trigger; ?>" size="50" /><br/>Specify a base URL for TinyWebDB API. For example, using 'api' as your API base URL would enable the following '<?php echo get_option('home'); ?>/api/'. <br>You can change the <em>api</em> part of your TinyWebDB APIs to something else. Enter without slashes.</td>
	</tr>

        <tr valign="top">
            <th scope="row">API Key</th>
		<td><input name="apikey" type="text" id="apikey" value="<?php echo $setting_apikey; ?>" size="50" /><br/>Set api key to protect your TinyWebDB API. Client mast set same api key to Store A Value to your site. (This function no ready)</td>
	</tr>

	<tr valign="top">
		<th scope="row">Tag type</th>
		<td>
			<input type="radio" name="tagtype" value="id" <?php if ($setting_tagtype == 'id') {	echo 'checked="checked"';} ?> /> Post ID
			<input type="radio" name="tagtype" value="slug"  <?php if ($setting_tagtype == 'slug') { echo 'checked="checked"';	} ?> /> Slug
			<br/>Select Tag mach to type <em>post_id</em> or <em>slug</em>.</td>
	</tr>

	</table>
	<input name="issubmitted" type="hidden" value="yes" />
	<p class="submit"><input type="submit" name="Submit" value="Save settings" /></p>
	</form>

	<?php
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
	echo '<div><a href="' . wp_tinywebdb_api_get_plugin_dir('url') . '/readme.txt">Documentation</a> </div>';
	echo '</div>';
}
?>
