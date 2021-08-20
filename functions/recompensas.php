<?php
add_action('pre_get_posts', 'gpd_sort_recompensas');

function gpd_sort_recompensas($query)
{
    if ($query->is_main_query() && !is_admin()) {
        if ($query->is_tax() || $query->is_post_type_archive('recompensas')) {
            $gpd_order = 'ASC';
            $query->set('orderby', 'name');
            if (isset($_GET['gpd_sort_recompensa']) && $_GET['gpd_sort_recompensa']) {
                $gpd_sort_recompensa = $_GET['gpd_sort_recompensa'];
                if ($gpd_sort_recompensa != 'nome') {
                    $gpd_order = $gpd_sort_recompensa == 'preco_desc' ? 'DESC' : 'ASC';
                    $query->set('meta_key', 'gpd_recompensa_preco');
                    $query->set('orderby', 'meta_value_num');
                }
                // gpd_debug($query);
            }
            $query->set('order', $gpd_order);
        }
    }
}

add_action('gpd_before_loop', 'gpd_show_recompensas_filters');

function gpd_show_recompensas_filters()
{ ?>
    <div class="row">
        <div class="clearfix visible-xs" style="margin-top: 20px;"></div>
        <div class="col-md-4">
            <form method="get" id="searchform" class="form-inline" action="<?php echo esc_url(home_url('/')); ?>" role="search">
                <input type="hidden" name="post_type" value="recompensas" />
                <div class="input-group">
                    <input type="search" class="form-control" name="s" id="s" value="<?php echo get_search_query(); ?>" placeholder="<?php esc_attr_e('Search', 'odin'); ?>" />
                    <span class="input-group-btn">
                        <button type="submit" class="btn btn-default" value="<?php esc_attr_e('Search', 'odin'); ?>">
                            <i class="glyphicon glyphicon-search"></i>
                        </button>
                    </span><!-- /input-group-btn -->
                </div><!-- /input-group -->
            </form><!-- /searchform -->
        </div>
        <!-- /.col-md-4 -->

        <div class="clearfix visible-xs" style="margin-bottom: 20px;"></div>

        <?php if (!is_search()) { ?>
            <div class="col-md-4 col-md-offset-4">
                <div class="gpd-sorting">
                    <form action="" class="gpd-sorting-form form-inline" method="get">
                        <select name="gpd_sort_recompensa" class="orderby form-control" onchange="this.form.submit()">
                            <?php
                            $gpd_selected = isset($_GET['gpd_sort_recompensa']) ? $_GET['gpd_sort_recompensa'] : 'nome';
                            $gpd_sorting_options = [];
                            $gpd_sorting_options['nome'] = __('Padrão', 'gpd');
                            $gpd_sorting_options['preco_desc'] = __('Valor: do maior para o menor', 'gpd');
                            $gpd_sorting_options['preco_asc'] = __('Valor: do menor para o maior', 'gpd');
                            foreach ($gpd_sorting_options as $k => $v) {
                                echo '<option value="' . $k . '"';
                                if ($gpd_selected == $k) echo ' selected';
                                echo '>' . $v . '</option>';
                            } ?>
                        </select>
                    </form>
                    <!-- /.gpd-sorting-form -->
                </div>
                <!-- /.gpd-sorting -->
            </div>
            <!-- /.col-md-4 -->

            <div class="clearfix visible-xs" style="margin-bottom: 20px;"></div>
            
        <?php } ?>
    </div>
    <!-- /.row -->
<?php }
