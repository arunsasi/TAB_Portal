jQuery(document).ready(function ($) {
    if ($("#member-table").length) {
        $(document).ready(function () {
            var table = $('#member-table').DataTable({
                processing: true,
                  serverSide: true,
                  "sortable": true,
                "ajax": {
                    url : apiUrl+"userlist",
                    type : 'GET',
                    data: function ( d ) {
                        d.role = 1;
                    },
                    error: function(){  // error handling
                        $(".member-table-error").html("");
                        $("#member-table").append('<tbody class="member-table-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                        $("#member-table_processing").css("display","none");
     
                    }
                },
                order: [[ 1, 'asc' ]],
                "columnDefs": [ 
                    {
                        "targets": [0], "searchable": false, "orderable": false, "visible": true
                    },
                    {
                        // The `data` parameter refers to the data for the cell (defined by the
                        // `data` option, which defaults to the column being worked with, in
                        // this case `data: 0`.
                        "render": function ( data, type, row ) {
                          //  return data +' ('+ row[3]+')';
                          var editurl = "user";
                          var deleteurl = "userremove";
                            return '<a href="javascript:void(0)" class="text-primary" onclick="editModelForm('+data+',\''+editurl+'\' )" data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target=""><i class="fas fa-edit"></i></a> <a href="javascript:void(0)" onclick="deletePop('+data+',\''+deleteurl+'\' )" class="text-danger"><i class="fas fa-trash-alt"></i></a>';

                           // return '<a class="btn" href="<?php echo site_url("Faq/view_faq_details/'+data[1]+'"); ?>" target="_blank" Title="View"><i class="fa fa-eye"></i></a>&nbsp;<?php if($role==ADMIN || $role== TO || $role== TL  || $role== CCM){ $perm1 = EDITFAQ; if(in_array($perm1, $permiss)){ ?><a class="btn" href="<?php echo site_url("Faq/edit_faq/'+data[1]+'"); ?>" target="_blank" Title="Edit"><i class="fa fa-pencil"></i></a><?php } $perm1 = DELETEFAQ; if(in_array($perm1, $permiss)){ ?><a class="btn" onclick="delete_faq('+data[1]+');" Title="Delete"><i class="fa fa-trash-o"></i></a><?php } } ?>';
                        },
                        "targets": -1,
                        "orderable": false
                    }
                ]
            });
        });
    }
});
/*{
    "targets": -1,
    "orderable": false,
    "data": null,
    "defaultContent": "<button>Click!</button>"
    } */