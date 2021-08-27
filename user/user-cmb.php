<?php
add_action('cmb2_admin_init', 'gpd_register_user_profile_metabox');

function gpd_register_user_profile_metabox()
{
    global $gpd_moeda;
    $cmb_user = new_cmb2_box(array(
        'id'               => 'gpd_user_edit',
        'title'            => esc_html__('Controle de Pontos', 'gpd'), // Doesn't output for user boxes
        'object_types'     => array('user'),
        'show_names'       => true,
        'new_user_section' => 'add-new-user', // where form will show on new user page. 'add-existing-user' is only other valid option.
        'priority'   => 'high',
    ));

    $cmb_user->add_field(array(
        'name'     => esc_html__('Controle de ', 'gpd') . ucfirst($gpd_moeda->nome_plural),
        'id'       => 'gpd_user_title',
        'type'     => 'title',
    ));

    $cmb_user->add_field(array(
        'name'     => esc_html__('Saldo', 'cmb2'),
        'id'       => 'gpd_user_saldo',
        'type'     => 'text_small',
        'save_field' => false,
        'attributes'    => array(
            'type'      => 'number',
            'min'       => '0',
            'disabled' => 'disabled',
            'readonly' => 'readonly',
        ),
        'default'       => 0,
        'before_field'  => '$ ',
        'after_field'  => function () {
            global $gpd_moeda;
            $current_screen = get_current_screen();
            // gpd_debug($current_screen);
            if ($current_screen->id !== 'user-edit' && $current_screen->id !== 'profile')
                return;
            if (!current_user_can('edit_users'))
                return;
            $gpd_uder_id = isset($_GET['user_id']) ? $_GET['user_id'] : get_current_user_id();
            $gpd_add_bulk_points_page = gpd_get_option('gpd_add_bulk_points_page');

            if (!$gpd_add_bulk_points_page)
                return;
            else
                return sprintf(__(' <p>Clique <a href="%s" target="_blank"><strong>aqui</strong></a> para adicionar ou remover %s do usuário.</p>', 'gpd'), get_permalink($gpd_add_bulk_points_page), $gpd_moeda->nome_plural);
            // return sprintf(__(' <p>Clique <a href="/wp-admin/post-new.php?post_type=log-transacao&gpd_user_id=%s"><strong>aqui</strong></a> para adicionar ou remover %s do usuário.</p>', 'gpd'), $gpd_uder_id, $gpd_moeda->nome_plural);
        },

    ));

    $cmb_user->add_field(array(
        'name'     => esc_html__('Registro de Transações ', 'gpd'),
        'id'       => 'gpd_user_log',
        'type'     => 'title',
        'after_field'  => function () {
            $current_screen = get_current_screen();
            // gpd_debug($current_screen);
            if ($current_screen->id !== 'user-edit' && $current_screen->id !== 'profile')
                return;
            $gpd_uder_id = isset($_GET['user_id']) ? $_GET['user_id'] : get_current_user_id();
            return sprintf(__(' <p>Clique <a href="' . get_site_url() . '/?gpd_user_id=' . $gpd_uder_id . '" target="_blank"><strong>aqui</strong></a> para visualizar o registro de transações do usuário.'));
        },
    ));
}
