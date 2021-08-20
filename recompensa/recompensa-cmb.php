<?php

add_action('cmb2_admin_init', 'gpd_register_recompensa_metabox');

function gpd_register_recompensa_metabox()
{

    $recompensa_cmb = new_cmb2_box(array(
        'id'            => 'gpd_recompensa_metabox',
        'title'         => esc_html__('Configuração da Recompensa', 'gpd'),
        'object_types'  => array('recompensas'), // Post type
        // 'context'    => 'normal',
        'priority'   => 'high',
        // 'show_names' => true, // Show field names on the left
        // 'cmb_styles' => false, // false to disable the CMB stylesheet
        // 'closed'     => true, // true to keep the metabox closed by default
        // 'classes'    => 'extra-class', // Extra cmb2-wrap classes
        // 'classes_cb' => 'gpd_add_some_classes', // Add classes through a callback.
        // 'mb_callback_args' => array( '__block_editor_compatible_meta_box' => false ),
    ));

    $recompensa_cmb->add_field(array(
        'name'       => esc_html__('Preço', 'gpd'),
        'id'         => 'gpd_recompensa_preco',
        'type'       => 'text_small',
        'attributes'    => array(
            'type'      => 'number',
            'min'       => '0',
        ),
        'default'       => 0,
        'before_field'  => '$ ',
    ));

    $recompensa_cmb->add_field(array(
        'name' => esc_html__('Validade', 'gpd'),
        'desc' => esc_html__('Usado apenas para informar o usuário, a recompensa precisa ser descadastrada manualmente.', 'gpd'),
        'id'   => 'gpd_recompensa_validade',
        'type' => 'text_date',
        'date_format' => 'd-m-Y',
    ));
}
