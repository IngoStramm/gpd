jQuery(function ($) {
    $(document).ready(function () {
        $('.gpd-table').DataTable(
            {
                'order': [0, 'desc'],
                'language': {
                    'sEmptyTable': 'Nenhum registro encontrado',
                    'sInfo': 'Mostrando de _START_ até _END_ de _TOTAL_ registros',
                    'sInfoEmpty': 'Mostrando 0 até 0 de 0 registros',
                    'sInfoFiltered': '(Filtrados de _MAX_ registros)',
                    'sInfoThousands': '.',
                    'sLengthMenu': '_MENU_ resultados por página',
                    'sLoadingRecords': 'Carregando...',
                    'sProcessing': 'Processando...',
                    'sZeroRecords': 'Nenhum registro encontrado',
                    'sSearch': 'Pesquisar',
                    'oPaginate': {
                        'sNext': '&raquo;',
                        'sPrevious': '&laquo;',
                        'sFirst': 'Primeiro',
                        'sLast': 'Último'
                    },
                    'oAria': {
                        'sSortAscending': ': Ordenar colunas de forma ascendente',
                        'sSortDescending': ': Ordenar colunas de forma descendente'
                    },
                    'select': {
                        'rows': {
                            '_': 'Selecionado %d linhas',
                            '0': 'Nenhuma linha selecionada',
                            '1': 'Selecionado 1 linha'
                        }
                    },
                    'buttons': {
                        'copy': 'Copiar para a área de transferência',
                        'copyTitle': 'Cópia bem sucedida',
                        'copySuccess': {
                            '1': 'Uma linha copiada com sucesso',
                            '_': '%d linhas copiadas com sucesso'
                        }
                    }
                },
                'initComplete': function () {
                    // const gpd_table_saldo_length = document.getElementById('gpd-table-saldo_length');
                    const gpd_dataTables_length = document.getElementsByClassName('dataTables_length');
                    if (typeof (gpd_dataTables_length) === 'undefined' && gpd_dataTables_length === null) {
                        return;
                    }

                    var gpd_table_saldo_length_select = gpd_dataTables_length[0].getElementsByTagName('select');
                    if (typeof (gpd_table_saldo_length_select) === 'undefined' && gpd_table_saldo_length_select === null) {
                        return;
                    }

                    gpd_table_saldo_length_select = gpd_table_saldo_length_select[0];

                    gpd_table_saldo_length_select.classList.remove('input-sm');

                    const gpd_table_saldo_length_label = gpd_dataTables_length[0].getElementsByTagName('label');
                    if (typeof (gpd_table_saldo_length_label) === 'undefined' && gpd_table_saldo_length_label === null) {
                        return;
                    }

                    gpd_table_saldo_length_label[0].innerHTML = '';
                    gpd_table_saldo_length_label[0].appendChild(gpd_table_saldo_length_select);


                    // const gpd_table_saldo_filter = document.getElementById('gpd-table-saldo_filter');
                    var gpd_dataTables_filter = document.getElementsByClassName('dataTables_filter');
                    if (typeof (gpd_dataTables_filter) === 'undefined' && gpd_dataTables_filter === null) {
                        return;
                    }

                    gpd_dataTables_filter = gpd_dataTables_filter[0];

                    var gpd_table_saldo_filter_input = gpd_dataTables_filter.getElementsByTagName('input');
                    if (typeof (gpd_table_saldo_filter_input) === 'undefined' && gpd_table_saldo_filter_input === null) {
                        return;
                    }
                    gpd_table_saldo_filter_input = gpd_table_saldo_filter_input[0];

                    gpd_table_saldo_filter_input.classList.remove('input-sm');
                    gpd_table_saldo_filter_input.style = 'margin: 0;';
                    gpd_table_saldo_filter_input.placeholder = 'Pesquisa';

                    const gpd_input_group = document.createElement('div');
                    gpd_input_group.classList.add('input-group');

                    const gpd_input_group_span = document.createElement('span');
                    gpd_input_group_span.classList.add('input-group-addon');
                    gpd_input_group_span.style = 'background-color: transparent;';

                    const gpd_input_group_i = document.createElement('i');
                    gpd_input_group_i.classList.add('glyphicon');
                    gpd_input_group_i.classList.add('glyphicon-search');

                    gpd_dataTables_filter.innerHTML = '';
                    gpd_dataTables_filter.appendChild(gpd_input_group);
                    gpd_input_group_span.appendChild(gpd_input_group_i);
                    gpd_input_group.appendChild(gpd_table_saldo_filter_input);
                    gpd_input_group.appendChild(gpd_input_group_span);
                }
            }
        );
    });
});

// Checkboxes gestão de pontos em massa
document.addEventListener('DOMContentLoaded', function () {

    // pega os checboxes selecionados
    const gpd_select_users = document.getElementsByClassName('gpd-select-users');
    const gpd_log_transacao_users_id = document.getElementById('gpd_log_transacao_users_id');
    const gpd_log_transacao_selected_users_label = document.getElementById('gpd_log_transacao_selected_users_label');
    const gpd_selected_users_id = [];
    Array.prototype.forEach.call(gpd_select_users, function (gpd_select, i) {
        gpd_select.addEventListener('change', function () {
            const pos = gpd_selected_users_id.indexOf(gpd_select.value);
            if (gpd_select.checked) {
                if (pos < 0) {
                    gpd_selected_users_id.push(gpd_select.value);
                }
            } else {
                if (pos >= 0) {
                    gpd_selected_users_id.splice(pos, 1);
                }
            }
            gpd_log_transacao_users_id.value = gpd_selected_users_id;
            gpd_log_transacao_selected_users_label.innerText = gpd_selected_users_id.length;
            if (gpd_selected_users_id.length > 0) {
                gpd_log_transacao_selected_users_label.classList.remove('label-danger');
                gpd_log_transacao_selected_users_label.classList.add('label-success');
            } else {
                gpd_log_transacao_selected_users_label.classList.remove('label-success');
                gpd_log_transacao_selected_users_label.classList.add('label-danger');
            }
            // var event = document.createEvent('HTMLEvents');
            // event.initEvent('change', true, false);
            // gpd_log_transacao_users_id.dispatchEvent(event);

        });
    });

    // seleciona todos checkboxes
    const gpd_toggle_checkboxes = document.getElementsByClassName('gpd-toggle-users');
    Array.prototype.forEach.call(gpd_toggle_checkboxes, function (gpd_toggle_checkbox, i) {
        gpd_toggle_checkbox.addEventListener('change', function () {
            const css_class = gpd_toggle_checkbox.dataset.toggleTarget;
            const gpd_checkboxes_to_toggle = document.getElementsByClassName(css_class);
            Array.prototype.forEach.call(gpd_checkboxes_to_toggle, function (gpd_checkbox_to_toggle) {
                // seleciona ou deseleciona o checkbox baseado no checkbox geral
                gpd_checkbox_to_toggle.checked = gpd_toggle_checkbox.checked;
                // dispara o evento change
                var event = document.createEvent('HTMLEvents');
                event.initEvent('change', true, false);
                gpd_checkbox_to_toggle.dispatchEvent(event);
            });
        });
    });

    const gpd_log_transacao_status1 = document.getElementById('gpd_log_transacao_status1');
    const gpd_log_transacao_status2 = document.getElementById('gpd_log_transacao_status2');
    const gpd_agendamento_groups = document.getElementsByClassName('gpd-agendamento-group');
    if (typeof (gpd_log_transacao_status2) !== 'undefined' && gpd_log_transacao_status2 !== null) {
        gpd_log_transacao_status1.addEventListener('change', function () {
            Array.prototype.forEach.call(gpd_agendamento_groups, function (gpd_agendamento_group) {
                if (gpd_log_transacao_status1.checked === true) {
                    gpd_agendamento_group.style.display = 'none';
                }
            });
        });
    }
    if (typeof (gpd_log_transacao_status2) !== 'undefined' && gpd_log_transacao_status2 !== null) {
        gpd_log_transacao_status2.addEventListener('change', function () {
            Array.prototype.forEach.call(gpd_agendamento_groups, function (gpd_agendamento_group) {
                if (gpd_log_transacao_status2.checked === true) {
                    gpd_agendamento_group.style.display = 'block';
                }
            });
        });
    }

    var serializeArray = function (form) {
        var arr = [];
        Array.prototype.slice.call(form.elements).forEach(function (field) {
            if (!field.name || field.disabled || ['file', 'reset', 'submit', 'button'].indexOf(field.type) > -1) { return; }
            if (field.type === 'select-multiple') {
                Array.prototype.slice.call(field.options).forEach(function (option) {
                    if (!option.selected) { return; }
                    arr.push({
                        name: field.name,
                        value: option.value
                    });
                });
                return;
            }
            if (['checkbox', 'radio'].indexOf(field.type) > -1 && !field.checked) { return; }
            arr.push({
                name: field.name,
                value: field.value
            });
        });
        return arr;
    };

    const form_add_bulk_points = document.getElementById('form-add-bulk-points');

    if (typeof (form_add_bulk_points) !== 'undefined' && form_add_bulk_points !== null) {
        form_add_bulk_points.addEventListener('submit', function (e) {
            e.preventDefault();
            const confirmar = confirm('Tem certeza que deseja executar este procedimento? Novas transações serão criadas e não será possível apagá-las.');
            if (!confirmar) {
                return;
            }
            const gpd_add_bulk_points_form_btn = document.getElementById('gpd-add-bulk-points-form-btn');

            if (typeof (gpd_add_bulk_points_form_btn) !== 'undefined' && gpd_add_bulk_points_form_btn !== null) {
                gpd_add_bulk_points_form_btn.disabled = true;
                gpd_add_bulk_points_form_btn.classList.add('disabled');
                gpd_add_bulk_points_form_btn.classList.add('btn-loading');
            }

            jQuery(function ($) {
                const data = {};
                const serializeArray_data = $(form_add_bulk_points).serializeArray();
                for (var i = 0; i < serializeArray_data.length; i++) {
                    data[serializeArray_data[i].name] = serializeArray_data[i].value;
                }
                data.action = 'gpd_add_bulk_points_page';
                data.post = serializeArray_data;
                $.ajax({
                    url: ajax_object.ajax_url, // Here goes our WordPress AJAX endpoint.
                    type: 'post',
                    data: data,
                    success: function (response) {
                        const entry_contents = document.getElementsByClassName('entry-content');
                        Array.prototype.forEach.call(entry_contents, function (entry_content){
                            entry_content.innerHTML = response;
                        });
                    },
                    fail: function (err) {
                        console.log('There was an error: ' + err);
                    },
                    complete: function() {
                        if (typeof (gpd_add_bulk_points_form_btn) !== 'undefined' && gpd_add_bulk_points_form_btn !== null) {
                            gpd_add_bulk_points_form_btn.disabled = false;
                            gpd_add_bulk_points_form_btn.classList.remove('disabled');
                            gpd_add_bulk_points_form_btn.classList.remove('btn-loading');
                        }
                    }
                });

                // This return prevents the submit event to refresh the page.
                return false;

            });
        });
    }

});