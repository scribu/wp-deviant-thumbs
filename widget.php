<?php

if ( !class_exists('scbWidget_06') )
	require_once(dirname(__FILE__) . '/inc/scbWidget.php');

class deviantThumbsWidget extends scbWidget_06 {

	protected function setup() {
		$this->name = 'Deviant Thumbs';
		$this->id_base = 'deviant-thumbs';

		$this->defaults = array(
			'title' => 'Deviant Thumbs',
			'query' => 'by:',
			'scraps' => false,
			'count' => 3,
			'carousel' => 1,
			'rand' => false,
			'cache' => 6
		);

		$this->widget_options = array('description' => 'Display thumbs from dA');
	}

	protected function content($instance) {
		extract($instance);

		$remove_scraps = '-in:scraps';
		if ( !$scraps && FALSE === strpos($query, $remove_scraps) )
			$query .= " $remove_scraps";

		// Generate content
		if ( $carousel && class_exists('deviantThumbsCarousel') )
			echo deviantThumbsCarousel::carousel($query, compact('count', 'rand', 'cache'));
		else {
			echo '<ul id="deviant-thumbs">';
			echo deviantThumbs::get($query, compact('count', 'rand', 'cache'));
			echo '</ul>';
		}
	}

	protected function control_update($new_instance, $old_instance) {
		if ( !isset($new_instance['title']) ) // user clicked cancel
				return false;

		$instance = $old_instance;
		$instance['title'] = wp_specialchars( $new_instance['title'] );
		$instance['query'] = wp_specialchars( $new_instance['query'] );
		$instance['count'] = (int) $new_instance['count'];
		$instance['carousel'] = (bool) $new_instance['carousel'];
		$instance['rand'] = (bool) $new_instance['rand'];
		$instance['scraps'] = (bool) $new_instance['scraps'];
		$instance['cache'] = (int) $new_instance['cache'];

		return $instance;
	}

	protected function control_form($instance) {
		$rows = array(
			array(
				'title' => 'Title:',
				'names' => 'title',
				'type' => 'text',
			),
			array(
				'title' => 'Selection (See the <a href="http://help.deviantart.com/577/" target="_blank">FAQ</a> on dA):',
				'names' => 'query',
				'type' => 'text',
				'desc' => 'Example: <em>by:username in:photography</em>'
			),
			array(
				'title' => 'Display %input% thumbs.',
				'names' => 'count',
				'type' => 'text',
				'extra' => 'class="widefat" style="width: 24px; text-align:right"'
			),
			array(
				'title' => 'Show scraps',
				'names' => 'scraps',
				'type' => 'checkbox'
			),
			array(
				'title' => 'Show random thumbs',
				'names' => 'rand',
				'type' => 'checkbox',
			),
			array(
				'title' => 'Show as a carousel',
				'names' => 'carousel',
				'type' => 'checkbox',
			),
			array(
				'title' => 'Update cache every %input% hours.',
				'names' => 'cache',
				'type' => 'text',
				'extra' => 'class="widefat" style="width: 24px; text-align:right"'
			)
		);

		foreach ( $rows as $row )
			echo $this->input($row, $instance);
	}
}

