<?php 


function bd_get_timestamp($field_name) {
    $time_string = get_field($field_name, 'options');
	if (empty($time_string)) { $time_string = get_sub_field($field_name); }
    //echo "Received date string for $field_name: $time_string\n"; // Debug output
    $date_format = 'Y-m-d H:i:s';
    $time_datetime = DateTime::createFromFormat($date_format, $time_string);
    if ($time_datetime === false) {
        $error = DateTime::getLastErrors();
        //echo "Error parsing date for $field_name: ";
        //print_r($error);
        return false;
    } else {
        $formatted_date = $time_datetime->format('Y-m-d H:i:s');
        $timestamp = strtotime($formatted_date);
        //echo "Converted timestamp for $field_name: $timestamp\n"; // Debug output
        return $timestamp;
    }
}

function baltic_carousel_shortcode() {
    // Check if ACF is active
    if (function_exists('have_rows')) {

        $current_time = strtotime(current_datetime()->format('Y-m-d H:i:s')); // Get the current WordPress time
        $display_default_carousel = true; // Flag to track which carousel to display

        // Check for 'repeater carousel' items
        if (have_rows('add_a_carousel', 'options')) {
            while (have_rows('add_a_carousel', 'options')) {
                the_row();

                $include_on_store_pages = get_sub_field('include_on_store_pages');
				$include_on_home_page = get_sub_field('include_on_home_page');
				$selected_page = get_field('target_a_specific_location', 'option');
                $cs_start_date = bd_get_timestamp('cs_start_date'); // Assuming this function is defined and working
                $cs_end_date = bd_get_timestamp('cs_end_date');
                $store_page_filter = get_sub_field('store_page_filter');
				//$carousel_html = 'Current Time: '.$current_time.'Start: '.$cs_start_date.'End: '.$cs_end_date;
                // Check conditions for homepage and 'rank_math_locations' post type
                 if ($current_time >= $cs_start_date && $current_time <= $cs_end_date) {
                    if (((is_front_page()) && $include_on_home_page) || (is_singular('rank_math_locations') && is_page($selected_page)) || (is_singular('rank_math_locations') && has_term($store_page_filter, 'post_tag') && $include_on_store_pages )) {
                        $display_default_carousel = false; // Found a valid 'repeater carousel'

                        // Initialize repeater carousel HTML
						if(is_singular('rank_math_locations')) {
							$carousel_html = '<h1 class="float-h1">' . get_the_title() . '</h1>';
						}
                    $carousel_html .= '<div class="carousel slider">';

                    // Loop through panels in the 'repeater carousel'
                    if (have_rows('add_a_panel')) {
                        while (have_rows('add_a_panel')) {
                            the_row();

                            // Get panel image data
                            $mobile_image = get_sub_field('mobile_image');
                            $landscape_image = get_sub_field('landscape_image');
                            $image_link = get_sub_field('image_link');

                            // Panel HTML
                            $carousel_html .= '<div>';
                            $carousel_html .= $image_link ? '<a href="' . esc_url($image_link) . '">' : '';
                            $carousel_html .= '<picture>';
                            $carousel_html .= $mobile_image ? '<source media="(max-width: 768px)" srcset="' . esc_url($mobile_image['url']) . '">' : '';
                            $carousel_html .= $landscape_image ? '<img src="' . esc_url($landscape_image['url']) . '" alt="' . esc_attr($landscape_image['alt']) . '">' : '';
                            $carousel_html .= '</picture>';
                            $carousel_html .= $image_link ? '</a>' : '';
                            $carousel_html .= '</div>';
                        }
                    }

                    $carousel_html .= '</div>';

                        // Return the repeater carousel HTML
                        return $carousel_html;
                    }
                }
            }
        }

        // Default carousel logic (same as your existing code)
        if ($display_default_carousel) {
        // Initialize carousel HTML
        $carousel_html = '<div class="carousel slider">';

        // Loop through carousel items
        if (have_rows('default_carousel', 'options')) {
            while (have_rows('default_carousel', 'options')) {
                the_row();

                // Get image data
                $mobile_image = get_sub_field('def_mobile_image');
                $landscape_image = get_sub_field('def_landscape_image');
                $image_link = get_sub_field('def_image_link');

                // Carousel item HTML
                $carousel_html .= '<div>';
                $carousel_html .= $image_link ? '<a href="' . esc_url($image_link) . '">' : '';
                $carousel_html .= '<picture>';
                $carousel_html .= $mobile_image ? '<source media="(max-width: 768px)" srcset="' . esc_url($mobile_image['url']) . '">' : '';
                $carousel_html .= $landscape_image ? '<img src="' . esc_url($landscape_image['url']) . '" alt="' . esc_attr($landscape_image['alt']) . '">' : '';
                $carousel_html .= '</picture>';
                $carousel_html .= $image_link ? '</a>' : '';
                $carousel_html .= '</div>';
            }
        }

        $carousel_html .= '</div>';

        // Return carousel HTML
        return $carousel_html;
    }
}

// Return empty string if ACF not active or no carousel is found
return '';
}
add_shortcode('bd_carousel', 'baltic_carousel_shortcode');

function bd_show_banner($atts = [], $content = null, $tag = '' ) {
	$bd_atts = shortcode_atts(
		array(
			'id' => '1',
		), $atts, $tag
	);
	$image = get_field('banner_'.$bd_atts['id'].'_image');
	$link = get_field('banner_'.$bd_atts['id'].'_link'); 
	$today = current_datetime()->format('d/m/Y H:i');
	$expiry = get_field('banner_'.$bd_atts['id'].'_expiry');
	$expired = ( $expiry > $today   )  ? 'false' : 'true';
	if(( !empty( $image ) ) && ($expired == 'false')) {
	$html = '<a href="'.esc_attr($link).'">';
    $html .= '<img src="'.esc_url($image['url']).'" alt="'.esc_attr($image['alt']).'" />';
	$html .= '</a>';
	} else { $html = '<p>Banner Expired</p>'; }
	return $html;
} 
add_shortcode ('bd_show_banner', 'bd_show_banner' ); 
	
function bd_simple_banner() {
	$current_datetime = strtotime(current_datetime()->format('Y-m-d H:i:s'));
	$text = get_field('top_banner_text', 'options');
	$text_color = get_field('text_color', 'options');
	$text_bkg = get_field('text_background_colour', 'options');
	$link_color = get_field('link_color', 'options');
	$text_transform = get_field('text_transform', 'options');
	$font_size = get_field('font_size', 'options');
	$starts = bd_get_timestamp('start_date');
	$ends = bd_get_timestamp('end_date');

	$text2 = get_field('top_banner_text_2', 'options');
	$text_color2 = get_field('text_color_2', 'options');
	$text_bkg2 = get_field('text_background_colour_2', 'options');
	$link_color2 = get_field('link_color_2', 'options');
	$text_transform2 = get_field('text_transform_2', 'options');
	$ends2 = bd_get_timestamp('end_date_2');

	$text3 = get_field('top_banner_text_3', 'options');
	$text_color3 = get_field('text_color_3', 'options');
	$text_bkg3 = get_field('text_background_colour_3', 'options');
	$link_color3 = get_field('link_color_3', 'options');
	$text_transform3 = get_field('text_transform_3', 'options');
	$ends3 = bd_get_timestamp('end_date_3');

	$text4 = get_field('top_banner_text_4', 'options');
	$text_color4 = get_field('text_color_4', 'options');
	$text_bkg4 = get_field('text_background_colour_4', 'options');
	$link_color4 = get_field('link_color_4', 'options');
	$text_transform4 = get_field('text_transform_4', 'options');
	$ends4 = bd_get_timestamp('end_date_4');


	if (($current_datetime >= $starts) && ($current_datetime <= $ends)) {
	$html = '<style> .simple-banner a { color:'.$link_color.'; } </style>';
	$html .= '<div class="simple-banner" style="padding:14px 0px; text-align:center; color:'.$text_color.'; background-color:'.$text_bkg.'; font-size:'.$font_size.'px; text-transform:'.$text_transform.';"><div class="simple-banner-text"><span><strong>';
	$html .= $text;
	$html .= '</strong></span></div></div>';
	} 

	elseif (($current_datetime >= $ends) && ($current_datetime <= $ends2)) {	
		
	$html = '<style> .simple-banner a { color:'.$link_color2.'; } </style>';
	$html .= '<div class="simple-banner" style="padding:14px 0px; text-align:center; color:'.$text_color2.'; background-color:'.$text_bkg2.'; font-size:'.$font_size.'px; text-transform:'.$text_transform2.';"><div class="simple-banner-text"><span><strong>';
	$html .= $text2;
	$html .= '</strong></span></div></div>';

		}
		
	elseif (($current_datetime >= $ends2) && ($current_datetime <= $ends3)) {	
		
			$html = '<style> .simple-banner a { color:'.$link_color3.'; } </style>';
			$html .= '<div class="simple-banner" style="padding:14px 0px; text-align:center; color:'.$text_color3.'; background-color:'.$text_bkg3.'; font-size:'.$font_size.'px; text-transform:'.$text_transform3.';"><div class="simple-banner-text"><span><strong>';
			$html .= $text3;
			$html .= '</strong></span></div></div>';
		
				}
				
	elseif (($current_datetime >= $ends3) && ($current_datetime <= $ends4)) {	
		
			$html = '<style> .simple-banner a { color:'.$link_color4.'; } </style>';
			$html .= '<div class="simple-banner" style="padding:14px 0px; text-align:center; color:'.$text_color4.'; background-color:'.$text_bkg4.'; font-size:'.$font_size.'px; text-transform:'.$text_transform4.';"><div class="simple-banner-text"><span><strong>';
			$html .= $text4;
			$html .= '</strong></span></div></div>';
				
						} else { 
							$start_date_string = get_field('start_date', 'options');
$manual_start_date = DateTime::createFromFormat('d/m/Y H:i', $start_date_string);

							
							$html = ''; } //'Manual Start Timestamp: ' . date('d/m/Y H:i', $manual_start_date->getTimestamp()) . 'Current: ' . date('d/m/Y H:i', $current_datetime) . ' - Starts: ' . date('d/m/Y H:i', $starts) . ' - Ends: ' . date('d/m/Y H:i', $ends) . 'Start Date String: ' . get_field('start_date', 'options') . 'End Date String: ' . get_field('end_date', 'options'); 
		
		if(!get_field('hide_all_top', 'options')) {					
	return $html;
		}
	} 
add_shortcode ('bd_simple_banner', 'bd_simple_banner' ); 

function bd_homepage_banner() {
	$current_datetime = strtotime(current_datetime()->format('Y-m-d H:i:s'));
	$image = get_field('home_banner_image', 'options');
	$link = get_field('home_banner_link', 'options');
	$alttext = get_field('home_banner_alt_text', 'options');
	$button_text = get_field('home_button_text', 'options');
	$starts = bd_get_timestamp('home_banner_start_time');
	$ends = bd_get_timestamp('home_banner_end_time');

	$image2 = get_field('home_banner_image_2', 'options');
	$link2 = get_field('home_banner_link_2', 'options');
	$alttext2 = get_field('home_banner_alt_text_2', 'options');
	$button_text2 = get_field('home_button_text_2', 'options');
	$ends2 = bd_get_timestamp('home_banner_end_time_2');

	$image3 = get_field('home_banner_image_3', 'options');
	$link3 = get_field('home_banner_link_3', 'options');
	$alttext3 = get_field('home_banner_alt_text_3', 'options');
	$button_text3 = get_field('home_button_text_3', 'options');
	$ends3 = bd_get_timestamp('home_banner_end_time_3');

	$image4 = get_field('home_banner_image_4', 'options');
	$link4 = get_field('home_banner_link_4', 'options');
	$alttext4 = get_field('home_banner_alt_text_4', 'options');
	$button_text4 = get_field('home_button_text_4', 'options');
	$ends4 = bd_get_timestamp('home_banner_end_time_4');

if (!get_field('show_buy_button', 'options')) { $button_css="style='display:none;'"; } else { $button_css=""; }
	
	if ((($current_datetime >= $starts) && ($current_datetime <= $ends))) {
	$html = '<a href="'.esc_attr($link).'">';
    $html .= '<img width="400px" src="'.esc_url($image['url']).'" alt="'.$alttext.'" title="'.$alttext.'" />';
	$html .= '</a><br/><br/>';
	$html .= '<a '.$button_css.' class="et_pb_button et_pb_button_1 et_pb_bg_layout_light" href="'.esc_attr($link).'" data-icon="$">'.$button_text.'</a>';
	} 
	elseif (( !empty( $image2 ) ) && ($current_datetime >= $ends) && ($current_datetime <= $ends2)) {

	$html = '<a href="'.esc_attr($link2).'">';
    $html .= '<img width="400px" src="'.esc_url($image2['url']).'" alt="'.$alttext2.'" title="'.$alttext2.'" />';
	$html .= '</a><br/><br/>';
	$html .= '<a '.$button_css.' class="et_pb_button et_pb_button_1 et_pb_bg_layout_light" href="'.esc_attr($link2).'" data-icon="$">'.$button_text2.'</a>';

	}

	elseif (( !empty( $image3 ) ) &&($current_datetime >= $ends2) && ($current_datetime <= $ends3)) {

		$html = '<a href="'.esc_attr($link3).'">';
		$html .= '<img width="400px" src="'.esc_url($image3['url']).'" alt="'.$alttext3.'" title="'.$alttext3.'" />';
		$html .= '</a><br/><br/>';
		$html .= '<a '.$button_css.' class="et_pb_button et_pb_button_1 et_pb_bg_layout_light" href="'.esc_attr($link3).'" data-icon="$">'.$button_text3.'</a>';
	
		}
		
	elseif (( !empty( $image4 ) ) && ($current_datetime >= $ends3) && ($current_datetime <= $ends4)) {

		$html = '<a href="'.esc_attr($link4).'">';
		$html .= '<img width="400px" src="'.esc_url($image4['url']).'" alt="'.$alttext4.'" title="'.$alttext4.'" />';
		$html .= '</a><br/><br/>';
		$html .= '<a '.$button_css.' class="et_pb_button et_pb_button_1 et_pb_bg_layout_light" href="'.esc_attr($link4).'" data-icon="$">'.$button_text4.'</a>';
		
			}
	
	else { $html = ''; } // 'Current: ' . date('Y-m-d H:i:s', $current_datetime) . ' - Starts: ' . date('Y-m-d H:i:s', $starts) . ' - Ends: ' . date('Y-m-d H:i:s', $ends); 
	
	if(!get_field('hide_home_banners', 'options')) {			
	return $html;
	}
}
add_shortcode ('bd_homepage_banner', 'bd_homepage_banner' ); 

function dynamic_page_banner() {
	$local_overide = get_field('local_overide');
	$current_datetime = strtotime(current_datetime()->format('Y-m-d H:i:s'));
	$starts = bd_get_timestamp('banner_start_time');
	$ends  = bd_get_timestamp('banner_end_time');
	$ends2  = bd_get_timestamp('banner_end_time_2');
	$ends3  = bd_get_timestamp('banner_end_time_3');
	$ends4  = bd_get_timestamp('banner_end_time_4');

	if ($local_overide) { 
		$image = get_field('banner_1_image');
		$video = get_field('banner_1_video');
		$link = get_field('banner_1_link');  }

	elseif (($current_datetime >= $starts) && ($current_datetime <= $ends)) {
		$image = get_field('banner_image', 'options');
		$video = get_field('banner_video', 'options');
		$link = get_field('banner_link', 'options');	
	} 
	elseif (($current_datetime >= $ends) && ($current_datetime <= $ends2)) {
		$image = get_field('banner_image_2', 'options');
		$video = get_field('banner_video_2', 'options');
		$link = get_field('banner_link_2', 'options');	
	}

	elseif (($current_datetime >= $ends2) && ($current_datetime <= $ends3)) {
		$image = get_field('banner_image_3', 'options');
		$video = get_field('banner_video_3', 'options');
		$link = get_field('banner_link_3', 'options');	
	}

	elseif (($current_datetime >= $ends3) && ($current_datetime <= $ends4)) {
		$image = get_field('banner_image_4', 'options');
		$video = get_field('banner_video_4', 'options');
		$link = get_field('banner_link_4', 'options');	
	}
	
	else { 
	$image = get_field('banner_1_image');
	$video = get_field('banner_1_video');
	$link = get_field('banner_1_link');  }
	if( !empty( $video ) ) {
		$html = '<a href="'.esc_attr($link).'">';
    $html .= '<video autoplay loop muted playsinline class="test" src="'.esc_url($video).'" alt="'.esc_attr($image['alt']).'" />';
	$html .= '</a>';
	} elseif( !empty( $image ) ) {
	$html = '<a href="'.esc_attr($link).'">';
    $html .= '<img class="test" src="'.esc_url($image['url']).'" alt="'.esc_attr($image['alt']).'" />';
	$html .= '</a>';
	} else { $html = ''; }
	return $html;
} 
add_shortcode ('dynamic_page_banner', 'dynamic_page_banner' );