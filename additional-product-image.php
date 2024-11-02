<?php
/*
Plugin Name: Additional Product Image
Description: Adds an additional image field in the WooCommerce product backend and displays it on the product page.
Version: 1.0
Author: Shoaib
Author URI: https://shoaibkhalid.ca
*/

if (!defined('ABSPATH')) exit; // Exit if accessed directly

// Enqueue necessary scripts and styles
add_action('admin_enqueue_scripts', 'enqueue_additional_product_image_uploader_scripts');
add_action('wp_enqueue_scripts', 'enqueue_frontend_scripts_styles');
function enqueue_additional_product_image_uploader_scripts() {
    wp_enqueue_media();
    wp_enqueue_script('admin-script', plugins_url('admin/script.js', __FILE__), array('jquery'), null, true);
    wp_enqueue_style('admin-styles', plugins_url('admin/style.css', __FILE__));
}

function enqueue_frontend_scripts_styles() {
    if (is_product()) {
        wp_enqueue_script('public-script', plugins_url('public/script.js', __FILE__), array('jquery'), null, true);
        wp_enqueue_style('public-styles', plugins_url('public/style.css', __FILE__));
    }
}



// Display additional image on the product page
add_action('woocommerce_single_product_summary', 'display_additional_product_image', 6);
function display_additional_product_image() {
    global $product;
    $additional_image_id = get_post_meta($product->get_id(), '_additional_product_image', true);
    $button_text = get_post_meta($product->get_id(), '_custom_view_image_text', true) ?: 'View Image'; // Default to 'View Image' if not set

    if ($additional_image_id) {
        $image_url = wp_get_attachment_url($additional_image_id);

        echo '<div class="additional-product-image" style="margin-top: 15px;">';
        echo '<button class="view-image-btn">' . esc_html($button_text) . '</button>';
        echo '<img class="additional-image" src="' . esc_url($image_url) . '" alt="Additional Product Image" />';
        echo '</div>';
    }
}


// Add image uploader below the product image in the backend
add_action('add_meta_boxes', 'add_additional_image_meta_box');
function add_additional_image_meta_box() {
    add_meta_box(
        'additional_product_image_uploader',
        __('Additional Product Image', 'woocommerce'),
        'additional_image_uploader_callback',
        'product',
        'side',
        'low'
    );
}

function additional_image_uploader_callback($post) {
    $additional_image_id = get_post_meta($post->ID, '_additional_product_image', true);
    $image_url = $additional_image_id ? wp_get_attachment_url($additional_image_id) : '';
    $button_text = get_post_meta($post->ID, '_custom_view_image_text', true) ?: 'View Image'; // Default text

    echo '<div class="options_group">';
    
    // Image uploader
    echo '<input type="hidden" id="_additional_product_image" name="_additional_product_image" value="' . esc_attr($additional_image_id) . '" />';
    echo '<p class="form-field">';
    echo '<img id="additional_product_image_preview" src="' . esc_url($image_url) . '" style="max-width: 100%; display: ' . ($image_url ? 'block' : 'none') . ';" />';
    echo '<button type="button" class="button" id="upload_additional_product_image">' . __('Upload Image', 'woocommerce') . '</button>';
    echo '<button type="button" class="button" id="remove_additional_product_image" style="display: ' . ($image_url ? 'inline-block' : 'none') . ';">' . __('Remove Image', 'woocommerce') . '</button>';
    echo '</p>';

    // Custom button text field
    echo '<p class="form-field">';
    echo '<label for="_custom_view_image_text">' . __('View Image Button Text', 'woocommerce') . '</label>';
    echo '<input type="text" id="_custom_view_image_text" name="_custom_view_image_text" value="' . esc_attr($button_text) . '" />';
    echo '</p>';

    echo '</div>';
}

// Save the image/text field
add_action('woocommerce_process_product_meta', 'save_additional_product_image_field');
function save_additional_product_image_field($post_id) {
    // Save the additional product image ID
    if (isset($_POST['_additional_product_image'])) {
        update_post_meta($post_id, '_additional_product_image', sanitize_text_field($_POST['_additional_product_image']));
    }

    // Save the custom view image button text
    if (isset($_POST['_custom_view_image_text'])) {
        update_post_meta($post_id, '_custom_view_image_text', sanitize_text_field($_POST['_custom_view_image_text']));
    }
}
