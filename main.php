<?php
/*
Plugin Name: Deviant Thumbs
Version: 1.8.2.1
Description: Display clickable deviation thumbs from deviantART.
Author: scribu
Author URI: http://scribu.net/
Plugin URI: http://scribu.net/wordpress/deviant-thumbs

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

class deviantThumbs {

	function __construct() {
		$wud = wp_upload_dir();
		define('DTHUMBS_CACHE_DIR', $wud['basedir'].'/deviant-thumbs');

		if ( !is_dir(DTHUMBS_CACHE_DIR) )
			@mkdir(DTHUMBS_CACHE_DIR);

		register_deactivation_hook(__FILE__, array($this, 'clear_cache'));
	}

	// PHP < 5
	function deviantThumbs() {
		$this->__construct();
	}

	function clear_cache() {
		$dir_handle = @opendir(DTHUMBS_CACHE_DIR);

		if ( FALSE == $dir_handle )
			return;

		while ( $file = readdir($dir_handle) )
			if ( $file != "." && $file != ".." )
				unlink(DTHUMBS_CACHE_DIR."/".$file);

		closedir($dir_handle);
		@rmdir(DTHUMBS_CACHE_DIR);
	}

	function get($query, $args = '') {
		extract(wp_parse_args($args, array(
			'count' => 6,
			'rand'  => true,
			'before' => '<li>',
			'after' => '</li>',
			'cache' => 6
		)));

		$cache *= 3600;

		// Set cache file path
		$file = DTHUMBS_CACHE_DIR . '/' . urlencode($query) . '.txt';

		// Get thumbs
		if ( file_exists($file) && (time() - filemtime($file) <= $cache) )
			$thumbs = explode("\n", file_get_contents($file));
		else
			$thumbs = deviantThumbs::get_from_pipe($query, $count, $file);

		// Randomize thumbs
		if ( $rand )
			shuffle($thumbs);

		// Wrap thumbs
		for ( $i=0; $i<$count && $i<count($thumbs); $i++ )
			$output .= $before . $thumbs[$i] . $after . "\n";

		return $output;
	}

	function get_from_pipe($query, $count, $file) {
		require_once(ABSPATH . WPINC . '/class-snoopy.php');

		// Set query sort
		if ( FALSE === strpos($query, 'sort:time') && FALSE === strpos($query, 'boost:popular') )
			$query = 'sort:time ' . $query;

		// Set pipeurl
		$pipeurl  = 'http://pipes.yahoo.com/pipes/pipe.run?_id=627f77f83f199773c5ce8a150a1e5977&_render=php';
		$pipeurl .= '&query=' . urlencode($query);

		// Fetch pipe content
		$snoop = new Snoopy();
		$snoop->fetch($pipeurl);

		if ( $snoop->error ) {
			trigger_error($snoop->error, E_USER_WARNING);
			return FALSE;
		}

		$data = unserialize($snoop->results);

		// Extract thumb list
		$thumbs_nr = count($data['value']['items']);
		for ( $i=0; $i<$thumbs_nr; $i++ )
			$thumbs[] = str_replace(' rel="nofollow" target="_blank"', '', $data['value']['items'][$i]['content']);

		// Put thumbs in cache
		@file_put_contents($file, implode("\n", $thumbs));

		return $thumbs;
	}
}

// Init
deviant_thumbs_init();

function deviant_thumbs_init() {
	require_once(dirname(__FILE__) . '/inc/scb/load.php');

	foreach ( array('carousel', 'widget', 'inline') as $file )
		require_once(dirname(__FILE__) . "/$file.php");

	new deviantThumbs();
	new deviantThumbsInline();
	new deviantThumbsWidget();
}

// Template tag
function deviant_thumbs($query, $args = '') {
	echo deviantThumbs::get($query, $args);
}

