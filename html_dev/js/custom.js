
function getCompanyList(model = '')
{ 
        var company_id = $('#'+model).find('input[name="company_id"]').val();
        var flag =       $('#'+model).find('input[name="flag"]').val();
        var selected;
        if(typeof company_id == 'undefined')
        {
            company_id = '';
        }
        if(typeof flag == 'undefined')
        {
            flag = '';
        }
        var method = 'post';
        var action = apiUrl + "get_companylist";
        $.ajax({
            type: method,
            url: action,		
            dataType: "json",
            async: true,
            success: function (data) {
                
                var response = data;
                
                if(response.status==true)
                {
                    if(typeof response.data !== 'undefined')
                    {
                        $('#company'+flag).empty();
                        $('#company'+flag).append("<option value='0'>Select Company</option>");
                        $.each(response.data, function(i, d) {
                            
                            var opt = $('<option />');
                            selected = '';
                            if(d.id == company_id)
                            {
                                selected = 'selected';
                            }
                            opt = "<option value='" + d.id + "' "+ selected +" >" + d.value + " </option>";
                            $('#company'+flag).append(opt);

                        });
                        
                    }
                }
            }
        });
     
}
function getCoordinatorList(model = '')
{
        var contact_id = $('#'+model).find('input[name="contact_id"]').val();
        var selected;
        if(typeof contact_id !== 'undefined')
        {
            contact_id = contact_id;
            edit = 'edit';
        }
        else
        {
            contact_id = '';
            edit = '';
        }
        var method = 'post';
        var action = apiUrl + "get_coordinatorlist";
        $.ajax({
            type: method,
            url: action,		
            dataType: "json",
            async: true,
            success: function (data) {
                
                var response = data;
                
                if(response.status==true)
                {
                    if(typeof response.data !== 'undefined')
                    {
                        $('#contact'+edit).empty();
                        $('#contact'+edit).append("<option value='0'>Select Contact</option>");
                        $.each(response.data, function(i, d) {
                            var opt = $('<option />');
                            selected = '';
                            if(d.id == contact_id)
                            {
                                selected = 'selected';
                            }
                            opt = "<option value='" + d.id + "' "+ selected +" >" + d.value + " </option>";
                            $('#contact'+edit).append(opt);

                        });
                        
                    }
                }
            }
        });
     
}