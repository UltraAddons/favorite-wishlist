jQuery(document).ready(function($){
    $(document).on('click', '.wpt-favorite-button', function(){
        var product_id = $(this).data('product_id');
        var user_id = $(this).data('user_id');
        var status = $(this).attr('data-status');
        $('.message-favorite-button-' + product_id).html("Loading...");
        $.ajax({
                type: 'POST',
                url: myAjax.ajaxurl,// + get_data,
                data: {
                    action:     'favorite_user_meta_update',
                    product_id: product_id,
                    user_id:    user_id,
                    status:     status,
                },
                complete: function(){

                },
                success: function(data) {
                    console.log(data);
                    if(data !== 'success'){
                        $('.message-favorite-button-' + product_id).html(data);
                        return;
                    }
                    if('liked' === status){
                        $('.message-favorite-button-' + product_id).html("Unliked");
                        
                        $('.favorite-button-' + product_id).removeClass('favorite-button-liked');
                        $('.favorite-button-' + product_id).addClass('favorite-button-no-like');
                        $('.favorite-button-' + product_id).attr('data-status','no-like');
                    }else{
                        $('.message-favorite-button-' + product_id).html("Liked");
                        $('.favorite-button-' + product_id).removeClass('favorite-button-no-like');
                        $('.favorite-button-' + product_id).addClass('favorite-button-liked');
                        $('.favorite-button-' + product_id).attr('data-status','liked');
                    }
                },
                error: function() {
                    
                },
            });
    });
});
