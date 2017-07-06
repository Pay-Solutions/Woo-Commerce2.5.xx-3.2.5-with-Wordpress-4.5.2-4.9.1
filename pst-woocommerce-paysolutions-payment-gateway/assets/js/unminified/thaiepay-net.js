jQuery( document ).ready( function( $ ){
    var payment_form = $( '#thaiepay_net_payment_form'),
        radio_change_handling = function(){
            var credit_card_form = $( '#pay_solutions_credit_card_form'),
                new_form = credit_card_form.find( '.new-profile-form' );

            if( credit_card_form.length != 0 ){
                credit_card_form.find( '.payment-profile-radio' ).change( function(){
                    var t = $( this );

                    if( t.is( '#pay_solutions_payment_profile_new:checked' ) ){
                        new_form.slideDown();
                    }
                    else{
                        new_form.slideUp();
                    }
                } );
            }
        };

    if( payment_form.length != 0 ){
        payment_form.find( 'input[type="submit"]').click();
    }

    $( 'body' ).on( 'updated_checkout', radio_change_handling );
    radio_change_handling();
} );