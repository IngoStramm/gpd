<?php

add_action('cmb2_admin_init', 'gpd_register_resgate_metabox');

function gpd_register_resgate_metabox()
{

    $resgate_cmb = new_cmb2_box(array(
        'id'            => 'gpd_resgate_metabox',
        'title'         => esc_html__('Dados do Resgate', 'gpd'),
        'object_types'  => array('resgate'), // Post type
        // 'context'    => 'normal',
        'priority'   => 'high',
        // 'show_names' => true, // Show field names on the left
        // 'cmb_styles' => false, // false to disable the CMB stylesheet
        // 'closed'     => true, // true to keep the metabox closed by default
        // 'classes'    => 'extra-class', // Extra cmb2-wrap classes
        // 'classes_cb' => 'gpd_add_some_classes', // Add classes through a callback.
        // 'mb_callback_args' => array( '__block_editor_compatible_meta_box' => false ),
    ));

    $resgate_cmb->add_field(array(
        // 'name'       => esc_html__('Valor da Recompensa resgatada', 'gpd'),
        'id'         => 'gpd_resgate_info',
        'type'       => 'title', 'after_field'  => function () {
            global $post;
            $gpd_resgate_id = isset($post) && isset($post->ID) ? $post->ID : $_GET['post'];

            if (!$gpd_resgate_id)
                return __('Não foi possível encontrar o ID do resgate.', 'gpd');

            $gpd_resgate_user_id = get_post_meta($gpd_resgate_id, 'gpd_resgate_user_id', true);

            if (!$gpd_resgate_user_id) {
                return __('ID do usuário não encontrado.', 'gpd');
            }

            $gpd_resgate_valor = get_post_meta($gpd_resgate_id, 'gpd_resgate_valor', true);

            if (!$gpd_resgate_valor) {
                return __('Valor do resgate não encontrado.', 'gpd');
            }

            $gpd_recompensa_id = get_post_meta($gpd_resgate_id, 'gpd_recompensa_id', true);

            if (!$gpd_recompensa_id) {
                return __('ID da recompensa não encontrado.', 'gpd');
            }

            $gpd_user_data = get_userdata($gpd_resgate_user_id);
            $gpd_moeda = new GPD_Moeda;
            $output = '';
            $output .= '<table>';
            // $output .= '<thead><tr>';
            // $output .= '</thead></tr>';
            $output .= '<tr>';
            $output .= '<td style="text-align: right;"><strong>' . __('Usuário', 'gpd') . ':</strong></td>';
            $output .= '<td>' . '<a href="' . admin_url() . 'user-edit.php?user_id=' . $gpd_user_data->ID . '" target="_blank">' . $gpd_user_data->display_name . '</a>' . '</td>';
            $output .= '</tr>';
            $output .= '<tr>';
            $output .= '<td style="text-align: right;"><strong>' . __('Recompensa', 'gpd') . ':</strong></td>';
            $output .= '<td>' . '<a href="' . admin_url() . 'post.php?post=' . $gpd_recompensa_id . '&action=edit" target="_blank">' . get_the_title($gpd_recompensa_id) . '</a>' . '</td>';
            $output .= '</tr>';
            $output .= '<tr>';
            $output .= '<td style="text-align: right;"><strong>' . __('Pontos', 'gpd') . ':</strong></td>';
            $output .= '<td>' . $gpd_resgate_valor . '</td>';
            $output .= '</tr>';
            $output .= '<tr>';
            $output .= '<td style="text-align: right;"><strong>' . __('Data', 'gpd') . ':</strong></td>';
            $output .= '<td>' . date(
                sprintf('d/m/Y, %s H:i', __('à\s', 'gpd')),
                strtotime($post->post_date)
            ) . '</td>';
            $output .= '</tr>';
            $output .= '<tbody>';
            $output .= '</tbody>';
            $output .= '</table>';
            return $output;
            // return sprintf(
            //     __('O usuário %s fez o resgate da recompensa %s, no valor de %s, na data %s.', 'gpd'),
            //     '<strong>' . $gpd_user_data->display_name . '</strong>',
            //     '<strong><a href="' . admin_url() . 'post.php?post=' . $gpd_recompensa_id . '&action=edit" target="_blank">' . get_the_title($gpd_recompensa_id) . '</a></strong>',
            //     '<strong>' . $gpd_resgate_valor . ' ' . $gpd_moeda->nome_plural . '</strong>',
            //     '<strong>' . date(
            //         sprintf('d/m/Y, %s H:i', __('à\s', 'gpd')),
            //         strtotime($post->post_date)
            //     ) . '</strong>'
            // );
        },

    ));
}
