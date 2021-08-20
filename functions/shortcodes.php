<?php

// Shortcode
// página customizada para recuperar a senha
// referência: https://steemit.com/utopian-io/@vallesleoruther/creating-a-front-end-change-password-form

function gpd_change_password_form()
{
    $gpd_user_id = get_current_user_id();
    if (!$gpd_user_id)
        return;
    $output = '';
    $output .= '    <div class="row">';
    $output .= '        <div class="col-md-6">';
    $output .= '            <form action="" method="post" id="trocar-senha-form">';
    $output .= wp_nonce_field('gpd-trocar-senha_' . $gpd_user_id, '_wpnonce', true, false);

    $output .= '                <div class="form-group">';
    $output .= '                    <label for="current_password">' . __('Digite a sua senha atual', 'gpd') . '</label>';
    $output .= '<input id="current_password" class="form-control" type="password" name="current_password" placeholder="' . __('Digite a sua senha atual', 'gpd') . '" ";required>';
    $output .= '                </div>';
    $output .= '                <!-- /.form-group -->';

    $output .= '                <div class="form-group">';
    $output .= '                    <label for="new_password">' . __('Digite a nova senha', 'gpd') . '</label>';
    $output .= '                    <input id="new_password" class="form-control" type="password" name="new_password" placeholder="' . __('Digite a nova senha', 'gpd') . '" required>';
    $output .= '                </div>';
    $output .= '                <!-- /.form-group -->';

    $output .= '                <div class="form-group">';
    $output .= '                    <label for="confirm_new_password">' . __('Confirme a nova senha', 'gpd') . '</label>';
    $output .= '                    <input id="confirm_new_password" class="form-control" type="password" name="confirm_new_password" placeholder="' . __('Confirme ";a nova senha', 'gpd') . '" required>';
    $output .= '                </div>';
    $output .= '                <!-- /.form-group -->';
    $output .= '<button class="gpd-btn" type="submit">' . __('Alterar a senha', 'gpd') . '</button>';
    $output .= '            </form>';
    $output .= '        </div>';
    $output .= '        <!-- /.col-md-6 -->';
    $output .= '    </div>';
    $output .= '    <!-- /.row -->';
    $output .= '    <div class="clearfix"></div>';
    return $output;
}

function gpd_change_password()
{
    if (isset($_POST['current_password'])) {
        $output = '';
        $_POST = array_map('stripslashes_deep', $_POST);
        $current_password = sanitize_text_field($_POST['current_password']);
        $new_password = sanitize_text_field($_POST['new_password']);
        $confirm_new_password = sanitize_text_field($_POST['confirm_new_password']);
        $user_id = get_current_user_id();
        $errors = array();
        $current_user = get_user_by('id', $user_id);
        // Check for errors

        $gpd_nonce = isset($_POST['_wpnonce']) ? wp_verify_nonce($_POST['_wpnonce'], 'user_id' . $user_id) : null;
        if (!$gpd_nonce)
            $gpd_error_msg['wp_nonce'] = __('Não foi possível validar a sua requisição.', 'gpd');

        if (empty($current_password) && empty($new_password) && empty($confirm_new_password)) {
            $errors[] = _('Todos os campos são obrigatórios', 'gpd');
        }
        if ($current_user && wp_check_password($current_password, $current_user->data->user_pass, $current_user->ID)) {
            //match
        } else {
            $errors[] = __('Senha atual incorreta', 'gpd');
        }
        if ($new_password != $confirm_new_password) {
            $errors[] = __('Nova senha não combina com a confirmação', 'gpd');
        }
        if (strlen($new_password) < 6) {
            $errors[] = __('Senha muito curta, mínimo de 6 caracteres', 'gpd');
        }
        if (empty($errors)) {
            wp_set_password($new_password, $current_user->ID);
            $output .= '<div class="alert alert-success">';
            $output .= '<h3 style="margin-top: 0; margin-bottom: 0;">' . __('Senha alterada com sucesso!', 'gpd') . '</h3>';
            $output .= '</div>';
        } else {
            // Echo Errors
            $output .= '<div class="alert alert-danger">';
            $output .= '<h3 style="margin-top: 0;">' . __('Erro(s)', 'gpd') . ':</h3>';
            foreach ($errors as $error) {
                $output .= '<ul>';
                $output .= '<li>' . $error . '</li>';
                $output .= '</ul>';
            }
            $output .= '</div>';
        }
        return $output;
    }
}

function gpd_form_shortcode()
{
    $output = '';
    $output .= gpd_change_password();
    $output .= gpd_change_password_form();
    return $output;
}
add_shortcode('changepassword_form', 'gpd_form_shortcode');

function gpd_user_saldo_header()
{
    $gpd_user_id = get_current_user_id();
    if (!$gpd_user_id)
        return;
    global $post;
    $gpd_title = get_the_title($post->ID);
    $output = '';
    $output .= '<div class="header-title">';
    $output .= '<h3>';
    $output .= '<span class="header-stretched-text">' . $gpd_title . '</span>';
    if (function_exists('gdp_current_balance')) {
        // $manage_users = current_user_can('edit_users');
        // if ($manage_users && isset($_GET['gpd_user_id']) && !empty($_GET['gpd_user_id'])) {
        //     $gpd_user_id = $_GET['gpd_user_id'];
        //     $gpd_user_data = get_userdata($gpd_user_id);
        //     $output .= $gpd_user_data->display_name . ': ';
        // }
        // gpd_debug($gpd_user_id);
        $output .= '<span class="current-saldo-message">';
        $output .= '<figure class="icon icon-moeda"><img src="' . get_template_directory_uri() . '/assets/images/moeda.png" /></figure>';
        $output .= '$' . gdp_current_balance($gpd_user_id);
        $output .= '</span> ';
    }
    $output .= '</h3>';
    $output .= '</div>';
    return $output;
}

function gpd_user_lancamentos_futuros_table()
{
    $gpd_user_id = get_current_user_id();
    if (!$gpd_user_id)
        return;
    $manage_users = current_user_can('edit_users');
    if ($manage_users && isset($_GET['gpd_user_id']) && !empty($_GET['gpd_user_id'])) {
        $gpd_user_id = $_GET['gpd_user_id'];
    }

    if (!$manage_users)
        return;

    $args = array(
        'numberposts' => -1,
        'post_type' => 'log-transacao',
        'meta_query' => array(
            array(
                'key'     => 'gpd_log_transacao_user_id',
                'value'   => $gpd_user_id,
            ),
        ),
        'fields' => 'ids',
        'post_status'   => 'future'
    );
    $log_transacoes = get_posts($args);
    $output = '<h4 class="table-title"><figure class="icon"><img src="' . get_template_directory_uri() . '/assets/images/transacoes.png" /></figure>' . __('Lançamentos Futuros', 'gpd');
    $manage_users = current_user_can('edit_users');
    if ($manage_users && isset($_GET['gpd_user_id']) && !empty($_GET['gpd_user_id'])) {
        $gpd_user_id = $_GET['gpd_user_id'];
        $gpd_user_data = get_userdata($gpd_user_id);
        $output .= ': ' . $gpd_user_data->display_name;
    }

    $output .= '</h4>';

    $output .= '<div class="table-responsive" style="margin-bottom: 80px;">';
    $output .= '<table id="gpd-table-saldo" class="table gpd-table gpd-table-saldo table-condensed">';
    $output .= '<thead>';
    $output .= '<tr>';
    $output .= '<th><span class="th-title">' . __('Código', 'gpd') . '</span></th>';
    $output .= '<th><span class="th-title">' . __('Data', 'gpd') . '</span></th>';
    $output .= '<th><span class="th-title">' . __('Tipo', 'gpd') . '</span></th>';
    $output .= '<th><span class="th-title">' . __('Descrição', 'gpd') . '</span></th>';
    $output .= '<th><span class="th-title">' . __('Valor', 'gpd') . '</span></th>';
    $output .= '<th></th>';
    // $output .= '<th>' . __('Responsável', 'gpd') . '</th>';
    $output .= '</tr>';
    $output .= '</thead>';
    $output .= '<tbody>';
    foreach ($log_transacoes as $log_transacao_id) {

        $gpd_log_transacao_qtd = get_post_meta($log_transacao_id, 'gpd_log_transacao_qtd', true);

        $gpd_log_transacao_acao = get_post_meta($log_transacao_id, 'gpd_log_transacao_acao', true);
        $gpd_log_transacao_acao_css_class = $gpd_log_transacao_acao == 'add' ? 'label label-success' : 'label label-danger';
        $gpd_log_transacao_acao_exibe = $gpd_log_transacao_acao == 'add' ? '+' : '-';

        $gpd_log_transacao_tipo = get_the_terms($log_transacao_id, 'tipo');
        $gpd_log_transacao_tipo_exibe = '-';
        if ($gpd_log_transacao_tipo) {
            if (is_array($gpd_log_transacao_tipo)) {
                $gpd_log_transacao_tipo_exibe = $gpd_log_transacao_tipo[0]->name;
            } else {
                $gpd_log_transacao_tipo_exibe = $gpd_log_transacao_tipo;
            }
        }
        $gpd_log_transacao_descricao = get_post_meta($log_transacao_id, 'gpd_log_transacao_descricao', true);
        $gpd_log_transacao_descricao = $gpd_log_transacao_descricao ? $gpd_log_transacao_descricao : '-';

        $gpd_log_transacao_responsavel_id = get_post_meta($log_transacao_id, 'gpd_log_transacao_responsavel_id', true);
        $gpd_log_transacao_responsavel_exibe = '-';
        if ($gpd_log_transacao_responsavel_id) {
            $gpd_log_transacao_responsavel_data = get_userdata($gpd_log_transacao_responsavel_id);
            $gpd_log_transacao_responsavel_exibe = $gpd_log_transacao_responsavel_data->display_name;
        }

        $output .= '<tr class="action-' . $gpd_log_transacao_acao . '">';

        $output .= '<td><strong>' . $log_transacao_id . '</strong></td>';
        $output .= '<td>' . get_the_date('d/m/y H\h:i\m', $log_transacao_id) . '</td>';
        $output .= '<td>' . $gpd_log_transacao_tipo_exibe . '</td>';
        $output .= '<td>' . $gpd_log_transacao_descricao . '</td>';
        $output .= '<td><span class="' . $gpd_log_transacao_acao_css_class . '">' . $gpd_log_transacao_acao_exibe . $gpd_log_transacao_qtd . '</span></td>';
        $output .= '<td><a class="text-danger" href="' . get_delete_post_link($log_transacao_id, '', true) . '"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
</td>';
        // $output .= '<td>' . $gpd_log_transacao_responsavel_exibe . '</td>';
        // $output .= '<td>' . get_the_date(sprintf('d/m/Y, %s H:i', __('à\s', 'gpd')), $log_transacao_id) . '</td>';

        $output .= '</tr>';
    }
    $output .= '</tbody>';
    $output .= '</table>';
    $output .= '</div>';
    return $output;
}

function gpd_user_saldo_table()
{
    $gpd_user_id = get_current_user_id();
    if (!$gpd_user_id)
        return;
    $manage_users = current_user_can('edit_users');
    if ($manage_users && isset($_GET['gpd_user_id']) && !empty($_GET['gpd_user_id'])) {
        $gpd_user_id = $_GET['gpd_user_id'];
    }
    $args = array(
        'numberposts' => -1,
        'post_type' => 'log-transacao',
        'meta_query' => array(
            array(
                'key'     => 'gpd_log_transacao_user_id',
                'value'   => $gpd_user_id,
            ),
        ),
        'fields' => 'ids'
    );
    $log_transacoes = get_posts($args);
    $output = '<h4 class="table-title"><figure class="icon"><img src="' . get_template_directory_uri() . '/assets/images/transacoes.png" /></figure>' . __('Transações', 'gpd');
    $manage_users = current_user_can('edit_users');
    if ($manage_users && isset($_GET['gpd_user_id']) && !empty($_GET['gpd_user_id'])) {
        $gpd_user_id = $_GET['gpd_user_id'];
        $gpd_user_data = get_userdata($gpd_user_id);
        $output .= ': ' . $gpd_user_data->display_name;
    }
    $output .= '</h4>';

    $output .= '<div class="table-responsive">';
    $output .= '<table id="gpd-table-saldo" class="table gpd-table gpd-table-saldo table-condensed">';
    $output .= '<thead>';
    $output .= '<tr>';
    $output .= '<th><span class="th-title">' . __('Código', 'gpd') . '</span></th>';
    $output .= '<th><span class="th-title">' . __('Data', 'gpd') . '</span></th>';
    $output .= '<th><span class="th-title">' . __('Tipo', 'gpd') . '</span></th>';
    $output .= '<th><span class="th-title">' . __('Descrição', 'gpd') . '</span></th>';
    $output .= '<th><span class="th-title">' . __('Valor', 'gpd') . '</span></th>';
    // $output .= '<th>' . __('Responsável', 'gpd') . '</th>';
    $output .= '</tr>';
    $output .= '</thead>';
    $output .= '<tbody>';
    foreach ($log_transacoes as $log_transacao_id) {

        $gpd_log_transacao_qtd = get_post_meta($log_transacao_id, 'gpd_log_transacao_qtd', true);

        $gpd_log_transacao_acao = get_post_meta($log_transacao_id, 'gpd_log_transacao_acao', true);
        $gpd_log_transacao_acao_css_class = $gpd_log_transacao_acao == 'add' ? 'label label-success' : 'label label-danger';
        $gpd_log_transacao_acao_exibe = $gpd_log_transacao_acao == 'add' ? '+' : '-';

        $gpd_log_transacao_tipo = get_the_terms($log_transacao_id, 'tipo');
        $gpd_log_transacao_tipo_exibe = '-';
        if ($gpd_log_transacao_tipo) {
            if (is_array($gpd_log_transacao_tipo)) {
                $gpd_log_transacao_tipo_exibe = $gpd_log_transacao_tipo[0]->name;
            } else {
                $gpd_log_transacao_tipo_exibe = $gpd_log_transacao_tipo;
            }
        }
        $gpd_log_transacao_descricao = get_post_meta($log_transacao_id, 'gpd_log_transacao_descricao', true);
        $gpd_log_transacao_descricao = $gpd_log_transacao_descricao ? $gpd_log_transacao_descricao : '-';

        $gpd_log_transacao_responsavel_id = get_post_meta($log_transacao_id, 'gpd_log_transacao_responsavel_id', true);
        $gpd_log_transacao_responsavel_exibe = '-';
        if ($gpd_log_transacao_responsavel_id) {
            $gpd_log_transacao_responsavel_data = get_userdata($gpd_log_transacao_responsavel_id);
            $gpd_log_transacao_responsavel_exibe = $gpd_log_transacao_responsavel_data->display_name;
        }

        $output .= '<tr class="action-' . $gpd_log_transacao_acao . '">';

        $output .= '<td><strong>' . $log_transacao_id . '</strong></td>';
        $output .= '<td>' . get_the_date(null, $log_transacao_id) . '</td>';
        $output .= '<td>' . $gpd_log_transacao_tipo_exibe . '</td>';
        $output .= '<td>' . $gpd_log_transacao_descricao . '</td>';
        $output .= '<td><span class="' . $gpd_log_transacao_acao_css_class . '">' . $gpd_log_transacao_acao_exibe . $gpd_log_transacao_qtd . '</span></td>';
        // $output .= '<td>' . $gpd_log_transacao_responsavel_exibe . '</td>';
        // $output .= '<td>' . get_the_date(sprintf('d/m/Y, %s H:i', __('à\s', 'gpd')), $log_transacao_id) . '</td>';

        $output .= '</tr>';
    }
    $output .= '</tbody>';
    $output .= '</table>';
    $output .= '</div>';
    return $output;
}

add_shortcode('saldo', 'gpd_user_saldo_view');

function gpd_user_saldo_view()
{
    $output = '';
    // $output .= gpd_user_saldo_header();
    $output .= gpd_user_lancamentos_futuros_table();
    $output .= gpd_user_saldo_table();
    return $output;
}
