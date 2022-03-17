<?php
add_action('init', 'gpd_recompensa_cpt', 1);

function gpd_recompensa_cpt()
{
    $recompensa = new Gpd_Post_Type(
        __('Recompensa', 'gpd'), // Nome (Singular) do Post Type.
        'recompensas' // Slug do Post Type.
    );
    
    $labels = array(
        'name'               => __('Recompensas', 'gpd'),
        'singular_name'      => __('Recompensa', 'gpd'),
        'view_item'          => __('Ver Recompensa', 'gpd'),
        'edit_item'          => __('Editar Recompensa', 'gpd'),
        'search_items'       => __('Pesquisar Recompensa', 'gpd'),
        'update_item'        => __('Atualizar Recompensa', 'gpd'),
        'parent_item_colon'  => __('Recompensa Pai:', 'gpd'),
        'menu_name'          => __('Recompensas', 'gpd'),
        'add_new'            => __('Adicionar Nova', 'gpd'),
        'add_new_item'       => __('Adicionar Nova Recompensa', 'gpd'),
        'new_item'           => __('Nova Recompensa', 'gpd'),
        'all_items'          => __('Todas Recompensas', 'gpd'),
        'not_found'          => __('Nenhuma Recompensa encontrada', 'gpd'),
        'not_found_in_trash' => __('Nenhuma Recompensa encontrada na Lixeira', 'gpd'),
    );
    
    $recompensa->set_labels($labels);
    
    $recompensa->set_arguments(
        array(
            'supports' => array('title', 'editor', 'thumbnail', 'revisions', 'author'),
            'menu_icon'         => 'dashicons-awards',
            'capability_type'     => 'page'
        )
    );
}
