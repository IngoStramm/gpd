<?php

// Referência: https://awhitepixel.com/blog/modify-add-custom-columns-post-list-wordpress-admin/

// Removendo a coluna "Título" do post 'log-transacao'
add_filter('manage_log-transacao_posts_columns', function ($columns) {
    unset($columns['title']);
    return $columns;
});

// Adicionando novas colunas
add_filter('manage_log-transacao_posts_columns', function ($columns) {
    global $gpd_moeda;
    return array_merge($columns, [
        'gpd_user' => __('Usuário', 'gpd'), 
        'gpd_acao' => __('Ação', 'gpd'),
        'gpd_pontos' => ucfirst($gpd_moeda->nome_plural),
        'gpd_view' => __('Visualizar', 'gpd'), 
    ]);
});

// Adicionando a informação exibida nas novas colunas
add_action('manage_log-transacao_posts_custom_column', function ($column_key, $post_id) {
    if ($column_key == 'gpd_view') {
        // http://gesto-por-desempenho.local/wp-admin/post.php?post=277&action=edit
        echo '<a class="button-link" href="' . admin_url() . 'post.php?post=' . $post_id . '&action=edit">' . __('Ver', 'gpd') . '</a>';
    } else if ($column_key == 'gpd_user') {
        $gpd_user_id = get_post_meta($post_id, 'gpd_log_transacao_user_id', true);
        if ($gpd_user_id) {
            $gpd_user = get_userdata($gpd_user_id);
            echo $gpd_user->display_name;
        }
    } else if ($column_key == 'gpd_acao') {
        $gpd_acao = get_post_meta($post_id, 'gpd_log_transacao_acao', true);
        if ($gpd_acao) {
            echo $gpd_acao === 'add' ? __('Adicionar', 'cmb2') : __('Remover', 'cmb2') ;
        }
    } else if ($column_key == 'gpd_pontos') {
        $gpd_pontos = get_post_meta($post_id, 'gpd_log_transacao_qtd', true);
        if ($gpd_pontos) {
            echo $gpd_pontos;
        }
    }
}, 10, 2);

// Definindo as colunas que podem ser sorteadas
add_filter('manage_edit-log-transacao_sortable_columns', function ($columns) {
    $columns['gpd_user'] = 'gpd_user';
    $columns['gpd_acao'] = 'gpd_acao';
    $columns['gpd_pontos'] = 'gpd_pontos';
    return $columns;
});

// Adicionando a query das colunas que podem ser sorteadas
add_action('pre_get_posts', function ($query) {
    if (!is_admin()) {
        return;
    }

    $orderby = $query->get('orderby');
    if ($orderby == 'gpd_user') {
        $query->set('meta_key', 'gpd_log_transacao_user_id');
        $query->set('orderby', 'meta_value_num');
    } else if ($orderby == 'gpd_acao') {
        $query->set('meta_key', 'gpd_log_transacao_acao');
        $query->set('orderby', 'meta_value_num');
    } else if ($orderby == 'gpd_pontos') {
        $query->set('meta_key', 'gpd_log_transacao_qtd');
        $query->set('orderby', 'meta_value_num');
    }
});