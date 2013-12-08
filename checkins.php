<?php
/**
 * Plugin Name: Checkins
 * Plugin URI: http://
 * Description: Show recent Foursquare checkins to your WordPress site.
 * Version: 1.0
 * Author: Joe Fearnley
 * Author URI: http://joefearnley.com
 * License: GPL2
 */

/*
    Copyright 2014 Joe Fearnley (email : joe.fearnley@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// TODO:
//add admin menu to enter foursquare username/user id

class Checkins_Widget extends \WP_Widget {

    public function __construct() {
        parent::__construct('checkins_widget',
                __('Checkins','text_domain'),
                ['description' => __( 'Display Foursquare Checkings', 'text_domain')]
            );
	}

	public function widget( $args, $instance ) {
		// outputs the content of the widget
	}

 	public function form( $instance ) {
		// outputs the options form on admin
	}

	public function update( $new_instance, $old_instance ) {
		// processes widget options to be saved
	}
}

add_action( 'widgets_init', function() {
    register_widget('Checkins_Widget');
});

