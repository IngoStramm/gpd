<?php

add_action('wp_enqueue_scripts', 'gpd_frontend_scripts');

function gpd_frontend_scripts()
{

    $min = (in_array($_SERVER['REMOTE_ADDR'], array('127.0.0.1', '::1', '10.0.0.3'))) ? '' : '.min';

    if (empty($min)) :
        wp_enqueue_script('gpd-livereload', 'http://localhost:35729/livereload.js?snipver=1', array(), null, true);
    endif;

    wp_enqueue_script('datatable-script', GPD_URL . 'assets/vendors/DataTables/datatables' . $min . '.js', array('jquery'), '1.10.25', true);

    wp_register_script('gpd-script', GPD_URL . 'assets/js/gpd' . $min . '.js', array('jquery', 'datatable-script'), '1.0.0', true);
    wp_enqueue_script('gpd-script');
    wp_localize_script('gpd-script', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));

    wp_enqueue_style('datatables-style', GPD_URL . 'assets/vendors/DataTables/datatables.min.css', array(), false, 'all');
    wp_enqueue_style('gpd-style', GPD_URL . 'assets/css/gpd.css', array('datatables-style'), false, 'all');
}
