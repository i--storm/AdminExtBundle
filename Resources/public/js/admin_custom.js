$(document).ready(function(){
    if($( ".sortable").length){
        $( ".sortable" ).sortable({
            update: function( event, ui ) {
                var order='';
                $(this).children().each(function(idx,elem){
                    order=order+$(elem).attr('image_id')+' ';
                });
                var url=$(this).attr('callback_url');
                $.ajax({
                    type: 'POST',
                    url: url,
                    data: { new_order: order },
                    success: function(data){

                    }
                });
            }
        });
        $( ".sortable" ).disableSelection();
    }
});