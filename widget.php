<?php

// register the widget
add_action( 'widgets_init', 'ajb_register_widget' );

if(!function_exists("ajb_register_widget")){
	function ajb_register_widget() {
		register_widget( 'AJB_Featured_Sponsor_Widget' );
	}
}

class AJB_Featured_Sponsor_Widget extends WP_Widget {

	function AJB_Featured_Sponsor_Widget() {
		$widget_options = array(
				'classname' => 'AJB_Featured_Sponsor_Widget',
				'description' => 'Display a featured sponsor logo and link to website'
		);
		$this->WP_Widget( 'AJB_Featured_Sponsor_Widget', 'AJB Featured Sponsor', $widget_options );

	}

	// The settings form for the widget
	function form($instance) {
		_log($instance);
		$defaults = array( 'title' => 'Featured Sponsor', 'logo' => '123', 'website' => '123322', 'sponsor' => 'gaeefs');
		$instance = wp_parse_args( (array) $instance, $defaults );
		$title = $instance['title'];
		$logo = $instance['logo'];
		$website = $instance['website'];
		$sponsor = $instance['sponsor'];

		?>
        <p>Title: <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /></p>
        <p>Sponsor: <input class="widefat" id="<?php echo $this->get_field_id( 'sponsor' ); ?>" name="<?php echo $this->get_field_name( 'sponsor' ); ?>" type="text" value="<?php echo esc_attr( $sponsor ); ?>" /></p>
        <p>Website: <input class="widefat" id="<?php echo $this->get_field_id( 'website' ); ?>" name="<?php echo $this->get_field_name( 'website' ); ?>" type="text" value="<?php echo esc_attr( $website ); ?>" /></p>
        <p>Logo: <input class="widefat" id="<?php echo $this->get_field_id( 'logo' ); ?>" name="<?php echo $this->get_field_name( 'logo' ); ?>" type="text" value="<?php echo esc_attr( $logo ); ?>" /></p>

        <?php
	}
	
	// save the widget settings
	function update($new_instance, $old_instance) {
		_log($new_instance);
		_log($old_instance);
		$instance = $old_instance;
		$instance['title'] = wp_strip_all_tags( $new_instance['title'], false );
		$instance['website'] = wp_strip_all_tags( $new_instance['website'], false );
		$instance['logo'] = wp_strip_all_tags( $new_instance['logo'], false );
		$instance['sponsor'] = wp_strip_all_tags( $new_instance['sponsor'], false );		
		return $instance;
	}
	
	
	// Display the widget
	function widget($args, $instance) {
		extract($args);
		
		echo $before_widget;
		$title = apply_filters( 'widget_title', $instance['title'] );
		$sponsor = empty( $instance['sponsor'] ) ? '&nbsp;' : $instance['sponsor'];
		$website = empty( $instance['website'] ) ? '&nbsp;' : $instance['website'];
		$logo = empty( $instance['logo'] ) ? '&nbsp;' : $instance['logo'];
		
		if (!empty($title) ) { echo $before_title . $title . $after_title; }
		echo '<p><a href="' . $website . '"><img src="' . $logo . '" width="160px" /></a></p>';
		echo '<p>' . $sponsor . '</p>';
		echo $after_widget;		
	}
				
}

?>