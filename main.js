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