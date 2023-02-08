'use strict';

if ($('.main-content').find('#ticket-reply').length) {
    const actualBtn = document.getElementById('file');
    const fileChosen = document.getElementById('file-chosen');

    $('#reply').on('submit', function() {
        $("#ticket-reply").attr("disabled", true);
        $(".spinner").removeClass('d-none');
        $("#ticket-reply-text").text(submittingText);
    });

    $(document).on('change', '#file', function() {
        let fileExtension = $('#file').val().replace(/^.*\./, '');
        let fileInput = document.getElementById('file'); 

        if (!extensions.includes(fileExtension)) {
            fileInput.value = '';
            $('.file-error').addClass('error').text(extensionsValidationMessage);
            $('#fileSpan').fadeIn('slow').delay(2000).fadeOut('slow');
            return false;
        } else {
            $('.file-error').text('');
            return true;
        }
    })

    $("#status").on('change', function () {
        var status_id = $(this).val();
        var ticket_id = $("#ticket_id").val();

        $.ajax({
            method: "POST",
            url: SITE_URL + "/ticket/change_reply_status",
            data: {
                status_id: status_id, 
                ticket_id: ticket_id
            }
        })
        .done(function (reply) {
            message = statusChangeText.replace(':x', reply.status); 
            var messageBox = '<div class="alert alert-success" role="alert">' + message + '</div><br>';
            $("#alertDiv").html(messageBox);
            setTimeout(function () {
                location.reload()
            }, 2000);
        });
    });
}