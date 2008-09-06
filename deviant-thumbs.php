<?php
/*
Plugin Name: Deviant Thumbs
Version: 1.5
Description: Display clickable deviation thumbs from deviantART.
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

$deviant_thumbs_carousel_enabled = TRUE;	// Set to FALSE if you don't want to use carousels at all.

define('DTHUMBSCACHE', dirname(__FILE__) . '/cache');

class deviantThumbs {
	var $localfile = '';
	var $thumbs = array();

	function generate($query, $count, $rand, $cache, $before, $after) {
		$cache *= 3600;

		$this->localfile = DTHUMBSCACHE . '/' . urlencode($query) . '.txt';

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

		$data = unserialize(file_get_contents($pipeurl));
		
		// Extract thumb list
		$thumbs_nr = count($data['value']['items']);
		for ($i=0; $i<$thumbs_nr; $i++) {
			$tmp = $data['value']['items'][$i]['content'];
			$this->thumbs[] = str_replace ( ' rel="nofollow" target="_blank"', '', $tmp);
		}

		$this->update_cache();
	}

	function update_cache() {
		$fp = @fopen($this->localfile, "w");
		
		if ( $fp === FALSE )
			return;

		$data = implode("\n", $this->thumbs);

		fwrite($fp, $data);
		fclose($fp);
	}

	function use_cache() {
		$this->thumbs = explode("\n", file_get_contents($this->localfile) );
	}
}

// Init
register_activation_hook(__FILE__, create_function('', 'mkdir(DTHUMBSCACHE, 757);') );
add_action('plugins_loaded', 'deviant_thumbs_init');

global $deviantThumbs, $deviantThumbsCarousel, $deviantThumbsWidget;

function deviant_thumbs_init() {
	global $deviant_thumbs_carousel_enabled, $deviantThumbsCarousel, $deviantThumbsWidget;

	if ( $deviant_thumbs_carousel_enabled )
		require_once ('inc/carousel/carousel.php');

	if ( function_exists('register_sidebar_widget') )
		require_once ('inc/widget.php');
}

// Functions
function deviant_thumbs($query, $count = 3, $rand = FALSE, $cache = 6, $before = '<li>', $after = '</li>') {
	global $deviantThumbs;

	if ( !isset($deviantThumbs) )
		$deviantThumbs = new deviantThumbs();

	echo $deviantThumbs->generate($query, $count, $rand, $cache, $before, $after);
}
?>
