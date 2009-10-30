<?php
/*
Plugin Name: Deviant Thumbs
Version: 1.9
Description: Display clickable deviation thumbs from deviantART.
Author: scribu
Author URI: http://scribu.net/
Plugin URI: http://scribu.net/wordpress/deviant-thumbs
Textdomain: deviant-thumbs

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

// Init
_deviant_thumbs_init();
function _deviant_thumbs_init()
{
	// Load scbFramework
	require_once dirname(__FILE__) . '/scb/load.php';

	foreach ( array('carousel', 'widget', 'inline') as $file )
		require_once dirname(__FILE__) . "/$file.php";

	deviantThumbs::init();
	deviantThumbsInline::init();

	scbWidget::init('deviantThumbsWidget', __FILE__, 'deviant-thumbs');
}

abstract class deviantThumbs 
{
	function init()
	{
	}

	static function get($query, $args = '')
	{
		extract(wp_parse_args($args, array(
			'count' => 6, 'rand'  => true,
			'before' => '<li>', 'after' => '</li>',
		)));

		$thumbs = deviantThumbs::remote_get($query, $count, $rand);

		// Wrap thumbs
		$output = '';
		foreach ( $thumbs as $thumb )
			$output .= $before . $thumb . $after . "\n";

		return $output;
	}

	static function remote_get($query, $count, $rand)
	{
		// Set query sort
		if ( FALSE === strpos($query, 'sort:time') && FALSE === strpos($query, 'boost:popular') )
			$query = 'sort:time ' . $query;

		// Set feed url
		$url  = add_query_arg('q', $query, 'http://backend.deviantart.com/rss.xml?type=deviation');

		add_filter('wp_feed_cache_transient_lifetime', array(__CLASS__, '_cache_time'));
		$rss = fetch_feed($url);
		remove_filter('wp_feed_cache_transient_lifetime', array(__CLASS__, '_cache_time'));

		if ( ! $rss )
		{
			trigger_error('Error while retrieving thumb list', E_USER_WARNING);
			return false;
		}

		$keys = range(0, $rss->get_item_quantity());

		if ( $rand )
			shuffle($keys);

		$thumbs = array();
		foreach ( array_slice($keys, 0, $count) as $i )
		{
			$item = $rss->get_item($i);

			$title = $item->get_title();
			$link = $item->get_permalink();
			$src = $item->get_enclosure(1)->get_thumbnail(1);

			$thumbs[] = "<a title='$title' href='$link'><img src='$src' /></a>";
		}

		return $thumbs;
	}

	static function _cache_time()
	{
		return 3600;
	}
}

// Template tag
function deviant_thumbs($query, $args = '')
{
	echo deviantThumbs::get($query, $args);
}

