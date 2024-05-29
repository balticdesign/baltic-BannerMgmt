<?php

if( function_exists('acf_add_options_page') ) {

    acf_add_options_sub_page(array(
        'page_title' => 'Banner Control',
        'menu_title' => 'Banner Control',
        'parent_slug' => 'banner-monitor',
    ));

}

add_action( 'admin_menu', 'bd_top_lvl_menu' );
 
function bd_top_lvl_menu(){
 
	add_menu_page(
		'Banner Monitor', // page <title>Title</title>
		'Banner Monitor', // link text
		'manage_options', // user capabilities
		'banner-monitor', // page slug
		'bd_slider_page_callback', // this function prints the page content
		'dashicons-images-alt2', // icon (from Dashicons for example)
		4 // menu position
	);
    
}

function is_banner_expired($post_id,$bd_id) {

    $today = current_datetime()->format('d/m/Y g:i a');
	$expiry = get_field('banner_'.$bd_id.'_expiry', $post_id);
    if ($expiry) {
	$expired = ( $expiry > $today )  ? '' : '| <span style="color:red"><strong>Banner '.$bd_id.' Expired</strong> - '.$expiry.'</span>';
} else { $expired = '';} 
    return $expired;
}
 
function bd_slider_page_callback(){

echo '<h1>Active Banners</h1>';

$current_datetime = current_datetime()->format('d/m/Y g:i a');

echo 'Current Time: '.$current_datetime.'<br/><br/>';

echo '<h4>Header (Top) Banner</h4><br/>';

$tb_starts = get_field('start_date', 'options');
$tb_ends = get_field('end_date', 'options');

$tb_ends2 = get_field('end_date_2', 'options');
$tb_ends3 = get_field('end_date_3', 'options');
$tb_ends4 = get_field('end_date_4', 'options');

$extrabanners = array($tb_ends2,$tb_ends3,$tb_ends4);

$banerno = count(array_filter($extrabanners));

$text = get_field('top_banner_text', 'options');
	$text_color = get_field('text_color', 'options');
	$text_bkg = get_field('text_background_colour', 'options');
	$link_color = get_field('link_color', 'options');
	$text_transform = get_field('text_transform', 'options');
	$font_size = get_field('font_size', 'options');
	$html = '<style> .simple-banner a { color:'.$link_color.'; } .simple-banner p { margin:0px; } </style>';
	$html .= '<div class="simple-banner" style="padding:14px 0px; text-align:center; color:'.$text_color.'; background-color:'.$text_bkg.'; font-size:'.$font_size.'px; text-transform:'.$text_transform.';"><div class="simple-banner-text"><span><strong>';
	$html .= $text;
	$html .= '</strong></span></div></div><br/><br/>';
	
    
    if (($current_datetime >= $tb_starts) && ($current_datetime <= $tb_ends)) {
        echo 'Running from '.$tb_starts.' till '. $tb_ends.'<br/>';
    } elseif ($current_datetime <= $tb_starts) { echo 'Begins '.$tb_starts.'<br/>'; } else { echo 'Ends '.$tb_ends.'<br/>'; }
    echo $banerno.' extra banners are scheduled in sequence';
    echo $html;

echo '<br/><h4>Home Page Banner</h4><br/>';


	$image = get_field('home_banner_image', 'options');
	
	$hp_starts = get_field('home_banner_start_time', 'options');
	$hp_ends = get_field('home_banner_end_time', 'options');

    $hp_ends2 = get_field('home_banner_end_time_2', 'options');
    $hp_ends3 = get_field('home_banner_end_time_3', 'options');
    $hp_ends4 = get_field('home_banner_end_time_4', 'options');

$hpextrabanners = array($hp_ends2,$hp_ends3,$hp_ends4);

$hpbanerno = count(array_filter($hpextrabanners));

    if (($current_datetime >= $hp_starts) && ($current_datetime <= $hp_ends)) {
        echo 'Running from '.$hp_starts.' till '. $hp_ends.'<br/>';
    } elseif ($current_datetime <= $hp_starts) { echo 'Begins '.$hp_starts.' - '.$hpbanerno.' extra banners are scheduled in sequence <br/>'; } else { echo 'Ends '.$hp_ends.' - '.$hpbanerno.' extra banners are scheduled in sequence <br/>'; }
    
    echo '<img width="300px" src="'.esc_url($image['url']).'" /><br/><br/>';

echo '<br/><h4>Store Locations</h4><br/>';

$banner_image = get_field('banner_image', 'options');
$banner_start_time = get_field('banner_start_time', 'options');
$banner_end_time = get_field('banner_end_time', 'options');

$gl_ends2 = get_field('banner_end_time_2', 'options');
$gl_ends3 = get_field('banner_end_time_3', 'options');
$gl_ends4 = get_field('banner_end_time_4', 'options');

$glextrabanners = array($gl_ends2,$gl_ends3,$gl_ends4);

$glbanerno = count(array_filter($glextrabanners));

if (($current_datetime >= $banner_start_time) && ($current_datetime <= $banner_end_time)) {
    echo 'Global Banner Running from '.$banner_start_time.' till '. $banner_end_time.' - '.$glbanerno.' extra banners are scheduled in sequence <br/>';
} elseif ($current_datetime <= $banner_start_time) { echo 'Begins '.$banner_start_time.' - '.$glbanerno.' extra banners are scheduled in sequence <br/>'; } else { echo 'Ends '.$banner_end_time.' - '.$glbanerno.' extra banners are scheduled in sequence <br/>'; }

echo '<img width="500px" src="'.esc_url($banner_image['url']).'" /><br/><br/>';


$args = array(
    'post_type' => 'page',
    'meta_key' => 'banner_1_image',
    'meta_value' => '',
    'meta_compare' => '!=',
    'order' => 'DESC'
  );

echo '<br/><h4>Default Banners added to: </h4><br/>';

$my_query = new WP_Query( $args );
    if ($my_query->have_posts()) : while ( $my_query->have_posts() ) : $my_query->the_post(); 

    $post_id = get_the_ID(); // For example
$post_url = add_query_arg( array( 
	'post' => $post_id, 
	'action' => 'edit', 
), admin_url( 'post.php' ) );
    
    ?>

<div><a href="<?php echo $post_url; ?>"><?php the_title(); ?></a> <?php echo is_banner_expired($post_id,'1'); echo is_banner_expired($post_id,'2');  echo is_banner_expired($post_id,'3'); ?> </div>
  <?php  endwhile;
else:
        $string="No Banners Were Found";
    echo $string;
endif;

}