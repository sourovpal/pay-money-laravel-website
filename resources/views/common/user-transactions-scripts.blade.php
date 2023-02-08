<script type="text/javascript">

    function checkRequestCreatorStatus()
    {
        var promiseObj = new Promise(function(resolve, reject)
        {
            var trans_id = $('.trxn').attr('data');
            $.ajax({
                url: SITE_URL + "/request_payment/check-creator-status",
                type : "GET",
                data: {
                    'trans_id': trans_id,
                },
                dataType: "json",
            })
            .done(function (res)
            {
                // console.log(res.status);
                resolve(res.status);
            })
            .fail(function(error) {
                reject(error);
                console.log(error);
            });
        });
        return promiseObj;
    }

    $(document).on('click', '.show_area', function (e)
    {
        e.preventDefault();

        var trans_id = $(this).attr('trans-id');
        var row_id = $(this).attr('id');
        var preRowId = (parseInt(row_id) - 1);

        $.ajax(
        {
            method: "POST",
            url: SITE_URL + "/get_transaction",
            dataType: "json",
            data: {id: trans_id},
            beforeSend: function () {
                $('.preloader').show();
            },
        })
        .complete(function()
        {
            $('.preloader').hide();
        })
        .done(function(response)
        {
            // console.log(response.html);
            $("#total_" + row_id).html(response.total);
            $("#html_" + row_id).html(response.html);
        })
        .fail(function(error)
        {
            console.log(error);
        });

        var totalClick = parseInt($(this).attr('click')) + 1;
        $(this).attr('click', totalClick);
        var nowClick = parseInt($(this).attr('click')) % 2;

        if (nowClick == 0) {
           // $(this).removeClass('leftBorderRow');
           // $("#collapseRow" + row_id).css('border-left', '5px solid #FFFFFF');
            $("#icon_" + row_id).removeClass("fa-arrow-circle-down").addClass("fa-arrow-circle-right");
        } else {
           // $(this).addClass('leftBorderRow');
            $("#icon_" + row_id).removeClass('fa-arrow-circle-right').addClass("fa-arrow-circle-down");
        }
    });

    //Request To - Cancel
    $(document).on('click', '.trxn', function (e)
    {
        e.preventDefault();

        var trans_id = $(this).attr('data');
        var type = $(this).attr('data-type');
        var notificationType = $(this).attr('data-notificationType');

        $.ajax(
        {
            method: "POST",
            url: SITE_URL + "/request_payment/cancel",
            dataType: "json",
            data: {
                id: trans_id,
                type: type,
                notificationType: notificationType,
            },
            beforeSend: function() {
                $("#status_" + trans_id).text("{{__("Cancelling...")}}");
                $("#status_"+trans_id).removeClass('Pending');
                $("#status_"+trans_id).addClass('Blocked');
                $("#btn_" + trans_id).attr("disabled", true).text("{{__("Cancelling...")}}");
                $('.trxn_accept').hide();
            },
        })
        .done(function(data)
        {
            // console.log(response);
            $("#status_" + trans_id).text("{{__("Cancelled")}}");
            $("#btn_" + trans_id).text("{{__("Cancelled")}}");

            setTimeout(function() {
                $("#btn_" + trans_id).fadeOut('fast');
            }, 1000); // <-- time in milliseconds
        })
        .fail(function(error)
        {
            console.log(error);
        });
    });

    //Request From - Cancel
    $(document).on('click', '.trxnreqfrom', function (e)
    {
        e.preventDefault();

        var trans_id = $(this).attr('data');
        var type = $(this).attr('data-type');
        var notificationType = $(this).attr('data-notificationType');

        $.ajax(
        {
            method: "POST",
            url: SITE_URL + "/request_payment/cancelfrom",
            dataType: "json",
            data: {
                id: trans_id,
                type: type,
                notificationType: notificationType,
            },
            beforeSend: function() {
                $("#status_" + trans_id).text("{{__("Cancelling...")}}");
                $("#btn_" + trans_id).attr("disabled", true).text("{{__("Cancelling...")}}");
                $('.trxn_accept').hide();
            },
        })
        .done(function(data)
        {
            // console.log(response);
            $("#status_" + trans_id).text("{{__("Cancelled")}}");
            $("#btn_" + trans_id).text("{{__("Cancelled")}}");
            setTimeout(function() {
                $("#btn_" + trans_id).fadeOut('fast');
            }, 1000); // <-- time in milliseconds
        })
        .fail(function(error)
        {
            console.log(error);
        });
    });

    //Request To - Accept - only
    $(document).on('click', '.trxn_accept', function (e)
    {
        e.preventDefault();

        // if Request Acceptor/Current User is suspended
        checkUserSuspended(e);

        //Check Whether Request Creator is Suspended
        checkRequestCreatorStatus()
        .then(res =>
        {
            if (res != "Suspended" && res != "Inactive")
            {
                // if not suspended
                window.location.replace(SITE_URL + "/request_payment/accept/" + ($(this).attr('data-rel')));
            }
            else
            {
                e.stopPropagation();
                if (res == "Suspended")
                {
                    window.location.href="{{ url('check-request-creator-suspended-status') }}";
                }
                else if(res == "Inactive")
                {
                    window.location.href="{{ url('check-request-creator-inactive-status') }}";
                }
                return false;
            }
        })
        .catch(error => {
            console.log(error);
        });
    });
    </script>
