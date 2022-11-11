<?php
/**
 * Woo Discount
 *
 * @package       WOODISCOUN
 * @author        Savji Rathod
 * @version       1.0
 *
 * @wordpress-plugin
 * Plugin Name:   Woo Discount
 * Plugin URI:    savajirathod.com
 * Description:   This Plugin is practical test
 * Version:       1.0
 * Author:        Savji Rathod
 * Author URI:    savajirathod.com
 * Text Domain:   woo-discount
 * Domain Path:   /languages
 */

// Exit if accessed directly.

// Add the tab to the tabs array
function filter_woocommerce_settings_tabs_array( $settings_tabs ) {
    $settings_tabs['my-discount-tab'] = __( 'Discount', 'woocommerce' );

    return $settings_tabs;
}
add_filter( 'woocommerce_settings_tabs_array', 'filter_woocommerce_settings_tabs_array', 99 );

// Add new sections to the page
add_action( 'woocommerce_sections_my-discount-tab', 'action_woocommerce_sections_my_discount_tab', 10 );
function action_woocommerce_sections_my_discount_tab() {
    global $current_section;
    $tab_id = 'my-discount-tab';
    // Must contain more than one section to display the links
    // Make first element's key empty ('')
    $sections = array(
        'new-discount'              => __( 'Add New Discount', 'woocommerce' ),
        'discount-list'              => __( 'Discount Listing', 'woocommerce' )
    );
    echo '<ul class="subsubsub">';
    $array_keys = array_keys( $sections );
    foreach ( $sections as $id => $label ) {
        echo '<li><a href="' . admin_url( 'admin.php?page=wc-settings&tab=' . $tab_id . '&section=' . sanitize_title( $id ) ) . '" class="' . ( $current_section == $id ? 'current' : '' ) . '">' . $label . '</a> ' . ( end( $array_keys ) == $id ? '' : '|' ) . ' </li>';
    }
    echo '</ul><br class="clear" />';
}


// Settings function
function get_discount_settings() {
    global $current_section;
        global $woocommerce, $post;
        if ( $current_section == 'new-discount' ) {
            $args = [
            'status'    => 'publish',
            'orderby' => 'name',
            'order'   => 'ASC',
            'limit' => -1,
        ];
        $all_products = wc_get_products($args);
        $products =array();
        foreach ($all_products as $key => $product) {
            $products[$product->id] =  $product->get_title();
        }

    $settings = array();
           $settings = array(
            // Title
            array(
                'title'     => __( 'Discount Section', 'woocommerce' ),
                'type'      => 'title',
                'id'        => 'custom_settings_1'
            ),
            // Select
            array(
                'title'     => __( 'Product', 'woocommerce' ),
                'desc'      => __( 'Your Product', 'woocommerce' ),
                'id'        => 'product',
                'class'     => 'wc-enhanced-select',
                'css'       => 'min-width:300px;',
                'default'   => '',
                'type'      => 'select',
                'options'   => $products,
                'desc_tip' => true,
            ),
            // Select
            array(
                'title'     => __( 'Discount Type', 'woocommerce' ),
                'desc'      => __( '', 'woocommerce' ),
                'id'        => 'discount_type',
                'class'     => 'wc-enhanced-select',
                'css'       => 'min-width:300px;',
                'default'   => '',
                'type'      => 'select',
                'options'   => array(
                    ''        => __( 'Select Discount Type', 'woocommerce' ),
                    'fixed'        => __( 'Fixed', 'woocommerce' ),
                    'percentage'        => __( 'Percentage', 'woocommerce' )
                ),
                'desc_tip' => true,
            ),
            // Text
            array(
                'title'     => __( 'Discount', 'text-domain' ),
                'type'      => 'text',
                'desc'      => __( 'Text field to enter a discount value (fixed amount or Percentage)', 'woocommerce' ),
                'desc_tip'  => true,
                'id'        => 'discount',
                'css'       => 'min-width:300px;'
            ),
            array(
                'type'      => 'sectionend',
                'id'        => 'custom_settings_1'
            ),
        );
           return $settings;
    }else{

        global $wpdb;

    // Styling the table a bit
    echo '<style> table.user-data th { font-weight: bold; } table.user-data, th, td { border: solid 1px #999; } </style>';

    $table_display = '<table class="user-data" cellspacing="0" cellpadding="6"><thead><tr>
    <th>'. __( 'ID', 'woocommerce' ) .'</th>
    <th>'. __( 'Product Name', 'woocommerce' ) .'</th>
    <th>'. __( 'Discount Type', 'woocommerce' ) .'</th>
    <th>'. __( 'Discount', 'woocommerce' ) .'</th>
    <th>'. __( 'Action', 'woocommerce' ) .'</th>
    </tr></thead>
    <tbody>';

    // Loop through customers
    foreach ( get_users( 'orderby=nicename&role=customer' ) as $key => $customer ) {
        // Customer total purchased
        $total_purchased = (float) $wpdb->get_var( "
            SELECT SUM(pm.meta_value) FROM {$wpdb->prefix}postmeta as pm
            INNER JOIN {$wpdb->prefix}posts as p ON pm.post_id = p.ID
            INNER JOIN {$wpdb->prefix}postmeta as pm2 ON pm.post_id = pm2.post_id
            WHERE p.post_status = 'wc-completed' AND p.post_type = 'shop_order'
            AND pm.meta_key = '_order_total' AND pm2.meta_key = '_customer_user'
            AND pm2.meta_value = {$customer->ID}
        " );
        // Customer orders count
        $orders_count = (int) $wpdb->get_var( "
            SELECT DISTINCT COUNT(p.ID) FROM {$wpdb->prefix}posts as p
            INNER JOIN {$wpdb->prefix}postmeta as pm ON p.ID = pm.post_id
            WHERE p.post_type = 'shop_order' AND pm.meta_key = '_customer_user'
            AND pm.meta_value = {$customer->ID}
        " );
        // Customer last order ID
        $last_order_id = (int) $wpdb->get_var( "
            SELECT MAX(p.ID) FROM {$wpdb->prefix}posts as p
            INNER JOIN {$wpdb->prefix}postmeta as pm ON p.ID = pm.post_id
            WHERE p.post_type = 'shop_order' AND pm.meta_key = '_customer_user'
            AND pm.meta_value = {$customer->ID}
        " );

        $user_link       = 'user-edit.php?user_id=' . $customer->ID;
        $last_order_link = 'post.php?post='.$last_order_id.'&action=edit';

        $table_display  .= '<tr>
        <td align="center"><a href="'.$user_link.'">' . esc_attr( $customer->ID ) .'</a></td>
        <td>' . esc_html( $customer->first_name ) .'</td>
        <td>' . esc_html( $customer->last_name ) .'</td>
        <td>' . esc_html( $customer->billing_address_1 ) .'</td>
        <td>' . esc_attr( $customer->billing_postcode ) .'</td>
        <td>' . esc_attr( $customer->billing_city ) .'</td>
        <td>' . esc_attr( $customer->billing_phone ) .'</td>
        <td><a href="mailto:'.$customer->billing_email.'">' . esc_attr( $customer->billing_email ) .'</a></td>
        <td align="right">'. ( $total_purchased > 0 ? wc_price( $total_purchased ) : ' - ' ) . '</td>
        <td align="center">'. $orders_count . '</td>
        <td align="center"><a href="'.$last_order_link.'">' .  ( $last_order_id > 0 ? $last_order_id : ' - ' ) . '</a></td>
        </tr>';
    }
    // Output the table
    echo $table_display . '</tbody></table>';
    }

    
}

// Add settings
add_action( 'woocommerce_settings_my-discount-tab', 'action_woocommerce_settings_my_discount_tab', 10 );
function action_woocommerce_settings_my_discount_tab() {

    $settings = get_discount_settings();
    WC_Admin_Settings::output_fields( $settings );

    
}

// Process/save the settings
add_action( 'woocommerce_settings_save_my-discount-tab', 'action_woocommerce_settings_save_my_discount_tab', 10 );
function action_woocommerce_settings_save_my_discount_tab() {
    $product_id = $_POST['product'];
    $discount_type = $_POST['discount_type'];
    $discount = $_POST['discount'];
    update_post_meta($product_id ,'discount_type',$discount_type);
    update_post_meta($product_id ,'discount',$discount);
}