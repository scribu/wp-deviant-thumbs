<?php
/*
Plugin Name: Deviant Thumbs
Version: 1.2.4
Description: Display clickable deviation thumbs from your DeviantArt account.
Author: scribu
Author URI: http://scribu.net/
Plugin URI: http://scribu.net/download/deviant-thumbs/
*/

/*  Copyright 2008  scribu  (email : scribu@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

function deviant_thumbs($query, $count=3, $rand=0, $cache=3){
	$query = implode('%20', explode(' ', $query));
	$localfile = dirname(__FILE__).'/deviant-thumbs-cache.txt';
	$cache *= 3600;

	$rebuild = TRUE;
	if($cache && (is_writable($localfile) || is_writable(dirname(__FILE__)))){
		$cacheon = TRUE;
		
		if(file_exists($localfile)){
			//Load file
			$fh = fopen($localfile, 'r');
			$aux = explode(' ',fgets($fh));
			fclose($fh);

			if($aux[0] == $query && $aux[1] >= $count && time()-filemtime($localfile) <= $cache){
				//Extract thumbs from cache
				$tmp = explode("\n\n",@file_get_contents($localfile));
				$thumbs = explode("\n",$tmp[1]);
				$rebuild = FALSE;
			}
		}
	}
	else $cacheon = FALSE;

	if($rebuild){
		$remotefile = "http://search.deviantart.com/?section=browse&qh=sort:time&q=" . $query;
		$open = "<span class=\"shadow\">";
		$close = "</span><!-- ^TTT -->";
		//Process remote file
		$source = @file_get_contents($remotefile);
		$items = explode($open,$source);
		array_shift($items);
		array_pop($items);

		for($i=0; $i<count($items); $i++){
			$aux = explode($close,$items[$i]);
			$thumbs[$i] = $aux[0];
		}
		//Write to cache
		if($cacheon){
			$fp = fopen($localfile, "w");
			$opt = implode(' ', compact("query", "count", "rand"));
			$tmp = $opt . "\n\n" . implode("\n",$thumbs);
			fwrite($fp, $tmp);
			fclose($fp);
		}
	}

	if($rand) shuffle($thumbs);
	
	echo '<ul class="deviant-thumbs">';
	for($i=0; $i<$count && $i<count($thumbs); $i++){
		echo "<li>".$thumbs[$i].$after."</li>\n";
	}
	echo '</ul>';
}

//Begin widget support
add_action('plugins_loaded', 'deviant_thumbs_widget_init');

function deviant_thumbs_widget_init(){
  if ( !function_exists('register_sidebar_widget') )
    return;

  $options = array(
    'title' => '',
    'query' => 'by:',
    'count' => 3,
	'rand' => 0,
	'cache' => 3);

  add_option('deviant thumbs', $options);

  register_sidebar_widget("Deviant Thumbs", "deviant_thumbs_widget");
  register_widget_control("Deviant Thumbs", "deviant_thumbs_widget_control", 250,200);
}

function deviant_thumbs_widget($args){
  extract($args);
  extract(get_option('deviant thumbs'));
  
  echo $before_widget;
  echo $before_title . $title . $after_title;
    deviant_thumbs($query, $count, $rand, $cache);
  echo $after_widget;
}

function deviant_thumbs_widget_control(){
	$options = $newoptions = get_option('deviant thumbs');

	if ( $_POST["deviant_thumbs-submit"] ) {
		$newoptions['title'] = strip_tags(stripslashes($_POST["deviant_thumbs-title"]));
		$newoptions['query'] = strip_tags(stripslashes($_POST["deviant_thumbs-query"]));
		$newoptions['count'] = (int) $_POST["deviant_thumbs-count"];
		$newoptions['rand'] = (int) $_POST["deviant_thumbs-rand"];
		$newoptions['cache'] = (int) $_POST["deviant_thumbs-cache"];
	}
	if ( $options != $newoptions ) {
		$options = $newoptions;
		update_option('deviant thumbs', $options);
	}

	extract($options);
?>

	<label for="deviant_thumbs-title" style="text-align:left; line-height: 35px; display: block;">Title:
		<input id="deviant_thumbs-title" name="deviant_thumbs-title" type="text" value="<?php echo $title; ?>" style="width: 200px;" />
	</label>

	<label for="deviant_thumbs-query" style="text-align:left; line-height: 35px; display: block;">Query (Ex: 'by:Username')
		<input id="deviant_thumbs-query" name="deviant_thumbs-query" type="text" value="<?php echo $query; ?>" style="width: 100%" />
	</label>

	<label for="deviant_thumbs-count" style="text-align:left; line-height: 35px; display: block;">Number of thumbs:
		<input id="deviant_thumbs-count" name="deviant_thumbs-count" type="text" value="<?php echo $count; ?>" style="width: 25px;" />
	</label>

	<label for="deviant_thumbs-rand" style="text-align:left; line-height: 35px; display: block;">Randomise thumbs:
		<input id="deviant_thumbs-rand" name="deviant_thumbs-rand" checked="unchecked" value="1" type="checkbox">
	</label>
	
	<label for="deviant_thumbs-cache" style="text-align:left; line-height: 35px; display: block;">Update cache every
		<input id="deviant_thumbs-cache" name="deviant_thumbs-cache" type="text" value="<?php echo $cache; ?>" style="width: 25px;" /> hours.
	</label>

  <input type="hidden" id="deviant_thumbs-submit" name="deviant_thumbs-submit" value="1" />

<?php } ?>