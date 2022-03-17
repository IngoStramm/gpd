<?php

/**
 * Plugin Name: Gestão por Desempenho
 * Plugin URI: https://agencialaf.com
 * Description: Plugin dos sistema de Gestão por Desempenho.
 * Version: 0.1.6
 * Author: Ingo Stramm
 * Text Domain: gpd
 * License: GPLv2
 */

defined('ABSPATH') or die('No script kiddies please!');

define('GPD_DIR', plugin_dir_path(__FILE__));
define('GPD_URL', plugin_dir_url(__FILE__));

function gpd_debug($debug)
{
    echo '<pre>';
    var_dump($debug);
    echo '</pre>';
}

require_once 'tgm/tgm.php';
require_once 'classes/classes.php';
require_once 'scripts.php';
require_once 'user/user.php';
require_once 'recompensa/recompensa.php';
require_once 'log-transacao/log-transacao.php';
require_once 'resgate/resgate.php';
require_once 'settings.php';
require_once 'functions/functions.php';

require 'plugin-update-checker-4.10/plugin-update-checker.php';
$updateChecker = Puc_v4_Factory::buildUpdateChecker(
    'https://raw.githubusercontent.com/IngoStramm/gpd/master/info.json',
    __FILE__,
    'gpd'
);
