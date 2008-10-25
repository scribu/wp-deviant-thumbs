<?php
abstract class deviantThumbsCarousel extends deviantThumbs {
	public function carousel($query, $count, $rand, $cache, $id = 'da-carousel') {
		ob_start();
		echo self::maybe_add_scripts();
?>
<script language="javascript" type="text/javascript">
$(document).ready(function(){simpleCarousel('#<?php echo $id ?>', 3, 'fast')});
</script>
<div id="<?php echo $id ?>">
	<div class="up">&nbsp;</div>
	<div class="down">&nbsp;</div>
	<ul>
<?php echo parent::generate($query, $count, $rand, $cache, "\t\t\t<li>", "</li>"); ?>
	</ul>
<div>
<?php
		return ob_get_clean();
	}

	private function maybe_add_scripts() {
		global $wp_scripts;

		if ( defined(DTHUMBS_CAROUSEL_SCRIPTS) )
			return;

		$carousel_url = self::get_plugin_url() . '/carousel';

		$script_code = "\n" . '<script language="javascript" type="text/javascript" src="' . $carousel_url . '/include.js"></script>';
		$script_code .= "\n" . '<script language="javascript" type="text/javascript">' . "\n";

		$script_code .= "include_css('{$carousel_url}/carousel.css');\n";
		if ( !isset($wp_scripts) || !in_array('jquery', $wp_scripts->done) )	// Check if jQuery is already loaded
		#	$script_code .= "include_js('" . get_option('siteurl') . "/wp-includes/js/jquery/jquery.js');\n";
			$script_code .= "include_js('{$carousel_url}/jquery.js');\n";

		$script_code .= "include_js('{$carousel_url}/carousel.js');\n";
		$script_code .= "</script>\n";

		define('DTHUMBS_CAROUSEL_SCRIPTS', TRUE);

		return $script_code;
	}

	private function get_plugin_url() {
		if ( function_exists('plugins_url') )
			return plugins_url(plugin_basename(dirname(__FILE__)));
		else
			// Pre-2.6 compatibility
			return get_option('siteurl') . '/wp-content/plugins/' . plugin_basename(dirname(__FILE__));
	}
}

// Template tag
function deviant_thumbs_carousel($query, $count = 3, $rand = FALSE, $cache = 6) {
	echo deviantThumbsCarousel::carousel($query, $count, $rand, $vertical, $cache);
}

