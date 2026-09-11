document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-datatable]').forEach(function (table) {
        if (typeof DataTable === 'undefined') {
            return;
        }

        const actionColumns = (table.dataset.datatableActions || '')
            .split(',')
            .map(function (column) {
                return Number.parseInt(column.trim(), 10);
            })
            .filter(function (column) {
                return Number.isInteger(column);
            });

        const options = {
            pageLength: Number.parseInt(table.dataset.datatablePageLength || '10', 10),
            lengthMenu: [10, 25, 50, 100],
            language: {
                search: 'Cari:',
                lengthMenu: 'Tampilkan _MENU_ data',
                info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
                infoEmpty: 'Belum ada data',
                zeroRecords: 'Data tidak ditemukan',
                emptyTable: 'Belum ada data',
                paginate: {
                    next: '>>',
                    previous: '<<'
                }
            }
        };

        if (actionColumns.length > 0) {
            options.columnDefs = actionColumns.map(function (column) {
                return {
                    targets: column,
                    orderable: false,
                    searchable: false
                };
            });
        }

        new DataTable(table, options);
    });
});
