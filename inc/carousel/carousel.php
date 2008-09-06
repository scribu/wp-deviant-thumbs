<?php
class deviantThumbsCarousel extends deviantThumbs {
	var $added_scripts = FALSE;

	function carousel($query, $count, $rand, $cache) {
		$output = $this->maybe_add_scripts_DOM();

		$output .= "<div id=\"deviant-thumbs-carousel\">\n";
		$output .= '<div id="dt-prev">&nbsp;</div>'."\n";
		$output .= '<div id="dt-next">&nbsp;</div>'."\n";
		$output .= "<div id=\"dt-carousel-vertical\">\n";
		$output .= "<ul>\n";
		$output .= $this->generate($query, $count, $rand, $cache, '<li>', '</li>');
		$output .= "\n</ul>";
		$output .= "\n<div>";
		$output .= "\n<div>";

		return $output;
	}

	function maybe_add_scripts_DOM() {
		global $wp_scripts;

		if (TRUE === $this->added_scripts)
			return;

		$carousel_url = $this->get_plugin_url();

		$script_code = "\n" . '<script language="javascript" type="text/javascript" src="' . $carousel_url . '/include.js"></script>';

		$script_code .= "\n" . '<script language="javascript" type="text/javascript">' . "\n";

		if ( !isset($wp_scripts) || !in_array('jquery', $wp_scripts->done) )	// Check if jQuery is already loaded
		#	$script_code .= "include_js('http://code.jquery.com/jquery-latest.pack.js');\n";
		#	$script_code .= "include_js('" . get_option('siteurl') . "/wp-includes/js/jquery/jquery.js');\n";
			$script_code .= "include_js('$carousel_url/jquery.js');\n";

		$script_code .= "include_js('$carousel_url/carousel.js');\n";
		$script_code .= "include_css('$carousel_url/carousel.css')\n";

		$script_code .= "</script>\n";

		$this->added_scripts = TRUE;

		return $script_code;
	}

	function get_plugin_url() {
		if ( function_exists('plugins_url') )
			return plugins_url( plugin_basename(dirname(__FILE__)) );
		else
			// Pre-2.6 compatibility
			return get_option('siteurl') . '/wp-content/plugins/' . plugin_basename(dirname(__FILE__));
	}
}

$deviantThumbsCarousel = new deviantThumbsCarousel();

// Functions
function deviant_thumbs_carousel($query, $count = 3, $rand = FALSE, $cache = 6) {
	global $deviantThumbsCarousel;

	if ( !isset($deviantThumbsCarousel) )
		$deviantThumbsCarousel = new deviantThumbsCarousel();

	echo $deviantThumbsCarousel->carousel($query, $count, $rand, $vertical, $cache);
}
?>
