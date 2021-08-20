<?php
add_action('init', 'gpd_resgate_cpt', 1);

function gpd_resgate_cpt()
{
    $resgate = new Gpd_Post_Type(
        __('Resgate', 'gpd'), // Nome (Singular) do Post Type.
        'resgate' // Slug do Post Type.
    );

    $labels = array(
        'name'               => __('Resgates', 'gpd'),
        'singular_name'      => __('Resgate', 'gpd'),
        'view_item'          => __('Ver Resgate', 'gpd'),
        'edit_item'          => __('Editar Resgate', 'gpd'),
        'search_items'       => __('Pesquisar Resgate', 'gpd'),
        'update_item'        => __('Atualizar Resgate', 'gpd'),
        'parent_item_colon'  => __('Resgate Pai:', 'gpd'),
        'menu_name'          => __('Resgates', 'gpd'),
        'add_new'            => __('Adicionar Novo', 'gpd'),
        'add_new_item'       => __('Adicionar Novo Resgate', 'gpd'),
        'new_item'           => __('Novo Resgate', 'gpd'),
        'all_items'          => __('Todos Resgates', 'gpd'),
        'not_found'          => __('Nenhum Resgate encontrado', 'gpd'),
        'not_found_in_trash' => __('Nenhum Resgate encontrado na Lixeira', 'gpd')
    );

    $resgate->set_labels($labels);

    $resgate->set_arguments(
        array(
            'supports' => array('title'),
            'menu_icon' => 'dashicons-cart',
            'show_in_nav_menus' => false,
            'show_in_admin_bar' => false,
        )
    );
}
