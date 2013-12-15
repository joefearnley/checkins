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

require_once 'vendor/autoload.php';

use Jcroll\FoursquareApiClient\Client\FoursquareClient;

class Checkins_Widget extends \WP_Widget {

    public function __construct() {
        $args = [
            'description' => __('Display Foursquare Checkins', 'text_domain'),
            'title' => __('Foursquare Checkins', 'text_domain')
        ];

        parent::__construct('checkins', __('Checkins', 'text_domain'), $args);
	}

    public function widget($args, $instance) {
        include 'checkins-config.php';

        echo $args['before_widget'];
        echo $args['before_title'] . $this->widget_options['title'] . $args['after_title'];

        $client = FoursquareClient::factory([
            'client_id' => $checkins_config['client_id'],
            'client_secret' => $checkins_config['client_secret']
        ]);

        $client->addToken($checkins_config['auth_token']);

        $command = $client->getCommand('users/checkins', [
            'user_id' => 'self',
            'limit' => '5'
        ]);

        $results = $command->execute();

        var_dump($resutls);
        die();

        $checkins = $results['response']['checkins']['items'];

        echo '<ul>';
        foreach($checkins as $checkin) {
            $day = date('m/d/Y', $checkin['createdAt']);
            $time = date('h:i:s a', $checkin['createdAt']);
            $location = $checkin['venue']['name'];

            echo '<li>'.$location.' on '.$day.' at '.$time.'</li>';
        }
        echo '</ul>';

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

