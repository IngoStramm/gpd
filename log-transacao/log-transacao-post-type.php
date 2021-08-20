<?php
add_action('init', 'gpd_log_transacao_cpt', 1);

function gpd_log_transacao_cpt()
{
    $transacao = new Gpd_Post_Type(
        __('Transação', 'gpd'), // Nome (Singular) do Post Type.
        'log-transacao' // Slug do Post Type.
    );

    $labels = array(
        'name'               => __('Transações', 'gpd'),
        'singular_name'      => __('Transação', 'gpd'),
        'view_item'          => __('Ver Transação', 'gpd'),
        'edit_item'          => __('Editar Transação', 'gpd'),
        'search_items'       => __('Pesquisar Transação', 'gpd'),
        'update_item'        => __('Atualizar Transação', 'gpd'),
        'parent_item_colon'  => __('Transação Pai:', 'gpd'),
        'menu_name'          => __('Transações', 'gpd'),
        'add_new'            => __('Adicionar Nova', 'gpd'),
        'add_new_item'       => __('Adicionar Nova Transação', 'gpd'),
        'new_item'           => __('Nova Transação', 'gpd'),
        'all_items'          => __('Todas Transações', 'gpd'),
        'not_found'          => __('Nenhuma Transação encontrada', 'gpd'),
        'not_found_in_trash' => __('Nenhuma Transação encontrada na Lixeira', 'gpd')
    );

    $transacao->set_labels($labels);

    $transacao->set_arguments(
        array(
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => false,
            'show_in_nav_menus' => false,
            'show_in_admin_bar' => false,
            'publicly_queryable' => true,
            'supports' => array(''),
            'menu_icon' => 'dashicons-money-alt',
        )
    );
}
