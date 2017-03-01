
(function ($) {
    $(document).ready(function(){
        var loading = false;
        $("#escardante-pagespeed-form").submit(function(event){
            event.preventDefault();
            if(loading) return;
            loading = true;
            $(".loading-spinner").fadeIn();

            var firstName = $(this).find("input[name='name']").val();
            var lasttName = $(this).find("input[name='last']").val();
            var email = $(this).find("input[name='email']").val();
            var url= $(this).find("input[name='url']").val();

            $(this).find("input").attr("disabled", "disabled");
            $(this).fadeOut();

            if( firstName != "" && email != "" && url != "" ){
                var data = {
                    'action': 'escardante_pagespeed_create_prospect',
                    'data': {
                        name: firstName,
                        last: lasttName,
                        email: email,
                        url: url
                    }
                };
                $.post( vars.ajaxurl, data)
                    .done(function( data ) {
                        if( data.success ){
                            location.href = data.link;
                        }else{
                            alert( "An error occurred" )
                            $(".loading-spinner").fadeOut();
                            $(this).find("input").removeAttr("disabled");
                            $(this).fadeIn();
                            loading = false;
                        }
                    });
            } else {
                alert( "Please fill in all the required fields" )
                $(".loading-spinner").fadeOut();
                $(this).find("input").removeAttr("disabled");
                $(this).fadeIn();
                loading = false;
            }

        });
    });
})(jQuery);
