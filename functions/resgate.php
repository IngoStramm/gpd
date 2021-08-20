<?php
// Tela de confirmação do Resgate

// add_filter('micro_office_filter_get_post_data', function ($post_data){
//     return $post_data;
// }, 10, 1);
add_filter('the_content', 'gpd_resgate_confirmation');

function gpd_resgate_confirmation($content)
{
    if (!in_the_loop())
        return $content;

    if (!is_singular())
        return $content;

    if (!is_main_query())
        return $content;
    $gpd_order_confirmation_page = gpd_get_option('gpd_order_confirmation_page');
    if (!$gpd_order_confirmation_page)
        gpd_debug('Página de confirmação de resgate não definida.');

    if ($gpd_order_confirmation_page != get_the_ID())
        return $content;

    if (!isset($_GET['resgate_id']) || empty('resgate_id')) {
        $content = '<h3>' . __('ID do resgate ausente!', 'gpd') . '</h3>';
        $content .= '<p>' . __('Não foi possível carregar as informações do resgate.', 'gpd') . '</p>';
        return $content;
    }

    remove_filter('the_content', 'function gpd_resgate_confirmation');

    $gpd_resgate_id = $_GET['resgate_id'];
    $gpd_resgate_user_id = get_post_meta($gpd_resgate_id, 'gpd_resgate_user_id', true);
    $gpd_resgate_valor = get_post_meta($gpd_resgate_id, 'gpd_resgate_valor', true);
    $gpd_recompensa_id = get_post_meta($gpd_resgate_id, 'gpd_recompensa_id', true);
    $current_user = wp_get_current_user();
    // $gpd_user_data = get_userdata($gpd_resgate_user_id);
    // $gpd_moeda = new GPD_Moeda;
    $output = '';
    $output .= '<div class="resgate-info">';
    $output .= '<h2>' . sprintf(__('Parabéns %s!', 'gpd'), $current_user->display_name) . '</h2>';
    $output .= '<h4>' . __('Seu resgate foi feito com sucesso.', 'gpd') . '</h4>';
    $output .= '<div class="resgate-block-msg">';
    $output .= __('Procure o coordenador ainda essa semana para falar sobre o resgate.');
    $output .= '</div><!-- /.resgate-block-msg -->';
    $output .= '';
    $output .= '';
    /*
    $output .= '<h3>' . __('Dados do Resgate', 'gpd') . '</h3>';
    $output .= '<table class="table table-responsive table-striped">';
    $output .= '<tbody>';
    $output .= '<tr>';
    $output .= '<td><strong>' . __('Código', 'gpd') . ':</strong></td>';
    $output .= '<td>#' . $gpd_resgate_id . '</td>';
    $output .= '</tr>';
    $output .= '<tr>';
    $output .= '<td><strong>' . __('Recompensa', 'gpd') . ':</strong></td>';
    $output .= '<td>' . '<a href="' . get_permalink($gpd_recompensa_id) . '" target="_blank">' . get_the_title($gpd_recompensa_id) . '</a>' . '</td>';
    $output .= '</tr>';
    $output .= '<tr>';
    $output .= '<td><strong>' . __('Pontos', 'gpd') . ':</strong></td>';
    $output .= '<td>' . $gpd_resgate_valor . '</td>';
    $output .= '</tr>';
    $output .= '<tr>';
    $output .= '<td><strong>' . __('Data', 'gpd') . ':</strong></td>';
    $output .= '<td>' . get_the_date(sprintf('d/m/Y, %s H:i', __('à\s', 'gpd')), $gpd_recompensa_id) . '</td>';
    $output .= '</tr>';
    $output .= '</tbody>';
    $output .= '</table>';
    */

    $output .= '</div>';
    $content = $output;
    return $content;
}

// Resgate

// Botão para ir para a tela dos resgats exibido na tela de confirmação de resgate
add_action('gpd_after_order_confirmation_content', 'gpd_show_rewards_page_link_button', 10, 1);

function gpd_show_rewards_page_link_button($gpd_post_id)
{
    if (!isset($_GET['resgate_id']) || empty($_GET['resgate_id']))
        return;

    $gpd_order_confirmation_page = gpd_get_option('gpd_order_confirmation_page');

    if ($gpd_order_confirmation_page != $gpd_post_id)
        return;

    $gpd_order_rewards_page = gpd_get_option('gpd_order_rewards_page');
    if (!$gpd_order_rewards_page)
        return;

    // $output = '<a href="' . esc_url(get_permalink($gpd_order_rewards_page)) . '" class="btn btn-large btn-primary">' . get_the_title($gpd_order_rewards_page) . '</a>';
    $output = '<a href="' . esc_url(get_bloginfo('url')) . '" class="gpd-btn">' . __('Voltar ao menu', 'gpd') . '</a>';
    echo $output;
}

// Listagem dos resgates
add_filter('gpd_after_rewards_content', 'gpd_rewards_list', 10, 1);

function gpd_rewards_list($gpd_post_id)
{
    global $gpd_moeda;
    $gpd_order_rewards_page = gpd_get_option('gpd_order_rewards_page');

    if ($gpd_order_rewards_page != $gpd_post_id)
        return;

    $gpd_user_id = get_current_user_id();
    if (!$gpd_user_id)
        return;
    $args = array(
        'numberposts' => -1,
        'post_type' => 'resgate',
        'meta_query' => array(
            array(
                'key'     => 'gpd_resgate_user_id',
                'value'   => $gpd_user_id,
            ),
        ),
        'fields' => 'ids'
    );
    $gpd_resgates = get_posts($args);

    $output = '<div class="table-responsive">';
    $output .= '<table id="table-resgates" class="table gpd-table table-condensed">';
    $output .= '<thead>';
    $output .= '<tr>';
    $output .= '<th><span class="th-title">' . __('Código', 'gpd') . '</span></th>';
    $output .= '<th><span class="th-title">' . __('Recompensa', 'gpd') . '</span></th>';
    $output .= '<th><span class="th-title">' . sprintf(__('%s gastos', 'gpd'), ucfirst($gpd_moeda->nome_plural)) . '</span></th>';
    $output .= '<th><span class="th-title">' . __('Data do Resgate', 'gpd') . '</span></th>';
    $output .= '</tr>';
    $output .= '</thead>';
    $output .= '<tbody>';
    foreach ($gpd_resgates as $gpd_resgate_id) {
        $gpd_recompensa_id = get_post_meta($gpd_resgate_id, 'gpd_recompensa_id', true);

        $gpd_recompensa_titulo = !$gpd_recompensa_id ? __('Recompensa não encontrada', 'gpd') : '<a href="' . esc_url(get_permalink($gpd_recompensa_id)) . '" target="_blank">' . get_the_title($gpd_recompensa_id) . '</a>';

        $gpd_resgate_valor = get_post_meta($gpd_resgate_id, 'gpd_resgate_valor', true);
        $output .= '<tr>';
        $output .= '<td><strong>' . $gpd_resgate_id . '</strong></td>';
        $output .= '<td>' . $gpd_recompensa_titulo . '</td>';
        $output .= '<td><span class="label label-danger">-' . $gpd_resgate_valor . '</span></td>';
        $output .= '<td>' . get_the_date(null, $gpd_resgate_id) . '</td>';
        // $output .= '<td>' . get_the_date(sprintf('d/m/Y, %s H:i', __('à\s', 'gpd')), $gpd_resgate_id) . '</td>';
        $output .= '</tr>';
    }
    $output .= '</tbody>';
    $output .= '</table>';
    $output .= '</div>';
    echo $output;
}