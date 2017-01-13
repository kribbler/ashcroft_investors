<?php
wp_register_script('jquery', ("http://code.jquery.com/jquery-latest.min.js"), false, '');
wp_enqueue_script('jquery');

wp_register_script('backstretch', (get_stylesheet_directory_uri() . "/js/jquery.backstretch.min.js"), false, '');
wp_enqueue_script('backstretch');

function child_ts_theme_widgets_init(){

    register_sidebar( array(
        'name' => __( 'Header Text', 'liva' ),
        'id' => 'header-text',
        'before_widget' => '<div id="%1$s" class="sidebar_widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<div class="sidebar_title"><h3>',
        'after_title' => '</h3></div>',
    ) );

    register_sidebar( array(
        'name' => __( 'Header Right', 'liva' ),
        'id' => 'header-right',
        'before_widget' => '<div id="%1$s" class="sidebar_widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<div class="sidebar_title"><h3>',
        'after_title' => '</h3></div>',
    ) );

	register_sidebar( array(
        'name' => __( 'Copyright area 1', 'liva' ),
        'id' => 'coyright-area-1',
        'before_widget' => '<div id="%1$s" class="sidebar_widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<div class="sidebar_title"><h3>',
        'after_title' => '</h3></div>',
    ) );
	
	register_sidebar( array(
        'name' => __( 'Copyright area 2', 'liva' ),
        'id' => 'coyright-area-2',
        'before_widget' => '<div id="%1$s" class="sidebar_widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<div class="sidebar_title"><h3>',
        'after_title' => '</h3></div>',
    ) );
	
    register_sidebar( array(
        'name' => __( 'Footer Above Home', 'liva' ),
        'id' => 'footer-above-home',
        'before_widget' => '<div id="%1$s" class="sidebar_widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<div class="sidebar_title"><h3>',
        'after_title' => '</h3></div>',
    ) );

    register_sidebar( array(
        'name' => __( 'Footer Above Inner', 'liva' ),
        'id' => 'footer-above-inner',
        'before_widget' => '<div id="%1$s" class="sidebar_widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<div class="sidebar_title"><h3>',
        'after_title' => '</h3></div>',
    ) );
}

add_action( 'widgets_init', 'child_ts_theme_widgets_init' );

add_shortcode( 'homepage_listings', 'show_featured_listings_homepage' );

function show_featured_listings_homepage($atts, $content = null) {
    extract(shortcode_atts(array(
        'id'       => '',
        'taxonomy' => '',
        'term'     => '',
        'limit'    => '',
        'columns'  => ''
    ), $atts ) );

    $limit = 8;

    $columns = 4;

    $query_args = array(
        'post_type'       => 'listing',
        'posts_per_page'  => $limit
    );

    if($term && $taxonomy) {
        $query_args = array(
            'post_type'       => 'listing',
            'posts_per_page'  => $limit,
            'tax_query'       => array(
                array(
                    'taxonomy' => $taxonomy,
                    'field'    => 'slug',
                    'terms'     => $term
                )
            )
        );
    }

    global $post;

    $listings_array = get_posts( $query_args );

    

    $output = '<div class="featured_listings">';
    $output .= '<div class="row">';

    $output .= '<div id="carousel_container">';
    $output .= '<div id="left_scroll"></div>';
    $output .= '<div id="carousel_inner">';
    $output .= '<ul id="carousel_ul">';

    $count = 0;
    $extra_info = array();

    foreach ( $listings_array as $post ) : setup_postdata( $post );
        $count++;

        $output .= '<li class="li_inactive" id="use_the_'.$count.'">';
        $output .= '<div class="span3 featured_h" id="featured_listing_'.$count.'">';
        $output .= '<a class="small_featured" href="javascript:void(0);"><img src="' . get_listing_thumb( $post->ID , 'homepage-featured-listing' ) . '" /></a>';
        //$output .= '<div class="small_featured">' . the_post_thumbnail('homepage-featured-listing', false) . '</div>';
        $output .= '<div class="listing_summary">';
            $output .= '<a class="featured_listing_title" href="' . get_permalink() . '">' . get_the_title() . '</a>';
            
            $output .= '<div class="featured_listing_bedrooms">' . get_post_meta( $post->ID, '_listing_bedrooms', true ) . '</div>';
            $output .= '<div class="featured_listing_bathrooms">' . get_post_meta( $post->ID, '_listing_bathrooms', true ) . '</div>';
            $output .= '<div class="clearfix"></div>';
            $output .= '<div class="featured_listing_price">$' . get_post_meta( $post->ID, '_listing_price', true ) . '</div>';
        $output .= '</div>';

        $output .= '</div>';

        $output .= '</li>';

        $extra_info[$count]['id'] = $count;
        $extra_info[$count]['specification'] = ( $post->post_excerpt ) ? $post->post_excerpt : $post->post_content;
        $extra_info[$count]['house_plan_image'] = get_master_plan($post->ID);
        $extra_info[$count]['video'] = get_post_meta( $post->ID, '_listing_video_home', true );

    endforeach;

    $output .= '</ul></div>'; //<div id='carousel_inner'>
    $output .= '<div id="right_scroll"></div>';
    $output .= '</div>'; //<div id='carousel_container'>

    $output .= '</div><!--row-->';

    $output .= '<div class="featured_listing_extend tri_1">';
    foreach ($extra_info as $info){
        $output .= '<div class="featured_listing_hidden_info" id="featured_hidden_'.$info['id'].'">';
            $output .= '<div class="row">';
            
                $output .= '<div class="span4">';
                $output .= '<h4>specification</h4>';
                $output .= $info['specification'];
                $output .= '</div><!--span4-->';
                
                $output .= '<div class="span4">';
                $output .= '<img src="'.$info['house_plan_image'].'" />';
                $output .= '</div><!--span4-->';

                $output .= '<div class="span4">';
                $output .= '<h4>Take a tour</h4>';
                $output .= $info['video'];
                $output .= '</div><!--span4-->';        

            $output .= '</div><!--row-->';
        $output .= '</div>';
    }
    $output .= '</div><!--featured_listing_extend-->';

    $output .= '</div>';

    $output .= "
    <script type='text/javascript'>
    jQuery(document).ready(function($) {
         
  });
    </script>";
    wp_reset_postdata();
    return $output;
}

//homepage_listings_tabbed
add_shortcode( 'homepage_listings_tabbed', 'show_tabbed_listings_homepage' );

function show_tabbed_listings_homepage($atts, $content = null) {
    extract(shortcode_atts(array(
        'id'       => '',
        'taxonomy' => '',
        'term'     => '',
        'limit'    => '',
        'columns'  => ''
    ), $atts ) );

    $limit = 5;

    $columns = 5;

    $query_args = array(
        'post_type'       => 'listing',
        'posts_per_page'  => $limit
    );

    $output = "";
    $output .= '<div id="tabbed_title">';
    $output .= '<span id="tab_1_title" class="tab_title visible_tab">NEW INVESTMENT PROPERTY FOR SALE</span>';
    $output .= '<span id="tab_2_title" class="tab_title">INVESTOR HOUSE & LAND PACKAGES</span>';
    $output .= '<span id="tab_3_title" class="tab_title">SUB-DIVIDING YOUR LAND FOR INVESTMENT</span>';
    $output .= '<span id="tab_4_title" class="tab_title">MULTI-SITE DEVELOPMENT</span>';
    $output .= '<span id="tab_5_title" class="tab_title">FIRST TIME INVESTOR?</span>';

    $output .= '</div><!-- tabbed_title -->';
    $taxonomy = "property-types";
    global $post;
    
    //get the 'Auckland New Homes For Sale' listings
    $term = "New Investment Property For Sale";

    if($term && $taxonomy) {
        $query_args = array(
            'post_type'       => 'listing',
            'posts_per_page'  => $limit,
            'tax_query'       => array(
                array(
                    'taxonomy' => $taxonomy,
                    'field'    => 'slug',
                    'terms'     => $term
                )
            )
        );
    }

    $listings_array = get_posts( $query_args );
    $output .= '<div id="tab_1" class="row listings_tab">';
    foreach ( $listings_array as $post ) : setup_postdata( $post );
        $output .= '<div class="span2 tabbed_span2">';
            $output .= '<div class="tab_listing_image">';
                //$output .= get_the_post_thumbnail( $post->ID, 'listings' );
                $output .= '<img src="' . get_listing_thumb( $post->ID , 'homepage-tabbed-listing' ) . '" />';
            $output .= '</div>';
            $output .= '<div class="tab_listing_city">';
            $output .= get_post_meta( $post->ID, '_listing_city', true );
            $output .= ' - $';
            $output .= get_post_meta( $post->ID, '_listing_price', true );
            $output .= '</div>';            
            $output .= '<div class="featured_listing_bedrooms">' . get_post_meta( $post->ID, '_listing_bedrooms', true ) . '</div>';
            $output .= '<div class="featured_listing_bathrooms">' . get_post_meta( $post->ID, '_listing_bathrooms', true ) . '</div>';
        $output .= '</div>';
    endforeach;

    $output .= '</div>';

    //get the 'House & Land Packages' listings
    $term = "Investor House & Land Packages";

    if($term && $taxonomy) {
        $query_args = array(
            'post_type'       => 'listing',
            'posts_per_page'  => $limit,
            'tax_query'       => array(
                array(
                    'taxonomy' => $taxonomy,
                    'field'    => 'slug',
                    'terms'     => $term
                )
            )
        );
    }

    $listings_array = get_posts( $query_args );
    $output .= '<div id="tab_2" class="row listings_tab">';
    foreach ( $listings_array as $post ) : setup_postdata( $post );
        $output .= '<div class="span2 tabbed_span2">';
            $output .= '<div class="tab_listing_image">';
                $output .= get_the_post_thumbnail( $post->ID, 'listings' );
            $output .= '</div>';
            $output .= '<div class="tab_listing_city">';
            $output .= get_post_meta( $post->ID, '_listing_city', true );
            $output .= ' - $';
            $output .= get_post_meta( $post->ID, '_listing_price', true );
            $output .= '</div>';            
            $output .= '<div class="featured_listing_bedrooms">' . get_post_meta( $post->ID, '_listing_bedrooms', true ) . '</div>';
            $output .= '<div class="featured_listing_bathrooms">' . get_post_meta( $post->ID, '_listing_bathrooms', true ) . '</div>';
        $output .= '</div>';
    endforeach;

    $output .= '</div>';

    //get the 'Available Soon' listings
    $term = "Sub Dividing Your Land for Investment";

    if($term && $taxonomy) {
        $query_args = array(
            'post_type'       => 'listing',
            'posts_per_page'  => $limit,
            'tax_query'       => array(
                array(
                    'taxonomy' => $taxonomy,
                    'field'    => 'slug',
                    'terms'     => $term
                )
            )
        );
    }

    $listings_array = get_posts( $query_args );
    $output .= '<div id="tab_3" class="row listings_tab">';
    foreach ( $listings_array as $post ) : setup_postdata( $post );
        $output .= '<div class="span2 tabbed_span2">';
            $output .= '<div class="tab_listing_image">';
                $output .= get_the_post_thumbnail( $post->ID, 'listings' );
            $output .= '</div>';
            $output .= '<div class="tab_listing_city">';
            $output .= get_post_meta( $post->ID, '_listing_city', true );
            $output .= ' - $';
            $output .= get_post_meta( $post->ID, '_listing_price', true );
            $output .= '</div>';            
            $output .= '<div class="featured_listing_bedrooms">' . get_post_meta( $post->ID, '_listing_bedrooms', true ) . '</div>';
            $output .= '<div class="featured_listing_bathrooms">' . get_post_meta( $post->ID, '_listing_bathrooms', true ) . '</div>';
        $output .= '</div>';
    endforeach;

    $output .= '</div>';


    //get the 'Available Soon' listings
    $term = "Multi-Site Development";

    if($term && $taxonomy) {
        $query_args = array(
            'post_type'       => 'listing',
            'posts_per_page'  => $limit,
            'tax_query'       => array(
                array(
                    'taxonomy' => $taxonomy,
                    'field'    => 'slug',
                    'terms'     => $term
                )
            )
        );
    }

    $listings_array = get_posts( $query_args );
    $output .= '<div id="tab_4" class="row listings_tab">';
    foreach ( $listings_array as $post ) : setup_postdata( $post );
        $output .= '<div class="span2 tabbed_span2">';
            $output .= '<div class="tab_listing_image">';
                $output .= get_the_post_thumbnail( $post->ID, 'listings' );
            $output .= '</div>';
            $output .= '<div class="tab_listing_city">';
            $output .= get_post_meta( $post->ID, '_listing_city', true );
            $output .= ' - $';
            $output .= get_post_meta( $post->ID, '_listing_price', true );
            $output .= '</div>';            
            $output .= '<div class="featured_listing_bedrooms">' . get_post_meta( $post->ID, '_listing_bedrooms', true ) . '</div>';
            $output .= '<div class="featured_listing_bathrooms">' . get_post_meta( $post->ID, '_listing_bathrooms', true ) . '</div>';
        $output .= '</div>';
    endforeach;

    $output .= '</div>';


    //get the 'Available Soon' listings
    $term = "First Time Investor?";

    if($term && $taxonomy) {
        $query_args = array(
            'post_type'       => 'listing',
            'posts_per_page'  => $limit,
            'tax_query'       => array(
                array(
                    'taxonomy' => $taxonomy,
                    'field'    => 'slug',
                    'terms'     => $term
                )
            )
        );
    }

    $listings_array = get_posts( $query_args );
    $output .= '<div id="tab_5" class="row listings_tab">';
    foreach ( $listings_array as $post ) : setup_postdata( $post );
        $output .= '<div class="span2 tabbed_span2">';
            $output .= '<div class="tab_listing_image">';
                $output .= get_the_post_thumbnail( $post->ID, 'listings' );
            $output .= '</div>';
            $output .= '<div class="tab_listing_city">';
            $output .= get_post_meta( $post->ID, '_listing_city', true );
            $output .= ' - $';
            $output .= get_post_meta( $post->ID, '_listing_price', true );
            $output .= '</div>';            
            $output .= '<div class="featured_listing_bedrooms">' . get_post_meta( $post->ID, '_listing_bedrooms', true ) . '</div>';
            $output .= '<div class="featured_listing_bathrooms">' . get_post_meta( $post->ID, '_listing_bathrooms', true ) . '</div>';
        $output .= '</div>';
    endforeach;

    $output .= '</div>';

    wp_reset_postdata();
    return $output;

    //echo "<pre>"; var_dump($listings_array); echo "</pre>";
}

function get_master_plan($id){
    $gallery = get_post_meta( $id, '_listing_gallery', false );
    //var_dump($gallery);
    $pattern ="/<img (.*?) src=\"(.*?)\" (.*?) \/>/s";
    preg_match_all(
        $pattern,
        $gallery[0],
        $matches,
        PREG_SET_ORDER
    );

    //echo "<pre>"; var_dump($matches);

    return $matches[0][2];
}

add_shortcode( 'show_7_processes', 'show_processes' );

function show_processes($atts, $content = null) {
    extract(shortcode_atts(array(
    ), $atts ) );

    $query_args = array(
        'post_type'       => 'post',
        'category_name'    => 'our-process',
        'posts_per_page'    => 7,
        'order'           => 'ASC'
    );

    $posts_array = get_posts( $query_args );

    $k = 1;
    $output = '<div id="our_process_graph">';
    foreach ( $posts_array as $post ) : setup_postdata( $post );
        $output .= '<div class="our_process_content" id="process_'.$k.'">';
        $output .= '<h5>' . get_the_title($post->ID) . '</h5>';
        $output .= apply_filters('the_content', get_post_field('post_content', $post->ID));
        $output .= '</div>';
        $k++;
    endforeach;
    $output .= '';
    $output .= '</div>';
    //get_bloginfo('stylesheet_directory');
    
    return $output;
}

function get_listing_thumb($post_id, $thumb){
    $url = wp_get_attachment_image_src( get_post_thumbnail_id($post_id), $thumb );
    //var_dump($url[0]);
    return $url[0];
}

add_image_size( 'homepage-featured-listing', 129, 84, true );
add_image_size( 'homepage-tabbed-listing', 210, 126, true );