/* 
 * Common js function and global variables
 */
$.ajaxSetup({ 
    headers: {
    //    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
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
var update_part = function ($target, $elem) {
    $.ajax({
        type: "POST",
        url: $target,
        success: function (data) {
            $($elem).html(data);
        },
        error: function () {
            return false;
        }
    });
};
var hideAlertFormCommon = function ($form) {
   // $.magnificPopup.close();
};
var hideProgressModal = function () {
   // $.magnificPopup.close();
};
var alertFormCommon = function ($str, $form) {
    hideProgressModal();
    $("#alert-modal .message").html($str);
};

var form_basic_reload = function (response, form, submit_btn) {

    if (response.status === "error") {
        alertFormCommon(response.message, form);
    } else if (response.status === "success") {
        alertFormCommon(response.message, form);
        $(form)[0].reset();
        window.location.reload();
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
        window.location = redirect_url;
    }
    submit_btn.removeAttr("disabled");
};

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
        var action = $(form).attr('action');var action2 = action;
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

        hideAlertFormCommon(form);
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
					 
					if (typeof response.html !== 'undefined' )
					{
						$('#list').html(response.html);
                    }
                    if ($(form).hasClass('remember_me'))
                    {   alert();
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
					if (typeof response.message !== 'undefined' )
					{ 
                        $("#msg").fadeIn('fast');
                        $("#msg").addClass('alert-success').removeClass('alert-danger');
                        $("#msg").html('Saved Successfully.');
                        $('#msg').delay(1000).fadeOut(2500);
                        $('#deleteRecord').modal('hide');
                        $('#activateRecord').modal('hide');
                        if (response.reset == true)
                        {
                            form.reset();
                        }if (response.reload == true)
                        {   //alert();
                            window.location.reload();
                        }if ($(form).hasClass('reload'))
                        {   //alert();
                            window.location.reload();
                        }
					}
                    if (response.refresh == true)
                    {
                        $('.listing').submit();
                    }
					
				}
				else if(response.status==false)
				{ 
					if (typeof response.message !== 'undefined' )
					{

                        $("#msg").fadeIn('fast');
                        $("#msg").addClass('alert-danger').removeClass('alert-success');
                        $("#msg").html(response.message);
                        $('#msg').delay(1000).fadeOut(2500); 
					}else{
                        $("#msg").fadeIn('fast');
                        $("#msg").addClass('alert-danger').removeClass('alert-success');
                        $("#msg").html('Something went wrong.');
                        $('#msg').delay(1000).fadeOut(2500); 
                    }
                     if (response.reset != false)
                        {
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
	
	
	$(document).on('submit', '.form-upload', function (e) {
        e.preventDefault();
        var form = this;
        
        if($(form).hasClass('tinymce')){
            tinyMCE.triggerSave();
        }

        var form = this;
        var form_id = $(form).attr('id');
        var method = $(form).attr('method');
        var action = $(form).attr('action');
        var name = $(form).attr('name');
        var callback = $(form).find("input.callback");
        var arg     = $(form).find("input.arg").val();
        var datastring = new FormData(this);
        var submit_btn = $(form).find('button[type=submit]');
        var submit_btn = $(form).find('button[type=submit]');
        var reset_btn = $(form).find('button[type=reset]');
        var page_reset = $(form).attr('page-reset');
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

        hideAlertFormCommon(form);
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
            cache: false,
            contentType: false,
            processData: false,
            success: function (data) {
                //var response = parse_JSON(data);
				var response = data;
                if(response.status==true)
                {
                     
                    if (typeof response.html !== 'undefined' )
                    {
                        $('#list').html(response.html);
                        totalcount = $("#list_count").val();
                        if (totalcount == null){ totalcount = 0; }
                        $("#totalcount").html('('+totalcount+')');
                    }
                    if (typeof response.message !== 'undefined' )
                    { 
                        $("#msg").fadeIn('fast');
                        $("#msg").addClass('alert-success').removeClass('alert-danger');
                        $("#msg").html('Saved Successfully.');
                        if (form_id == "enquiry_form")
                        {
                            $("#msg").append('<button type="button" class="close" onclick="$(\'.alert\').hide();" aria-label="Close"><span aria-hidden="true">&times;</span></button>');
                        }
                        else
                        {
                            $('#msg').delay(1000).fadeOut(2500);
                        }


                        /*$( form ).find(".message").addClass("alert");
                        $( form ).find(".message").addClass("alert-success");
                        $( form ).find('.message').html(response.message);

                        setTimeout(function() {
                            $( form ).find(".message").empty();
                            $( form ).find(".message").removeClass("alert-success");
                            $( form ).find(".message").removeClass("alert");
                            $('#deleteRecord').modal('hide');
                            $('#activateRecord').modal('hide');
                        }, 3000);*/
                        if (response.reset == true)
                        {
                            form.reset();
                        }if (response.reload == true)
                        {   //alert();
                            window.location.reload();
                        }if ($(form).hasClass('reload'))
                        {   //alert();
                            window.location.reload();
                        }

                        
                    }
                    if (response.refresh == true)
                    {
                        $('.listing').submit();
                    }
                    
                }
                else if(response.status==false)
                { 
                    

                    if (typeof response.message !== 'undefined' )
                    {

                        $("#msg").fadeIn('fast');
                        $("#msg").addClass('alert-danger').removeClass('alert-success');
                        $("#msg").html('Something went wrong.');
                        $('#msg').delay(1000).fadeOut(2500);

                        /*$( form ).find(".message").addClass("alert");
                        $( form ).find(".message").addClass("alert-danger");
                        $( form ).find('.message').html(response.message);
                        $( form ).find('.message').html(response.message);
                        setTimeout(function() {
                            $( form ).find(".message").empty();
                            $( form ).find(".message").removeClass("alert-danger");
                            $( form ).find(".message").removeClass("alert");
                        }, 3000);*/
                    }
                     if (response.reset != false)
                        {
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
                 if (callback) { //show_helpdesk_listing(response, form, submit_btn);
                    if(arg == 0){
                        window[callback]();
                    }else{
                        window[callback](response, form, submit_btn);
                    }
                } 
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
            }
        });

        return false;
    });
	
	$(document).on('click', '.popup-modal-dismiss', function (e) {
        //$.magnificPopup.close();
    });

    $(document).on('click', 'button[type="reset"]', function (e) {
        var form = $(this).closest("form");
        $( form ).find(".form-control").removeClass("red_border");
        $( form ).find(".form-control").removeClass("text-danger");
        $( form ).find("span.error").empty();
    });
	
  $('body').on('click', '.first .pagination a', function(e) {
                e.preventDefault();


                var pagination = $(this).attr('href');
				var fields = pagination.split('?page=');

				var url = fields[0];
				var page = fields[1];
				$("#pageno").val(page);
				$('.listing').submit();
            });
	
	 		
	$('body').on('click', '.second .pagination a', function(e) {
                e.preventDefault();

				  var pagination = $(this).attr('href');
				var fields = pagination.split('?page=');

				var url = fields[0];
				var page = fields[1];
				$("#pageno").val(page); 
				$('.listing2').submit();
            }); 
  
	$('.listing').submit();
    //If you need to reset pageno of a listing page on an element click (for eg. find button), add a class "reset-pageno" to the element
    $('.reset-pageno').on('click', function() {
        $('.listing').attr('page-reset', 'true');
    });
});


   
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
