<?php

namespace Etn\Core\Event;

use Etn\Traits\Singleton;

defined( 'ABSPATH' ) || exit;

class Helper{

	use Singleton;

	/**
	* Return currency symbol
	*/
	public function get_currency() {
		$symbol = '';
		if( class_exists('Wpeventin_Pro') && class_exists('\Etn_Pro\Core\Modules\Sells_Engine\Sells_Engine') ) {
			$sells_engine = \Etn_Pro\Core\Modules\Sells_Engine\Sells_Engine::instance()->check_sells_engine();
			if ( 'woocommerce' == $sells_engine ) {
				$symbol = get_woocommerce_currency_symbol();
			}
			else if ( 'stripe' == $sells_engine ) {
				$get_data = \Etn_Pro\Utils\Helper::retrieve_country_currency();
				$symbol   = !empty( $get_data ) ? $get_data['currency'] : '';
			}
		}else{
			$symbol = get_woocommerce_currency_symbol();
		}

		return $symbol;
	}

	/**
	* Add recurring tag
	*/
	public function recurring_tag( $data ){
		if (( is_array($data) && count($data) > 0 )) {
			foreach($data as $index => $post){
				$post_id =	$post->ID;
				$is_recurring_parent = \Etn\Utils\Helper::get_child_events( $post_id );
				if( $is_recurring_parent ){
					$post->etn_recurring = true;
				}
			}
		}
		return $data;
	}

	public function get_event_location( $event_id ){
		$location = '';
		$location_type    = get_post_meta( $event_id, 'etn_event_location_type', true );
        if ( $location_type == 'existing_location' ) {
            $location     = get_post_meta($event_id, 'etn_event_location', true);
        } else {
            $location_arr = maybe_unserialize( get_post_meta($event_id, 'etn_event_location_list', true) );

            if ( !empty( $location_arr ) && is_array( $location_arr ) ) {
                $location_names = [];

                foreach( $location_arr as $index => $location_slug ) {
                    $location_details = get_term_by( 'slug',  $location_slug, 'etn_location' );
                    $location_names[] = $location_details->name;
                }

                $location = join( ', ', $location_names );
            }
        }

        return $location;
	}
}


