<?php


// Header
// Exibe o saldo atual do usuário
function gdp_current_balance($gpd_user_id = null)
{
    global $gpd_moeda;
    if (empty($gpd_user_id) || is_null($gpd_user_id))
        $gpd_user_id = get_current_user_id();

    if (!$gpd_user_id)
        return;
    // gpd_debug($gpd_user_id);
    $gpd_user_saldo = get_user_meta($gpd_user_id, 'gpd_user_saldo', true);
    $gpd_user_saldo = empty($gpd_user_saldo) || is_null($gpd_user_saldo) ? '0' : $gpd_user_saldo;
    // return $gpd_user_saldo . ' ' . $gpd_moeda->nome_plural;
    return $gpd_user_saldo;
}

// Remove o admin bar para todos os usuários que não sejam administradores
add_action('after_setup_theme', 'gpd_remove_admin_bar');

function gpd_remove_admin_bar()
{
    show_admin_bar(false);
}
