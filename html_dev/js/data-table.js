jQuery(document).ready(function ($) {
    if ($("#member-table").length) {
        $(document).ready(function () {
            var table = $('#member-table').DataTable({
                processing: true,
                  serverSide: true,
                  sortable: true,
                ajax: {
                    url : apiUrl+"userlist",
                    type : 'GET',
                    data: function ( d ) {
                        d.role = 2;
                    },
                    error: function(){  // error handling
                        $(".member-table-error").html("");
                        $("#member-table").append('<tbody class="member-table-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                        $("#member-table_processing").css("display","none");
     
                    }
                },
                order: [[ 1, 'asc' ]],
                columns: [
                    { "data": "slno" },
                    { "data": "name" },
                    { "data": "role" },
                    { "data": "contact_no" },
                    { "data": "company_name" },
                    { "data": "status" },
                    { "data": "id" }

                ],
                columnDefs: [ 
                    {
                        "targets": [0], "searchable": false, "orderable": false, "visible": true
                    },
                    {
                        // The `data` parameter refers to the data for the cell (defined by the
                        // `data` option, which defaults to the column being worked with, in
                        "render": function ( data, type, row ) {
                          //  return data +' ('+ row[3]+')';
                          var editurl = "user";
                          var deleteurl = "userremove";
                            return '<a href="javascript:void(0)" class="text-primary" onclick="editModelForm('+data+',\''+editurl+'\' );" data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target=""><i class="fas fa-edit"></i></a> <a href="javascript:void(0)" onclick="deletePop('+data+',\''+deleteurl+'\' )" class="text-danger"><i class="fas fa-trash-alt"></i></a>';

                           // return '<a class="btn" href="<?php echo site_url("Faq/view_faq_details/'+data[1]+'"); ?>" target="_blank" Title="View"><i class="fa fa-eye"></i></a>&nbsp;<?php if($role==ADMIN || $role== TO || $role== TL  || $role== CCM){ $perm1 = EDITFAQ; if(in_array($perm1, $permiss)){ ?><a class="btn" href="<?php echo site_url("Faq/edit_faq/'+data[1]+'"); ?>" target="_blank" Title="Edit"><i class="fa fa-pencil"></i></a><?php } $perm1 = DELETEFAQ; if(in_array($perm1, $permiss)){ ?><a class="btn" onclick="delete_faq('+data[1]+');" Title="Delete"><i class="fa fa-trash-o"></i></a><?php } } ?>';
                        },
                        "targets": -1,
                        "orderable": false
                    }
                ]
            });
        });
    }

    //Judges List
    if ($("#judge-table").length) {
        $(document).ready(function () {
            var currentUserRole= $('#role-id').val();
            var table = $('#judge-table').DataTable({
                processing: true,
                  serverSide: true,
                  sortable: true,
                ajax: {
                    url : apiUrl+"userlist",
                    type : 'GET',
                    data: function ( d ) {
                        d.role = 3;
                    },
                    error: function(){  // error handling
                        $(".judge-table-error").html("");
                        $("#judge-table").append('<tbody class="judge-table-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                        $("#judge-table_processing").css("display","none");
     
                    }
                },
                order: [[ 1, 'asc' ]],
                columns: [
                    { "data": "slno" },
                    { "data": "name" },
                    { "data": "role" },
                    { "data": "contact_no" },
                    { "data": "about" },
                    { "data": "status" },
                    { "data": "id" }

                ],
                columnDefs: [ 
                    {
                        "targets": [0], "searchable": false, "orderable": false, "visible": true
                    },
                    {
                        "render": function ( data, type, row ) {
                            var editurl = "user";
                            var deleteurl = "userremove";
                            edit = '<a href="javascript:void(0)" class="text-primary" onclick="editModelForm('+data+',\''+editurl+'\' )" data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target=""><i class="fas fa-edit"></i></a> ';
                            deletefun = '<a href="javascript:void(0)" onclick="deletePop('+data+',\''+deleteurl+'\' )" class="text-danger"><i class="fas fa-trash-alt"></i></a> ';
                            if(currentUserRole === '1')
                            {
                                return edit+' '+deletefun;
                            }
                            else if(currentUserRole === '2')
                            {
                                return edit;
                            }
                            else {
                                return '';
                            }
                        },
                        "targets": -1,
                        "orderable": false
                    }
                ]
            });
        });
    }

     //Event List
     
     if ($("#eventlist-table").length) {
        $(document).ready(function () { 
            var currentUserRole= $('#role-id').val();
            var table = $('#eventlist-table').DataTable({
                processing: true,
                  serverSide: true,
                  sortable: true,
                ajax: {
                    url : apiUrl+"eventlist",
                    type : 'GET',
                    
                    error: function(){  // error handling
                        $(".eventlist-table-error").html("");
                        $("#eventlist-table").append('<tbody class="eventlist-table-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                        $("#eventlist-table_processing").css("display","none");
     
                    }
                },
                drawCallback:function( settings, json){
                    $('[data-toggle="tooltip"]').tooltip();
                },
                order: [[ 1, 'asc' ]],
                columns: [
                    { "data": "slno" },
                    { "data": "event_name" },
                    { "data": "eventDate" },
                    { "data": "venue" },
                    { "data": "prelims" },
                    { "data": "status" },
                    { "data": "id" }

                ],
                columnDefs: [ 
                    {
                        "targets": [0], "searchable": false, "orderable": false, "visible": true,
                        "targets": [4], "searchable": false, "orderable": false, "visible": true
                    },
                    {
                        "render": function ( data, type, row ) {
                            eventdate = '';
                            if(row['prelims'] != 0 && row['prelims_date'] !== null && row['prelims_date'] != '')
                            {
                                eventdate = 'Prelims : '+ row['prelims_date'] + '<br> Main : ';
                            }
                            return eventdate + data;
                        },
                        "targets": 2,
                        "orderable": false
                    },
                    {
                        "render": function ( data, type, row ) {
                            venue = '';
                            if(row['prelims'] != 0 && row['prelims_venue'] !== null && row['prelims_venue'] != '')
                            {
                                venue = 'Prelims : '+ row['prelims_venue'] + '<br> Main : ';
                            }
                            return venue + data;
                        },
                        "targets": 3,
                        "orderable": false
                    },
                    {
                        "render": function ( data, type, row ) {
                            if(row['prelims'] == 0)
                            {
                                return 'No';
                            }
                            return 'Yes';
                        },
                        "targets": 4,
                        "orderable": false
                    },
                    {
                        "render": function ( data, type, row ) {
                            
                            if(row['status'] == '0')
                            {
                                return 'Active';
                            }
                            else if(row['status'] == '1')
                            {
                                return 'Judgement Open';
                            }
                            else if(row['status'] == '2')
                            {
                                return 'Judgement Closed';
                            }
                            return '';
                        },
                        "targets": 5,
                        "orderable": false
                    },
                    {
                        "render": function ( data, type, row ) {
                          var editurl = "event";
                          var deleteurl = "eventremove";
                          edit = '<a href="javascript:void(0)" class="text-primary" onclick="editModelForm('+data+',\''+editurl+'\' )" data-backdrop="static" data-keyboard="false" data-toggle="tooltip" data-placement="top" data-original-title="Edit Event"><i class="fas fa-edit"></i></a> ';
                          deletefun = '<a href="javascript:void(0)" onclick="deletePop('+data+',\''+deleteurl+'\' )" class="text-danger" data-toggle="tooltip" data-placement="top" data-original-title="Delete Event"><i class="fas fa-trash-alt"></i></a> ';
                          addContestant = '<a href="javascript:void(0)" class="text-success" onclick="addContestantModelForm('+data+')" data-backdrop="static" data-keyboard="false" data-toggle="tooltip" data-placement="top" data-original-title="Add Contestants"><i class="fas fa-user-plus"></i></a> ';
                          contestantlist = '<a href="./event-contestants.html?event='+data+'" class="text-secondary" data-backdrop="static" data-keyboard="false" data-toggle="tooltip" data-placement="top" data-original-title="Contestants List"><i class="fas fa-eye"></i></a> ';
                          manageJudges = '<a href="javascript:void(0)" class="text-info" onclick="manageJudgesPop('+data+',\''+editurl+'\' )" data-backdrop="static" data-keyboard="false" data-toggle="tooltip" data-placement="top" data-original-title="Manage Judges"><i class="fas fa-user-secret"></i></a> ';
                          addCriteria = '<a href="./judge-criteria.html?event='+data+'&name='+row['event_name']+'" data-backdrop="static" data-keyboard="false" data-toggle="tooltip" data-placement="top" data-original-title="Judging Creteria"><i class="fas fa-list-alt"></i></a> ';

                          if(currentUserRole === '1')
                            {
                                return edit+' '+deletefun+ '&nbsp;&nbsp;&nbsp;'+addContestant+contestantlist+'&nbsp;&nbsp;&nbsp;'+manageJudges+addCriteria;
                            }
                            else if(currentUserRole === '2')
                            {
                                return addContestant+contestantlist+'&nbsp;&nbsp;&nbsp;'+manageJudges+addCriteria;
                            }
                            else {
                                return '';
                            }
                        },
                        "targets": -1,
                        "orderable": false
                    }
                ]
                
            });
            
        });
    }

    //Contestant List
     
    if ($("#contestantlist-table").length) {
        $(document).ready(function () { 
            var table = $('#contestantlist-table').DataTable({
                processing: true,
                  serverSide: true,
                  sortable: true,
                ajax: {
                    url : apiUrl+"contestantlist",
                    type : 'GET',
                    data: function ( d ) {
                        d.eventId = $('#eventid').val();
                    },
                    
                    error: function(){  // error handling
                        $(".contestantlist-table-error").html("");
                        $("#contestantlist-table").append('<tbody class="contestantlist-table-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                        $("#contestantlist-table_processing").css("display","none");
     
                    }
                },
                drawCallback:function( settings, json){
                    $('[data-toggle="tooltip"]').tooltip();
                },
                order: [[ 1, 'asc' ]],
                columns: [
                    { "data": "slno" },
                    { "data": "event_name" },
                    { "data": "name" },
                    { "data": "company_name" },
                    { "data": "contact_no" },
                    { "data": "email" },
                    { "data": "status" },
                    { "data": "id" }

                ],
                columnDefs: [ 
                    {
                        "targets": [0], "searchable": false, "orderable": false, "visible": true,
                        "targets": [4], "searchable": false, "orderable": false, "visible": true
                    },
                    {
                        "render": function ( data, type, row ) {
                            
                            if(row['status'] == '1')
                            {
                                return 'Active';
                            }
                            else if(row['status'] == '2')
                            {
                                return 'Blocked';
                            }
                            return '';
                        },
                        "targets": 6,
                        "orderable": false
                    },
                    {
                        "render": function ( data, type, row ) {
                          var editurl = "contestant";
                          var deleteurl = "contestantremove";
                            return '<a href="javascript:void(0)" class="text-primary" onclick="editModelForm('+data+',\''+editurl+'\' )" data-backdrop="static" data-keyboard="false" data-toggle="tooltip" data-placement="top" data-original-title="Edit Event"><i class="fas fa-edit"></i></a> <a href="javascript:void(0)" onclick="deletePop('+data+',\''+deleteurl+'\' )" class="text-danger" data-toggle="tooltip" data-placement="top" data-original-title="Delete Event"><i class="fas fa-trash-alt"></i></a>';
                        },
                        "targets": -1,
                        "orderable": false
                    }
                ]
                
            });
            
        });
    }

    //Judge Event List
     
    if ($("#judgeeventlist-table").length) {
        $(document).ready(function () { 
            var currentUserRole= $('#role-id').val();
            var table = $('#judgeeventlist-table').DataTable({
                processing: true,
                  serverSide: true,
                  sortable: true,
                ajax: {
                    url : apiUrl+"judgeeventlist",
                    type : 'GET',
                    
                    error: function(){  // error handling
                        $(".judgeeventlist-table-error").html("");
                        $("#judgeeventlist-table").append('<tbody class="judgeeventlist-table-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                        $("#judgeeventlist-table_processing").css("display","none");
     
                    }
                },
                drawCallback:function( settings, json){
                    $('[data-toggle="tooltip"]').tooltip();
                },
                order: [[ 1, 'asc' ]],
                columns: [
                    { "data": "slno" },
                    { "data": "event_name" },
                    { "data": "eventDate" },
                    { "data": "venue" },
                    { "data": "prelims" },
                    { "data": "status" },
                    { "data": "id" }

                ],
                columnDefs: [ 
                    {
                        "targets": [0], "searchable": false, "orderable": false, "visible": true,
                        "targets": [4], "searchable": false, "orderable": false, "visible": true
                    },
                    {
                        "render": function ( data, type, row ) {
                            eventdate = '';
                            if(row['prelims'] != 0 && row['prelims_date'] !== null && row['prelims_date'] != '')
                            {
                                eventdate = 'Prelims : '+ row['prelims_date'] + '<br> Main : ';
                            }
                            return eventdate + data;
                        },
                        "targets": 2,
                        "orderable": false
                    },
                    {
                        "render": function ( data, type, row ) {
                            venue = '';
                            if(row['prelims'] != 0 && row['prelims_venue'] !== null && row['prelims_venue'] != '')
                            {
                                venue = 'Prelims : '+ row['prelims_venue'] + '<br> Main : ';
                            }
                            return venue + data;
                        },
                        "targets": 3,
                        "orderable": false
                    },
                    {
                        "render": function ( data, type, row ) {
                            if(row['prelims'] == 0)
                            {
                                return 'No';
                            }
                            return 'Yes';
                        },
                        "targets": 4,
                        "orderable": false
                    },
                    {
                        "render": function ( data, type, row ) {
                            
                            if(row['status'] == '0')
                            {
                                return 'Active';
                            }
                            else if(row['status'] == '1')
                            {
                                return 'Judgement Open';
                            }
                            else if(row['status'] == '2')
                            {
                                return 'Judgement Closed';
                            }
                            return '';
                        },
                        "targets": 5,
                        "orderable": false
                    },
                    {
                        "render": function ( data, type, row ) {
                          var editurl = "event";
                          var deleteurl = "eventremove";
                          edit = '<a href="javascript:void(0)" class="text-primary" onclick="editModelForm1('+data+',\''+editurl+'\' )" data-backdrop="static" data-keyboard="false" data-toggle="tooltip" data-placement="top" data-original-title="Edit Event"><i class="fas fa-edit"></i></a> ';
                          

                          if(currentUserRole === '3')
                            {
                                return edit;
                            }
                                                    
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