'use strict';

if ($('.main-content').find('#dispute-discussion').length) {
    const actualBtn = document.getElementById('file');
	const fileChosen = document.getElementById('file-chosen');

	$('#reply').on('submit', function() {
		$("#dispute-reply").attr("disabled", true);
		$(".spinner").removeClass('d-none');
		$("#dispute-reply-text").text(submittingText);
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

	
	$("#status").on('change', function()
	{
		var status = $(this).val();
		var id = $("#id").val();
		$.ajax({
			method: "POST",
			url: SITE_URL+"/dispute/change_reply_status",
			data: { status: status, id:id}
		})
		.done(function( data )
		{
			if (status == 'Open') { status = 'Open'}
			else if (status == 'Solve') { status = 'Solved'}
			else if (status == 'Close') { status = 'Closed'}

			message = statusChangeText.replace(':x', status);
			var messageBox = '<div class="alert alert-success" role="alert">'+ message +'</div><br>';
			$("#alertDiv").html(messageBox);

			setTimeout(function() {
				location.reload()
			}, 2000);
		});
	});
}