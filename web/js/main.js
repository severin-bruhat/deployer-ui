$( "#update_recipe" ).bind( "click", function() {
    $.ajax({
        type: "POST",
        url: "/web/ajax/updateRecipe",
        data: {
            content: {recipe:$( "#update_recipe" ).data("ref"), deploy_file_content:$('#deploy_file').val()} 
        },
        success: function( data ) {
            console.log(data);
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            console.log(xhr.status);
            console.log(thrownError);
        }
    })  
});

$( "#deployStaging" ).bind( "click", function() {
    $.ajax({
        type: "POST",
        url: "/web/ajax/deployStaging",
        data: {
            content: {recipe:$( "#deployStaging" ).data("ref")} 
        },
        success: function( data ) {
            console.log(data);
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            console.log(xhr.status);
            console.log(thrownError);
        }
    })  
});

 $('textarea.code').ace({ theme: 'monokai', lang: 'php' });

