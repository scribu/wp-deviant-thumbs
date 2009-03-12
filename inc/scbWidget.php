<?php

if ( ! class_exists('scbForms_06') )
	require_once(dirname(__FILE__) . '/scbForms.php');

abstract class scbWidget_06 extends scbForms_06 {
	//
	// Interesting member variables.
	protected $name;			// Name for this widget type.
	protected $id_base;			// Root id for all widgets of this type.
	protected $defaults;		// Default option values

	protected $widget_options;	// Option array passed to wp_register_sidebar_widget()
	protected $control_options;	// Option array passed to wp_register_widget_control()

	//
	// Not-so-interesting member variables
	protected $number = false;	// Unique ID number of the current instance.
	protected $id = false; 		// Unique ID string of the current instance (id_base-number)
	protected $instances;		// scbOptions object holder


	//
	// Member functions that you must over-ride.

	// This is where the widget options and defaults go
	abstract protected function setup();

	/** Echo the actual widget content. Subclasses should over-ride this function
	 *	to generate their widget code. */
	abstract protected function content($instance);

	/** Update a particular instance.
	 *	This function should check that $new_instance is set correctly.
	 *	The newly calculated value of $instance should be returned. */
	abstract protected function control_update($new_instance, $old_instance);

	// Echo a control form for the current instance.
	abstract protected function control_form($instance);


	//
	// Helper methods that you might want to over-ride (optional)

	// Echoes the widget args and calls content()
	protected function content_helper($args, $instance) {
		extract($args);

		echo $before_widget . $before_title . $instance['title'] . $after_title;
		$this->content($instance);
		echo $after_widget;
	}

	// Sets defaults and calls control_form()
	protected function control_form_helper($instance) {
		// Set defaults for new instances
		if ( empty($instance) && isset($this->defaults) )
			$instance = $this->defaults;

		$this->control_form($instance);
	}

	// Calls setup(), checks widget options and registers widget
	public function __construct() {
		$this->setup();

		// Check for required fields
		if ( empty($this->name) ) {
			trigger_error('Widget name cannot be blank', E_USER_WARNING);
			return false;
		}

		if ( empty($this->id_base) )
			$this->id_base = sanitize_title_with_dashes($this->name);

		$option_name = 'multiwidget_' . $this->id_base;

		$this->widget_options =	wp_parse_args($this->widget_options, array('classname' => $option_name));
		$this->control_options = wp_parse_args($this->control_options, array('id_base' => $this->id_base));

		// Create instances object
		if ( ! class_exists('scbOptions_05') )
			require_once(dirname(__FILE__) . '/scbOptions.php');
		$this->instances = new scbOptions_05($option_name);

		add_action('widgets_init', array($this, 'register'));
	}


	//
	// Functions you'll want to call

	// This adds a widget input field
	public function input($args, $options = array() ) {
		// Add default label position
		if ( !in_array($args['type'], array('checkbox', 'radio')) && empty($args['desc_pos']) )
			$args['desc_pos'] = 'before';

		// First check names
		if ( FALSE !== $args['check'] ) {
			parent::check_names($args['names'], $options);
			$args['check'] = false;
		}

		// Then add prefix to names and options
		$new_options = array();
		foreach ( (array) $args['names'] as $name )
			$new_options[ $this->get_field_name($name) ] = $options[$name];
		$new_names = array_keys($new_options);

		// Finally, replace the old names
		if ( 1 == count($new_names) )
			$args['names'] = $new_names[0];
		else
			$args['names'] = $new_names;

		// Remember $desc and replace with $title
		if ( $args['desc'] )
			$desc = "<small>{$args['desc']}</small>";
		$args['desc'] = $args['title'];
		unset($args['title']);

		$input = parent::input($args, $new_options);

		return "<p>{$input}\n<br />\n$desc\n</p>\n";
	}


	//
	// PRIVATE FUNCTIONS. Don't worry about these.

	/** Helper function to be called by input().
	 *	Returns an HTML name for the field. */
	protected function get_field_name($field_name) {
		return 'widget-'.$this->id_base.'['.$this->number.']['.$field_name.']';
	}

	/** Helper function to be called by input().
	 *	Returns an HTML id for the field. */
	protected function get_field_id($field_name) {
		return 'widget-'.$this->id_base.'-'.$this->number.'-'.$field_name;
	}

	/** Sets current instance number and id */
	protected function _set($number) {
		$this->number = $number;
		$this->id = $this->id_base.'-'.$number;
	}

	/** Helper function: Registers a single instance. */
	protected function _register_one($number = -1) {
		wp_register_sidebar_widget(
			$this->id, 
			$this->name, 
			array($this, 'widget_callback'), 
			$this->widget_options, 
			array( 'number' => $number )
		);
		wp_register_widget_control(
			$this->id, 
			$this->name, 
			array($this, 'control_callback'),
			$this->control_options, 
			array( 'number' => $number )
		);
	}

	/** Registers this widget-type.
	 *	Must be called during the 'widget_init' action. */
	public function register() {
		$all_instances = (array) $this->instances->get();

		$registered = false;
		foreach( array_keys($all_instances) as $number ) {
			// Old widgets can have null values for some reason
			if( !isset($all_instances[$number]['__multiwidget']) )
				continue;

			$this->_set($number);
			$registered = true;
			$this->_register_one($number);
		}

		// If there are none, we register the widget's existance with a
		// generic template
		if( !$registered ) {
			$this->_set(1);
			$this->_register_one();
		}
	}

	/** Generate the actual widget content.
	 *	Just finds the instance and calls content_helper().
	 *	Do NOT over-ride this function. */
	public function widget_callback($args, $widget_args = 1) {
		if( is_numeric($widget_args) )
			$widget_args = array( 'number' => $widget_args );
		$widget_args = wp_parse_args( $widget_args, array( 'number' => -1 ) );
		$this->_set( $widget_args['number'] );

		// Data is stored as array:
		//	array( number => data for that instance of the widget, ... )
		$instance = $this->instances->get($this->number);
		if( isset($instance) )
			$this->content_helper($args, $instance);
	}

	/** Deal with changed settings and generate the control form.
	 *	Do NOT over-ride this function. */
	public function control_callback($widget_args = 1) {
		global $wp_registered_widgets;

		if( is_numeric($widget_args) )
			$widget_args = array( 'number' => $widget_args );
		$widget_args = wp_parse_args( $widget_args, array( 'number' => -1 ) );

		// Data is stored as array:
		//	array( number => data for that instance of the widget, ... )
		$all_instances = (array) $this->instances->get();

		// We need to update the data
		if( !$updated && !empty($_POST['sidebar']) ) {
			// Tells us what sidebar to put the data in
			$sidebar = (string) $_POST['sidebar'];

			$sidebars_widgets = wp_get_sidebars_widgets();
			if( isset($sidebars_widgets[$sidebar]) )
				$this_sidebar =& $sidebars_widgets[$sidebar];
			else
				$this_sidebar = array();

			foreach( $this_sidebar as $_widget_id )
				// Remove all widgets of this type from the sidebar.	We'll add the
				// new data in a second. This makes sure we don't get any duplicate
				// data since widget ids aren't necessarily persistent across multiple
				// updates
				$number = $wp_registered_widgets[$_widget_id]['params'][0]['number'];
				if( isset($number) )
					if( !in_array( $this->id_base.'-'.$number, $_POST['widget-id'] ) )
						// the widget has been removed.
						unset($all_instances[$number]);

			foreach( (array) $_POST['widget-'.$this->id_base] as $number => $new_instance) {
				$this->_set($number);
				if( isset($all_instances[$number]) )
					$instance = $this->control_update($new_instance, $all_instances[$number]);
				else
					$instance = $this->control_update($new_instance, array());
				if( !empty($instance) ) {
					$instance['__multiwidget'] = $number;
					$all_instances[$number] = $instance;
				}
			}

			$this->instances->update($all_instances);
			static $updated = true; // So that we don't go through this more than once
		}

		// Here we echo out the form
		if( -1 == $widget_args['number'] ) {
			// We echo out a template for a form which can be converted to a
			// specific form later via JS
			$this->_set('%i%');
			$instance = array();
		} else {
			$this->_set($widget_args['number']);
			$instance = $all_instances[ $widget_args['number'] ];
		}
		$this->control_form_helper($instance);
	}
}
