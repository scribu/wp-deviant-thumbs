<?php
class deviantThumbsWidget {
	var $options;

	public function __construct($file) {
		if ( ! class_exists('scbOptions') )
			require_once('inc/scbOptions.php');

		$this->options = new scbOptions('deviant thumbs');

		register_activation_hook($file, array($this, 'install'));
		register_uninstall_hook($file, array($this, 'uninstall'));

		add_action('plugins_loaded', array($this, 'init'));
	}

	public function install() {
		$this->options->update(array(
			'title' => 'Deviant Thumbs',
			'query' => 'by:',
			'count' => 3,
			'carousel' => 1,
			'rand' => 0,
			'cache' => 6
		), false);
	}

	public function uninstall() {
		$this->options->delete();
		deviantThumbs::clear_cache();
	}

	public function init() {
		if ( !function_exists('register_sidebar_widget') )
			return;

		register_sidebar_widget('Deviant Thumbs', array($this, 'display'));
		register_widget_control('Deviant Thumbs', array($this, 'control'), 250, 200);
	}

	public function display($args) {
		// Get variables
		extract($args);
		extract($this->options->get());

		// Generate content
		if ( $carousel && class_exists('deviantThumbsCarousel') )
			$content .= deviantThumbsCarousel::carousel($query, compact('count', 'rand', 'cache'));
		else {
			$content .= '<ul id="deviant-thumbs">';
			$content .= deviantThumbs::get($query, compact('count', 'rand', 'cache'));
			$content .= '</ul>';
		}

		// Wrap it up
		echo $before_widget . $before_title . $title . $after_title . $content . $after_widget;
	}

	public function control() {
		$this->form_handler();
		extract($this->options->get());
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

	private function form_handler() {
		if ( !$_POST['deviant_thumbs-submit'] )
			return;

		// Collect new options
		$newoptions['title'] = strip_tags(stripslashes($_POST['deviant_thumbs-title']));
		$newoptions['query'] = strip_tags(stripslashes($_POST['deviant_thumbs-query']));
		$newoptions['count'] = (int) $_POST['deviant_thumbs-count'];
		$newoptions['carousel'] = (bool) $_POST['deviant_thumbs-carousel'];
		$newoptions['rand'] = (bool) $_POST['deviant_thumbs-rand'];
		$newoptions['cache'] = (int) $_POST['deviant_thumbs-cache'];

		$this->options->update($newoptions);
	}
}

// < WP 2.7
if ( !function_exists('register_uninstall_hook') ) :
function register_uninstall_hook() {}
endif;
