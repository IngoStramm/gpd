<?php

// faz com que o editor possa gerenciar os usuários
add_action('init', 'gpd_editor_manage_users');

function gpd_editor_manage_users()
{

    if (get_option('gpd_add_cap_editor_once') != 'done') {

        // let editor manage users

        $edit_editor = get_role('editor'); // Get the user role
        $edit_editor->add_cap('edit_users');
        $edit_editor->add_cap('list_users');
        $edit_editor->add_cap('promote_users');
        $edit_editor->add_cap('create_users');
        $edit_editor->add_cap('add_users');
        $edit_editor->add_cap('delete_users');

        update_option('gpd_add_cap_editor_once', 'done');
    }
}

// Previne que o editor possa editar um administrador
$gpd_user_caps = new GPD_User_Caps();

add_action('pre_user_query', 'gpd_pre_user_query');

function gpd_pre_user_query($user_search)
{

    $user = wp_get_current_user();

    if (!current_user_can('manage_options')) {

        global $wpdb;

        $user_search->query_where =
            str_replace(
                'WHERE 1=1',
                "WHERE 1=1 AND {$wpdb->users}.ID IN (
                 SELECT {$wpdb->usermeta}.user_id FROM $wpdb->usermeta 
                    WHERE {$wpdb->usermeta}.meta_key = '{$wpdb->prefix}capabilities'
                    AND {$wpdb->usermeta}.meta_value NOT LIKE '%administrator%')",
                $user_search->query_where
            );
    }
}

// Adiciona a permissão para editar o banner para roles específicos 
// baseado em uma capabillity customizada
// add_action('init', 'gpd_add_custom_caps', 11);

function gpd_add_custom_caps()
{
    $roles = array(
        'administrator',
        'editor',
        'author',
        'contributor',
    );

    foreach ($roles as $role_name) {
        $role_obj = get_role($role_name);
        $role_obj->remove_cap('gpd_billing_access', true);
    }
}
