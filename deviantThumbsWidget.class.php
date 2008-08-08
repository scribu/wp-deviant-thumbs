<?php
class deviantThumbsWidget extends deviantThumbs {
	function __construct() {
		global $deviant_thumbs_carousel_enabled;

		$options = array(
			'title' => '',
			'query' => 'by:',
			'count' => 3,
			'carousel' => $deviant_thumbs_carousel_enabled,
			'rand' => 0,
			'cache' => 6
		);

		add_option('deviant thumbs', $options);

		register_sidebar_widget('Deviant Thumbs', array(&$this, 'widget'));
		register_widget_control('Deviant Thumbs', array(&$this, 'widget_control'), 250, 200);
	}

	function widget($args) {
		global $deviantThumbsCarousel, $deviant_thumbs_carousel_enabled;

		extract($args);
		extract(get_option('deviant thumbs'));

		echo $before_widget;
		echo $before_title . $title . $after_title;
		if ( $carousel && $deviant_thumbs_carousel_enabled )
			echo $deviantThumbsCarousel->carousel($query, $count, $rand, TRUE, $cache);
		else {
			echo '<ul id="deviant-thumbs-sidebar">';
			echo $this->generate($query, $count, $rand, $cache, '<li>', '</li>');
			echo '</ul>';
		}
		echo $after_widget;
	}

	function widget_control() {
		global $deviantThumbsCarousel, $deviant_thumbs_carousel_enabled;
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

	<?php if ( $deviant_thumbs_carousel_enabled ) { ?>
	<p><label for="deviant_thumbs-carousel">Show as a carousel:</label>
		<input id="deviant_thumbs-carousel" name="deviant_thumbs-carousel" type="checkbox" <?php if ($carousel) echo 'checked="checked"'; ?> value="1">
	</p>
	<?php } ?>

	<input type="hidden" id="deviant_thumbs-submit" name="deviant_thumbs-submit" value="1" />

<?php }
}

$deviantThumbsWidget = new deviantThumbsWidget();
?>
