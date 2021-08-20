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