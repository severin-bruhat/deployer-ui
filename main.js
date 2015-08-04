$( "#update_recipe" ).bind( "click", function() {
    $.ajax({
        type: "POST",
        url: "/web/ajax/updateRecipe",
        data: {
            content: "data-ref"
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