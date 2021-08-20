<?php
// Custom Actions
// Exibição de dados/form da recompensa

// Ação para exibir o preço da recompensa
add_action('gpd_recompensa_preco', 'gpd_exibe_recompensa_preco', 10, 1);

function gpd_exibe_recompensa_preco($gpd_recompensa_id)
{
    global $gpd_moeda;
    $post_type =  get_post_type($gpd_recompensa_id);
    if ($post_type !== 'recompensas')
        return;

    $gpd_recompensa_preco = intval(get_post_meta($gpd_recompensa_id, 'gpd_recompensa_preco', true));
    $output = '<div class="gpd-recompensa-preco">$' . $gpd_recompensa_preco . ' ' . ($gpd_recompensa_preco > 1 ? $gpd_moeda->nome_plural : $gpd_moeda->nome) . '</div>';
    echo $output;
}

// Ação para exibir a data de validade da recompensa
add_action('gpd_recompensa_validade', 'gpd_exibe_recompensa_validade', 10, 1);

function gpd_exibe_recompensa_validade($gpd_recompensa_id)
{
    $post_type =  get_post_type($gpd_recompensa_id);
    if ($post_type !== 'recompensas')
        return;

    $gpd_recompensa_validade = get_post_meta($gpd_recompensa_id, 'gpd_recompensa_validade', true);
    $gpd_formated_date = strtotime($gpd_recompensa_validade);
    $output = '<div class="gpd-recompensa-validade">Válido até ' . date('d/m/Y', $gpd_formated_date) . '</div>';
    echo $output;
}


// Ação para exibir o formulário de resgate da recompensa (botão resgatar recompensa)
add_action('gpd_resgatar_recompensa', 'gpd_form_resgatar_recompensa', 10, 1);

function gpd_form_resgatar_recompensa($gpd_recompensa_id)
{
    $post_type =  get_post_type($gpd_recompensa_id);
    if ($post_type !== 'recompensas')
        return;

    global $gpd_moeda;
    $gpd_recompensa_preco = intval(get_post_meta($gpd_recompensa_id, 'gpd_recompensa_preco', true));
    $gpd_user_id = get_current_user_id();
    if ($gpd_user_id) { ?>
        <?php
        $gpd_user_saldo = intval(get_user_meta($gpd_user_id, 'gpd_user_saldo', true));
        if ($gpd_user_saldo >= $gpd_recompensa_preco) { ?>
            <form action="" method="post" onsubmit="return gpd_confirm_resgate_request(this);">
                <?php wp_nonce_field('gpd-resgatar-recompensa_' . $gpd_user_id); ?>
                <input type="hidden" name="gpd_step_resgate" value="true">
                <input type="hidden" name="gpd_recompensa_preco" value="<?php echo $gpd_recompensa_preco; ?>">
                <input type="hidden" name="gpd_recompensa_id" value="<?php echo $gpd_recompensa_id; ?>">
                <button class="gpd-btn gpd-icon icon-resgatar"><?php _e('Resgatar', 'gpd'); ?></button>
            </form>
            <script>
                function gpd_confirm_resgate_request() {
                    return confirm("<?php _e('Tem certeza que deseja resgatar esta recompensa? O valor da recompensa será abatido do seu saldo.', 'gpd'); ?>");
                }
            </script>
        <?php } else { ?>
            <div class="bg-danger laf-message"><?php printf(__('Você não possui <strong>%s</strong> suficientes para resgatar esta recompensa. Seu saldo atual é de $<strong>%s</strong> %s', 'gpd'), $gpd_moeda->nome_plural, $gpd_user_saldo, ($gpd_user_saldo > 1) ? $gpd_moeda->nome_plural : $gpd_moeda->nome); ?></div>
        <?php } ?>
<?php }
}

// Ação para processar o resgate da recompensa
add_action('wp_head', 'gpd_processar_resgate_recompensa');

function gpd_processar_resgate_recompensa()
{
    global $gpd_moeda;
        // Verifica se o form foi disparado
    if (!isset($_POST['gpd_step_resgate']) || $_POST['gpd_step_resgate'] !== 'true')
        return;

    // Validações
    $gpd_error_msg = [];

    $gpd_user_id = get_current_user_id();
    if (!$gpd_user_id)
        $gpd_error_msg['user_id'] = __('É necessário estar logado para resgatar uma recompensa', 'gpd');

    $gpd_nonce = isset($_POST['_wpnonce']) ? wp_verify_nonce($_POST['_wpnonce'], 'gpd-resgatar-recompensa_' . $gpd_user_id) : null;
    if (!$gpd_nonce)
        $gpd_error_msg['wp_nonce'] = __('Não foi possível validar a sua requisição.', 'gpd');

    if (!is_single() && get_post_type() !== 'recompensas')
        $gpd_error_msg['wrong_pange'] = __('Esta ação não pode ser processada a partir desta tela.', 'gpd');

    $gpd_recompensa_id = isset($_POST['gpd_recompensa_id']) ? intval($_POST['gpd_recompensa_id']) : null;
    $gpd_check_recompensa_id = get_the_ID();
    if ($gpd_check_recompensa_id !== $gpd_recompensa_id)
        $gpd_error_msg['recompensa_id'] = __('ID da recompensa não confere.', 'gpd');

    $gpd_recompensa_preco = isset($_POST['gpd_recompensa_preco']) ? intval($_POST['gpd_recompensa_preco']) : null;
    $gpd_check_recompensa_preco = intval(get_post_meta($gpd_recompensa_id, 'gpd_recompensa_preco', true));
    if ($gpd_recompensa_preco !== $gpd_check_recompensa_preco)
        $gpd_error_msg['preco_id'] = __('Preço da recompensa não confere.', 'gpd');

    $gpd_user_saldo = intval(get_user_meta($gpd_user_id, 'gpd_user_saldo', true));
    if ($gpd_user_saldo < $gpd_recompensa_preco)
        $gpd_error_msg['saldo'] = sprintf(__('Você não possui %s suficientes para resgatar esta recompensa. Seu saldo atual é de $ <strong>%s</strong>', 'gpd'), $gpd_moeda->nome_plural, $gpd_user_saldo);

    if (count($gpd_error_msg) > 0) {
        $output = '';
        $output .= '<ul>';
        foreach ($gpd_error_msg as $error_msg) {
            $output .= '<li>' . $error_msg . '</li>';
        }
        $output .= '</ul>';
        echo $output;
        return;
    }

    // A partir deste ponto, a requisição passou por todas as validações
    $gpd_user_data = get_userdata($gpd_user_id);
    $transacao_arr = array(
        'post_title'   => sprintf(__('Resgate da recompensa %s pelo usuário %s.', 'gpd'), get_the_title($gpd_recompensa_id), $gpd_user_data->display_name),
        'post_content' => sprintf(__('Resgate da recompensa %s pelo usuário %s.', 'gpd'), get_the_title($gpd_recompensa_id), $gpd_user_data->display_name),
        'post_status'  => 'publish',
        'post_author'  => get_current_user_id(),
        'post_type'     => 'log-transacao',
        'meta_input'   => array(
            'gpd_log_transacao_user_id' => $gpd_user_id,
            'gpd_log_transacao_qtd' => $gpd_recompensa_preco,
            'gpd_log_transacao_acao' => 'remove',
            'gpd_log_transacao_descricao' => sprintf(__('Resgate da recompensa %s.', 'gpd'), get_the_title($gpd_recompensa_id)),
            'gpd_log_transacao_responsavel_id' => $gpd_user_id,
        ),
    );

    // Cria um novo registro no log-transacao
    $new_log_transacao_id = wp_insert_post($transacao_arr, true);
    // verifica se ocorreu algum erro
    if (is_wp_error($new_log_transacao_id)) {
        gpd_debug($new_log_transacao_id->get_error_message());
    } else {
        // atribui o tipo 'Resgate de Recompensa' para o registro criado
        $gpd_tipo_term = wp_set_post_terms($new_log_transacao_id, array('Resgate de Recompensa'), 'tipo');
        // verifica se ocorreu algum erro
        if (is_wp_error($gpd_tipo_term)) {
            gpd_debug($gpd_tipo_term->get_error_message());
        } else {
            // Resgate realizado com sucesso!
            // Necessário criar um "resgate"
            // Redirecionar para tela de confirmação
            // A confirmação por parte do usuário é feita via JS, assim evita que o usuário resgate a mesma recompensa mais de uma vez ao usar o botão salvar
            // testar se está funcionando a verificação e o redirecionamento para a tela de confirmação
            $resgate_arr = array(
                'post_title'   => sprintf(__('Resgate da recompensa %s pelo usuário %s.', 'gpd'), get_the_title($gpd_recompensa_id), $gpd_user_data->display_name),
                'post_content' => sprintf(__('Resgate da recompensa %s pelo usuário %s.', 'gpd'), get_the_title($gpd_recompensa_id), $gpd_user_data->display_name),
                'post_status'  => 'publish',
                'post_author'  => get_current_user_id(),
                'post_type'     => 'resgate',
                'meta_input'   => array(
                    'gpd_resgate_user_id' => $gpd_user_id,
                    'gpd_resgate_valor' => $gpd_recompensa_preco,
                    'gpd_recompensa_id' => $gpd_recompensa_id,
                ),
            );
            $new_resgate_id = wp_insert_post($resgate_arr, true);
            if (is_wp_error($new_resgate_id)) {
                gpd_debug($new_resgate_id->get_error_message());
            } else {
                // Notifica o usuário sobre o resgate
                $site_name = get_bloginfo();
                $to = $gpd_user_data->user_email;
                $subject = $site_name . ' | ' . sprintf(__('Resgate da recompensa "%s"', 'gpd'), get_the_title($gpd_recompensa_id));
                $body = '';
                $body .= '<h3>' . sprintf(__('Olá, %s!', 'gpd'), $gpd_user_data->display_name) . '</h3>';
                $body .= '<p>' . sprintf(__('Recebemos o seu pedido de resgate da recompensa "%s", no valor de %s %s.', 'gpd'), get_the_title($gpd_recompensa_id), $gpd_recompensa_preco, $gpd_moeda->nome_plural) . ' ';
                $body .= sprintf(__('O código do seu resgate é "%s".', 'gpd'), $new_resgate_id) . '</p>';
                $body .= '<p>' . __('Obrigado!', 'gpd') . '</p>';
                $headers = array(
                    'Content-Type: text/html; charset=UTF-8',
                    'From: ' . $site_name . '<noreply@' . $_SERVER['HTTP_HOST'] . '>',
                    'Cc:',
                    'Bcc: ingo@agencialaf.com'
                );
                wp_mail($to, $subject, $body, $headers);

                // Notifica o admin sobre o resgate
                $admin_emails = gpd_get_notifications_option('gpd_notifications_email');
                if ($admin_emails) {
                    $to = implode(',', $admin_emails);
                    $subject = $site_name . ' | ' . sprintf(__('Novo Resgate #"%s"', 'gpd'), $new_resgate_id);
                    $body = '';
                    $body .= '<h3>' . sprintf(__('Resgate #%s!', 'gpd'), $new_resgate_id) . '</h3>';
                    $body .= '<p>' . sprintf(__('Um resgate da recompensa "%s", no valor de %s %s, foi feito pelo usuário %s.', 'gpd'), get_the_title($gpd_recompensa_id), $gpd_recompensa_preco, $gpd_moeda->nome_plural, $gpd_user_data->display_name) . ' ';
                    $headers = array(
                        'Content-Type: text/html; charset=UTF-8',
                        'From: ' . $site_name . '<noreply@' . $_SERVER['HTTP_HOST'] . '>',
                        'Cc:',
                        'Bcc: ingo@agencialaf.com'
                    );
                    wp_mail($to, $subject, $body, $headers);
                }

                $gpd_order_confirmation_page = gpd_get_option('gpd_order_confirmation_page');
                if (!$gpd_order_confirmation_page)
                    gpd_debug('Página de confirmação de resgate não definida, não foi possível redirecionar o usuário.');

                $gpd_order_confirmation_page_url = get_permalink($gpd_order_confirmation_page);
                $output = '';
                $output .= '<script>';
                $output .= 'window.location.href="' . $gpd_order_confirmation_page_url . '?resgate_id=' . $new_resgate_id . '";';
                $output .= '</script>';
                echo $output;
            }
        }
    }
}
