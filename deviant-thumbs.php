<?php
/*
Plugin Name: Deviant Thumbs
Version: 1.4.3
Description: Display clickable deviation thumbs from your DeviantArt account.
Author: scribu
Author URI: http://scribu.net/
Plugin URI: http://scribu.net/projects/deviant-thumbs.html
*/

/*
Copyright (C) 2008 scribu.net (scribu AT gmail DOT com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

/******** Carousel Options  **********/
$deviant_thumbs_carousel_enabled = TRUE;	// Set to FALSE if you don't want to use carousels at all.
$deviant_thumbs_carousel_skin = 'deviantart';
/************************************/

class deviantThumbs {
	var $cacheon = FALSE;
	var $dir = '';
	var $localfile = '';
	var $thumbs = array();

	function __construct() {
		$this->dir = dirname(__FILE__) . '/cache';

		register_activation_hook(__FILE__, array(&$this, 'init_cache'));

		if ( is_writable($this->dir) )
			$this->cacheon = TRUE;
	}
	
	function generate($query, $count, $rand, $cache, $before, $after) {
		$cache *= 3600;
		$this->localfile = $this->dir . '/' . urlencode($query) . $count . '.txt';

		if ( file_exists($this->localfile) && (time()-filemtime($this->localfile) <= $cache) )
			$this->use_cache();
		else
			$this->rebuild($query, $count);

		if ($rand) shuffle($this->thumbs);

		$thumbs_nr = count($this->thumbs);
		for ($i=0; $i<$count && $i<$thumbs_nr; $i++)
			$output .= $before . $this->thumbs[$i] . $after ."\n";

		return $output;
	}

	function rebuild($query, $count) {
		$pipeurl = 'http://pipes.yahoo.com/pipes/pipe.run?_id=627f77f83f199773c5ce8a150a1e5977&_render=php';
		$pipeurl .= '&query=' . urlencode($query);
		$pipeurl .= '&count=' . $count;

		$data = unserialize(file_get_contents($pipeurl));
		
		// Extract thumb list
		$thumbs_nr = count($data['value']['items']);
		for ($i=0; $i<$thumbs_nr; $i++) {
			$tmp = $data['value']['items'][$i]['content'];
			$this->thumbs[] = str_replace ( ' rel="nofollow" target="_blank"', '', $tmp);
		}

		$this->update_cache();
	}

	function init_cache() {
		mkdir($this->dir);
	}

	function update_cache() {
		if (!$this->cacheon)
			return;

		$fp = fopen($this->localfile, "w");
		$data = implode("\n", $this->thumbs);
		fwrite($fp, $data);
		fclose($fp);
	}

	function use_cache() {
		$this->thumbs = explode("\n", file_get_contents($this->localfile) );
	}
}

class deviantThumbsCarousel extends deviantThumbs {
	var $skin;
	var $plugin_url;

	function __construct() {
		$this->carousel_init();
	}

	function carousel_init() {
		global $deviant_thumbs_carousel_enabled, $deviant_thumbs_carousel_skin;

		if (!$deviant_thumbs_carousel_enabled)
			return FALSE;

		// Set plugin url
		if ( function_exists('plugin_url') )
			$this->plugin_url = plugin_url();
		else
			// Pre-2.6 compatibility
			$this->plugin_url = get_option('siteurl') . '/wp-content/plugins/' . plugin_basename(dirname(__FILE__));

		// Set skin
		$this->skin = $deviant_thumbs_carousel_skin;

		// Add js and css to head
		add_action('template_redirect', array(&$this, 'carousel_js'));
		add_action('wp_head', array(&$this, 'carousel_css'));

		return TRUE;
	}

	function carousel_js() {
		$js_url = $this->plugin_url . '/jcarousel';

		wp_enqueue_script( 'jcarousel', $js_url . '/lib/jquery.jcarousel.pack.js', array('jquery'));
		wp_enqueue_script( 'jcarousel_init', $js_url . '/init.js');
	}

	function carousel_css() {
		$css_url = $this->plugin_url . '/jcarousel';
		$skin_url = $css_url . '/skins/' . $this->skin . '/skin.css';

		echo '<link rel="stylesheet" href="' . $css_url . '/lib/jquery.jcarousel.css" type="text/css" media="screen" />'."\n";
		echo '<link rel="stylesheet" href="' . $skin_url . '" type="text/css" media="screen" />'."\n";
	}

	function carousel($query, $count, $rand, $vertical, $cache) {
		// Set orientation
		$orientation = $vertical ? 'vertical' : 'horizontal';

		// Generate output
		$output = '<ul class="deviant-thumbs-' . $orientation . ' jcarousel-skin-' . $this->skin . '">' ."\n";
		$output .= $this->generate($query, $count, $rand, $cache, '<li>', '</li>');
		$output .= "</ul>\n";

		return $output;
	}
}

class deviantThumbsWidget extends deviantThumbsCarousel {
	function __construct() {

		$options = array(
			'title' => '',
			'query' => 'by:',
			'count' => 3,
			'carousel' => $this->carousel_init(),
			'rand' => 0,
			'cache' => 6
		);

		add_option('deviant thumbs', $options);

		register_sidebar_widget('Deviant Thumbs', array(&$this, 'widget'));
		register_widget_control('Deviant Thumbs', array(&$this, 'widget_control'), 250, 200);
	}

	function widget($args) {
		extract($args);
		extract(get_option('deviant thumbs'));

		echo $before_widget;
		echo $before_title . $title . $after_title;
		if ($carousel)
			echo $this->carousel($query, $count, $rand, TRUE, $cache);
		else {
			echo '<ul id="deviant-thumbs-sidebar">';
			echo $this->generate($query, $count, $rand, $cache, '<li>', '</li>');
			echo '</ul>';
		}
		echo $after_widget;
	}

	function widget_control() {
		global $deviant_thumbs_carousel_enabled;
		$options = $newoptions = get_option('deviant thumbs');

		// Set new options
		if ( $_POST['deviant_thumbs-submit'] ) {
			$newoptions['title'] = strip_tags(stripslashes($_POST['deviant_thumbs-title']));
			$newoptions['query'] = strip_tags(stripslashes($_POST['deviant_thumbs-query']));
			$newoptions['count'] = (int) $_POST['deviant_thumbs-count'];
			$newoptions['carousel'] = $_POST['deviant_thumbs-carousel'];
			$newoptions['rand'] = $_POST['deviant_thumbs-rand'];
			$newoptions['cache'] = (int) $_POST['deviant_thumbs-cache'];
		}

		// Update options if necessary
		if ( $options != $newoptions ) {
			$options = $newoptions;
			update_option('deviant thumbs', $options);
		}

		// Reload options and display form
		extract($options);
?>

	<p><label for="deviant_thumbs-title">Title:</label>
		<input id="deviant_thumbs-title" name="deviant_thumbs-title" type="text" value="<?= $title; ?>" style="width: 180px;" />
	</p>

	<p><label for="deviant_thumbs-query">Query:</label>
		<input id="deviant_thumbs-query" name="deviant_thumbs-query" type="text" value="<?= $query; ?>" style="width: 100%" />
		<br />Example: 'by:Username in:Photography'
	</p>

	<p><label for="deviant_thumbs-count">Number of thumbs:</label>
		<input id="deviant_thumbs-count" name="deviant_thumbs-count" type="text" value="<?= $count; ?>" style="width: 20px;" />
	</p>

	<p><label for="deviant_thumbs-cache">Update cache every</label>
		<input id="deviant_thumbs-cache" name="deviant_thumbs-cache" type="text" value="<?= $cache; ?>" style="width: 20px;" /> hours.
	</p>

	<p><label for="deviant_thumbs-rand">Show thumbs in a random order:</label>
		<input id="deviant_thumbs-rand" name="deviant_thumbs-rand" type="checkbox" <?php if ($rand) echo 'checked="checked"'; ?> value="1">
	</p>

	<?php if ($deviant_thumbs_carousel_enabled) { ?>
	<p><label for="deviant_thumbs-carousel">Show as a carousel:</label>
		<input id="deviant_thumbs-carousel" name="deviant_thumbs-carousel" type="checkbox" <?php if ($carousel) echo 'checked="checked"'; ?> value="1">
	</p>
	<?php } ?>

	<input type="hidden" id="deviant_thumbs-submit" name="deviant_thumbs-submit" value="1" />

<?php }
}

global $deviantThumbs, $deviantThumbsCarousel, $deviantThumbsWidget;

// Init
function deviant_thumbs_init() {
	global $deviantThumbsCarousel, $deviantThumbsWidget;

	if ( function_exists('register_sidebar_widget') )
		$deviantThumbsWidget = new deviantThumbsWidget();
	else
		$deviantThumbsCarousel = new deviantThumbsCarousel();
}

add_action('plugins_loaded', 'deviant_thumbs_init');

// Functions
function deviant_thumbs($query, $count=3, $rand=FALSE, $cache=6, $before='<li>', $after='</li>') {
	global $deviantThumbs;
	if ( !isset($deviantThumbs) )
		$deviantThumbs = new deviantThumbs();

	echo $deviantThumbs->generate($query, $count, $rand, $cache, $before, $after);
}

function deviant_thumbs_carousel($query, $count=3, $rand=FALSE, $vertical=FALSE, $cache=6) {
	global $deviantThumbsCarousel;
	if ( !isset($deviantThumbsCarousel) )
		$deviantThumbsCarousel = new deviantThumbsCarousel();

	echo $deviantThumbsCarousel->carousel($query, $count, $rand, $vertical, $cache);
}
?>
