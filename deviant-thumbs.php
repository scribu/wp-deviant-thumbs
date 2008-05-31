<?php
/*
Plugin Name: Deviant Thumbs
Version: 1.2.7
Description: Display clickable deviation thumbs from your DeviantArt account.
Author: scribu
Author URI: http://scribu.net/
Plugin URI: http://scribu.net/downloads/deviant-thumbs.html
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

register_activation_hook(__FILE__, 'deviant_thumbs_init');

function deviant_thumbs_init(){
	chmod(dirname(__FILE__), 0757);
}

function deviant_thumbs($query, $count=3, $rand=0, $cache=6, $before='<li>', $after='</li>'){
	$query = urlencode($query);
	$dir = dirname(__FILE__);
	$localfile = $dir . '/deviant-thumbs-cache.txt';
	$cache *= 3600;

	$rebuild = TRUE;
	if( file_exists($localfile) ){
		# Load parameters
		$fh = fopen($localfile, 'r');
		$aux = explode(' ',fgets($fh));
		fclose($fh);

		if($aux[0] == $query && $aux[1] >= $count && time()-filemtime($localfile) <= $cache){
			# Extract thumbs from cache
			$tmp = explode("\n\n", @file_get_contents($localfile));
			$thumbs = explode("\n", $tmp[1]);
			$rebuild = FALSE;
		}
	}

	if($rebuild){
		$remotefile = 'http://search.deviantart.com/?section=browse&qh=sort:time&q=' . $query;
		$open = '<span class="shadow">';
		$close = '</span><!-- ^TTT -->';

		# Extract thumbs from remote file
		$source = @file_get_contents($remotefile);
		$items = explode($open,$source);
		array_shift($items);
		array_pop($items);
		foreach($items as $item){
			$aux = explode($close,$item);
			$thumbs[] = $aux[0];
		}
		
		if(!$thumbs){
			echo 'Error: No thumbs found.';
			return 0;
		}

		if($cache && (is_writable($dir) || is_writable($localfile)))
			$cacheon = TRUE;
		else
			$cacheon = FALSE;

		if($cacheon){
			#Write to cache
			$fp = fopen($localfile, "w");
			$opt = implode(' ', compact('query', 'count', 'rand'));
			$tmp = $opt . "\n\n" . implode("\n",$thumbs);
			fwrite($fp, $tmp);
			fclose($fp);
		}
	}

	if($rand) shuffle($thumbs);

	for($i=0; $i<$count && $i<count($thumbs); $i++){
		echo $before.$thumbs[$i].$after."\n";
	}

	return 1;
}

//Begin widget support
add_action('plugins_loaded', 'deviant_thumbs_widget_init');

function deviant_thumbs_widget_init(){
	if( !function_exists('register_sidebar_widget') )
		return;

	$options = array(
		'title' => '',
		'query' => 'by:',
		'count' => 3,
		'rand' => 0,
		'cache' => 6
	);

	add_option('deviant thumbs', $options);

	register_sidebar_widget('Deviant Thumbs', 'deviant_thumbs_widget');
	register_widget_control('Deviant Thumbs', 'deviant_thumbs_widget_control', 250,200);
}

function deviant_thumbs_widget($args){
	extract($args);
	extract(get_option('deviant thumbs'));

	echo $before_widget;
	echo $before_title . $title . $after_title;
	echo '<ul class="deviant-thumbs">';
		deviant_thumbs($query, $count, $rand, $cache);
	echo '</ul>';
	echo $after_widget;
}

function deviant_thumbs_widget_control(){
	$options = $newoptions = get_option('deviant thumbs');

	if ( $_POST['deviant_thumbs-submit'] ) {
		$newoptions['title'] = strip_tags(stripslashes($_POST['deviant_thumbs-title']));
		$newoptions['query'] = strip_tags(stripslashes($_POST['deviant_thumbs-query']));
		$newoptions['count'] = (int) $_POST['deviant_thumbs-count'];
		$newoptions['rand'] = (int) $_POST['deviant_thumbs-rand'];
		$newoptions['cache'] = (int) $_POST['deviant_thumbs-cache'];
	}
	if ( $options != $newoptions ) {
		$options = $newoptions;
		update_option('deviant thumbs', $options);
	}

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

	<p><label for="deviant_thumbs-rand">Show thumbs in a random order:</label>
		<input id="deviant_thumbs-rand" name="deviant_thumbs-rand" type="checkbox" <?php if($rand) echo 'checked="checked"'; ?> value="1">
	</p>

	<p><label for="deviant_thumbs-cache">Update cache every</label>
		<input id="deviant_thumbs-cache" name="deviant_thumbs-cache" type="text" value="<?= $cache; ?>" style="width: 20px;" /> hours.
	</p>
	
	<input type="hidden" id="deviant_thumbs-submit" name="deviant_thumbs-submit" value="1" />

<?php } ?>