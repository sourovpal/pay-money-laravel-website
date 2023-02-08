'use strict';

function restrictNumberToPrefdecimalOnInput(e)
{
    var type = $('select#currency_id').find(':selected').data('type')
    restrictNumberToPrefdecimal(e, type);
}

function determineDecimalPoint() {

    var currencyType = $('select#currency_id').find(':selected').data('type')

    if (currencyType == 'crypto') {
        $('.pFees, .fFees, .total_fees').text(CRYPTODP);
        $("#amount").attr('placeholder', CRYPTODP);

    } else if (currencyType == 'fiat') {

        $('.pFees, .fFees, .total_fees').text(FIATDP);
        $("#amount").attr('placeholder', FIATDP);
    }
}

if ($('.main-content').find('#crypto-send-create').length) {

    var amount;
    var receiverAddress;
    var priority = 'low';
    
    var receiverAddressValidationError = $('.receiver-address-validation-error');
    var amountValidationError = $('.amount-validation-error');

    var receiverAddressErrorFlag = false;
    var amountErrorFlag = false;

    function checkSubmitBtn()
    {
        if (!receiverAddressErrorFlag && !amountErrorFlag) {
            $("#crypto-send-submit-btn").attr("disabled", false);
        } else {
            $("#crypto-send-submit-btn").attr("disabled", true);
        }
    }

    let currencyType = $('#network').data('type');
    if (currencyType == 'crypto' || currencyType == 'crypto_asset') {
        $("#amount").attr('placeholder', CRYPTODP);
    }

    // Address validity check
    function checkAddressValidity(receiverAddress, walletCurrencyCode)
    {
        return new Promise(function(resolve, reject) {
            $.ajax({
                method: "GET",
                url: validateAddressUrl,
                dataType: "json",
                data: {
                    'receiverAddress': receiverAddress,
                    'walletCurrencyCode': walletCurrencyCode,
                },
                beforeSend: function ()
                {
                    swal(pleaseWait, loading, {
                        closeOnClickOutside: false,
                        closeOnEsc: false,
                        buttons: false,
                    });
                },
            })
            .done(function(res)
            {
                swal.close();
                if (res.status != 200) {
                    receiverAddressValidationError.text(res.message);
                    receiverAddressErrorFlag = true;
                    checkSubmitBtn();
                } else {
                    receiverAddressValidationError.text('');
                    receiverAddressErrorFlag = false;
                    checkSubmitBtn();
                }
                resolve(res.status);
            })
            .fail(function(err)
            {
                swal.close();
                err.responseText.hasOwnProperty('message') == true ? alert(JSON.parse(err.responseText).message) : alert(err.responseText);
                reject(err);
                return false;
            });
        });
    }


    // Check minimum amount
    function checkMinimumAmount(message)
    {
        amountValidationError.text(message);
        receiverAddressErrorFlag = true;
        amountErrorFlag = true;
        checkSubmitBtn();
    }

    // Check amount validity
    function checkAmountValidity(amount, senderAddress, receiverAddress, walletCurrencyCode)
    {
        priority = getPriority($('#priority').val());
        return new Promise(function(resolve, reject)
        {
            // Minimum Amounts You can prepare transactions for sending at least 0.02 DOGE, 0.00002 BTC, or 0.0002 LTC. (BlockIo)
            if ((walletCurrencyCode == 'DOGE' || walletCurrencyCode == 'DOGETEST') && amount < 2) {
                checkMinimumAmount(`${minCryptoAmount.replace(':x', 2 + ' ' + walletCurrencyCode)}`)
            }
            else if ((walletCurrencyCode == 'BTC' || walletCurrencyCode == 'BTCTEST') && amount < 0.00002) {
                checkMinimumAmount(`${minCryptoAmount.replace(':x', 0.00002 + ' ' + walletCurrencyCode)}`)
            }
            else if ((walletCurrencyCode == 'LTC' || walletCurrencyCode == 'LTCTEST') && amount < 0.0002) {
                checkMinimumAmount(`${minCryptoAmount.replace(':x', 0.0002 + ' ' + walletCurrencyCode)}`)
            } else {
                $.ajax({
                    method: "GET",
                    url: validateBalanceUrl,
                    dataType: "json",
                    data: {
                        'amount': amount,
                        'senderAddress': senderAddress,
                        'receiverAddress': receiverAddress,
                        'walletCurrencyCode': walletCurrencyCode,
                        'priority': priority,
                    },
                    beforeSend: function ()
                    {
                        swal(pleaseWait, loading, {
                            closeOnClickOutside: false,
                            closeOnEsc: false,
                            buttons: false,
                        });
                    },
                })
                .done(function(res) {
                    swal.close();
                    if (res.status == 401) {
                        amountValidationError.text(res.message);
                        amountErrorFlag = true;
                        checkSubmitBtn();
                    } else {
                        amountValidationError.text('');
                        receiverAddressErrorFlag = false;
                        amountErrorFlag = false;
                        checkSubmitBtn();
                    }
                    resolve();
                })
                .fail(function(err)
                {
                    swal.close();
                    err.responseText.hasOwnProperty('message') == true ? alert(JSON.parse(err.responseText).message) : alert(err.responseText);
                    reject(err);
                    return false;
                });
            }
        });
    }

    $(window).on('load', function (e)
    {
        var previousUserCrytoSentUrl = window.localStorage.getItem("previousUserCrytoSentUrl");
        var userConfirmationCryptoSentUrl = cryptoSendConfirmUrl;
        var userCryptoSendAmount = window.localStorage.getItem('user-crypto-sent-amount');
        var userCryptoReceiverAddress = window.localStorage.getItem('user-crypto-receiver-address');
        var priority = window.localStorage.getItem('priority');

        if ((userConfirmationCryptoSentUrl == previousUserCrytoSentUrl) && userCryptoSendAmount != null && userCryptoReceiverAddress != null) {
            swal(pleaseWait, loading, {
                closeOnClickOutside: false,
                closeOnEsc: false,
                buttons: false,
            });

            $('.amount').val(userCryptoSendAmount);
            $('.receiverAddress').val(userCryptoReceiverAddress);
            $('.priority').val(priority);

            //Get network fees
            checkAmountValidity($('.amount').val().trim(), senderAddress, $(".receiverAddress").val().trim(), walletCurrencyCode)
            .then(() =>
            {
                $("#crypto-send-submit-btn").attr("disabled", false);
                $(".spinner").hide();
                $("#crypto-send-submit-btn-txt").html(sendBtn);
                window.localStorage.removeItem('user-crypto-sent-amount');
                window.localStorage.removeItem('user-crypto-receiver-address');
                window.localStorage.removeItem('priority');
                window.localStorage.removeItem('previousUserCrytoSentUrl');
                swal.close();
            })
            .catch(error => {
                swal.close();
                error.responseText.hasOwnProperty('message') == true ? alert(JSON.parse(error.responseText).message) : alert(error.responseText);
            });
        }
    });

    //Validate Address
    $(document).on('keyup', ".receiverAddress", $.debounce(1000, function(e) {
        receiverAddress = $(this).val().trim();
        amount = $('.amount').val().trim();

        if (receiverAddress.length == 0) {
            receiverAddressValidationError.text('');
            receiverAddressErrorFlag = false;
            checkSubmitBtn();
        } else {
            checkAddressValidity(receiverAddress, walletCurrencyCode)
            .then(res =>
            {
                //If amount is not empty and response is 200
                if (amount.length > 0 && !isNaN(amount) && res == 200) {
                    checkAmountValidity(amount, senderAddress, receiverAddress, walletCurrencyCode)
                }
            })
            .catch(error => {
                error.responseText.hasOwnProperty('message') == true ? alert(JSON.parse(error.responseText).message) : alert(error.responseText);
            });
        }
    }));

    //Validate Amount
    $(document).on('keyup', '.amount', $.debounce(700, function(e) {
        amount = $(this).val().trim();
        receiverAddress = $(".receiverAddress").val().trim();

        if (amount.length > 0 && receiverAddress.length > 0 && !isNaN(amount)) {
            checkAmountValidity(amount, senderAddress, receiverAddress, walletCurrencyCode).then(res =>
            {
                if (receiverAddress != '' && res == 200) {
                    checkAddressValidity(receiverAddress, walletCurrencyCode)
                }
            })
            .catch(error => {
                error.responseText.hasOwnProperty('message') == true ? alert(JSON.parse(error.responseText).message) : alert(error.responseText);
            });
        } else {
            amountValidationError.text('');
            amountErrorFlag = false;
            checkSubmitBtn();
        }
    }));

     $('#priority').on('change', function ()
    {
        amount = $('.amount').val().trim();
        receiverAddress = $(".receiverAddress").val().trim();

        if (amount.length > 0 && receiverAddress.length > 0 && !isNaN(amount)) {
            checkAmountValidity(amount, senderAddress, receiverAddress, walletCurrencyCode).then(res =>
            {
                if (receiverAddress != '' && res == 200) {
                    checkAddressValidity(receiverAddress, walletCurrencyCode)
                }
            })
            .catch(error => {
                error.responseText.hasOwnProperty('message') == true ? alert(JSON.parse(error.responseText).message) : alert(error.responseText);
            });
        } else {
            amountValidationError.text('');
            amountErrorFlag = false;
            checkSubmitBtn();
        }
    });

    function getPriority(priority)
    {
        if (priority == 'high') {
            priority = 'high';
        } else if (priority == 'medium') {
            priority = 'medium';
        } else {
            priority = 'low';
        }
        return priority;
    }

    $(document).on('submit', '#crypto-send-form', function() {
        //Set amount to localstorage for showing on create page on going back from confirm page
        window.localStorage.setItem("user-crypto-sent-amount", $('.amount').val().trim());
        window.localStorage.setItem("user-crypto-receiver-address", $(".receiverAddress").val().trim());
        window.localStorage.setItem("priority", $(".priority").val().trim());

        $("#crypto-send-submit-btn").attr("disabled", true);
        $(".fa-spinner").removeClass('d-none');
        $("#crypto-send-submit-btn-txt").text(sending);

        setTimeout(function() {
            $(".fa-spinner").addClass('d-none');
            $("#crypto-send-submit-btn").attr("disabled", false);
            $("#crypto-send-submit-btn-txt").text(send);
        }, 10000);
    });
}


if ($('.main-content').find('#crypto-send-confirm').length) {

    function userCryptoSendConfirmBack() {
        window.localStorage.setItem("previousUserCrytoSentUrl", document.URL);
        window.location.replace(cryptoSentCreateUrl);
    }
    
    //Only go back by back button, if submit button is not clicked
    $(document).on('click', '.crypt-send-confirm-back-button', function(e) {
        e.preventDefault();
        userCryptoSendConfirmBack();
    });

    $(document).on('click', '.crypto-send-confirm', function(e) {

        window.localStorage.removeItem('user-crypto-sent-amount');
        window.localStorage.removeItem('user-crypto-receiver-address');
        window.localStorage.removeItem('priority');
        
        $(this).attr("disabled", true);
        $(".fa-spin").removeClass('d-none')
        $('.crypto-send-confirm-text').text(confirming);
        $('.crypto-send-confirm-link').click(function(e) {
            e.preventDefault();
        });
    
        //Make back button disabled and prevent click
        $('.crypt-send-confirm-back-button').attr("disabled", true).click(function(e) {
            e.preventDefault();
        });
    
        //Make back anchor prevent click
        $('.send-money-confirm-back-link').click(function(e) {
            e.preventDefault();
        });
    });
}

function printFunc() {
    window.print();
}

if ($('.main-content').find('#crypto-send-success').length) {
    
    $(document).ready(function() {
        window.history.pushState(null, "", window.location.href);
        window.onpopstate = function() {
            window.history.pushState(null, "", window.location.href);
        };
    });

    //disabling F5
    function disable_f5(e) {
        if ((e.which || e.keyCode) == 116) {
            e.preventDefault();
        }
    }
    $(document).ready(function() {
        $(document).bind("keydown", disable_f5);
    });

    //disabling ctrl+r
    function disable_ctrl_r(e) {
        if (e.keyCode == 82 && e.ctrlKey) {
            e.preventDefault();
        }
    }
    $(document).ready(function() {
        $(document).bind("keydown", disable_ctrl_r);
    });
}

if ($('.main-content').find('#crypto-receive-create').length) {

    $(window).on('load', function (e)
    {
        jQuery('#wallet-address').qrcode({
            text : addressText
        });
    });

    $(document).on('click','.wallet-address-copy-btn',function ()
    {
        $('#wallet-address-input').select();
        document.execCommand('copy');

        swal({
            title: copied,
            text: addressCopyText,
            type: "info",
            icon: "success",
            closeOnClickOutside: false,
            closeOnEsc: false,
        });
    })
}


