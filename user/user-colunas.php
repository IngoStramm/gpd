<?php

// Referência: https://awhitepixel.com/blog/modify-add-custom-columns-post-list-wordpress-admin/

// gpd_resgate_user_id
// gpd_recompensa_id
// gpd_resgate_valor

// Adicionando novas colunas
add_filter('manage_users_columns', function ($columns) {
    unset($columns['posts']);
    $columns = array_merge($columns, [
        'gpd_user_cargo' => __('Cargo', 'gpd'),
        'gpd_user_saldo' => __('Saldo', 'gpd'),
    ]);
    return $columns;
});

// // Adicionando a informação exibida nas novas colunas
add_filter('manage_users_custom_column', function ($output, $column_name, $user_id) {
    if ($column_name == 'gpd_user_saldo') {
        $gpd_user_saldo = get_user_meta($user_id, 'gpd_user_saldo', true);
        if (!$gpd_user_saldo)
            $gpd_user_saldo = '0';
        return $gpd_user_saldo;
    } else if ($column_name == 'gpd_user_cargo') {
        $gpd_cargos = get_terms('cargos', array('hide_empty' => false));
        $gpd_current_cargo = null;
        if ($gpd_cargos) {
            foreach ($gpd_cargos as $gpd_cargo) {
                if (is_object_in_term($user_id, 'cargos', $gpd_cargo->slug))
                    $gpd_current_cargo = $gpd_cargo->name;
            }
        }
        if (!$gpd_current_cargo)
            $gpd_current_cargo = '—';
        return $gpd_current_cargo;
    }
    return $output;
}, 10, 3);

// // Definindo as colunas que podem ser sorteadas
add_filter('manage_edit-users_sortable_columns', function ($columns) {
    $columns['gpd_user_saldo'] = 'gpd_user_saldo';
    $columns['gpd_user_cargo'] = 'gpd_user_cargo';
    return $columns;
});
