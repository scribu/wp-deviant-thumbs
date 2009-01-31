<?php

if ( !class_exists('scbWidget_05') )
	require_once(dirname(__FILE__) . '/inc/scbWidget.php');

class deviantThumbsWidget extends scbWidget_05 {

	protected function setup() {
		$this->name = 'Deviant Thumbs';
		$this->slug = 'deviant-thumbs-widget';

		$this->defaults = array(
			'title' => 'Deviant Thumbs',
			'query' => 'by:',
			'scraps' => false,
			'count' => 3,
			'carousel' => 1,
			'rand' => false,
			'cache' => 6
		);
	}

	protected function content() {
		// Get variables
		extract($this->options->get());

		$remove_scraps = '-in:scraps';
		if ( !$scraps && FALSE === strpos($query, $remove_scraps) )
			$query .= " $remove_scraps";

		// Generate content
		if ( $carousel && class_exists('deviantThumbsCarousel') )
			$content .= deviantThumbsCarousel::carousel($query, compact('count', 'rand', 'cache'));
		else {
			$content .= '<ul id="deviant-thumbs">';
			$content .= deviantThumbs::get($query, compact('count', 'rand', 'cache'));
			$content .= '</ul>';
		}

		return $content;
	}

	protected function control() {
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

		$options = $this->options->get();

		foreach ( $rows as $row )
			echo $this->input($row, $options);
	}

/*
	private function form_handler_old() {
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
*/
}

