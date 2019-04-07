/* 
 * Common js function and global variables
 */
$.ajaxSetup({ 
    headers: {
    //    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
var apiUrl = 'http://localhost/natanahtml/TAB/index.php/';
var page_refresh = function () {
    window.location.reload(true);
};
var page_redirect = function ($page) {
    window.location = $page;
};
var parse_JSON = function (data) {
    try {
        var obj = JSON && JSON.parse(data) || $.parseJSON(data);
        return obj;
    } catch (e) {
        // not json
        console.log(data);
        alert("Oops, it looks like there is an issue, we are looking to fix it");
        return false;
    }

};

var alertFormCommon = function ($str, $form) {

    $("#alert-modal .message").html($str);
};

var form_basic_reload = function (response, form, submit_btn) {

    if (response.status === "error") {
        alertFormCommon(response.message, form);
    } else if (response.status === "success") {
        alertFormCommon(response.message, form);
        $(form)[0].reset();
        page_refresh();
    }
    submit_btn.removeAttr("disabled");
};

var form_basic_no_reload = function (response, form, submit_btn) {

    if (response.status === "error") {
        alertFormCommon(response.message, form);
    } else if (response.status === "success") {
        alertFormCommon(response.message, form);
        $(form)[0].reset();
    }
    submit_btn.removeAttr("disabled");
};

var form_basic_redirect = function (response, form, submit_btn) {

    if (response.status === "error") {
        alertFormCommon(response.message, form);
    } else if (response.status === "success") {
        alertFormCommon(response.message, form);
        $(form)[0].reset();
        var redirect_url = $(form).find("input.callback-path").val();
        page_redirect(redirect_url);
    }
    submit_btn.removeAttr("disabled");
};

var form_url_redirect = function (response, form, submit_btn) {

    if (response.status === true) {
        submit_btn.removeAttr("disabled");
        $(form)[0].reset();
        page_redirect(response.url);
    }
    
};

var remeberme_cookie = function (response, form) {
    if (response.status === true) {
        if ($(form).hasClass('remember_me'))
        {
            if ($('#remember_me').is(':checked')) {
                // save username and password
                localStorage.usrname = $('#officialemail').val();
                localStorage.pass = $('#password').val();
                localStorage.chkbx = $('#remember_me').val();
            } else {
                localStorage.usrname = '';
                localStorage.pass = '';
                localStorage.chkbx = '';
            }
        }
    }
};

var check_session = function ()
{
    var url      = window.location.href; 
    var method = 'post';
    var action = apiUrl + 'check_session';
    var datastring = {
        url : url
    };

    $.ajax({
        type: method,
        url: action,
        data: datastring,	
        async: false,		
        dataType: "json",
        success: function (data) {
            
            var response = data;
            
            if(typeof response.status !== 'undefined' 
            && response.status == false 
            && typeof response.url !== 'undefined' 
            && response.url !=='')
            {
                page_redirect(response.url);
            }
            else if(typeof response.data !== 'undefined')
            {
                var div = '';
                $.each(response.data, function (key, val) {
                    div = div + '<input type="hidden" id="'+key+'" value="'+val+'" >';
                });
                var $div = $(div).appendTo('body');
            }
        },
        error: function (data) {  

           
        }
    });
}
	/** function for loader show and hide starts **/
					
	function showLoader()
    {
		$('.loader-container').show();
        $('.loader-back').show();
    }
			   
	function hideLoader()
    {
        $('.loader-container').hide();
        $('.loader-back').hide();
    }

	/** loader function ends **/	
$(document).ready(function () {
    check_session();
   
    /*
     * Just add the .form-common class on the form you want to submit
     * define the method and action
     * optionally you can define a custom callback function 
     * for response handling
     */
   
    $(document).on('submit', '.form-common', function (e) {
        e.preventDefault();
        showLoader();
        var form = this;
        var form_id = $(form).attr('id');
        var method = $(form).attr('method');
        var action = apiUrl + $(form).attr('action');
        var name = $(form).attr('name');
        var callback = $(form).find("input.callback");
        var arg     = $(form).find("input.arg").val();
        var datastring = $(form).serialize();
        var submit_btn = $(form).find('button[type=submit]');
        var reset_btn = $(form).find('button[type=reset]');
        var pageReset = $(form).attr('page-reset');
        console.log('formname'+name);
        if (callback.length > 0) {
            callback = callback.val();
        } else {
            callback = false;
        }
		var page = $("#pageno").val();
		if (typeof page !== 'undefined' )
		{
            if (typeof pageReset !== typeof undefined && pageReset !== 'false')
            {
                page = 1;
                $("#pageno").val(page);
            }
			action = action+'?page='+page;
		} 

        submit_btn.attr('disabled', 'disabled');
        reset_btn.attr('disabled', 'disabled');
        submit_btn.html('Please wait..');
		$( form ).find(".form-control").removeClass("red_border");
		$( form ).find(".form-control").removeClass("text-danger");
        $( form ).find("span.error").empty();

        $.ajax({
            type: method,
            url: action,
            data: datastring,			
			dataType: "json",
            success: function (data) {
				
                var response = data;
                
				if(response.status==true)
				{
                    remeberme_cookie(response, form);
					if (typeof response.html !== 'undefined' )
					{
						$('#list').html(response.html);
                    }
                    if (typeof response.url !== 'undefined' && response.url !=='')
					{
						form_url_redirect(response, form, submit_btn);
                    }
					if (typeof response.msg !== 'undefined' )
					{ 
                            
                        $(".alert").fadeIn('fast');
                        $(".alert").addClass('alert-success').removeClass('alert-danger');
                        $(".alert").html(response.msg);
                        $('.alert').delay(10000).fadeOut(2500); 
                        //$('#editModelDiv').modal('hide');
                        if (response.reset == true)
                        {
                            form.reset();
                        }if (response.reload == true)
                        {   //alert();
                            page_refresh();
                        }if ($(form).hasClass('reload'))
                        {   //alert();
                            page_refresh();
                        }
					}
                    if (response.refresh == true)
                    {
                        $('.listing').submit();
                    }
					
				}
				else if(response.status==false)
				{ 
					if (typeof response.error !== 'undefined' )
					{
                        $.each(response.error, function (key, val) {
                            var msg = '<ul class="parsley-errors-list filled" ><li class="parsley-required">'+val+'</li></ul>'
                            $('.'+key+'-div').append(msg);
                           
                        });
					}else if (typeof response.msg !== 'undefined' ){
                            $(".alert").fadeIn('fast');
                            $(".alert").addClass('alert-danger').removeClass('alert-success');
                            $(".alert").html(response.msg);
                            $('.alert').delay(1000).fadeOut(2500); 

                        
                    }
                     if (response.reset != false)
                        {alert()
                            form.reset();
                        }
                    if (response.refresh == true)
                    {
                        $('.listing').submit();
                    }
					
				}
				else
				{
					//alertFormCommon("Result Error");
				}
                submit_btn.removeAttr("disabled");
                reset_btn.removeAttr("disabled");
                submit_btn.html('Submit');
                $(form).removeAttr('page-reset');
                /* Hide pop-up after submiting form */
                if($( form ).hasClass('hide_modal')){ 
                    var modal_name = $('.modal_name').val();
                    $("#"+modal_name).modal('hide'); 
                }
				if (callback) { //show_helpdesk_listing(response, form, submit_btn);
                    if(arg == 0){
                        window[callback]();
                    }else{
                        window[callback](response, form, submit_btn);
                    }
                }
				hideLoader();		
            },
            error: function (data) {  

                $("#msg").fadeIn('fast');
                $("#msg").addClass('alert-danger').removeClass('alert-success');
                $("#msg").html('Validation Error.');
                $('#msg').delay(1000).fadeOut(2500);

                /*$( form ).find(".message").addClass("alert");
                        $( form ).find(".message").addClass("alert-danger");
                        $( form ).find('.message').html("Validation error");

                        setTimeout(function() {
                            $( form ).find(".message").empty();
                            $( form ).find(".message").removeClass("alert-danger");
                            $( form ).find(".message").removeClass("alert");
                        }, 3000);*/

				$.each(data.responseJSON.errors, function (i) {

                    $.each(data.responseJSON.errors, function (key, val) {
                       /*$("#"+form_id+" #"+key).addClass("red_border");
                       $("#"+form_id+" #"+key).addClass("text-danger");
                       $("#"+form_id+" #"+key+'_err').html(val);*/


                        $( form ).find("#"+key).addClass("red_border");
                        $( form ).find("#"+key).addClass("text-danger");
                        $( form ).find("#"+key+'_err').html(val);
                       
                    });
                });
                submit_btn.removeAttr("disabled");
                reset_btn.removeAttr("disabled");
                submit_btn.html('Submit');
                $(form).removeAttr('page-reset');
				hideLoader();
            }
        });

        return false;
    });

    $(document).on('click', 'button[type="reset"]', function (e) {
        var form = $(this).closest("form");
        $( form ).find(".form-control").removeClass("red_border");
        $( form ).find(".form-control").removeClass("text-danger");
        $( form ).find("span.error").empty();
    });
    
    
});
$('#editModelDiv').on('hidden.bs.modal', function (e) {
    page_refresh()
  })
function editModelForm(id, url, model)
{
    var method = 'post';
    var action = apiUrl + url+'/'+id;
    var datastring = {
        id : id
    };

    $.ajax({
        type: method,
        url: action,		
        dataType: "json",
        success: function (data) {
            
            var response = data;
            
            if(response.status==true)
            {
                if(typeof response.data !== 'undefined')
                {
                    $.each(response.data, function (key, val) {
                        $('#'+key).val(val);
                    });
                    $('#editModelDiv').modal('show');
                }
            }
        }
    });
}



function ressetListForm()
{
    document.forms["form-common"].reset();
}
function ressetListForm_withCountBox()
{
    document.forms["form-common"].reset();
	$('.query_status').empty();
	$('.query_status').append("<option value='0'>Select Status</option>");
}

//common function for showing popup for deletion
function deletePop(actionUrl, id = null)
{	
    $("#deleteRecord form").attr('action', actionUrl);
	$("#deleteRecord #id").val(id);
	$('#deleteRecord').modal({backdrop: false, keyboard: false});
}
function activatePop(actionUrl, categoryid = null,type=null)
{   
    $("#activateRecord form").attr('action', actionUrl);
    $("#activateRecord #categoryid").val(categoryid);
    if(type != null)
    {
        $("#activateRecord #type").val(type);
    }
    $('#activateRecord').modal({backdrop: false, keyboard: false});
}
function refresh()
{
    $('.listing').submit();
}

$(function() {
 
    if (localStorage.chkbx && localStorage.chkbx != '') {
        $('#remember_me').attr('checked', 'checked');
        $('#officialemail').val(localStorage.usrname);
        $('#password').val(localStorage.pass);
    } else {
        $('#remember_me').removeAttr('checked');
        $('#officialemail').val('');
        $('#password').val('');
    }
});
$(function() {
    var roleId = $('#role-id').val();
    if(roleId==1)
    {
        $('#nav-menu-session').load('./includes/adminnav.html');
        
    $('#current-user').val('hhhhh');
    }
    else if(roleId==2)
    {
        $('#nav-menu-session').load('./includes/committeenav.html');
    }
    else if(roleId==3)
    {
        $('#nav-menu-session').load('./includes/judgenav.html');
    }
    
});
function onNewMainContent() { 
    alert(111);
    var currentUser = $('#user-name').val();
    var currentUser1 = $('#current-user').val();
    alert(currentUser1)
    $('#current-user').html(currentUser);

}

