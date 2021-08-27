<?php
add_action('admin_head-post.php', 'gpd_log_transacao_hide_publishing_actions');
add_action('admin_head-post-new.php', 'gpd_log_transacao_hide_publishing_actions');

// Admin
// Gestão de pontos

// Esconde as opções de publicar como rascunho e agendamento do post "log-transacao"
function gpd_log_transacao_hide_publishing_actions()
{
    $my_post_type = 'log-transacao';
    global $post;
    if ($post->post_type == $my_post_type) {
        echo '
                <style type="text/css">
                /* #misc-publishing-actions, */
                #misc-publishing-actions .misc-pub-section.misc-pub-post-status,
                #misc-publishing-actions .misc-pub-section.misc-pub-visibility,
                    #minor-publishing-actions,
                    .notice.updated,
                    #delete-action {
                        display:none;
                    }    
                </style>    
            ';
    }
}
add_filter('wp_insert_post_data', 'gpd_log_transacao_force_published');

// Força que os posts do "log-transacao" usem o status published
function gpd_log_transacao_force_published($post)
{
    if ('trash' !== $post['post_status'] && 'future' !== $post['post_status']) { /* We still want to use the trash */
        if (in_array($post['post_type'], array('page', 'log-transacao'))) {
            $post['post_status'] = 'publish';
        }
    }
    // gpd_debug($post['post_status']);
    return $post;
}

// Desabilita o salvamento automático como rascunho para o post "log-transacao"
add_action('admin_enqueue_scripts', 'gpd_disable_log_transacao_auto_draft');

function gpd_disable_log_transacao_auto_draft()
{
    if ('log-transacao' == get_post_type())
        wp_dequeue_script('autosave');
}

// Define um título padrão para quando o post "log-transacao" é salvo
// Não deu certo, mantido como referência
// add_filter('wp_insert_post_data', 'gpd_add_custom_title', 999, 2);

function gpd_add_custom_title($data, $postarr)
{
    // gpd_debug($data->post_type);
    if ($postarr['post_type'] == 'log-transacao') {
        if (!isset($postarr['ID']))
            return $postarr;

        $log_mvt_id = $postarr['ID'];
        $gpd_user_id = get_post_meta($log_mvt_id, 'gpd_log_transacao_user_id', true);
        if (!$gpd_user_id)
            return $data;
        $gpd_user = get_userdata($gpd_user_id);
        $data['post_title'] = $gpd_user->display_name . ' | ' . $postarr['post_date'];
    }
    return $data;
}

// Remove o box de tags "tipo" do sidebar na tela de criação/edição do post "log-movimentação"
add_action('admin_menu', function () {
    remove_meta_box('tagsdiv-tipo', 'log-transacao', 'normal');
});

// Atualiza o saldo do usuário quando o status de um post_type "log-transacao" muda para publicado
add_action('transition_post_status', 'gpd_log_transacao_updated', 11, 3);

function gpd_log_transacao_updated($new_status, $old_status, $post)
{
    if ($post->post_type !== 'log-transacao' && $new_status !== 'publish')
        return;

    $post_ID = $post->ID;
    $gpd_user_id = get_post_meta($post_ID, 'gpd_log_transacao_user_id', true);
    $gpd_user_saldo_atual = get_user_meta($gpd_user_id, 'gpd_user_saldo', true);
    $gpd_user_saldo_atual = empty($gpd_user_saldo_atual) ? '0' : $gpd_user_saldo_atual;
    $gpd_log_transacao_qtd = get_post_meta($post_ID, 'gpd_log_transacao_qtd', true);
    $gpd_log_transacao_acao = get_post_meta($post_ID, 'gpd_log_transacao_acao', true);
    $gpd_user_novo_saldo = intval($gpd_user_saldo_atual);
    if ($gpd_log_transacao_acao === 'add') {
        $gpd_user_novo_saldo = intval($gpd_user_saldo_atual) + intval($gpd_log_transacao_qtd);
    } else {
        $gpd_user_novo_saldo = intval($gpd_user_saldo_atual) - intval($gpd_log_transacao_qtd);
        $gpd_user_novo_saldo = $gpd_user_novo_saldo < 0 ? 0 : $gpd_user_novo_saldo;
    }
    $updated = update_user_meta($gpd_user_id, 'gpd_user_saldo', $gpd_user_novo_saldo);
}

// Notifica o usuário quando ocorre uma nova transação
// add_action('save_post', 'gpd_notifica_user_nova_transacao', 19, 3);
add_action('transition_post_status', 'gpd_notifica_user_nova_transacao', 11, 3);

function gpd_notifica_user_nova_transacao($new_status, $old_status, $post)
{
    if ($post->post_type !== 'log-transacao' && $new_status !== 'publish')
        return;

    global $gpd_moeda;
    $post_ID = $post->ID;
    $gpd_user_id = get_post_meta($post_ID, 'gpd_log_transacao_user_id', true);
    $gpd_user_saldo_atual = get_user_meta($gpd_user_id, 'gpd_user_saldo', true);
    $gpd_log_transacao_qtd = get_post_meta($post_ID, 'gpd_log_transacao_qtd', true);
    $gpd_log_transacao_acao = get_post_meta($post_ID, 'gpd_log_transacao_acao', true);
    $gpd_log_transacao_tipos = get_the_terms($post_ID, 'tipo');

    if (!$gpd_user_id || !$gpd_user_saldo_atual || !$gpd_log_transacao_qtd || !$gpd_log_transacao_acao)
        return;

    $gpd_user_saldo_atual = empty($gpd_user_saldo_atual) ? '0' : $gpd_user_saldo_atual;

    $site_name = get_bloginfo();
    $gpd_user_data = get_userdata($gpd_user_id);
    $to = $gpd_user_data->user_email;
    $subject = $site_name . ' | ' . __('Aviso de movimentação na sua conta', 'gpd');
    $body = '';
    $body .= '<h3>' . sprintf(__('Olá, %s!', 'gpd'), $gpd_user_data->display_name) . '</h3>';

    $gpd_log_transacao_qtd = intval($gpd_log_transacao_qtd);

    if ($gpd_log_transacao_qtd > 1) { // plural
        $gpd_exibir_nome_moeda = $gpd_moeda->nome_plural;
        $gpd_acao_array = array(
            'add' => sprintf(__('adicionad%ss', 'gpd'), $gpd_moeda->artigo),
            'remove' => sprintf(__('removid%ss', 'gpd'), $gpd_moeda->artigo)
        );
        $gpd_verbo = __('foram', 'gpd');
    } else { // singular
        $gpd_exibir_nome_moeda = $gpd_moeda->nome;
        $gpd_acao_array = array(
            'add' => sprintf(__('adicionad%s', 'gpd'), $gpd_moeda->artigo),
            'remove' => sprintf(__('removid%s', 'gpd'), $gpd_moeda->artigo)
        );
        $gpd_verbo = __('foi', 'gpd');
    }

    $body .= '<p>' . sprintf(__('O seu saldo acabou de ser atualizado. O valor de %s %s %s %s.', 'gpd'), $gpd_log_transacao_qtd, $gpd_exibir_nome_moeda, $gpd_verbo, $gpd_acao_array[$gpd_log_transacao_acao]) . ' ';
    $body .= __('Acesse o seu extrato para visualizar os detalhes!', 'gpd') . '</p>';
    $body .= '<p>' . __('Obrigado!', 'gpd') . '</p>';
    $headers = array(
        'Content-Type: text/html; charset=UTF-8',
        'From: ' . $site_name . '<noreply@' . $_SERVER['HTTP_HOST'] . '>',
        'Cc:',
        'Bcc: ingo@agencialaf.com'
    );
    wp_mail($to, $subject, $body, $headers);
}

// Redireciona de volta para a tela do usuário após salvar o post "log-transacao"
add_action('save_post', 'gpd_redirect_log_transacao', 999, 3);

function gpd_redirect_log_transacao($post_ID, $post, $update)
{
    if (is_admin() && $post->post_type === 'log-transacao' && !wp_doing_ajax()) {
        $gpd_user_id = get_post_meta($post_ID, 'gpd_log_transacao_user_id', true);

        if (!$gpd_user_id)
            return;

        if (isset($_GET['action']) && $_GET['action'] === 'delete')
            return;

        $url =  admin_url() . 'user-edit.php?user_id=' . $gpd_user_id . '#cmb2-metabox-gpd_user_edit';
        if (wp_safe_redirect($url))
            exit;
        // gpd_debug($post['teste']);
    }
}

// Admin
// Transação

// Esconde os botões de adicionar, editar e excluir transações
add_action('current_screen', 'gpd_esconde_btn_nova_transacao');

function gpd_esconde_btn_nova_transacao()
{
    $current_screen = get_current_screen();
    // Hide sidebar link
    global $submenu;
    // gpd_debug($submenu);
    unset($submenu['edit.php?post_type=log-transacao'][10]);
    unset($submenu['edit.php?post_type=resgate'][10]);

    // Hide link on listing page
    if ($current_screen->id == 'edit-log-transacao' || $current_screen->id == 'resgate') {
        echo '<style type="text/css">
        .page-title-action,
        .row-actions { 
            display:none; 
        }
        </style>';
    }
    if ($current_screen->id == 'resgate') {
        echo '<style type="text/css">
        #major-publishing-actions,
        #preview-action,
        #edit-slug-buttons,
        .edit-post-status,
        .edit-visibility,
        .edit-timestamp { 
            display:none; 
        }
        </style>';
    }
}

// Esconde via CSS alguns elementos
add_action('admin_head', 'gpd_admin_style');

function gpd_admin_style()
{
?>
    <style>
        .subsubsub .administrator {
            display: none;
        }

        div#wpadminbar,
        ul#adminmenu,
        ul#adminmenu .wp-submenu,
        div#adminmenuback,
        div#adminmenuwrap {
            background-color: #004793;
        }

        ul#adminmenu .wp-has-current-submenu .wp-submenu .wp-submenu-head,
        ul#adminmenu .wp-menu-arrow,
        ul#adminmenu .wp-menu-arrow div,
        ul#adminmenu li.current a.menu-top,
        ul#adminmenu li.wp-has-current-submenu a.wp-has-current-submenu,
        div#wpadminbar .ab-top-menu>li.hover>.ab-item,
        div#wpadminbar.nojq .quicklinks .ab-top-menu>li>.ab-item:focus,
        div#wpadminbar:not(.mobile) .ab-top-menu>li:hover>.ab-item,
        div#wpadminbar:not(.mobile) .ab-top-menu>li>.ab-item:focus,
        div#wpadminbar .menupop .ab-sub-wrapper,
        div#wpadminbar .shortlink-input {
            background-color: #ffc559;
            color: #004793;
        }

        #wpadminbar .quicklinks .menupop ul li a,
        #wpadminbar .quicklinks .menupop ul li a strong,
        #wpadminbar .quicklinks .menupop.hover ul li a,
        #wpadminbar.nojs .quicklinks .menupop:hover ul li a {
            color: #6d839a;
        }

        ul#adminmenu .current div.wp-menu-image:before,
        ul#adminmenu .wp-has-current-submenu div.wp-menu-image:before,
        ul#adminmenu a.current:hover div.wp-menu-image:before,
        ul#adminmenu a.wp-has-current-submenu:hover div.wp-menu-image:before,
        ul#adminmenu li.wp-has-current-submenu a:focus div.wp-menu-image:before,
        ul#adminmenu li.wp-has-current-submenu.opensub div.wp-menu-image:before,
        ul#adminmenu li.wp-has-current-submenu:hover div.wp-menu-image:before,
        div#wpadminbar .quicklinks .ab-sub-wrapper .menupop.hover>a,
        div#wpadminbar .quicklinks .menupop ul li a:focus,
        div#wpadminbar .quicklinks .menupop ul li a:focus strong,
        div#wpadminbar .quicklinks .menupop ul li a:hover,
        div#wpadminbar .quicklinks .menupop ul li a:hover strong,
        div#wpadminbar .quicklinks .menupop.hover ul li a:focus,
        div#wpadminbar .quicklinks .menupop.hover ul li a:hover,
        div#wpadminbar .quicklinks .menupop.hover ul li div[tabindex]:focus,
        div#wpadminbar .quicklinks .menupop.hover ul li div[tabindex]:hover,
        div#wpadminbar li #adminbarsearch.adminbar-focused:before,
        div#wpadminbar li .ab-item:focus .ab-icon:before,
        div#wpadminbar li .ab-item:focus:before,
        div#wpadminbar li a:focus .ab-icon:before,
        div#wpadminbar li.hover .ab-icon:before,
        div#wpadminbar li.hover .ab-item:before,
        div#wpadminbar li:hover #adminbarsearch:before,
        div#wpadminbar li:hover .ab-icon:before,
        div#wpadminbar li:hover .ab-item:before,
        div#wpadminbar.nojs .quicklinks .menupop:hover ul li a:focus,
        div#wpadminbar.nojs .quicklinks .menupop:hover ul li a:hover {
            color: #004793;
        }
    </style>
<?php
}

add_action('init', 'gpd_checka_administrador');

function gpd_checka_administrador()
{
    $user = wp_get_current_user();
    $roles = (array) $user->roles;
    if (isset($roles[0]) && $roles[0] !== 'administrator')
        gpd_custom_admin();
}

function gpd_custom_admin()
{
    // Remove os Widgets do dashboard
    add_action('wp_dashboard_setup', 'gpd_dashboard_widgets');

    function gpd_dashboard_widgets()
    {
        global $wp_meta_boxes;
        // Agora
        unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_right_now']);
        // Postagem rápida
        unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press']);
        // Novidades WP
        unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']);
        // Atividades
        unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_activity']);
        // Health Check
        unset($wp_meta_boxes['dashboard']['normal']['core']['health_check_status']);
        unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_site_health']);
    }

    // Remove os itens do menu do sidebar
    add_action('admin_menu', 'gpd_remove_menus_editor');

    function gpd_remove_menus_editor()
    {
        global $menu, $submenu;
        add_menu_page(__('Editar Menus'), __(' Menus '), 'edit_theme_options', 'nav-menus.php', null, 'dashicons-menu', 60);

        remove_menu_page('edit.php');                       //Posts
        remove_menu_page('edit.php?post_type=page');        //Páginas
        remove_menu_page('upload.php');                     //Mídia
        remove_menu_page('themes.php');                     //Appearance
        remove_menu_page('tools.php');                      //Tools
        remove_menu_page('edit-comments.php');              //Comentários
    }
}

// remove itens do admin bar

add_action('admin_bar_menu', 'gpd_remove_bar_menu_items', 999);

function gpd_remove_bar_menu_items($wp_admin_bar)
{
    $user = wp_get_current_user();
    $roles = (array) $user->roles;
    if (isset($roles[0]) && $roles[0] !== 'administrator') {
        $wp_admin_bar->remove_node('comments');
        $wp_admin_bar->remove_node('new-content');
        $wp_admin_bar->remove_node('archive');
    }
}


// Tela de gestão em massa de Pontos

// add_action('admin_menu', 'gpd_bulk_points_menu');

function gpd_bulk_points_menu()
{
    global $gpd_moeda;
    add_submenu_page('users.php', sprintf(__('Adicionar %s em massa', 'gpd'), $gpd_moeda->nome), sprintf(__('%s em massa', 'gpd'), ucfirst($gpd_moeda->nome)), 'edit_others_pages', 'add_bulk_points', 'gpd_bulk_points_page', null);
}

function gpd_bulk_points_page()
{
    global $gpd_moeda;
?>
    <div class="wrap">
        <h1 class="wp-heading"><?php echo sprintf(__('%s em massa', 'gpd'), ucfirst($gpd_moeda->nome)); ?></h1>
        <label for="usuarios[]" id="usuarios[]"><?php echo __('Selecione os usuários', 'gpd'); ?>
            <select class="js-example-basic-multiple" name="usuarios[]" multiple="multiple">
                <option value="option-01">Opção 1</option>
                <option value="option-02">Opção 2</option>
                <option value="option-03">Opção 3</option>
                <option value="option-04">Opção 4</option>
            </select>
        </label>
    </div><!-- /.wrap-->
    <?php
}

add_action('manage_users_extra_tablenav', 'gpd_add_admin_bulk_points_page_link');

function gpd_add_admin_bulk_points_page_link($which)
{
    global $gpd_moeda;
    if ($which == 'top') {
        $gpd_add_bulk_points_page = gpd_get_option('gpd_add_bulk_points_page');
        if (!$gpd_add_bulk_points_page)
            return;
    ?>
        <a href="<?php echo get_permalink($gpd_add_bulk_points_page); ?>" class="button" target="_blank"><?php echo sprintf(__('Adicionar %s em massa', 'gpd'), ucfirst($gpd_moeda->nome_plural)); ?></a>
<?php
    }
}
