<?php
/*
Plugin Name: PageSpeed Tool
Description: Google PageSpeed Insights embeddable tool for WordPress.
Version: 1.0.0
Author: Gerardo Escalante ( escardante@gmail.com )
*/

define("TEXT_DOMAIN", "escardante-pagespeed-tool");

// Register Custom Post Type
function pagespeed_prospect_cpt() {

    $labels = array(
        'name'                  => _x( 'PageSpeed Client Prospects', 'Post Type General Name', TEXT_DOMAIN ),
        'singular_name'         => _x( 'PageSpeed Client Prospect', 'Post Type Singular Name', TEXT_DOMAIN ),
        'menu_name'             => __( 'PageSpeed Tool', TEXT_DOMAIN ),
        'name_admin_bar'        => __( 'PageSpeed Tool', TEXT_DOMAIN ),
        'archives'              => __( 'PageSpeed Client Prospects Archives', TEXT_DOMAIN ),
        'parent_item_colon'     => __( 'Parent Item:', TEXT_DOMAIN ),
        'all_items'             => __( 'Client Prospects', TEXT_DOMAIN ),
        'add_new_item'          => __( 'Add New Client Prospect', TEXT_DOMAIN ),
        'add_new'               => __( 'Add New', TEXT_DOMAIN ),
        'new_item'              => __( 'New Client Prospect', TEXT_DOMAIN ),
        'edit_item'             => __( 'Edit Client Prospect', TEXT_DOMAIN ),
        'update_item'           => __( 'Update Client Prospect', TEXT_DOMAIN ),
        'view_item'             => __( 'View Client Prospect', TEXT_DOMAIN ),
        'search_items'          => __( 'Search PageSpeed Client Prospects', TEXT_DOMAIN ),
        'not_found'             => __( 'Not found', TEXT_DOMAIN ),
        'not_found_in_trash'    => __( 'Not found in Trash', TEXT_DOMAIN ),
        'featured_image'        => __( 'Featured Image', TEXT_DOMAIN ),
        'set_featured_image'    => __( 'Set featured image', TEXT_DOMAIN ),
        'remove_featured_image' => __( 'Remove featured image', TEXT_DOMAIN ),
        'use_featured_image'    => __( 'Use as featured image', TEXT_DOMAIN ),
        'insert_into_item'      => __( 'Insert into item', TEXT_DOMAIN ),
        'uploaded_to_this_item' => __( 'Uploaded to this item', TEXT_DOMAIN ),
        'items_list'            => __( 'Items list', TEXT_DOMAIN ),
        'items_list_navigation' => __( 'Items list navigation', TEXT_DOMAIN ),
        'filter_items_list'     => __( 'Filter items list', TEXT_DOMAIN ),
    );
    $args = array(
        'label'                 => __( 'PageSpeed Client Prospect', TEXT_DOMAIN ),
        'description'           => __( 'PagesPeed Client Prospect', TEXT_DOMAIN ),
        'labels'                => $labels,
        'supports'              => array( 'custom-fields', ),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'menu_icon'             => 'dashicons-admin-users',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => false,
        'exclude_from_search'   => true,
        'publicly_queryable'    => true,
        'capability_type'       => 'page',
    );
    register_post_type( 'pagespeed_prospect', $args );

}

function get_permalink_by_slug( $slug, $post_type = '' ) {

    // Initialize the permalink value
    $permalink = null;

    // Build the arguments for WP_Query
    $args = array(
        'name'          => $slug,
        'max_num_posts' => 1
    );

    // If the optional argument is set, add it to the arguments array
    if( '' != $post_type ) {
        $args = array_merge( $args, array( 'post_type' => $post_type ) );
    }

    // Run the query (and reset it)
    $query = new WP_Query( $args );
    if( $query->have_posts() ) {
        $query->the_post();
        $permalink = get_permalink( get_the_ID() );
        wp_reset_postdata();
    }
    return $permalink;
}

// Add columns to table for custom post type
function filter_cpt_columns( $columns ) {
    $columns['escardante_pst_name'] = 'First Name';
    $columns['escardante_pst_last'] = 'Last Name';
    $columns['escardante_pst_email'] = 'Email';
    $columns['escardante_pst_website'] = 'Website URL';
    return $columns;
}

// Add actual content to CPT columns
function action_custom_columns_content ( $column_id, $post_id ) {

    switch( $column_id ) {
        case 'escardante_pst_name':
            echo ($value = get_post_meta($post_id, 'escardante_pst_name', true ) ) ? $value : 'No First Name Given';
            break;
        case 'escardante_pst_last':
            echo ($value = get_post_meta($post_id, 'escardante_pst_last', true ) ) ? $value : 'No Last Name Given';
            break;
        case 'escardante_pst_email':
            echo ($value = get_post_meta($post_id, 'escardante_pst_email', true ) ) ? $value : 'No Email Given';
            break;
        case 'escardante_pst_website':
            echo ($value = get_post_meta($post_id, 'escardante_pst_website', true ) ) ? $value : 'No Website Given';
            break;

    }
}

add_action( 'init', 'pagespeed_prospect_cpt', 0 );
add_filter('manage_pagespeed_prospect_posts_columns','filter_cpt_columns' );
add_action( 'manage_posts_custom_column','action_custom_columns_content', 10, 2 );

// Create the configuration page using our own fork of Rational Option Pages, with wp_editor fixed
require_once('lib/RationalOptionPages/RationalOptionPages.php');

// Get the current pages
$wp_pages = get_pages();
$pages_array = array('-1' => "Select an empty page where to render the form");
$pages_array2 = array('-1' => "Select an empty page where to render the results");
foreach ($wp_pages as $wp_page) {
    $pages_array[ $wp_page->post_name ] = $wp_page->post_title;
    $pages_array2[ $wp_page->post_name ] = $wp_page->post_title;
}

global $escardante_psoptions;
$escardante_psoptions = get_option( 'pagespeed-tool', array() );
$config = array(
    'pagespeed-tool'   => array(
        'page_title'    => __( 'Configuration', TEXT_DOMAIN ),
        'parent_slug'   => 'edit.php?post_type=pagespeed_prospect',
        'sections'      => array(
            'form'   => array(
                'title'         => __( 'Form Configuration', TEXT_DOMAIN ),
                'fields'        => array(
                    'form_page'        => array(
                        'title'         => __( 'Form Page', TEXT_DOMAIN ),
                        'type'          => 'select',
                        'choices'       => $pages_array
                    ),
                    'results_page'        => array(
                        'title'         => __( 'Results Page', TEXT_DOMAIN ),
                        'type'          => 'select',
                        'choices'       => $pages_array2
                    ),
                    'welcome_text'     => array(
                        'title'         => __( 'Form Welcome Text', TEXT_DOMAIN ),
                        'type'          => 'wp_editor',
                        'value'         => '<h1>Test Your Website</h1><p>Is your website running slow? Are your visitors getting the best viewability on mobile on their devices? Enter your information here and see how your site stacks up.</p><hr><p>Are you ready to make your website fast with an awesome user experience and fresh design across all devices? <a href="">Contact Us</a> today and we\'ll walk you through our process of getting you a new site.</p>',
                    ),
                    'button_text'       => array(
                        'title'         => __( 'Button Text', TEXT_DOMAIN ),
                        'type'          => 'default',
                        'value'     => 'Analyze Website >'
                    ),
                    'api_key'       => array(
                        'title'         => __( 'Api Key', TEXT_DOMAIN ),
                        'text'          => 'You need your own google api key here, please change this value for production. You can read about this at https://developers.google.com/speed/docs/insights/v2/first-app#auth',
                        'type'          => 'default',
                        'value'     => 'AIzaSyBHKi5qgGt7d1goVVup5v1S9Y3OyEsOd7E'
                    )
                ),
            ),
        ),
    ),
);

$option_page = new RationalOptionPages( $config );

if( isset( $escardante_psoptions['form_page'] ) ){
    function enqueue_escardante_pagespeed_form_scripts() {
        global $post;
        global $escardante_psoptions;
        if( $post->post_name == $escardante_psoptions['form_page'] ) {
            wp_enqueue_style('escardante_pagespeed_tool_form_style', plugin_dir_url(__FILE__) . 'form-template.css');
            wp_enqueue_script('escardante_pagespeed_tool_form_script', plugin_dir_url(__FILE__) . 'form-template.js', array('jquery'), '1.0.0', true);
            wp_localize_script( 'escardante_pagespeed_tool_form_script', 'vars', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );

        }
    }
    add_action( 'wp_enqueue_scripts', 'enqueue_escardante_pagespeed_form_scripts' );

    function escardante_pagespeed_form_the_content_filter($content) {
        global $post;
        global $escardante_psoptions;
        $results_template = "";
        if( $post->post_name == $escardante_psoptions['form_page'] ) {
            ob_start();
            include('form-template.php');
            $results_template = ob_get_contents();
            ob_end_clean ();
        }
        return $content . $results_template;
    }

    add_filter( 'the_content', 'escardante_pagespeed_form_the_content_filter' );
}


if( isset( $escardante_psoptions['results_page'] ) ){
    function enqueue_escardante_pagespeed_results_scripts() {
        global $post;
        global $escardante_psoptions;
        if( $post->post_name == $escardante_psoptions['results_page'] ) {
            wp_enqueue_style('escardante_pagespeed_tool_results_style', plugin_dir_url(__FILE__) . 'results-template.css');
            wp_enqueue_script('escardante_pagespeed_tool_results_script', plugin_dir_url(__FILE__) . 'results-template.js', array('jquery'), '1.0.0', true);
        }
    }
    add_action( 'wp_enqueue_scripts', 'enqueue_escardante_pagespeed_results_scripts' );

    function escardante_pagespeed_results_the_content_filter($content) {
        global $post;
        global $escardante_psoptions;
        $entry = filter_input(INPUT_GET, 'pagespeed_entry', FILTER_SANITIZE_NUMBER_INT);
        global $url_pst;
        $url_pst = get_post_meta( $entry, 'escardante_pst_website', true );

        $results_template = "";
        if( $post->post_name == $escardante_psoptions['results_page'] ) {
            ob_start();
            include('results-template.php');
            $results_template = ob_get_contents();
            ob_end_clean ();
        }
        return $content . $results_template;
    }

    add_filter( 'the_content', 'escardante_pagespeed_results_the_content_filter' );
}

function escardante_pagespeed_create_prospect(){

    global $escardante_psoptions;

    $data =  $_POST['data'];

    $post_id = wp_insert_post(array (
        'post_type' => 'pagespeed_prospect',
        'post_title' => $data['url'],
        'post_content' => '',
        'post_status' => 'publish'
    ));

    $response = array();

    if($post_id){
        add_post_meta($post_id, 'escardante_pst_name', $data['name']);
        add_post_meta($post_id, 'escardante_pst_last', $data['last']);
        add_post_meta($post_id, 'escardante_pst_email', $data['email']);
        add_post_meta($post_id, 'escardante_pst_website', $data['url']);
        $response["success"] = true;
        $response["entry"] = $post_id;
        $response["link"] = get_permalink_by_slug($escardante_psoptions['results_page'], 'page') . '?pagespeed_entry=' . $post_id; //get_permalink( get_page_by_path( $escardante_psoptions['results_page'] ) ) . '?pagespeed_entry=' . $post_id;
    } else {
        $response["success"] = false;
    }
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;


}
add_action( 'wp_ajax_escardante_pagespeed_create_prospect', 'escardante_pagespeed_create_prospect' );
add_action( 'wp_ajax_nopriv_escardante_pagespeed_create_prospect', 'escardante_pagespeed_create_prospect' );