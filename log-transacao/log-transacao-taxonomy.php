<?php

add_action('init', 'gpd_log_transacao_taxonomy', 1);

function gpd_log_transacao_taxonomy()
{
    $video = new Gpd_Taxonomy(
        __('Tipo','gpd'), // Nome (Singular) da nova Taxonomia.
        'tipo', // Slug do Taxonomia.
        'log-transacao' // Nome do tipo de conteúdo que a taxonomia irá fazer parte.
    );

    $video->set_labels(
        array(
            'name'                       => __('Tipos', 'gpd'),
            'singular_name'              => __('Tipo', 'gpd'),
            'add_or_remove_items'        => __('Adicionar ou Remover Tipos', 'gpd'),
            'view_item'                  => __('Visualizar Tipo', 'gpd'),
            'edit_item'                  => __('Editar Tipo', 'gpd'),
            'search_items'               => __('Pesquisar Tipo', 'gpd'),
            'update_item'                => __('Atualizar Tipo', 'gpd'),
            'parent_item'                => __('Tipo Pai:', 'gpd'),
            'parent_item_colon'          => __('Tipo Pai:', 'gpd'),
            'menu_name'                  => __('Tipos', 'gpd'),
            'add_new_item'               => __('Adicionar Novo Tipo', 'gpd'),
            'new_item_name'              => __('Novo Tipo', 'gpd'),
            'all_items'                  => __('Todos Tipos', 'gpd'),
            'separate_items_with_commas' => __('Tipos separados por vírgula', 'gpd'),
            'choose_from_most_used'      => __('Escolha entre os Tipos mais usados', 'gpd')
        )
    );

    $video->set_arguments(
        array(
            'hierarchical' => false
        )
    );
}