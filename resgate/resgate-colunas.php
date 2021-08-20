<?php

// Referência: https://awhitepixel.com/blog/modify-add-custom-columns-post-list-wordpress-admin/

// gpd_resgate_user_id
// gpd_recompensa_id
// gpd_resgate_valor

// Adicionando novas colunas
add_filter('manage_resgate_posts_columns', function ($columns) {
    global $gpd_moeda;
    return array_merge($columns, [
        'gpd_resgate_user' => __('Usuário', 'gpd'),
        'gpd_recompensa_nome' => __('Recompensa', 'gpd'),
        'gpd_resgate_valor' => ucfirst($gpd_moeda->nome_plural),
    ]);
});

// Adicionando a informação exibida nas novas colunas
add_action('manage_resgate_posts_custom_column', function ($column_key, $post_id) {
    if ($column_key == 'gpd_resgate_user') {
        $gpd_user_id = get_post_meta($post_id, 'gpd_resgate_user_id', true);
        if ($gpd_user_id) {
            $gpd_user = get_userdata($gpd_user_id);
            echo $gpd_user->display_name;
        }
    } else if ($column_key == 'gpd_recompensa_nome') {
        $gpd_recompensa_id = get_post_meta($post_id, 'gpd_recompensa_id', true);
        if ($gpd_recompensa_id) {
            $gpd_recompensa_title = get_the_title($gpd_recompensa_id);
            echo $gpd_recompensa_title;
        }
    } else if ($column_key == 'gpd_resgate_valor') {
        $gpd_pontos = get_post_meta($post_id, 'gpd_resgate_valor', true);
        if ($gpd_pontos) {
            echo $gpd_pontos;
        }
    }
}, 10, 2);

// Definindo as colunas que podem ser sorteadas
add_filter('manage_edit-resgate_sortable_columns', function ($columns) {
    $columns['gpd_resgate_user'] = 'gpd_resgate_user';
    $columns['gpd_recompensa_nome'] = 'gpd_recompensa_nome';
    $columns['gpd_resgate_valor'] = 'gpd_resgate_valor';
    return $columns;
});

// Adicionando a query das colunas que podem ser sorteadas
add_action('pre_get_posts', function ($query) {
    if (!is_admin()) {
        return;
    }

    $orderby = $query->get('orderby');
    if ($orderby == 'gpd_user') {
        $query->set('meta_key', 'gpd_resgate_user_id');
        $query->set('orderby', 'meta_value_num');
    } else if ($orderby == 'gpd_acao') {
        $query->set('meta_key', 'gpd_recompensa_id');
        $query->set('orderby', 'meta_value_num');
    } else if ($orderby == 'gpd_pontos') {
        $query->set('meta_key', 'gpd_resgate_valor');
        $query->set('orderby', 'meta_value_num');
    }
});

// Reposicionando a coluna data
add_filter('manage_resgate_posts_columns', function ($columns) {
    $taken_out = $columns['date'];
    unset($columns['date']);
    $columns['date'] = $taken_out;
    return $columns;
});
