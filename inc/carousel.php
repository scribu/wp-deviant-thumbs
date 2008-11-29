<?php
class deviantThumbsCarousel extends deviantThumbs {
	public function carousel($query, $args = '') {
		extract(wp_parse_args($args, array(
			'id' => 'da-carousel',
			'count' => 6,
			'show' => 3,
			'rand'  => true,
			'speed' => 'fast',
			'cache' => 6
		)), EXTR_SKIP);

		$before = "\t\t<li>";
		$after = "</li>";

		$thumbs = parent::generate($query, compact('count', 'rand', 'cache', 'before', 'after'));

		$output = self::maybe_add_scripts();
		$output .= sprintf('
<div id="%1$s">
	<i class="down">&nbsp;</i>
	<i class="up">&nbsp;</i>
	<ul>
%2$s
	</ul>
</div>
<script language="javascript" type="text/javascript">
$(document).ready(function(){new simpleCarousel("#%1$s", "%3$s", "%4$s")});
</script>', $id, $thumbs, $show, $speed);
		return $output;
	}

	private function maybe_add_scripts() {
		global $wp_scripts, $dt_scripts;

		if ( $dt_scripts )
			return;

		$dt_scripts = true;

		$carousel_url = self::get_plugin_url() . '/carousel';
		ob_start();
?>
<script language="javascript" type="text/javascript" src="<?php echo $carousel_url ?>/include.js"></script>
<script language="javascript" type="text/javascript">
<?php if ( !@in_array('jquery', $wp_scripts->done) ) { ?>
include_js('<?php echo $carousel_url ?>/jquery.js');
<?php } ?>
include_js('<?php echo $carousel_url ?>/carousel.js');
include_css('<?php echo $carousel_url ?>/carousel.css');
</script>
<?php
		return ob_get_clean();
	}

	private function get_plugin_url() {
		if ( function_exists('plugins_url') )
			return plugins_url(plugin_basename(dirname(__FILE__)));
		else
			// Pre-2.6 compatibility
			return get_option('siteurl') . '/wp-content/plugins/' . plugin_basename(dirname(__FILE__));
	}
}

// Template tag
function deviant_thumbs_carousel($query, $args = '') {
	echo deviantThumbsCarousel::carousel($query, $args);
}

