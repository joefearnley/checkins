<?php
/**
 * Plugin Name: Checkins
 * Plugin URI: http://joefearnley.com/checkins
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

include_once dirname(__FILE__) . '/includes/checkins-config.php';
include_once dirname(__FILE__) . '/vendor/autoload.php';

class Checkins_Widget extends \WP_Widget {

    /**
     * @var Mustache template engine
     */
    private $mustache;

    /**
     * Register the widget
     */
    public function __construct() {
        $args = [
            'description' => __('Display Foursquare Checkins', 'text_domain'),
            'title' => __('Foursquare Checkins', 'text_domain'),
            'limit' => 5
        ];

        $this->mustache = new Mustache_Engine();

        parent::__construct('checkins', __('Checkins', 'text_domain'), $args);
    }

    /**
     * Front-end display of widget.
     *
     * @see WP_Widget::widget()
     *
     * @param array $args Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget($args, $instance) {
        echo $args['before_widget'];

        $title = (!empty($instance['title'])) ? $instance['title'] : $this->widget_options['title'];
        $limit = (!empty($instance['title'])) ? $instance['limit'] : $this->widget_options['limit'];

        echo $args['before_title'] . $title . $args['after_title'];
        echo '<ul>';

        $feed_url = $instance['feed'];

        if(!empty($feed_url)) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_URL, $feed_url);
            $results = curl_exec($ch);
            curl_close($ch);

            $feed = simplexml_load_string($results);

            for($i = 0; $i < $limit; $i++) {
                $checkin = $feed->channel->item[$i];
                $checkin_date = date_parse($checkin->pubDate);

                $context = [
                    'link' => $checkin->link,
                    'location' => $checkin->title,
                    'date' => $checkin_date['month'] . '/' . $checkin_date['day']
                ];

                $template = '<li><a href="{{link}}">{{location}} on {{date}}</a></li>';
                echo $this->mustache->render($template, $context);
            }
        } else {
            echo $this->mustache->render('<li>Please enter Foursquare history feed in Checkins Widget settings</li>');
        }

        echo '</ul>';
        echo $args['after_widget'];
    }

    /**
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     */
    public function form($instance) {
        $defaults = ['title' => 'Foursquare Checkins', 'email' => ''];
        $instance = wp_parse_args((array) $instance, $defaults);

        $title = $instance['title'];
        $feed = $instance['feed'];
        $limit = $instance['limit'];

        $template = '<p><label for="{{id}}">{{label_text}}:</label>
                    <input class="widefat" id="{id}}" name="{{name}}" type="text" value="{{value}}"/></p>';

        $context = [
            'id' => $this->get_field_id('title'),
            'name' => $this->get_field_name('title'),
            'label_text' => 'Title',
            'value' => $title
        ];
        echo $this->mustache->render($template, $context);

        $context = [
            'id' => $this->get_field_id('feed'),
            'name' => $this->get_field_name('feed'),
            'label_text' => 'Foursquare RSS Feed',
            'value' => $feed
        ];
        echo $this->mustache->render($template, $context);

        $template = '<p><label for="{{id}}">{{label_text}}:</label>
                    <input id="{id}}" name="{{name}}" type="text" size="3" value="{{value}}"/></p>';

        $context = [
            'id' => $this->get_field_id('limit'),
            'name' => $this->get_field_name('limit'),
            'label_text' => 'Number of checkins to show',
            'value' => $limit
        ];
        echo $this->mustache->render($template, $context);
    }

    /**
     * Sanitize widget form values as they are saved.
     *
     * @see WP_Widget::update()
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     */
    public function update($new_instance, $old_instance) {
        $instance = [];
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['feed'] = (!empty($new_instance['feed'])) ? strip_tags($new_instance['feed']) : '';
        $instance['limit'] = (!empty($new_instance['limit'])) ? strip_tags($new_instance['limit']) : '';

        return $instance;
    }
}

add_action( 'widgets_init', function() {
    register_widget('Checkins_Widget');
});

