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

class Checkins_Widget extends \WP_Widget {

    public function __construct() {
        $args = [
            'description' => __('Display Foursquare Checkins', 'text_domain'),
            'title' => __('Foursquare Checkins', 'text_domain')
        ];

        parent::__construct('checkins', __('Checkins', 'text_domain'), $args);
	}

    public function widget($args, $instance) {
        echo $args['before_widget'];
        echo $args['before_title'] . $this->widget_options['title'] . $args['after_title'];

        echo __('<ul><li><a href="#">sadfasdfasdf</a></li><li><a href="#">yep yep</a></li></ul>', 'text_domain');
        echo $args['after_widget'];
    }

 	public function form($instance) {
        echo '<label for="userid">Foursquare Username:</label>';
        echo '<input type="text" name="userid" value="12345" />';
	}

    public function update($new_instance, $old_instance) {
    }
}

add_action( 'widgets_init', function() {
    register_widget('Checkins_Widget');
});

