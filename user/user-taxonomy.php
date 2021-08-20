<?php

function custom_taxonomy()
{
    $labels = array(
        'name'                       => _x('Cargos', 'Nome dos Cargos', 'gpd'),
        'singular_name'              => _x('Cargo', 'Nome do Cargo', 'gpd'),
        'menu_name'                  => __('Cargos', 'gpd'),
        'all_items'                  => __('Todos os Cargos', 'gpd'),
        'parent_item'                => __('Cargo Pai', 'gpd'),
        'parent_item_colon'          => __('Cargo Pai:', 'gpd'),
        'new_item_name'              => __('Nome do Novo Cargo', 'gpd'),
        'add_new_item'               => __('Adicionar Cargo', 'gpd'),
        'edit_item'                  => __('Editar Cargo', 'gpd'),
        'update_item'                => __('Atualizar Cargo', 'gpd'),
        'view_item'                  => __('Visualizar Cargo', 'gpd'),
        'separate_items_with_commas' => __('Cargo separadopor vírgulas', 'gpd'),
        'add_or_remove_items'        => __('Adicionar ou remover cargo', 'gpd'),
        'choose_from_most_used'      => __('Escolha entre os mais usados', 'gpd'),
        'popular_items'              => __('Cargos mais usados', 'gpd'),
        'search_items'               => __('Procurar Cargos', 'gpd'),
        'not_found'                  => __('Não encontrado', 'gpd'),
        'no_terms'                   => __('Nenhum cargo', 'gpd'),
        'items_list'                 => __('Lista de Cargos', 'gpd'),
        'items_list_navigation'      => __('Navegação da Lista de Cargos', 'gpd'),
    );
    $args = array(
        'labels'                     => $labels,
        'hierarchical'               => false,
        'public'                     => true,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_nav_menus'          => true,
        'show_tagcloud'              => true,
    );
    register_taxonomy('cargos', 'user', $args);
}
add_action('init', 'custom_taxonomy', 0);

/**
 * Admin page for the 'cargos' taxonomy
 */
function gpd_add_cargos_taxonomy_admin_page()
{
    $tax = get_taxonomy('cargos');
    add_users_page(
        esc_attr($tax->labels->menu_name),
        esc_attr($tax->labels->menu_name),
        $tax->cap->manage_terms,
        'edit-tags.php?taxonomy=' . $tax->name
    );
}
add_action('admin_menu', 'gpd_add_cargos_taxonomy_admin_page');

/**
 * Unsets the 'posts' column and adds a 'users' column on the manage cargos admin page.
 */
function gpd_manage_cargos_user_column($columns)
{
    unset($columns['posts']);
    $columns['users'] = __('Usuários', 'gpd');
    return $columns;
}
add_filter('manage_edit-cargos_columns', 'gpd_manage_cargos_user_column');

/**
 * @param string $display WP just passes an empty string here.
 * @param string $column The name of the custom column.
 * @param int $term_id The ID of the term being displayed in the table.
 */
function gpd_manage_cargos_column($display, $column, $term_id)
{
    if ('users' === $column) {
        $term = get_term($term_id, 'cargos');
        echo $term->count;
    }
}
add_filter('manage_cargos_custom_column', 'gpd_manage_cargos_column', 10, 3);

/**
 * @param object $user The user object currently being edited.
 */
function gpd_edit_user_cargo_section($user)
{
    global $pagenow;
    $tax = get_taxonomy('cargos');
    /* Make sure the user can assign terms of the cargos taxonomy before proceeding. */
    if (!current_user_can($tax->cap->assign_terms))
        return;
    $user_id = isset($user->ID) ? $user->ID : null;
    /* Get the terms of the 'cargos' taxonomy. */
    $terms = get_terms('cargos', array('hide_empty' => false)); ?>
    <h3><?php _e('Cargos', 'gpd'); ?></h3>
    <table class="form-table">
        <tr>
            <th><label for="cargos"><?php _e('Lista de Cargos', 'gpd'); ?></label></th>
            <td><?php
                /* If there are any cargos terms, loop through them and display checkboxes. */
                if (!empty($terms)) {
                    echo gpd_custom_form_field('cargos', $terms, $user_id, $pagenow, 'dropdown');
                }
                /* If there are no cargos terms, display a message. */ else {
                    _e('Nenhum cargo encontrado.', 'gpd');
                }
                ?></td>
        </tr>
    </table>
    <?php }
add_action('show_user_profile', 'gpd_edit_user_cargo_section');
add_action('edit_user_profile', 'gpd_edit_user_cargo_section');
add_action('user_new_form', 'gpd_edit_user_cargo_section');

/**
 * return field as dropdown or checkbox, by default checkbox if no field type given
 * @param: name = taxonomy, options = terms avaliable, userId = user id to get linked terms
 */
function gpd_custom_form_field($name, $options, $userId, $pagenow, $type = 'checkbox')
{
    switch ($type) {
        case 'checkbox':
            foreach ($options as $term) :
    ?>
                <label for="cargos-<?php echo esc_attr($term->slug); ?>">
                    <input type="checkbox" name="cargos[]" id="cargos-<?php echo esc_attr($term->slug); ?>" value="<?php echo $term->slug; ?>" <?php if ($pagenow !== 'user-new.php') checked(true, is_object_in_term($userId, 'cargos', $term->slug)); ?>>
                    <?php echo $term->name; ?>
                </label><br />
<?php
            endforeach;
            break;
        case 'dropdown':
            $selectTerms = [];
            foreach ($options as $term) {
                $selectTerms[$term->term_id] = $term->name;
            }

            // get all terms linked with the user
            $usrTerms = get_the_terms($userId, 'cargos');
            $usrTermsArr = [];
            if (!empty($usrTerms)) {
                foreach ($usrTerms as $term) {
                    $usrTermsArr[] = (int) $term->term_id;
                }
            }
            // Dropdown
            echo "<select name='{$name}'>";
            echo "<option value=''>" . __('Selecione um cargo', 'gpd') . "</option>";
            foreach ($selectTerms as $options_value => $options_label) {
                $selected = (in_array($options_value, array_values($usrTermsArr))) ? " selected='selected'" : "";
                echo "<option value='{$options_value}' {$selected}>{$options_label}</option>";
            }
            echo "</select>";
            break;
    }
}

/**
 * @param int $user_id The ID of the user to save the terms for.
 */
function gpd_save_user_cargo_terms($user_id)
{
    $tax = get_taxonomy('cargos');
    /* Make sure the current user can edit the user and assign terms before proceeding. */
    if (!current_user_can('edit_user', $user_id) && current_user_can($tax->cap->assign_terms))
        return false;
    $term = $_POST['cargos'];
    $terms = is_array($term) ? $term : (int) $term; // fix for checkbox and select input field
    /* Sets the terms (we're just using a single term) for the user. */
    wp_set_object_terms($user_id, $terms, 'cargos', false);
    clean_object_term_cache($user_id, 'cargos');
}
add_action('personal_options_update', 'gpd_save_user_cargo_terms');
add_action('edit_user_profile_update', 'gpd_save_user_cargo_terms');
add_action('user_register', 'gpd_save_user_cargo_terms');

/**
 * @param string $username The username of the user before registration is complete.
 */
function gpd_disable_cargos_username($username)
{
    if ('cargos' === $username)
        $username = '';
    return $username;
}
add_filter('sanitize_user', 'gpd_disable_cargos_username');

/**
 * Update parent file name to fix the selected menu issue
 */
function gpd_change_parent_file($parent_file)
{
    global $submenu_file;
    if (
        isset($_GET['taxonomy']) &&
        $_GET['taxonomy'] == 'cargos' &&
        $submenu_file == 'edit-tags.php?taxonomy=cargos'
    )
        $parent_file = 'users.php';
    return $parent_file;
}
add_filter('parent_file', 'gpd_change_parent_file');
