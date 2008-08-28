<?php
class deviantThumbsCarousel extends deviantThumbs {
	var $skin = '';
	var $plugin_url = '';
	var $added_scripts = FALSE;

	function __construct() {
		global $deviant_thumbs_carousel_enabled, $deviant_thumbs_carousel_skin;

		$this->skin = $deviant_thumbs_carousel_skin;

		$this->plugin_url = $this->get_plugin_url();
	}

/*
	function maybe_add_scripts() {
		global $wp_scripts;

		if ($this->added_scripts)
			return;
		
		if ( !isset($wp_scripts) || !in_array('jquery', $wp_scripts->done) )	// Check if jquery isn't already loaded
			$js_urls[] = '/lib/jquery-1.2.3.pack.js';

		$js_urls[] = '/lib/jquery.jcarousel.pack.js';
		$js_urls[] = '/init.js';

		$css_urls[] = '/lib/jquery.jcarousel.css';
		$css_urls[] = '/skins/' . $this->skin . '/skin.css';

		$carousel_url = $this->plugin_url . '/jcarousel';

		foreach($js_urls as $js_url)
			$script_code .= "\n" . '<script language="javascript" type="text/javascript" src="' . $carousel_url . $js_url . '"></script>';

		foreach($css_urls as $css_url)
			$script_code .= "\n" . '<link rel="stylesheet" href="' . $carousel_url . $css_url . '" type="text/css" media="screen">';

		$this->added_scripts = TRUE;

		return $script_code;
	}
*/

	function maybe_add_scripts_DOM() {
		global $wp_scripts;

		if ($this->added_scripts)
			return;

		$carousel_url = $this->plugin_url . '/jcarousel';

		$script_code = "\n" . '<script language="javascript" type="text/javascript" src="' . $carousel_url . '/include.js"></script>';

		$script_code .= "\n" . '<script language="javascript" type="text/javascript">' . "\n";
	#	$script_code .= 'function afterload(){' . "\n";

		if ( !isset($wp_scripts) || !in_array('jquery', $wp_scripts->done) )	// Check if jQuery is already loaded
			$script_code .= 'include_js("' . $carousel_url . '/lib/jquery-1.2.3.pack.js");' . "\n";

		$script_code .= 'include_js("' . $carousel_url . '/lib/jquery.jcarousel.pack.js");' . "\n";
		$script_code .= 'include_js("' . $carousel_url . '/init.js");' . "\n";
		$script_code .= 'include_css("' . $carousel_url . '/lib/jquery.jcarousel.css");' . "\n";
		$script_code .= 'include_css("' . $carousel_url . '/skins/' . $this->skin . '/skin.css");' . "\n";

	#	$script_code .= '}' . "\n";
	#	$script_code .= 'window.onload = afterload();' . "\n";
		$script_code .= '</script>' . "\n";

		$this->added_scripts = TRUE;

		return $script_code;
	}

	function carousel($query, $count, $rand, $vertical, $cache) {
		$output = $this->maybe_add_scripts_DOM();

		// Set orientation
		$orientation = $vertical ? 'vertical' : 'horizontal';

		// Generate output
		$output .= '<ul class="deviant-thumbs-' . $orientation . ' jcarousel-skin-' . $this->skin . '">' ."\n";
		$output .= $this->generate($query, $count, $rand, $cache, '<li>', '</li>');
		$output .= "</ul>\n";

		return $output;
	}

	function get_plugin_url() {
		if ( function_exists('plugins_url') )
			return plugins_url( plugin_basename( dirname(dirname(__FILE__))) );
		else
			// Pre-2.6 compatibility
			return get_option('siteurl') . '/wp-content/plugins/' . plugin_basename( dirname(dirname(__FILE__)) );
	}
}

$deviantThumbsCarousel = new deviantThumbsCarousel();
?>
