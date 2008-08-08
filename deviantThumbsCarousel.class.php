<?php
class deviantThumbsCarousel extends deviantThumbs {
	var $skin = '';
	var $plugin_url = '';
	var $added_scripts = FALSE;

	function __construct() {
		global $deviant_thumbs_carousel_enabled, $deviant_thumbs_carousel_skin;

		// Set skin
		$this->skin = $deviant_thumbs_carousel_skin;

		// Set plugin url
		$this->plugin_url = $this->get_plugin_url();
	}

	function maybe_add_scripts() {
		global $wp_scripts;

		if ($this->added_scripts)
			return;

		if ( !isset($wp_scripts) || !in_array('jquery', $wp_scripts->done) )
			$script_code = "\n" . '<script language="javascript" type="text/javascript" src="' . $this->plugin_url . '/jcarousel/lib/jquery-1.2.3.pack.js"></script>' . "\n";
		$script_code .= '<script language="javascript" type="text/javascript" src="' . $this->plugin_url . '/jcarousel/lib/jquery.jcarousel.pack.js"></script>' . "\n";
		$script_code .= '<script language="javascript" type="text/javascript" src="' . $this->plugin_url . '/jcarousel/init.js"></script>' . "\n";

		$script_code .= '<link rel="stylesheet" href="' . $this->plugin_url . '/jcarousel/lib/jquery.jcarousel.css" type="text/css" media="screen">' . "\n";
		$script_code .= '<link rel="stylesheet" href="' . $this->plugin_url . '/jcarousel/skins/' . $this->skin . '/skin.css" type="text/css" media="screen">' . "\n";

		$this->added_scripts = TRUE;

		return $script_code;
	}

	function maybe_add_scripts_DOM() {
		global $wp_scripts;

		if ($this->added_scripts)
			return;

		$script_code = "\n" . '<script language="javascript" type="text/javascript" src="' . $this->plugin_url . '/jcarousel/include.js"></script>' . "\n";
		$script_code .= '<script language="javascript" type="text/javascript">' . "\n";
	#	$script_code .= 'function afterload(){' . "\n";

		if ( !isset($wp_scripts) || !in_array('jquery', $wp_scripts->done) )
			$script_code .= 'include_js("' . $this->plugin_url . '/jcarousel/lib/jquery-1.2.3.pack.js");' . "\n";

		$script_code .= 'include_js("' . $this->plugin_url . '/jcarousel/lib/jquery.jcarousel.pack.js");' . "\n";
		$script_code .= 'include_js("' . $this->plugin_url . '/jcarousel/init.js");' . "\n";
		$script_code .= 'include_css("' . $this->plugin_url . '/jcarousel/lib/jquery.jcarousel.css");' . "\n";
		$script_code .= 'include_css("' . $this->plugin_url . '/jcarousel/skins/' . $this->skin . '/skin.css");' . "\n";

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
		if ( function_exists('plugin_url') )
			return plugin_url();
		else
			// Pre-2.6 compatibility
			return get_option('siteurl') . '/wp-content/plugins/' . plugin_basename(dirname(__FILE__));
	}
}

$deviantThumbsCarousel = new deviantThumbsCarousel();
?>
