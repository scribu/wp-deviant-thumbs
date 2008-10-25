<?php
abstract class deviantThumbsCarousel extends deviantThumbs {
	public function carousel($query, $args = '') {
		$defaults = array(
			'id' => 'da-carousel',
			'count' => 6,
			'show' => 3,
			'rand'  => true,
			'speed' => 'fast',
			'cache' => 6
		);

		$r = wp_parse_args($args, $defaults);
		extract( $r, EXTR_SKIP );

		ob_start();
		self::maybe_add_scripts();
?>
<div id="<?php echo $id ?>">
	<i class="down">&nbsp;</i>
	<i class="up">&nbsp;</i>
	<ul>
<?php echo parent::generate($query, $count, $rand, $cache, "\t\t<li>", "</li>"); ?>
	</ul>
<div>
<script language="javascript" type="text/javascript">
<?php echo "$(document).ready(function(){new simpleCarousel('#{$id}', {$show}, '{$speed}')});" ?>
</script>
<?php
		return ob_get_clean();
	}

	private function maybe_add_scripts() {
		global $wp_scripts, $da_scripts;

		if ( $da_scripts )
			return;

		$da_scripts = true;

		$carousel_url = self::get_plugin_url() . '/carousel';
?>
<script language="javascript" type="text/javascript" src="<?php echo $carousel_url ?>/include.js"></script>
<script language="javascript" type="text/javascript">
<?php	if ( !@in_array('jquery', $wp_scripts->done) ) {	?>
include_js('<?php echo $carousel_url ?>/jquery.js');
<?php	} ?>
include_js('<?php echo $carousel_url ?>/carousel.js');
include_css('<?php echo $carousel_url ?>/carousel.css');
</script>
<?php
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

