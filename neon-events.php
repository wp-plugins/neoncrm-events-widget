<?php
/*
Plugin Name: NeonCRM Events Widget
Plugin URI: https://wordpress.org/plugins/neoncrm-events-widget/
Description: Retrieves a list of upcoming events from NeonCRM, and displays them as a widget.
Version: 0.11
Author: Colin Pizarek
Author URI: https://profiles.wordpress.org/colinpizarek/
License: GPL2
*/

/**
 * Include the NeonCRM PHP Library
 */
require_once('neon-api.php');

/**
 * Adds NeonCRM Event List widget
 */
class Neoncrm_Events extends WP_Widget {

	/** 
	 * Registers the widget
	 */
	function neoncrm_events() {
		parent::WP_Widget(false, $name = __('NeonCRM Upcoming Events', 'neoncrm_events_widget') );
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	function form( $instance ) {
		// Check for saved setting values
		if ( $instance ) {
			// Retrieve saved values for settings
			$title               = esc_attr( $instance['title'] );
			$api_key             = esc_attr( $instance['api_key'] );
			$org_id              = esc_attr( $instance['org_id'] );
			$per_page            = esc_attr( $instance['per_page'] );
			$event_name          = esc_attr( $instance['event_name'] );
			$event_start         = esc_attr( $instance['event_start'] );
			$event_end           = esc_attr( $instance['event_end'] );
			$event_start_time    = esc_attr( $instance['event_start_time'] );
			$event_end_time      = esc_attr( $instance['event_end_time'] );
			$event_location      = esc_attr( $instance['event_location'] );
			$event_register_link = esc_attr( $instance['event_register_link'] );
			$event_detail_link   = esc_attr( $instance['event_detail_link'] );
			$event_campaign      = esc_attr( $instance['event_campaign'] );
			$event_category      = esc_attr( $instance['event_category'] );
			 $cache_time         = esc_attr( $instance['cache_time'] );
		} else {
			// Use default values for settings
			$title               = 'Upcoming Events';
			$api_key             = '';
			$org_id              = '';
			$per_page            = 5;
			$event_name          = 1;
			$event_start         = 1;
			$event_end           = 0;
			$event_start_time    = 0;
			$event_end_time      = 0;
			$event_location      = 0;
			$event_register_link = 0;
			$event_detail_link   = 0;
			$event_campaign      = '';
			$event_category      = '';
			$cache_time          = 60;
		}
		
		// Check if cURL is enabled on the server
		if ( ! function_exists('curl_version') ) {
			echo '<p style="color: red">The cURL extension is disabled on your server. This plugin requires cURL to be enabled.</p>';
		}
		
		// Check if a compatible version of PHP is running
		if ( version_compare( phpversion(), '5.2.0', '<' ) ) {
			echo '<p style="color: red">This plugin requires PHP version 5.2.0 or higher. Your server is running version ' . phpversion() . '.</p>';
		}
	?>
		<strong>NeonCRM Credentials</strong>
		<p>
			<label for="<?php echo $this->get_field_id('org_id'); ?>"><?php _e('Organization ID', 'neoncrm_events_widget'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('org_id'); ?>" name="<?php echo $this->get_field_name('org_id'); ?>" type="text" value="<?php echo $org_id; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('api_key'); ?>"><?php _e('API Key', 'neoncrm_events_widget'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('api_key'); ?>" name="<?php echo $this->get_field_name('api_key'); ?>" type="text" value="<?php echo $api_key; ?>" />
		</p>
		<hr />
		<strong>Widget Settings</strong>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Widget Title', 'neoncrm_events_widget'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('per_page'); ?>"><?php _e('# of events to display:', 'neoncrm_events_widget'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('per_page'); ?>" name="<?php echo $this->get_field_name('per_page'); ?>" value="<?php echo $per_page; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('cache_time'); ?>"><?php _e('Refresh event list every # minutes:', 'neoncrm_events_widget'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('cache_time'); ?>" name="<?php echo $this->get_field_name('cache_time'); ?>" type="text" value="<?php echo $cache_time; ?>" />
		</p>
		<hr />
		<strong>Which fields do you want to display?</strong>
		<p>
			<input id="<?php echo $this->get_field_id('event_name'); ?>" name="<?php echo $this->get_field_name('event_name'); ?>" type="checkbox" value="1" <?php checked( '1', $event_name ); ?> />
			<label for="<?php echo $this->get_field_id('event_name'); ?>"><?php _e('Event Name', 'neoncrm_events_widget'); ?></label>
		</p>
		<p>
			<input id="<?php echo $this->get_field_id('event_start'); ?>" name="<?php echo $this->get_field_name('event_start'); ?>" type="checkbox" value="1" <?php checked( '1', $event_start ); ?> />
			<label for="<?php echo $this->get_field_id('event_start'); ?>"><?php _e('Start Date', 'neoncrm_events_widget'); ?></label>
		</p>
		<p>
			<input id="<?php echo $this->get_field_id('event_start_time'); ?>" name="<?php echo $this->get_field_name('event_start_time'); ?>" type="checkbox" value="1" <?php checked( '1', $event_start_time ); ?> />
			<label for="<?php echo $this->get_field_id('event_start_time'); ?>"><?php _e('Start Time', 'neoncrm_events_widget'); ?></label>
		</p>
		<p>
			<input id="<?php echo $this->get_field_id('event_end'); ?>" name="<?php echo $this->get_field_name('event_end'); ?>" type="checkbox" value="1" <?php checked( '1', $event_end ); ?> />
			<label for="<?php echo $this->get_field_id('event_end'); ?>"><?php _e('End Date', 'neoncrm_events_widget'); ?></label>
		</p>
		<p>
			<input id="<?php echo $this->get_field_id('event_end_time'); ?>" name="<?php echo $this->get_field_name('event_end_time'); ?>" type="checkbox" value="1" <?php checked( '1', $event_end_time ); ?> />
			<label for="<?php echo $this->get_field_id('event_end_time'); ?>"><?php _e('End Time', 'neoncrm_events_widget'); ?></label>
		</p>
		<p>
			<input id="<?php echo $this->get_field_id('event_location'); ?>" name="<?php echo $this->get_field_name('event_location'); ?>" type="checkbox" value="1" <?php checked( '1', $event_location ); ?> />
			<label for="<?php echo $this->get_field_id('event_location'); ?>"><?php _e('Location Name', 'neoncrm_events_widget'); ?></label>
		</p>
		<p>
			<input id="<?php echo $this->get_field_id('event_register_link'); ?>" name="<?php echo $this->get_field_name('event_register_link'); ?>" type="checkbox" value="1" <?php checked( '1', $event_register_link ); ?> />
			<label for="<?php echo $this->get_field_id('event_register_link'); ?>"><?php _e('Registration Link', 'neoncrm_events_widget'); ?></label>
		</p>
		<p>
			<input id="<?php echo $this->get_field_id('event_detail_link'); ?>" name="<?php echo $this->get_field_name('event_detail_link'); ?>" type="checkbox" value="1" <?php checked( '1', $event_detail_link ); ?> />
			<label for="<?php echo $this->get_field_id('event_detail_link'); ?>"><?php _e('Detail Link', 'neoncrm_events_widget'); ?></label>
		</p>
		<hr />
		<strong>Limit your events to a category or campaign.</strong>
		<p>
			<label for="<?php echo $this->get_field_id('event_campaign'); ?>"><?php _e('Campaign:', 'neoncrm_events_widget'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('event_campaign'); ?>" name="<?php echo $this->get_field_name('event_campaign'); ?>" type="text" value="<?php echo $event_campaign; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('event_category'); ?>"><?php _e('Category:', 'neoncrm_events_widget'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('event_category'); ?>" name="<?php echo $this->get_field_name('event_category'); ?>" type="text" value="<?php echo $event_category; ?>" />
		</p>
	<?php
	}
  
  /**
   * Clear cached version of the events list
   *
   */
    public function clear_neoncrm_cache($widget_id) {
        delete_transient($widget_id);
    }
	
  /**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 */
	function update( $new_instance, $old_instance ) {
    
    // Clear the cache when new settings are saved
    $this->clear_neoncrm_cache();
    
		$instance = $old_instance;
		// Fields
		$instance['title']               = strip_tags($new_instance['title']);
		$instance['api_key']             = strip_tags($new_instance['api_key']);
		$instance['org_id']              = strip_tags($new_instance['org_id']);
		$instance['per_page']            = strip_tags($new_instance['per_page']);
		$instance['event_name']          = strip_tags($new_instance['event_name']);
		$instance['event_start']         = strip_tags($new_instance['event_start']);
		$instance['event_end']           = strip_tags($new_instance['event_end']);
		$instance['event_end_time']      = strip_tags($new_instance['event_end_time']);
		$instance['event_start_time']    = strip_tags($new_instance['event_start_time']);
		$instance['event_location']      = strip_tags($new_instance['event_location']);
		$instance['event_register_link'] = strip_tags($new_instance['event_register_link']);
		$instance['event_detail_link']   = strip_tags($new_instance['event_detail_link']);
		$instance['event_campaign']      = strip_tags($new_instance['event_campaign']);
		$instance['event_category']      = strip_tags($new_instance['event_category']);
		$instance['cache_time']          = strip_tags($new_instance['cache_time']);
		return $instance;
		}
    

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	function widget( $args, $instance ) {
		extract( $args );
		// Define the widget's options
		$title               = apply_filters('widget_title', $instance['title']);
		$api_key             = $instance['api_key'];
		$org_id              = $instance['org_id'];
		$per_page            = $instance['per_page'];
		$event_name          = $instance['event_name'];
		$event_start         = $instance['event_start'];
		$event_end           = $instance['event_end'];
		$event_start_time    = $instance['event_start_time'];
		$event_end_time      = $instance['event_end_time'];
		$event_location      = $instance['event_location'];
		$event_register_link = $instance['event_register_link'];
		$event_detail_link   = $instance['event_detail_link'];
		$event_campaign      = $instance['event_campaign'];
		$event_category      = $instance['event_category'];
		$cache_time          = $instance['cache_time'];
		
		// Check for cached data
		$cached_events = get_transient( $this->id );
		//$cached_events = false; // Disables caching for testing purposes.
		
		// If the data isn't cached, go get it and then cache it.
		if ( $cached_events === false ) {
			
			// Instantiate the Neon class
			$neon = new Neon;
			
			// Check settings for API credentials
			if ( $api_key && $org_id ) {
				
				// Authenticate credentials with NeonCRM API
				$keys = array( 'orgId' => $org_id, 'apiKey' => $api_key );
				$authenticate = $neon->login( $keys );
				
				// Check for successful authentication with NeonCRM
				if ( $authenticate['operationResult'] == 'SUCCESS' ) {
				
					// Build the API search query
					$search = array();
					$search['method'] = 'event/listEvents';
					
					// Always include the Event Id and Start Date as columns
					$search['columns']['standardFields'][] = 'Event Start Date';
					$search['columns']['standardFields'][] = 'Event Id';
					
					// Check settings to include other columns
					if ( $event_name == 1 ) {
						$search['columns']['standardFields'][] = 'Event Name';
					}
					if ( $event_start == 1 ) {
						$search['columns']['standardFields'][] = 'Event Start Date';
					}
					if ( $event_end == 1 ) {
						$search['columns']['standardFields'][] = 'Event End Date';
					}
					if ( $event_start_time == 1 ) {
						$search['columns']['standardFields'][] = 'Event Start Time';
					}
					if ( $event_end_time == 1 ) {
						$search['columns']['standardFields'][] = 'Event End Time';
					}
					if ( $event_location == 1 ) {
						$search['columns']['standardFields'][] = 'Location';
					}
					
					// Always search by start date
					$search['criteria'][] = array( 'Event Start Date', 'GREATER_AND_EQUAL', date( 'm/d/Y' ) );
					
					// Check settings to include other search criteria
					if ( $event_campaign ) {
						$search['criteria'][] = array( 'Event Campaign', 'EQUAL', $event_campaign );
					}
					if ( $event_category ) {
						$search['criteria'][] = array( 'Event Category', 'EQUAL', $event_category );
					}
					
					// Check settings for pagination preference
					if ( $per_page ) {
						$search['page']['pageSize'] = $per_page;
					} else {
						// Use API's default page size
						$search['page']['pageSize'] = null;
					}
					
					// Always sort ascending by Event Start Date
					$search['page']['sortColumn'] = 'Event Start Date';
					$search['page']['sortDirection'] = 'ASC';
					
					// Execute the query
					$result = $neon->search( $search );
				} else {
					// If authentication fails, do not continue
					$result = null;
				}
				
				// If request is successful, parse the API server response
				if ( $result['operationResult'] == 'SUCCESS' && $result['page']['totalResults'] > 0 ) {
					
					// Wrap the widget
					$events_output = '<div class="widget-text neoncrm_events_widget_box">';
					
					// Check for title, display title
					if ( $title ) {
						$events_output .= $before_title . $title . $after_title;
					}
					
					// Iterate through search results
					foreach ( $result['searchResults'] as $key => $event ) {
						
						// Wrap each event in a div
						$events_output .= '<div class="neoncrm-event neoncrm-event-' . $event['Event Id'] . '">';
						
						// Display Event Name
						if ( isset( $event['Event Name'] ) ) {
							$events_output .= '<p class="neoncrm-event-name">' . $event['Event Name'] . '</p>';
						}
						
						// Reformat times and dates
						if ( isset( $event['Event Start Date'] ) ) { 
							$event['Event Start Date'] = date( 'm/d/Y', ( strtotime( $event['Event Start Date'] ) ) ); 
						}
						if ( isset( $event['Event End Date'] ) ) { 
							$event['Event End Date'] = date( 'm/d/Y', ( strtotime( $event['Event End Date'] ) ) ); 
						}
						if ( isset( $event['Event Start Time'] ) ) { 
							$event['Event Start Time'] = date( 'g:i a', ( strtotime( $event['Event Start Time'] ) ) ); 
						}
						if ( isset( $event['Event End Time'] ) ) { 
							$event['Event End Time'] = date( 'g:i a', ( strtotime( $event['Event End Time'] ) ) ); 
						}
						
						// Display Event Start/End Time/Date 
						if ( $event['Event Start Date'] || $event['Event Start Time'] || $event['Event End Date'] || $event['Event End Time'] ) {
							$events_output .= '<p class="neoncrm-event-time">';
						}
						if ( isset( $event['Event Start Date'] ) && isset( $event['Event Start Time'] ) ) {
							$events_output .= $event['Event Start Date'] . ' ' . $event['Event Start Time'];
						} else if ( isset( $event['Event Start Date'] ) && !isset( $event['Event Start Time'] ) ) {
							$events_output .= $event['Event Start Date'];
						} else if ( isset( $event['Event Start Time'] ) && !isset( $event['Event Start Date'] ) ) {
							$events_output .= $event['Event Start Time'];
						}
						if ( ( isset( $event['Event Start Date'] ) || isset( $event['Event Start Time'] ) ) && ( isset( $event['Event End Date'] ) || isset( $event['Event End Time'] ) ) ) {
							$events_output .= ' - ';
						}
						if ( isset( $event['Event End Date'] ) && isset( $event['Event End Time'] ) ) {
							$events_output .= $event['Event End Date'] . ' ' . $event['Event End Time'];
						} else if ( isset( $event['Event End Date'] ) && !isset( $event['Event End Time'] ) ) {
							$events_output .= $event['Event End Date'];
						} else if ( isset( $event['Event End Time'] ) && !isset( $event['Event End Date'] ) ) {
							$events_output .= $event['Event End Time'];
						}
						if ( isset( $event['Event Start Date'] ) || isset( $event['Event Start Time'] ) || isset( $event['Event End Date'] ) || isset( $event['Event End Time'] ) ) {
							$events_output .= '</p>';
						}
						
						// Display Event Location Name
						if ( isset( $event['Location'] ) ) {
							$events_output .= '<p class="neoncrm-event-location">' . $event['Location'] . '</p>';
						}
						
						// Display Register Link or Detail Link section
						if ( $event_register_link == 1 || $event_detail_link == 1 ) {
							$events_output .= '<p class="neoncrm-event-links">';
						}
						if ( $event_detail_link == 1 && isset( $event['Event Id'] ) ) {
							$events_output .= '<a href="https://' . $keys['orgId'] . '.z2systems.com/np/clients/' . $keys['orgId'] . '/event.jsp?event=' . $event['Event Id'] . '">Details</a>';
						}
						if ( $event_register_link == 1 && $event_detail_link == 1 ) {
							$events_output .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
						}
						if ( $event_register_link == 1 && isset( $event['Event Id'] ) ) {
							$events_output .= '<a href="https://' . $keys['orgId'] . '.z2systems.com/np/clients/' . $keys['orgId'] . '/eventRegistration.jsp?event=' . $event['Event Id'] . '">Register</a> ';
						}
						if ( $event_register_link == 1 || $event_detail_link == 1 ) {
							$events_output .= '</p>';
						}
						
						// Close the event wrapper
						$events_output .= '</div>';	
					}
					
          //$events_output .= '<pre>' . $this->id . '</pre>'; // For testing the transient cache
          
          // Close the widget wrapper
					$events_output .= '</div>';
					
					// Check for cache settings
					if ( $cache_time ) {
						$cache_time = $cache_time * 60;
					} else {
						// Default cache time is one hour
						$cache_time = 60 * 60;
					}
          
					// Save the output data to the transient cache
					set_transient( $this->id, $events_output, $cache_time );
					
					// Save the output to $cached_events
					$cached_events = $events_output;
				}
			}
		} 
		
		// Display $before_widget
		echo $before_widget;

		// Display widget content
		echo $cached_events;

		// Debugging - Displays the API server's response
		// echo '<pre>';
		// var_dump( $result );
		// echo '</pre>';
		
		// Display $after_widget
		echo $after_widget;
	}
}

// Register NeonCRM Events widget
add_action('widgets_init', create_function('', 'return register_widget("Neoncrm_Events");'));



  ?>