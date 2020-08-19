$(document).ready(function() {
    $('#dataTable thead tr').clone(true).appendTo( '#dataTable thead' );
    $('#dataTable thead tr:eq(1) th').each( function (i) {
        var title = $(this).text();
        $(this).html( '<input type="text" placeholder="Szukaj '+title+'" />' );

        $( 'input', this ).on( 'keyup change', function () {
            if ( table.column(i).search() !== this.value ) {
                table
                    .column(i)
                    .search( this.value )
                    .draw();
            }
        } );
    } );
    var table = $('#dataTable').DataTable( {
        orderCellsTop: true,
        fixedHeader: true,
        order: [[4, 'asc']],
        "drawCallback": function () {
            var api = this.api();
            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };
            $('#sum_info').html(
                `<tr>
                    <th colspan="4" style="text-align:right" >Suma: </th>
                    <th>` +
                     api.column( 3, {page:'current'} ).data().reduce(function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0)
                     + "zł</th> </tr>"
            );

        }
    } );
    $.fn.dataTable.ext.search.push(
        function (settings, data, dataIndex) {
            var min = $('#date-from-grid').val();
            var max = $('#date-to-grid').val();
            var createdAt = data[4] || 0;
            if ((min == "" || max == "") || (moment(createdAt).isSameOrAfter(min) && moment(createdAt).isSameOrBefore(max))) {
                return true;
            }
            return false;
        }
    );

    $('.date-range-filter').change(function () {
        table.draw();
    });

} );
