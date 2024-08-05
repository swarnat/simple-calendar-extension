<?php
/*
Plugin Name: Simple Calendar Extensions
Version: 1.0
Author: Datentechnik Warnat
GitHub Plugin URI: https://github.com/swarnat/simple-calendar-extension
Author URI: https://www.datentechnik-warnat.de
*/

use DTW\GoogleCalendarEventsExtension\EventBuilder;
use SimpleCalendar\Calendars\Default_Calendar;

wp_enqueue_style('google-calendar-styles', plugins_url( 'assets/style.css', __FILE__ ), false);

add_action("simcal_calendar_get_view", function($view) {

});

add_action("init", function() {
	
	add_filter("simcal_event_tags_add_custom", function($tags) {
		$tags[] = "image_diashows";
		$tags[] = "eventbox_dates";
		$tags[] = "eventbox_location";
		$tags[] = "eventbox_time";
		
		return $tags;
	});
	
	add_filter("simcal_event_tags_do_custom", function($content, $tag, $partial, $attr, $event) {

		require_once(__DIR__ . DIRECTORY_SEPARATOR . "event-builder.php");
        $builder = new EventBuilder();
        $builder->setCalendar($event->calendar);
        
        return $builder->generate($tag, $event, $attr);
	}, 10, 5);
	
});

