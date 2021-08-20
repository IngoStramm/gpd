<?php
// Login
// Pega a url atual (usado para redirecionar o usuário após fazer login)
function gpd_get_url()
{
    $url  = isset($_SERVER['HTTPS']) && 'on' === $_SERVER['HTTPS'] ? 'https' : 'http';
    $url .= '://' . $_SERVER['SERVER_NAME'];
    // $url .= in_array($_SERVER['SERVER_PORT'], array('80', '443')) ? '' : ':' . $_SERVER['SERVER_PORT'];
    $url .= $_SERVER['REQUEST_URI'];
    return $url;
}

// Força o usuário a estar logado para acessar o site
add_action('get_header', 'gpd_force_login');

function gpd_force_login()
{
    if (!is_user_logged_in()) {
        wp_safe_redirect(wp_login_url(), 302);
        exit;
    }
}

// Redireciona o usuário para a tela dos termos de adesão após o login
add_action('wp_login', 'gpd_show_membership_terms', 10, 2);

function gpd_show_membership_terms($user_login, $user)
{
    $gpd_membership_terms_page = gpd_get_option('gpd_membership_terms_page');

    if (!$gpd_membership_terms_page)
        return;

    $gpd_membership_terms_page_modified_date = get_the_modified_date('m/d/Y H:i:s', $gpd_membership_terms_page);

    $gpd_last_time_membership_terms_page_user_viewed = get_user_meta($user->ID, '_gpd_last_time_membership_terms_page_user_viewed', true);

    $gpd_last_time_membership_terms_page_user_viewed = !$gpd_last_time_membership_terms_page_user_viewed ? 'null' : $gpd_last_time_membership_terms_page_user_viewed;

    // Aqui é feita a verificação se o usuário será ou não redirecionado à tela dos termos de adesão

    // Redireciona para os termos de adesão
    // Salva a url atual em um parâmetro na query (talvez não seja necessário)
    $current_url = gpd_get_url();
    $redirect_to = isset($_GET['redirect_to']) ? $_GET['redirect_to'] : null;
    $gpd_membership_terms_page_url = get_page_link($gpd_membership_terms_page);

    wp_safe_redirect($gpd_membership_terms_page_url . '?gpd_user_id=' . $user->ID . '&last_time=' . $gpd_last_time_membership_terms_page_user_viewed);
    exit();
}

// Exibe o formulário da tela dos termos de adesão
add_filter('the_content', 'gpd_membership_page_content');

function gpd_membership_page_content($content)
{

    $gpd_membership_terms_page = gpd_get_option('gpd_membership_terms_page');

    // if (!$gpd_membership_terms_page)
    //     gpd_debug('Página de Termos de Adesão não definida.');

    if (!$gpd_membership_terms_page || get_the_ID() != $gpd_membership_terms_page)
        return $content;

    $output = '';
    $gpd_user_id = get_current_user_id();
    $output .= '<div class="gpd-terms-form">';
    $output .= '<form action="" method="post">';
    $output .= wp_nonce_field('gpd-terms-form_' . $gpd_user_id, '_wpnonce', true, false);
    $output .= '<input type="hidden" name="gpd_terms_forms_submitted" value="true">';
    $output .= '<div class="checkbox">';
    $output .= '<label><input type="checkbox" name="gpd_agreement_confirmation" id="gpd_agreement_confirmation" required>' . __('Concordo com os Termos de Adesão', 'gpd') . '</label>';
    $output .= '</div><!-- /.checkbox -->';
    $output .= '<button type="submit" class="gpd-btn">' . __('Ok', 'gpd') . '';
    $output .= '</form>';
    $output .= '</div>';
    $content = '<div class="terms-content">' . $content . '</div>' . $output;
    return $content;
};

// processa o formulário da tela dos termos de adesão
add_action('wp_head', 'gpd_process_membership_terms_form');

function gpd_process_membership_terms_form()
{
    $gpd_membership_terms_page = gpd_get_option('gpd_membership_terms_page');

    if (!$gpd_membership_terms_page)
        gpd_debug('Página de Termos de Adesão não definida.');

    if (!$gpd_membership_terms_page)
        return;

    $gpd_user_id = get_current_user_id();

    if (isset($_POST['gpd_terms_forms_submitted']) && $_POST['gpd_terms_forms_submitted']) {

        $gpd_error_msg = null;
        $gpd_nonce = isset($_POST['_wpnonce']) ? wp_verify_nonce($_POST['_wpnonce'], 'gpd-terms-form_' . $gpd_user_id) : null;
        if (!$gpd_nonce)
            $gpd_error_msg['wp_nonce'] = __('Não foi possível validar a sua requisição.', 'gpd');

        if ($gpd_error_msg)
            gpd_debug($gpd_error_msg);

        $gpd_agreement_confirmation = isset($_POST['gpd_agreement_confirmation']) ? $_POST['gpd_agreement_confirmation'] : null;
        $gpd_terms_forms_submitted = isset($_POST['gpd_terms_forms_submitted']) ? $_POST['gpd_terms_forms_submitted'] : null;

        echo '<script>location.href="' . get_site_url() . '";</script>';
    }
}
