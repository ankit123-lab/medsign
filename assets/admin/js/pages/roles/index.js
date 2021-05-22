var data_table = '#datatable_list';
function get_all_data() {
    var status_filter = $('.status_filter').selectpicker('val');
    var email_filter = $('.email_filter').val();
    var name_filter = $('.name_filter').val();
    jQuery(data_table).DataTable({
        responsive: true,
        processing: true,
        serverSide: true,
        pagingType: "full_numbers",
        scrollY: '50vh',
        scrollX: true,
        scrollCollapse: true,
        searching: false,
        order: [1, 'DESC'],
        pageLength : per_page,
        "columns": [
            {"data": "name"},
            {"data": "created_at"},
            {"data": "status"},
            {"data": "action"}
        ],
        columnDefs: [
            {
                targets: [0],
                searchable: true,
                sortable: true,
            },
            {
                targets: [1],
                searchable: true,
                sortable: true
            },
            {
                targets: [2],
                searchable: false,
                sortable: false,
            },
            {
                targets: [3],
                searchable: false,
                sortable: false,
            }
        ],
        language: {
            emptyTable: "No data available",
            zeroRecords: "No matching records found...",
            infoEmpty: "No records available"
        },
        oLanguage: {
            "sProcessing": ''
        },
        ajax: {
            "url": controller_url + '/get_all_data',
            "type": "POST",
            "async": false,
            "data": {'name_filter': name_filter, 'email_filter': email_filter, 'status_filter': status_filter},
        },
        drawCallback: function () {
            jQuery('<li><a onclick="refresh_tab()" style="cursor:pointer" title="Refresh"><i class="material-icons">cached</i></a></li>').prependTo('div.dataTables_paginate ul.pagination');
        }
    });
}

function refresh_tab() {
    jQuery(data_table).dataTable().fnDestroy();
    get_all_data();
    jQuery("#datatable_list_filter").css('display', 'none');
}

function filterGlobal() {
    jQuery(data_table).DataTable().search(
            jQuery('#global_filter').val()
            ).draw();
}

function filterColumn(i) {
    jQuery(data_table).DataTable().column(i).search(
            jQuery('#col' + i + '_filter').val()
            ).draw();
}

jQuery(document).ready(function () {
    get_all_data();

    // change active / inactive status
    jQuery(document).on('change', '.status_change', function () {
        var status;
        var id;
        if (jQuery(this).is(':checked')) {
            status = 1;
        } else {
            status = 2;
        }
        id = jQuery(this).attr('data-id');
        if (!isNaN(id)) {
            jQuery.ajax({
                "url": controller_url + '/change_status',
                type: "POST",
                data: {
                    'id': id, 'status': status
                },
                dataType: 'json',
                cache: false,
                success: function (response) {
                    if (response.success == true) {
                        toastr.success(response.message);
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function () {
                    toastr.error("Problem in performing your action.");
                }
            });
        } else {
            toastr.error("Invalid request...!");
        }
    });

    // delete data
    jQuery(document).on('click', '.delete_data_button', function () {
        var id;
        var this_row = jQuery(this);
        id = jQuery(this).attr('data-id');
        swal({
            title: "Are you sure you want to delete this role?",
            text: "This data will be permanently removed, and not revertible, Also all associated users will affected.",
            type: "info",
            showCancelButton: true,
            closeOnConfirm: false,
            showLoaderOnConfirm: true,
        }, function () {
            if (!isNaN(id)) {
                jQuery.ajax({
                    "url": controller_url + '/delete',
                    type: "POST",
                    data: {
                        'id': id
                    },
                    dataType: 'json',
                    cache: false,
                    success: function (response) {
                        if (response.success == true) {
                            setTimeout(function () {
                                swal(response.message);
                                jQuery(data_table).DataTable().row(this_row).remove().draw(false);
                            }, 2000);

                        } else {
                            setTimeout(function () {
                                swal(response.message);
                            }, 2000);
                        }
                    },
                    error: function () {
                        setTimeout(function () {
                            swal("Problem in performing your action.");
                        }, 2000);
                    }
                });
            } else {
                setTimeout(function () {
                    swal("Invalid request...!");
                }, 2000);
            }
        });
    });

    jQuery('.search_filter').click(function () {
        jQuery(data_table).dataTable().fnDestroy();
        get_all_data();
    });

    jQuery('.reset_filter').click(function () {
        $('.status_filter').selectpicker('deselectAll');
        $('.email_filter').val('');
        $('.name_filter').val('');
        jQuery('.smart_search').find('input:text, input:password, input:file, select, textarea').val('');
        jQuery('.smart_search').find('input:radio, input:checkbox').removeAttr('checked').removeAttr('selected');
        setTimeout(function () {
            jQuery(data_table).dataTable().fnDestroy();
            get_all_data();
        }, 100);
    });

});