## Checkins

Checkins ia a WordPress Widget that displays Foursquare checkins on your site.

## Dependencies
* PHP 5.3+
* [Composer](http://getcomposer.org/)

## Installation
1. Download the plugin 
2. unzip in the `%wordpress_home%/wp-content/plugins/` directory
3. Install dependencies using composer
    $ composer install
4. Launch WordPress installation
3. Activate in the admin area (`Plugins -> Installed Plugins`) by
   clicking `Activate` under **Checkins**

## Adding to wordpress site
# Get Foursquare activity feed
1. Log in to you Foursquare account through the website.
2. After logging in navagate to
   [https://foursquare.com/feeds/](https://foursquare.com/feeds/).
3. Under **Your Foursquare Feeds** copy the **RSS** link, you will need
   that in a minute.

# Add widget to site
1. In the WordPress admin area navigate to `Appearence -> Widgets`
2. Under **Available Widgets** drag **Checkins** over the **Main Widget
   Area** and click on it.
3. Fill in the three fields accordingly:
    1. Title - how you want the widget title to be displayed
    2. Rss Feed - the feed you copied from the Foursquare site
    3. Number of checkins to show - self explanitory

## Liscense
* [MIT](https://github.com/joefearnley/checkins/blob/master/LICENSE)
