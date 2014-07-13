<?php
/*
Plugin Name: Deviant Thumbs
Version: 1.8.7a
Description: Display clickable deviation thumbs from deviantART.
Author: scribu
Author URI: http://scribu.net/
Plugin URI: http://scribu.net/wordpress/deviant-thumbs
Textdomain: deviant-thumbs

Copyright (C) 2009 Cristi Burcă (mail@scribu.net)

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

// Load scbFramework
require_once dirname(__FILE__) . '/scb/load.php';

// Init
function _deviant_thumbs_init() {
	foreach ( array('carousel', 'widget', 'inline') as $file )
		require_once dirname(__FILE__) . "/$file.php";

	deviantThumbs::init();
	deviantThumbsInline::init();

	scbWidget::init('deviantThumbsWidget', __FILE__, 'deviant-thumbs');
}
scb_init('_deviant_thumbs_init');

class deviantThumbs {
	static $cache_dir;

	static function init() {
		$wud = wp_upload_dir();
		self::$cache_dir = $wud['basedir'] . DIRECTORY_SEPARATOR . 'deviant-thumbs';

		if ( !is_dir(self::$cache_dir) )
			@mkdir(self::$cache_dir);

		register_deactivation_hook(__FILE__, array(__CLASS__, 'clear_cache'));
	}

	static function clear_cache() {
		$dir_handle = @opendir(self::$cache_dir);

		if ( FALSE == $dir_handle )
			return;

		while ( $file = readdir($dir_handle) )
			if ( $file != "." && $file != ".." )
				unlink(self::$cache_dir . DIRECTORY_SEPARATOR .$file);

		closedir($dir_handle);
		@rmdir(self::$cache_dir);
	}

	static function get($query, $args = '') {
		extract(wp_parse_args($args, array(
			'count' => 6,
			'rand'  => true,
			'before' => '<li>',
			'after' => '</li>',
			'cache' => 6
		)));

		$cache *= 3600;

		// Set cache file path
		$file = self::$cache_dir . DIRECTORY_SEPARATOR . urlencode($query) . '.txt';

		// Get thumbs
		if ( file_exists($file) && (time() - filemtime($file) <= $cache) )
			$thumbs = explode("\n", file_get_contents($file));
		else
			$thumbs = deviantThumbs::get_from_pipe($query, $count, $file);

		// Randomize thumbs
		if ( $rand )
			shuffle($thumbs);

		// Wrap thumbs
		$output = '';
		for ( $i=0; $i<$count && $i<count($thumbs); $i++ )
			$output .= $before . $thumbs[$i] . $after . "\n";

		return $output;
	}

	static function get_from_pipe($query, $count, $file) {
		// Set query sort
		if ( FALSE === strpos($query, 'sort:time') && FALSE === strpos($query, 'boost:popular') )
			$query = 'sort:time ' . $query;

		// Set pipeurl
		$pipeurl  = 'http://pipes.yahoo.com/pipes/pipe.run?_id=627f77f83f199773c5ce8a150a1e5977&_render=php';
		$pipeurl .= '&query=' . urlencode($query);

		$data = unserialize(wp_remote_retrieve_body(wp_remote_get($pipeurl)));

		if ( !$data ) {
			trigger_error('Error while retrieving thumb list', E_USER_WARNING);
			return false;
		}

		// Extract thumb list
		$thumbs_nr = count($data['value']['items']);
		for ( $i=0; $i<$thumbs_nr; $i++ )
			$thumbs[] = str_replace(' rel="nofollow" target="_blank"', '', $data['value']['items'][$i]['content']);

		// Put thumbs in cache
		@file_put_contents($file, implode("\n", $thumbs));

		return $thumbs;
	}
}

// Template tag
function deviant_thumbs($query, $args = '') {
	echo deviantThumbs::get($query, $args);
}
