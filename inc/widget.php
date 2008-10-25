<?php
abstract class deviantThumbsWidget {
	public function install() {
		$options = array(
			'title' => 'Deviant Thumbs',
			'query' => 'by:',
			'count' => 3,
			'carousel' => 1,
			'rand' => 0,
			'cache' => 6
		);

		add_option('deviant thumbs', $options);
	}

	public function init() {
		if ( !function_exists('register_sidebar_widget') )
			return;

		register_sidebar_widget('Deviant Thumbs', array('deviantThumbsWidget', 'display'));
		register_widget_control('Deviant Thumbs', array('deviantThumbsWidget', 'control'), 250, 200);
	}

	public function display($args) {
		// Get variables
		extract($args);
		extract(get_option('deviant thumbs'));

		// Generate content
		if ( $carousel && class_exists('deviantThumbsCarousel') )
			$content .= deviantThumbsCarousel::carousel($query, compact('count', 'rand', 'cache'));
		else {
			$content .= '<ul id="deviant-thumbs">';
			$content .= deviantThumbs::generate($query, compact('count', 'rand', 'cache'));
			$content .= '</ul>';
		}

		// Wrap it up
		echo $before_widget . $before_title . $title . $after_title . $content . $after_widget;
	}

	public function control() {
		extract(self::options());
?>
	<p><label for="deviant_thumbs-title">Title:</label>
		<input name="deviant_thumbs-title" type="text" value="<?php echo $title; ?>" style="width: 195px" />
	</p>

	<p><label for="deviant_thumbs-query">Selection (See the <a href="http://help.deviantart.com/577/" target="_blank">FAQ</a> on dA)</label>
		<input name="deviant_thumbs-query" type="text" value="<?php echo $query; ?>" style="width: 227px" />
		<br />Example: <em>by:username in:photography</em>
	</p>

	<p><label for="deviant_thumbs-count">Number of thumbs:</label>
		<input name="deviant_thumbs-count" type="text" value="<?php echo $count; ?>" style="width: 20px" />
	</p>

	<p><label for="deviant_thumbs-cache">Update cache every</label>
		<input name="deviant_thumbs-cache" type="text" value="<?php echo $cache; ?>" style="width: 20px" /> hours.
	</p>

	<p><label for="deviant_thumbs-rand">Show random thumbs:</label>
		<input name="deviant_thumbs-rand" type="checkbox" <?php if ($rand) echo 'checked="checked"'; ?> value="1">
	</p>

	<?php if ( class_exists('deviantThumbsCarousel') ) { ?>
	<p><label for="deviant_thumbs-carousel">Show as a carousel:</label>
		<input name="deviant_thumbs-carousel" type="checkbox" <?php if ($carousel) echo 'checked="checked"'; ?> value="1">
	</p>
	<?php } ?>

	<input name="deviant_thumbs-submit" type="hidden" value="1" />
<?php }

	private function options() {
		$oldoptions = get_option('deviant thumbs');

		if ( !$_POST['deviant_thumbs-submit'] )
			return $oldoptions;

		// Set new options
		$newoptions['title'] = strip_tags(stripslashes($_POST['deviant_thumbs-title']));
		$newoptions['query'] = strip_tags(stripslashes($_POST['deviant_thumbs-query']));
		$newoptions['count'] = (int) $_POST['deviant_thumbs-count'];
		$newoptions['carousel'] = (bool) $_POST['deviant_thumbs-carousel'];
		$newoptions['rand'] = (bool) $_POST['deviant_thumbs-rand'];
		$newoptions['cache'] = (int) $_POST['deviant_thumbs-cache'];

		if ( $oldoptions == $newoptions )
			return $oldoptions;

		update_option('deviant thumbs', $newoptions);

		return $newoptions;
	}
}

// Init
register_activation_hook(__FILE__, array('deviantThumbsWidget', 'install'));
add_action('plugins_loaded', array('deviantThumbsWidget', 'init'));

