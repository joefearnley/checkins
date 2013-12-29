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

use Jcroll\FoursquareApiClient\Client\FoursquareClient;

class Checkins_Widget extends \WP_Widget {

    /**
     * @var Foursquare API client
     */
    private $foursqaure_client;

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
            'title' => __('Foursquare Checkins', 'text_domain')
        ];

        $this->foursquare_client = FoursquareClient::factory([
            'client_id' => CHECKINS_CLIENT_ID,
            'client_secret' => CHECKINS_CLIENT_SECRET
        ]);

        $this->foursquare_client->addToken(CHECKINS_AUTH_TOKEN);

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

        $title = $this->widget_options['title'];
        $custom_title = apply_filters('widget_title', $instance['title']);

        if(!empty($custom_title)) {
            $title = $custom_title;
        }

        echo $args['before_title'] . $title . $args['after_title'];
        echo '<ul>';

        try {
            $command = $this->foursquare_client->getCommand('users/checkins', [
                'user_id' => 'self',
                'limit' => '5'
            ]);

            $results = $command->execute();
            $checkins = $results['response']['checkins']['items'];

            foreach($checkins as $checkin) {
                $context = [
                    'id' => $checkin['id'],
                    'location' => $checkin['venue']['name'],
                    'date' => date('m/d', $checkin['createdAt'])
                ];

                $template = '<li><a href="http://foursquare.com/joefearnley/checkin/{{id}}">{{location}} on {{date}}</a></li>';
                echo $this->mustache->render($template, $context);
            }
        } catch (Exception $e) {
            echo $this->mustache->render('<li>Error Fetching Frousquare checkins - {{message}}</li>', ['message' => $e->getMessage()]);
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
        $email = $instance['email'];

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
            'id' => $this->get_field_id('email'),
            'name' => $this->get_field_name('email'),
            'label_text' => 'Foursquare Email'
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

        $title = $new_instance['title'];
        $email = $new_instance['email'];

        $instance = [];
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';

        // check for user name / id and store it....
        try {
            $command = $this->foursquare_client->getCommand('users/search', [
                'email' => $email
            ]);

            $results = $command->execute();
            $instance['user_id'] = $results['response']['results']['id'];
        } catch (Exception $e) {
            // not sure what the hell this function does, so not sure what he hell to do here...
            echo '<p>' . $e->getMessage() . '</p>';
            die();
        }
 
        return $instance;
    }
}

add_action( 'widgets_init', function() {
    register_widget('Checkins_Widget');
});

