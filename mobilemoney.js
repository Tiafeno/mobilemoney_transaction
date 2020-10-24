var successCallback = function (data) {

    var checkout_form = $('form.woocommerce-checkout');

    // add a token to our hidden input field
    //checkout_form.find('#misha_token').val(data.token);

    // deactivate the tokenRequest function event
    checkout_form.off('checkout_place_order', tokenRequest);

    // submit the form now
    checkout_form.submit();

};

var errorCallback = function (data) {
    console.log(data);
};

var tokenRequest = function () {
    console.log("teste");
    // here will be a payment gateway function that process all the card data from your form,
    // maybe it will need your Publishable API key which is mmm_params.publishableKey
    // and fires successCallback() on success and errorCallback on failure
    return true;

};

jQuery(function ($) {

    var checkout_form = $('form.woocommerce-checkout');
    checkout_form.on('checkout_place_order', tokenRequest);

    $(document).ready(function () {
        setTimeout(function () {
            $('#paiement_visa').on('click', function (e) {
                console.log("Ready!")
                var approcarte = window.open("https://approcarte.orange.mg/", "Approcarte", "width=750,height=480");
                if(approcarte) approcarte.focus();

            });

            $("#mm_transaction").keyup(function (e) {
                e.preventDefault();
                var el = e.currentTarget;
                var testInput = /([A-Z0-9]{7}\.[0-9]{3,4}\.[A-Z0-9]{5})/.test(e.target.value);
                var color = testInput ? 'green' : 'red';
                $(el).css({'border': `4px solid ${color}`});
            });

        }, 2000);

    });

});