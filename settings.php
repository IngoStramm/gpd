<?php
// Controle de exibição das páginas do sistema nos selects de listagem de páginas
// Previne que uma página que já esteja sendo usada em um select, seja exibida em outro select
$gpd_pages_array = [
    'gpd_order_confirmation_page', 'gpd_membership_terms_page', 'gpd_order_rewards_page',
    'gpd_password_page', 'gpd_add_bulk_points_page'
];

function gpd_exclude_options_pages($current_page)
{
    global $gpd_pages_array;
    $gpd_exclude_pages_array = $gpd_pages_array;

    if (($k = array_search($current_page, $gpd_exclude_pages_array)) !== false) {
        unset($gpd_exclude_pages_array[$k]);
    }

    $gpd_exclude_pages_id = [];

    foreach ($gpd_exclude_pages_array as $gpd_exclude_page) {

        $gpd_get_page_id_to_exclude = gpd_get_option($gpd_exclude_page);
        if ($gpd_get_page_id_to_exclude)
            $gpd_exclude_pages_id[] = $gpd_get_page_id_to_exclude;
    }

    $pages = get_pages(array(
        'sort_order'    => 'DESC',
        'sort_column'   => 'post_title',
        'exclude'       => $gpd_exclude_pages_id
    ));

    $pages_options = [];

    foreach ($pages as $page) {
        $pages_options[$page->ID] = $page->post_title;
    }
    return $pages_options;
}

function gpd_return_users()
{

    $users = get_users(array('role__in' => array('author', 'editor')));
    $users_arr = [];
    foreach ($users as $user) {
        $users_arr[$user->ID] = $user->display_name;
    }
    return $users_arr;
}

add_action('cmb2_admin_init', 'gpd_register_options_metabox');

function gpd_register_options_metabox()
{
    global $gpd_pages_array, $gpd_moeda;
    /**
     * Registers options page menu item and form.
     */
    $cmb_options = new_cmb2_box(array(
        'id'           => 'gpd_option_metabox',
        'title'        => esc_html__('Sistema', 'gpd'),
        'object_types' => array('options-page'),
        'option_key'      => 'gpd_options', // The option key and admin menu page slug.
        'icon_url'        => 'dashicons-admin-generic', // Menu icon. Only applicable if 'parent_slug' is left empty.
        'menu_title'      => esc_html__('Opções', 'gpd'), // Falls back to 'title' (above).
        // 'parent_slug'     => 'themes.php', // Make options page a submenu item of the themes menu.
        // 'capability'      => 'manage_options', // Cap required to view options-page.
        // 'position'        => 1, // Menu position. Only applicable if 'parent_slug' is left empty.
        // 'admin_menu_hook' => 'network_admin_menu', // 'network_admin_menu' to add network-level options page.
        // 'display_cb'      => false, // Override the options-page form output (CMB2_Hookup::options_page_output()).
        // 'save_button'     => esc_html__( 'Save Theme Options', 'gpd' ), // The text for the options-page save button. Defaults to 'Save'.
    ));

    // $cmb_options->add_field(array(
    //     'name'    => __('Notificações', 'gpd'),
    //     'desc'    => __('Opções de notificação do sistema.', 'gpd'),
    //     'id'      => 'gpd_notifications_settings_title',
    //     'type'    => 'title',
    // ));

    // $cmb_options->add_field(array(
    //     'name'    => __('E-mails que receberão os avisos de novos resgates', 'gpd'),
    //     'id'      => 'gpd_notifications_email',
    //     'type'    => 'text_email',
    //     'repeatable'   => true
    // ));

    $cmb_options->add_field(array(
        'name'    => __('Exibição do Nome da Moeda', 'gpd'),
        'desc'    => __('Defina qual será o nome da moeda que será exibido pelo sistema. O padrão é "ponto(s)".', 'gpd'),
        'id'      => 'gpd_currency_settings_title',
        'type'    => 'title',
    ));

    $cmb_options->add_field(array(
        'name'    => __('Nome da Moeda no singular', 'gpd'),
        'desc'    => __('Nome da moeda usada no sistema no singular (o padrão é "ponto").', 'gpd'),
        'id'      => 'gpd_single_currency_name',
        'type'    => 'text',
        'default'   => 'ponto'
    ));

    $cmb_options->add_field(array(
        'name'    => __('Nome da Moeda no plural', 'gpd'),
        'desc'    => __('Nome da moeda usada no sistema no plural (o padrão é "pontos").', 'gpd'),
        'id'      => 'gpd_plural_currency_name',
        'type'    => 'text',
        'default'   => 'pontos'
    ));

    $cmb_options->add_field(array(
        'name'    => __('Gênero da Moeda', 'gpd'),
        'desc'    => __('Se o sistema deve usar o artigo masculino (o) ou feminino (a) ao exibir o nome da moeda usada no sistema (o padrão é "o").', 'gpd'),
        'id'      => 'gpd_currency_gender',
        'type'    => 'radio',
        'options'   => array(
            'o' => __('Masculino (o)', 'gpd'),
            'a' => __('Feminino (a)', 'gpd'),
        ),
        'default'   => 'o'
    ));

    $cmb_options->add_field(array(
        'name'    => __('Páginas do Sistema', 'gpd'),
        'desc'    => __('Defina quais serão as páginas usdadas pelo sistema. Não tente usar a mesma página em mais de uma opção.', 'gpd'),
        'id'      => 'gpd_pages_settings_title',
        'type'    => 'title',
    ));

    $cmb_options->add_field(array(
        'name'    => __('Defina qual é a página usada como confirmação do Resgate da Recompensa', 'gpd'),
        'id'      => $gpd_pages_array[0],
        'type'    => 'select',
        'show_option_none' => true,
        'options_cb'   => function () {
            global $gpd_pages_array;
            return gpd_exclude_options_pages($gpd_pages_array[0]);
        }
    ));

    $cmb_options->add_field(array(
        'name'    => __('Defina qual é a página dos Termos de Adesão', 'gpd'),
        'id'      => $gpd_pages_array[1],
        'type'    => 'select',
        'show_option_none' => true,
        'options_cb'   => function () {
            global $gpd_pages_array;
            return gpd_exclude_options_pages($gpd_pages_array[1]);
        }
    ));

    $cmb_options->add_field(array(
        'name'    => __('Defina qual é a página usada como listagem das recompensas resgatadas', 'gpd'),
        'id'      => $gpd_pages_array[2],
        'type'    => 'select',
        'show_option_none' => true,
        'options_cb'   => function () {
            global $gpd_pages_array;
            return gpd_exclude_options_pages($gpd_pages_array[2]);
        }
    ));

    $cmb_options->add_field(array(
        'name'    => __('Defina qual é a página de Troca de Senha', 'gpd'),
        'id'      => $gpd_pages_array[3],
        'type'    => 'select',
        'show_option_none' => true,
        'options_cb'   => function () {
            global $gpd_pages_array;
            return gpd_exclude_options_pages($gpd_pages_array[3]);
        }
    ));

    $cmb_options->add_field(array(
        'name'    => sprintf(
            __('Defina qual é a página de gestão de %s em massa', 'gpd'),
            $gpd_moeda->nome_plural
        ),
        'id'      => $gpd_pages_array[4],
        'type'    => 'select',
        'show_option_none' => true,
        'options_cb'   => function () {
            global $gpd_pages_array;
            return gpd_exclude_options_pages($gpd_pages_array[4]);
        }
    ));

    $cmb_options->add_field(array(
        'name'    => __('Acesso Financeiro', 'gpd'),
        'desc'    => __('Defina quais serão os usuários que terão acesso as áreas "Resgate" e "Recompensas".', 'gpd'),
        'id'      => 'gpd_billing_users_title',
        'type'    => 'title',
    ));

    $cmb_options->add_field(array(
        'name'    => __('selecione os usuários', 'gpd'),
        'desc'    => __('Apenas os usuários com a função "editor" e "autor" aparecem nesta listagem.', 'gpd'),
        'id'      => 'gpd_billing_users',
        'type'    => 'multicheck',
        'options_cb' => 'gpd_return_users',
    ));
}

add_action('cmb2_admin_init', 'gpd_register_notifications_options_metabox');

function gpd_register_notifications_options_metabox()
{
    global $gpd_pages_array;
    /**
     * Registers options page menu item and form.
     */
    $cmb_options = new_cmb2_box(array(
        'id'           => 'gpd_notifications_option_metabox',
        'title'        => esc_html__('Notificações', 'gpd'),
        'object_types' => array('options-page'),
        'option_key'      => 'gpd_notifications_options', // The option key and admin menu page slug.
        'icon_url'        => 'dashicons-admin-generic', // Menu icon. Only applicable if 'parent_slug' is left empty.
        // 'menu_title'      => esc_html__( 'Options', 'gpd' ), // Falls back to 'title' (above).
        'parent_slug'     => 'gpd_options', // Make options page a submenu item of the themes menu.
        'capability'      => 'edit_others_pages', // Cap required to view options-page.
        // 'position'        => 1, // Menu position. Only applicable if 'parent_slug' is left empty.
        // 'admin_menu_hook' => 'network_admin_menu', // 'network_admin_menu' to add network-level options page.
        // 'display_cb'      => false, // Override the options-page form output (CMB2_Hookup::options_page_output()).
        // 'save_button'     => esc_html__( 'Save Theme Options', 'gpd' ), // The text for the options-page save button. Defaults to 'Save'.
    ));

    $cmb_options->add_field(array(
        'name'    => __('Notificações', 'gpd'),
        'desc'    => __('Opções de notificação do sistema.', 'gpd'),
        'id'      => 'gpd_notifications_settings_title',
        'type'    => 'title',
    ));

    $cmb_options->add_field(array(
        'name'    => __('E-mails que receberão os avisos de novos resgates', 'gpd'),
        'id'      => 'gpd_notifications_email',
        'type'    => 'text_email',
        'repeatable'   => true
    ));
}


add_action('cmb2_admin_init', 'gpd_register_banner_options_metabox');

function gpd_register_banner_options_metabox()
{
    global $gpd_pages_array;
    /**
     * Registers options page menu item and form.
     */
    $cmb_group = new_cmb2_box(array(
        'id'           => 'gpd_banner_option_metabox',
        'title'        => esc_html__('Banner', 'gpd'),
        'object_types' => array('options-page'),
        'option_key'      => 'gpd_banner_options', // The option key and admin menu page slug.
        'icon_url'        => 'dashicons-admin-generic', // Menu icon. Only applicable if 'parent_slug' is left empty.
        // 'menu_title'      => esc_html__( 'Options', 'gpd' ), // Falls back to 'title' (above).
        'parent_slug'     => 'gpd_options', // Make options page a submenu item of the themes menu.
        'capability'      => 'edit_others_posts', // Cap required to view options-page.
        // 'position'        => 1, // Menu position. Only applicable if 'parent_slug' is left empty.
        // 'admin_menu_hook' => 'network_admin_menu', // 'network_admin_menu' to add network-level options page.
        // 'display_cb'      => false, // Override the options-page form output (CMB2_Hookup::options_page_output()).
        // 'save_button'     => esc_html__( 'Save Theme Options', 'gpd' ), // The text for the options-page save button. Defaults to 'Save'.
    ));

    $group_field_id = $cmb_group->add_field(array(
        'id'          => 'gpd_banner_group_metabox',
        'type'        => 'group',
        'description' => '<h1>' . esc_html__('Opções do Banner', 'cmb2') . '</h1>',
        'options'     => array(
            'group_title'    => esc_html__('Slide {#}', 'gpd'), // {#} gets replaced by row number
            'add_button'     => esc_html__('Adicionar novo Slide', 'gpd'),
            'remove_button'  => esc_html__('Remover Slide', 'gpd'),
            'sortable'       => true,
            // 'closed'      => true, // true to have the groups closed by default
            // 'remove_confirm' => esc_html__( 'Are you sure you want to remove?', 'cmb2' ), // Performs confirmation before removing group.
        ),
    ));


    $cmb_group->add_group_field($group_field_id, array(
        'name'    => __('Selecione uma imagem para ser usada neste slide', 'gpd'),
        'desc'    => __('Dimensões recomendadas: <strong>1200px de largura</strong> e <strong>282px de altura</strong>.</br>Ferramenta online para diminuir o peso (KBs) da imagem: <a href="https://tinypng.com/" target="_blank">Tiny PNG</a>.', 'gpd'),
        'id'      => 'gpd_banner_image',
        'type'    => 'file',
    ));

    $cmb_group->add_group_field($group_field_id, array(
        'name'    => __('Url do Slide', 'gpd'),
        // 'desc'    => __('Opções do banner usado nas páginas internas.', 'gpd'),
        'id'      => 'gpd_banner_url',
        'type'    => 'text_url',
        'attributes' => array(
            'placeholder' => 'https://'
        )
    ));
}

/**
 * Wrapper function around cmb2_get_option
 * @since  0.1.0
 * @param  string $key     Options array key
 * @param  mixed  $default Optional default value
 * @return mixed           Option value
 */
function gpd_get_option($key = '', $default = false)
{
    if (function_exists('cmb2_get_option')) {
        // Use cmb2_get_option as it passes through some key filters.
        return cmb2_get_option('gpd_options', $key, $default);
    }

    // Fallback to get_option if CMB2 is not loaded yet.
    $opts = get_option('gpd_options', $default);

    $val = $default;

    if ('all' == $key) {
        $val = $opts;
    } elseif (is_array($opts) && array_key_exists($key, $opts) && false !== $opts[$key]) {
        $val = $opts[$key];
    }

    return $val;
}
function gpd_get_notifications_option($key = '', $default = false)
{
    if (function_exists('cmb2_get_option')) {
        // Use cmb2_get_option as it passes through some key filters.
        return cmb2_get_option('gpd_notifications_options', $key, $default);
    }

    // Fallback to get_option if CMB2 is not loaded yet.
    $opts = get_option('gpd_notifications_options', $default);

    $val = $default;

    if ('all' == $key) {
        $val = $opts;
    } elseif (is_array($opts) && array_key_exists($key, $opts) && false !== $opts[$key]) {
        $val = $opts[$key];
    }

    return $val;
}

function gpd_get_banner($key = '', $default = false)
{
    if (function_exists('cmb2_get_option')) {
        // Use cmb2_get_option as it passes through some key filters.
        return cmb2_get_option('gpd_banner_options', $key, $default);
    }

    // Fallback to get_option if CMB2 is not loaded yet.
    $opts = get_option('gpd_banner_options', $default);

    $val = $default;

    if ('all' == $key) {
        $val = $opts;
    } elseif (is_array($opts) && array_key_exists($key, $opts) && false !== $opts[$key]) {
        $val = $opts[$key];
    }

    return $val;
}

function gpd_get_gpd_billing_users($default = false)
{
    if (function_exists('cmb2_get_option')) {
        // Use cmb2_get_option as it passes through some key filters.
        return cmb2_get_option('gpd_options', 'gpd_billing_users', $default);
    }

    // Fallback to get_option if CMB2 is not loaded yet.
    $opts = get_option('gpd_options', $default);

    $val = $default;

    if ('all' == 'gpd_billing_users') {
        $val = $opts;
    } elseif (is_array($opts) && array_key_exists('gpd_billing_users', $opts) && false !== $opts['gpd_billing_users']) {
        $val = $opts['gpd_billing_users'];
    }

    return $val;
}
