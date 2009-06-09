<?php

class deviantThumbsWidget extends scbWidget
{
	function deviantThumbsWidget()
	{
	
		$widget_ops = array(
			'title' => 'Deviant Thumbs',
			'description' => 'Display thumbs from dA',
		);

		$this->WP_Widget('deviant-thumbs', 'Deviant Thumbs', $widget_ops);
	}

	function content($instance)
	{
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

	function update($new_instance, $old_instance)
	{
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

	function form($instance)
	{
		if ( empty($instance) )
			$instance = array(
				'query' => 'by:',
				'scraps' => false,
				'count' => 3,
				'carousel' => 1,
				'rand' => false,
				'cache' => 6
			);

		$rows = array(
			array(
				'title' => 'Title:',
				'name' => 'title',
				'type' => 'text',
			),
			array(
				'title' => 'Selection (See the <a href="http://help.deviantart.com/577/" target="_blank">FAQ</a> on dA):',
				'name' => 'query',
				'type' => 'text',
				'desc' => 'Example: <em>by:username in:photography</em>'
			),
			array(
				'title' => 'Display %input% thumbs.',
				'name' => 'count',
				'type' => 'text',
				'extra' => 'class="widefat" style="width: 24px; text-align:right"'
			),
			array(
				'title' => 'Show scraps',
				'name' => 'scraps',
				'type' => 'checkbox'
			),
			array(
				'title' => 'Show random thumbs',
				'name' => 'rand',
				'type' => 'checkbox',
			),
			array(
				'title' => 'Show as a carousel',
				'name' => 'carousel',
				'type' => 'checkbox',
			),
			array(
				'title' => 'Update cache every %input% hours.',
				'name' => 'cache',
				'type' => 'text',
				'extra' => 'class="widefat" style="width: 24px; text-align:right"'
			)
		);

		foreach ( $rows as $row )
			echo $this->input($row, $instance);
	}
}

add_action('widgets_init', create_function('', "register_widget('deviantThumbsWidget');"));

