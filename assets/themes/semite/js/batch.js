/**
 * Created by root on 10/18/16.
 */
$('#proccess_batch').on('click',function(){

    $('.alert-validation').hide();

    var error = false;
    var cardnumber = $('input[name=\'creditCard[cardNumber]\']');
    var expMonth = $('input[name=\'creditCard[cardExpiryMonth]\']');
    var expYear = $('input[name=\'creditCard[cardExpiryYear]\']');
    var cardCvv = $('input[name=\'creditCard[cardCvv]\']');
    var countryId = $('select[name=\'countryId\']');
    var currencyId = $('select[name=\'currencyId\']');
    var amount = $('input[name=\'amount\']');
    var tracking = $('input[name=\'trackingMemberCode\']');
    var phone = $('input[name=\'additionalInfo[phone]\']');
    var email = $('input[name=\'additionalInfo[email]\']');
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    var isValidMoney = /^\d{0,4}(\.\d{0,2})?$/.test(amount.val());
    var method = $('select[name=\'method\']');
    var threeds = $('input[name=\'threeds\']');

    if (cardnumber.val() == ""){
        error = true;
        cardnumber.next().show();
    }

    if (error == false) {

        $.ajax({
            url: site_url + 'terminal/validatepayment',
            type: 'post',
            data: 'cardnumber=' + cardnumber.val(),
            dataType: 'json',
            beforeSend: function () {
            },
            complete: function () {
            },
            success: function (json) {
                if (json.status != "valid"){
                    error = true;
                    cardnumber.next().show();
                } else {
                    cardnumber.next().hide();
                }
            }
        });
    }

    if (expMonth.val() == ""){
        error = true;
        expMonth.next().show();
    }

    if (expYear.val() == ""){
        error = true;
        expYear.next().show();
    }

    if (error == false) {

        var exp_date = expMonth.val()+'/'+expYear.val();
        var status = checkExpire(exp_date);

        if (status !=true){

            error = true;

            $('#general-error').html('Credit Card Expiry date is Invalid!');
            $('#general-error').show();

        } else {
            $('#general-error').hide();
        }
    }

    if (cardCvv.val() == "" || !$.isNumeric(cardCvv.val())){
        error = true;
        cardCvv.next().show();
    }

    if (countryId.val() == ""){
        error = true;
        countryId.next().show();
    }

    if (currencyId.val() == ""){
        error = true;
        currencyId.next().show();
    }

    if (amount.val() == "" || !isValidMoney){
        error = true;
        amount.next().show();
    }

    if (tracking.val() == ""){
        error = true;
        tracking.next().show();
    }

    if (phone.val() != "" &&!$.isNumeric(phone.val())){
        error = true;
        phone.next().show();
    }

    if (email.val() == "" || !email.val().match(re)){
        error = true;
        email.next().show();
    }

    if (error == true){

        return false;
    }


    var data = $('.terminal-form').serialize();

    var process_url = site_url + 'batch/process';

    setTimeout(function () {
        $.ajax({
            url: process_url,
            type: 'post',
            data: data,
            dataType: 'json',
            beforeSend: function () {
                $('.panel-body').hide();
                $('.panel-body').html('<div class="loading"><h4 class="text-center text-muted"><i class="fa fa-spinner fa-spin"></i>  Please wait</h4><p class="text-center">Processing your payment<br/>using <b>Basic Operations</b></p></div>');
                $('.panel-body').show();
            },
            complete: function () {
            },
            success: function (json) {
                $('.loading').hide();
                $('.panel-body').load(site_url + 'batch/success/' + json.id);

            }
        });
    }, 200);

});

$('#batch_process').on('click',function(){

    $('.alert-validation').hide();

    var error = false;
    var batch = $('input[name=\'brn\']');

    if (batch.val() == ""){
        error = true;
        batch.next().show();
    }

    if (error == true){

        return false;
    }

    setTimeout(function () {
        $.ajax({
            url: site_url + 'batch/processor',
            type: 'post',
            data: 'batch='+batch.val(),
            dataType: 'json',
            beforeSend: function () {
                $('.panel-body').hide();
                $('.panel-body').html('<div class="loading"><h4 class="text-center text-muted"><i class="fa fa-spinner fa-spin"></i>  Please wait</h4><p class="text-center">Processing your payment<br/>using <b>Basic Operations</b></p></div>');
                $('.panel-body').show();
            },
            complete: function () {
            },
            success: function (json) {
                $('.loading').hide();

                if (json.hasOwnProperty('error')){

                    $('.panel-body').load(site_url + 'batch/failed_process/' + json.error);
                } else {
                    console.log(json);

                    if (json.response_code == '1'){
                        $('.panel-body').load(site_url + 'batch/success_process/' + json.response_code);

                    } else {

                        $('.panel-body').load(site_url + 'batch/failed_process/' + json.response_code);

                    }
                }

            }
        });
    }, 200);



});

$(document).ready(function() {

    /* Account Balance */
    var exRowTable3 = $('#batch-log').DataTable({
        responsive: true,
        dom: 'Bfrtip',
        className: 'btn btn-default',
        buttons: [
            'csv', 'excel', 'pdf', 'print'
        ],
        'ajax': site_url + 'batch/batch_list',
        'columns': [{
            'class': 'details-control',
            'orderable': false,
            'data': null,
            'defaultContent': ''
        },
            {'data': 'id'},
            {'data': 'status'},
            {'data': 'date_added'}
        ],
        'order': [[3, 'desc']]
    });
});