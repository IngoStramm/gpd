<?php

add_action('cmb2_admin_init', 'gpd_register_log_transacao_metabox');

function gpd_register_log_transacao_metabox()
{
    global $gpd_moeda;
    $log_transacao_cmb = new_cmb2_box(array(
        'id'            => 'gpd_log_transacao_metabox',
        'title'         => esc_html__('Configuração da Transação', 'gpd'),
        'object_types'  => array('log-transacao'), // Post type
        // 'context'    => 'normal',
        'priority'   => 'high',
        // 'show_names' => true, // Show field names on the left
        // 'cmb_styles' => false, // false to disable the CMB stylesheet
        // 'closed'     => true, // true to keep the metabox closed by default
        // 'classes'    => 'extra-class', // Extra cmb2-wrap classes
        // 'classes_cb' => 'gpd_add_some_classes', // Add classes through a callback.
        // 'mb_callback_args' => array( '__block_editor_compatible_meta_box' => false ),
    ));

    $log_transacao_cmb->add_field(array(
        'name' => esc_html__('Usuário', 'gpd'),
        'id'   => 'gpd_log_transacao_show_user_info',
        'type'       => 'title',
        'after_field'  => function () {
            $gpd_user_id = isset($_GET['gpd_user_id']) && !empty($_GET['gpd_user_id']) ? $_GET['gpd_user_id'] : get_post_meta($_GET['post'], 'gpd_log_transacao_user_id', true);

            if (!$gpd_user_id)
                return;
            $gpd_user_data = get_userdata($gpd_user_id);
            $gpd_saldo_atual = get_user_meta($gpd_user_id, 'gpd_user_saldo', true);
            // gpd_debug($gpd_saldo_atual);
            $gpd_saldo_atual = empty($gpd_saldo_atual) ? '0' : $gpd_saldo_atual;

            $output = '<ul>';
            $output .= '<li>' . $gpd_user_data->display_name . '</li>';
            $output .= '<li>Saldo atual: $ <strong>' . $gpd_saldo_atual . '</strong></li>';
            $output .= '</ul>';
            return $output;
        },
        // 'after'        => '<p>Testing <b>"after"</b> parameter</p>',

    ));

    $log_transacao_cmb->add_field(array(
        'name' => esc_html__('ID do Usuário', 'gpd'),
        'id'   => 'gpd_log_transacao_user_id',
        'type'       => 'hidden',
        'default_cb' => function ($field_args, $field) {
            if (isset($_GET['gpd_user_id']) && !empty($_GET['gpd_user_id']))
                return $_GET['gpd_user_id'];
        },

    ));


    $log_transacao_cmb->add_field(array(
        'name'       => esc_html__('Quantidade de pontos', 'gpd'),
        'id'         => 'gpd_log_transacao_qtd',
        'type'       => 'text_small',
        'attributes'    => array(
            'required'  => true,
            'type'      => 'number',
            'min'       => '0'
        ),
        'default'       => 0,
        'before_field'  => '$ '

    ));

    $log_transacao_cmb->add_field(array(
        'name'       => esc_html__('Ação da movimentação', 'gpd'),
        'desc'      => esc_html('selecione se deseja adicionar ou remover pontos do saldo do usuário selecionado.', 'gpd'),
        'id'         => 'gpd_log_transacao_acao',
        'type'       => 'radio',
        'options'          => array(
            'add' => esc_html__('Adicionar', 'cmb2'),
            'remove'   => esc_html__('Remover', 'cmb2'),
        ),
        'attributes'    => array(
            'required'  => true,
        ),

    ));

    $args = [];
    $args['hide_empty'] = false;
    $tipo_resgate_recompensa_object = get_term_by('name', 'Resgate de Recompensa', 'tipo');
    if ($tipo_resgate_recompensa_object)
        $args['exclude'] = $tipo_resgate_recompensa_object->term_id;

    $log_transacao_cmb->add_field(array(
        'name'       => esc_html__('Tipo', 'gpd'),
        'id'         => 'gpd_log_transacao_tipo',
        'type'     => 'taxonomy_radio', // Or `taxonomy_radio_inline`/`taxonomy_radio_hierarchical`
        'taxonomy' => 'tipo', // Taxonomy Slug
        // 'inline'  => true, // Toggles display to inline
        'show_option_none' => false,
        'query_args' => $args,
        'attributes'    => array(
            'required'  => true
        ),
    ));

    $log_transacao_cmb->add_field(array(
        'name'       => esc_html__('Descrição', 'gpd'),
        'id'         => 'gpd_log_transacao_descricao',
        'type'       => 'textarea',
        'attributes'    => array(
            'required'  => true
        ),
    ));

    $log_transacao_cmb->add_field(array(
        'name'       => esc_html__('Responsável', 'gpd'),
        'id'         => 'gpd_log_transacao_responsavel_id',
        'type'       => 'hidden',
        'default'   => get_current_user_id()
    ));

    $log_transacao_cmb->add_field(array(
        'name'       => esc_html__('Responsável', 'gpd'),
        'id'         => 'gpd_log_transacao_responsavel_nome_exbicao',
        'type'       => 'title',
        'after_field'  => function () {
            global $post;
            $gpd_transacao_id = isset($post) && isset($post->ID) ? $post->ID : $_GET['post'];

            if (!$gpd_transacao_id)
                return __('Criando a transação, responsável ainda não definido.', 'gpd');

            if ($post->post_status == 'auto-draft') {
                $gpd_user_data = wp_get_current_user();
                return $gpd_user_data->display_name;
            }

            $gpd_log_transacao_responsavel_id = get_post_meta($gpd_transacao_id, 'gpd_log_transacao_responsavel_id', true);

            if (!$gpd_log_transacao_responsavel_id) {
                return __('ID do responsável não encontrado.', 'gpd');
            }

            $gpd_user_data = get_userdata($gpd_log_transacao_responsavel_id);
            return $gpd_user_data->display_name;
        },
    ));
}
