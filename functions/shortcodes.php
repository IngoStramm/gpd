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

add_shortcode('gpd-add-bulk-points', 'gpd_add_bulk_points_page_form_output');

function gpd_add_bulk_points_page_form_output()
{
    global $gpd_moeda;
    $output = '';
    $output .= '<form class="form-horizontal" id="form-add-bulk-points" method="post">';

    $output .=
        '<div class="form-group">
            <label class="col-sm-6 control-label">' . __('Quando os pontos devem entrar em vigor?', 'gpd') . '</label>
            <div class="col-sm-6">
                <div class="radio">
                    <label for="gpd_log_transacao_status1">
                        <input type="radio" name="gpd_log_transacao_status" id="gpd_log_transacao_status1" value="publish" checked required>' . __('Imediatamente', 'gpd') . '
                    </label>
                </div>
                <div class="radio">
                    <label for="gpd_log_transacao_status2">
                        <input type="radio" name="gpd_log_transacao_status" id="gpd_log_transacao_status2" value="future" required>' . __('Agendar', 'gpd') . '
                    </label>
                </div>
            </div>
        </div>';

    $output .=
        '<div class="form-group gpd-agendamento-group">
            <label for="jj" class="col-sm-6 control-label">' . __('Dia', 'gpd') . '</label>
            <div class="col-sm-3">
                <input type="number" class="form-control" name="jj" id="jj" maxlength="2" min="1" max="31" value="' . wp_date('d') . '">
            </div>
        </div>';

    $output .=
        '<div class="form-group gpd-agendamento-group">
            <label for="mm" class="col-sm-6 control-label">' . __('Mês', 'gpd') . '</label>
            <div class="col-sm-3">
                <select class="form-control" name="mm" id="mm">';

    $gpd_meses_array = array(
        '01' => __('01-jan', 'gpd'),
        '02' => __('02-fev', 'gpd'),
        '03' => __('03-mar', 'gpd'),
        '04' => __('04-abr', 'gpd'),
        '05' => __('05-maio', 'gpd'),
        '06' => __('06-jun', 'gpd'),
        '07' => __('07-jul', 'gpd'),
        '08' => __('08-ago', 'gpd'),
        '09' => __('09-set', 'gpd'),
        '10' => __('10-out', 'gpd'),
        '11' => __('11-nov', 'gpd'),
        '12' => __('12-dez', 'gpd')
    );

    foreach ($gpd_meses_array as $m => $gpd_mes) {
        $output .= '<option value="' . $m . '" ' . ($m == wp_date('m') ? 'selected' : '') . '>' . $gpd_mes . '</option>';
    }

    $output .=
        '           </select>
            </div>
        </div>';

    $output .=
        '<div class="form-group gpd-agendamento-group">
            <label for="aa" class="col-sm-6 control-label">' . __('Ano', 'gpd') . '</label>
            <div class="col-sm-3">
                <input type="number" class="form-control" name="aa" id="aa" maxlength="4" min="' . wp_date('Y') . '" max="' . (5 + wp_date('Y')) . '" value="' . wp_date('Y') . '">
            </div>
        </div>';

    $output .=
        '<div class="form-group gpd-agendamento-group">
            <label for="hh" class="col-sm-6 control-label">' . __('Hora', 'gpd') . '</label>
            <div class="col-sm-3">
                <input type="number" class="form-control" name="hh" id="hh" maxlength="2" min="0" max="23" value="' . wp_date('H') . '">
            </div>
        </div>';

    $output .=
        '<div class="form-group gpd-agendamento-group">
            <label for="mn" class="col-sm-6 control-label">' . __('Minuto', 'gpd') . '</label>
            <div class="col-sm-3">
                <input type="number" class="form-control" name="mn" id="mn" maxlength="2" min="0" max="59" value="' . wp_date('i') . '">
            </div>
        </div>';

    $output .=
        '<div class="form-group">
            <label for="gpd_log_transacao_qtd" class="col-sm-6 control-label">' . sprintf(__('Quantidade de %s', 'gpd'), $gpd_moeda->nome_plural) . '</label>
            <div class="col-sm-3">
                <input type="number" class="form-control" id="gpd_log_transacao_qtd" name="gpd_log_transacao_qtd" placeholder="0" min="0" required>
            </div>
        </div>';

    $output .=
        '<div class="form-group">
            <label class="col-sm-6 control-label">' . __('Ação da movimentação', 'gpd') . '</label>
            <div class="col-sm-6">
                <div class="radio">
                    <label for="gpd_log_transacao_acao1">
                        <input type="radio" name="gpd_log_transacao_acao" id="gpd_log_transacao_acao1" value="add" required>' . __('Adicionar', 'gpd') . '
                    </label>
                </div>
                <div class="radio">
                    <label for="gpd_log_transacao_acao2">
                        <input type="radio" name="gpd_log_transacao_acao" id="gpd_log_transacao_acao2" value="remove" required>' . __('Remover', 'gpd') . '
                    </label>
                </div>
            </div>
        </div>';

    $tipo_resgate_recompensa_object = get_term_by('name', 'Resgate de Recompensa', 'tipo');
    $gpd_tipos = get_terms(array(
        'taxonomy' => 'tipo',
        'hide_empty' => true,
        'exclude' => $tipo_resgate_recompensa_object->term_id
    ));

    if ($gpd_tipos) {
        $output .=
            '<div class="form-group">
                <label class="col-sm-6 control-label">' . __('Tipo', 'gpd') . '</label>
                <div class="col-sm-6">';
        $count = 1;
        foreach ($gpd_tipos as $gpd_tipo) {
            $output .=
                '<div class="radio">
                        <label for="gpd_log_transacao_tipo' . $count . '">
                            <input type="radio" name="gpd_log_transacao_tipo" id="gpd_log_transacao_tipo' . $count . '" value="' . $gpd_tipo->slug . '" required>' . $gpd_tipo->name . '
                        </label>
                    </div>';
            $count++;
        }
        $output .=
            '</div>
            </div>';
    }

    $output .=
        '<div class="form-group">
            <label for="gpd_log_transacao_descricao" class="col-sm-6 control-label">' . __('Descrição', 'gpd') . '</label>
            <div class="col-sm-6">
                <textarea class="form-control" id="gpd_log_transacao_descricao" name="gpd_log_transacao_descricao" rows="4" required></textarea>
            </div>
        </div>';

    $output .=
        '<div class="form-group">
            <label for="gpd_log_transacao_users_id" class="col-sm-6 control-label">' . __('Usuários selecionados', 'gpd') . ':</label>
            <div class="col-sm-6">
                <p class="form-control-static"><span id="gpd_log_transacao_selected_users_label" class="label label-danger">0</span></p>
                <input type="text" class="form-control hidden" name="gpd_log_transacao_users_id" id="gpd_log_transacao_users_id" required />
            </div>
        </div>';

    $output .= wp_nonce_field('gpd_add_bulk_points_form_action', 'gpd_add_bulk_points_form_nonce_field');

    $output .=
        '<div class="form-group">
            <div class="col-sm-offset-6 col-sm-6">
                <button type="submit" id="gpd-add-bulk-points-form-btn" class="btn btn-primary">
                    <span class="btn-text">' . __('Processar', 'gpd') . '</span>
                    <span class="spinner btn-spinner">
                        <span class="bounce1"></span>
                        <span class="bounce2"></span>
                        <span class="bounce3"></span>
                    </span>
                </button>
            </div>
        </div>';

    $output .= '</form><!-- ./form-horizontal -->';

    $output .= '<div class="clearfix" style="margin-bottom: 40px;"></div>';

    $output .= '<h4 class="table-title"><figure class="icon"><img src="' . get_template_directory_uri() . '/assets/images/transacoes.png" /></figure>' . __('Selecione os usuários', 'gpd') . '</h4>';

    $output .= '<div class="table-responsive">';
    $output .= '<table id="gpd-table-saldo" class="table gpd-table gpd-table-saldo table-condensed">';
    $output .= '<thead>';
    $output .= '<tr>';
    $output .= '<th style="text-align: center;"><div class="checkbox"><label><input class="gpd-toggle-users" type="checkbox" data-toggle-target="gpd-select-users" /></label></div></th>';
    $output .= '<th><span class="th-title">' . __('Usuário', 'gpd') . '</span></th>';
    $output .= '<th><span class="th-title">' . __('Cargo', 'gpd') . '</span></th>';
    // $output .= '<th><span class="th-title">' . __('Tipo', 'gpd') . '</span></th>';
    // $output .= '<th><span class="th-title">' . __('Descrição', 'gpd') . '</span></th>';
    // $output .= '<th><span class="th-title">' . __('Valor', 'gpd') . '</span></th>';
    // $output .= '<th>' . __('Responsável', 'gpd') . '</th>';
    $output .= '</tr>';
    $output .= '</thead>';
    $output .= '<tbody>';
    $args = array(
        'role'    => 'subscriber',
        'orderby' => 'user_nicename',
        'order'   => 'ASC'
    );
    $users = get_users($args);
    // gpd_debug($users);

    foreach ($users as $user) {
        $show_name = isset($user->display_name) && !empty($user->display_name) ? $user->display_name : $user->user_email;
        $gpd_cargos = get_terms('cargos', array('hide_empty' => false));
        $gpd_current_cargo = null;
        if ($gpd_cargos) {
            foreach ($gpd_cargos as $gpd_cargo) {
                if (is_object_in_term($user->ID, 'cargos', $gpd_cargo->slug)) {
                    // gpd_debug($gpd_cargo);
                    $gpd_current_cargo = $gpd_cargo->name;
                }
            }
        }
        $output .= '<tr>';
        $output .= '<td><div class="checkbox"><label><input class="gpd-select-users" type="checkbox" name="gpd_selec_users[]" value="' . $user->ID . '" /></label></div></td>';
        // $output .= '<td>' . esc_html($show_name) . '</td>';
        $output .= '<td>' . esc_html($user->user_email) . '</td>';
        $output .= '<td>' . esc_html($gpd_current_cargo) . '</td>';
        $output .= '</tr>';
    }
    $output .= '</tbody>';
    $output .= '</table>';
    $output .= '</div>';
    return $output;
}

add_action('wp_ajax_gpd_add_bulk_points_page', 'gpd_add_bulk_points_page');
add_action('wp_ajax_nopriv_gpd_add_bulk_points_page', 'gpd_add_bulk_points_page');

function gpd_add_bulk_points_page()
{
    if (!current_user_can('edit_others_pages'))
        return;

    global $gpd_moeda;
    // gpd_debug(date('Y-m-d H:i:s', time()));
    if (!isset($_POST) || empty($_POST)) {
        echo 'nenhum POST encontrado';
        wp_die();
    }

    $gpd_log_transacao_status = $_POST['gpd_log_transacao_status'];
    $gpd_log_transacao_qtd = $_POST['gpd_log_transacao_qtd'];
    $gpd_log_transacao_acao = $_POST['gpd_log_transacao_acao'];
    $gpd_log_transacao_tipo = $_POST['gpd_log_transacao_tipo'];
    $gpd_log_transacao_descricao = $_POST['gpd_log_transacao_descricao'];
    $gpd_log_transacao_users_id = $_POST['gpd_log_transacao_users_id'];

    $errors_validation = [];

    // wp_nonce_field('gpd_add_bulk_points_form_action', 'gpd_add_bulk_points_form_nonce_field')
    if (
        !isset($_POST['gpd_add_bulk_points_form_nonce_field'])
        || !wp_verify_nonce($_POST['gpd_add_bulk_points_form_nonce_field'], 'gpd_add_bulk_points_form_action')
    )
        $errors_validation[] = __('Erro de validação do formulário.', 'gpd');

    if ((!isset($gpd_log_transacao_status) || empty($gpd_log_transacao_status)))
        $errors_validation[] = __('Não foi encontrado o status da movimentação.', 'gpd');
    elseif ($gpd_log_transacao_status === 'future') {
        $dia = $_POST['jj'];
        $mes = $_POST['mm'];
        $ano = $_POST['aa'];
        $hora = $_POST['hh'];
        $minuto = $_POST['mn'];

        if ((!isset($dia) || empty($dia)))
            $errors_validation[] = __('Não foi encontrado o dia para o agendamento da movimentação.', 'gpd');

        if ((!isset($mes) || empty($mes)))
            $errors_validation[] = __('Não foi encontrado o ms para o agendamento da movimentação.', 'gpd');

        if ((!isset($ano) || empty($ano)))
            $errors_validation[] = __('Não foi encontrado o ano para o agendamento da movimentação.', 'gpd');

        if ((!isset($hora) || empty($hora)))
            $errors_validation[] = __('Não foi encontrado a hora para o agendamento da movimentação.', 'gpd');

        if ((!isset($minuto) || empty($minuto)))
            $errors_validation[] = __('Não foi encontrado o minuto para o agendamento da movimentação.', 'gpd');
    }

    if ((!isset($gpd_log_transacao_qtd) || empty($gpd_log_transacao_qtd)))
        $errors_validation[] = sprintf(__('Não foi encontrado o valor d%ss %s.', 'gpd'), $gpd_moeda->artigo,  $gpd_moeda->nome_plural);

    if ((!isset($gpd_log_transacao_acao) || empty($gpd_log_transacao_acao)))
        $errors_validation[] = __('Não foi encontrado a ação da movimentação.', 'gpd');

    if ((!isset($gpd_log_transacao_tipo) || empty($gpd_log_transacao_tipo)))
        $errors_validation[] = __('Não foi encontrado o tipo da movimentação.', 'gpd');

    if ((!isset($gpd_log_transacao_descricao) || empty($gpd_log_transacao_descricao)))
        $errors_validation[] = __('Não foi encontrado a descrição da movimentação.', 'gpd');

    if ((!isset($gpd_log_transacao_users_id) || empty($gpd_log_transacao_users_id)))
        $errors_validation[] = __('Não foi encontrado nenhum usuário para a movimentação.', 'gpd');

    if (!is_null($errors_validation) && count($errors_validation) > 0) {
        $output_errors = '';
        $output_errors .= '<div class="errors-list">';
        $output_errors .= '<h3 class="text-danger">' . __('Ocorreram os seguintes erros ao validar o formulário', 'gpd') . '</h3>';
        $output_errors .= '<div class="alert alert-danger" role="alert"><ul>';
        foreach ($errors_validation as $error_validation) {
            $output_errors .= '<li>' . $error_validation . '</li>';
        }
        $output_errors .= '</ul></div>';
        $output_errors .= '</div>';
        echo $output_errors;
        wp_die();
    } else {
        // gpd_debug($gpd_log_transacao_status);
        // gpd_debug($gpd_log_transacao_qtd);
        // gpd_debug($gpd_log_transacao_acao);
        // gpd_debug($gpd_log_transacao_tipo);
        // gpd_debug($gpd_log_transacao_descricao);
        // gpd_debug($gpd_log_transacao_users_id);
        $gpd_user_id = get_current_user_id();
        $args = array(
            'post_author' => $gpd_user_id,
            'post_status' => $gpd_log_transacao_status,
            'post_type' => 'log-transacao',
            'comment_status' => 'closed',
            'ping_status' => 'closed',
            'tax_input' => array(
                'tipo' => $gpd_log_transacao_tipo
            )
        );
        $meta_input = array(
            'gpd_log_transacao_qtd' => $gpd_log_transacao_qtd,
            'gpd_log_transacao_acao' => $gpd_log_transacao_acao,
            'gpd_log_transacao_tipo' => $gpd_log_transacao_tipo,
            'gpd_log_transacao_descricao' => $gpd_log_transacao_descricao,
            'gpd_log_transacao_responsavel_id' => $gpd_user_id
        );
        if ($gpd_log_transacao_status === 'future') {
            // 2021-08-25 19:53:33
            $args['post_date'] = $ano . '-' . $mes . '-' . $dia . ' ' . $hora . ':' . $minuto . ':00';
        }
        $gpd_log_transacao_users_id_arr = explode(',', $gpd_log_transacao_users_id);
        $new_posts = [];

        foreach ($gpd_log_transacao_users_id_arr as $gpd_log_transacao_user_id) {
            $meta_input['gpd_log_transacao_user_id'] = $gpd_log_transacao_user_id;
            $args['meta_input'] = $meta_input;
            $new_posts[$gpd_log_transacao_user_id] = wp_insert_post($args, true);
        }
        $gpd_add_bulk_points_page = gpd_get_option('gpd_add_bulk_points_page');
        // gpd_debug($gpd_add_bulk_points_page);
        $success = [];
        $errors = [];
        foreach ($new_posts as $user_id => $new_post) {
            if (is_wp_error($new_post)) {
                $errors[$user_id] = $new_post->get_error_message;
            } else {
                $success[$user_id] = $new_post;
            }
        }
        $output = '';
        $output .= '<div class="gpd_bulk-points-messages">';
        if (count($success) > 0) {
            $output .= '<h4 class="text-success">' . sprintf(_('%s %s com sucesso'), count($success), (count($success) > 1 ? __('novas transações foram adicionadas', 'gpd') : __('nova transação foi adicionada', 'gpd'))) . '</h4>';
            $output .= '<div class="alert alert-success" role="alert"><ul>';
            foreach ($success as $user_id => $log_transacao_id) {
                $user_data = get_userdata($user_id);
                $output .= '<li>Cód: <span class="label label-success">' . $log_transacao_id . '</span> '  . __('usuário', 'gpd') . ': <a href="' .  get_site_url() . '/?gpd_user_id=' . $user_id . '" target="_blank" class="alert-link">' . $user_data->user_email . '</a></li>';
            }
            $output .= '</ul></div>';
        }
        if (count($errors) > 0) {
            $output .= '<h4 class="text-danger">' . sprintf(_('Ocorreram %s erros ao tentar criar as transações'), count($errors)) . '</h4>';
            $output .= '<div class="alert alert-danger" role="alert"><ul>';
            foreach ($errors as $user_id => $error) {
                $user_data = get_userdata($user_id);
                $output .= '<li>' . __('Erro') . ': <strong>' . $error . '</strong> - ' . __('usuário', 'gpd') . ': <a href="' .  get_site_url() . '/?gpd_user_id=' . $user_id . '" target="_blank" class="alert-link">' . $user_data->user_email . '</a></li>';
            }
            $output .= '</ul></div>';
        }
        $output .= '<p><a class="btn btn-primary" href="' . get_permalink($gpd_add_bulk_points_page) . '"/>' . __('Voltar', 'gpd') . '</a></p>';
        $output .= '</div><!-- /.gpd_bulk-points-messages -->';
        echo $output;
        wp_die();
        // adicionar o wp_nonce
        // confirmação js alert (que nem a recompensa)
        // ou fazer a requisição via ajax ou redirecionar para outra página
    }
}
