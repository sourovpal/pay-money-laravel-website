<script src="{{theme_asset('public/js/jquery.min.js')}}" type="text/javascript"></script>
<!-- popper.min.js must place before bootstrap.min.js, else won't work-->
<script src="{{theme_asset('public/js/popper.min.js')}}" type="text/javascript"></script>
<script src="{{theme_asset('public/js/bootstrap.min.js')}}" type="text/javascript"></script>
<script src="{{theme_asset('public/js/jquery.waypoints.min.js')}}" type="text/javascript"></script>
<script src="{{theme_asset('public/js/main.js')}}" type="text/javascript"></script>
<script src="{{theme_asset('public/js/moment.js')}}" type="text/javascript"></script>

{!! settings('head_code') !!}

<script type="text/javascript">
	//  Theme mode
	$('#sliderlight').on('click', function (e) {
		var currentClass = $('html').attr('class');
		if(currentClass== 'light'){
			$('#dark').addClass('d-flex').removeClass('d-none');
			$('#light').addClass('d-none').removeClass('d-flex');

			localStorage.setItem('theme', 'dark');
			$('html').toggleClass('dark light');
		} else {
			$('#light').addClass('d-flex').removeClass('d-none');
			$('#dark').addClass('d-none').removeClass('d-flex');

			localStorage.setItem('theme', 'light');
			$('html').toggleClass('dark light');
		}
	});

	$('#sliderdark').on('click', function (e) {
		var currentClass = $('html').attr('class');
		if(currentClass== 'light'){
			$('#dark').addClass('d-flex').removeClass('d-none');
			$('#light').addClass('d-none').removeClass('d-flex');

			localStorage.setItem('theme', 'dark');
			$('html').toggleClass('dark light');
		} else {
			$('#light').addClass('d-flex').removeClass('d-none');
			$('#dark').addClass('d-none').removeClass('d-flex');

			localStorage.setItem('theme', 'light');
			$('html').toggleClass('dark light');
		}
	});

	var theme = localStorage.getItem('theme');

	if(theme == 'dark') {
		$('#dark').addClass('d-flex').removeClass('d-none');
		$('#light').addClass('d-none').removeClass('d-flex');
		$('html').addClass('dark').removeClass('light');
	} else {
		$('#light').addClass('d-flex').removeClass('d-none');
		$('#dark').addClass('d-none').removeClass('d-flex');
		$('html').addClass('light').removeClass('dark');
	}

	$('#delete-warning-modal').on('show.bs.modal', function (e) {
		$message  = $(e.relatedTarget).attr('data-message');
		$(this).find('.modal-body p').text($message);
		$title    = $(e.relatedTarget).attr('data-title');
		$(this).find('.modal-title').text($title);

		// Pass form reference to modal for submission on yes/ok
		var form  = $(e.relatedTarget).closest('form');
		$(this).find('.modal-footer #delete-modal-yes').data('form', form);
	});

	$('#delete-warning-modal').find('.modal-footer #delete-modal-yes').on('click', function(e){
		$(this).data('form').submit();
	});
</script>

<script type="text/javascript">

	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	function log(log) {
		console.log(log);
	}

	function resizeHeaderOnScroll() {
		const distanceY = window.pageYOffset || document.documentElement.scrollTop,
			shrinkOn = 100,
			headerEl = document.getElementById('js-header');

		if (headerEl) {
			if (distanceY > shrinkOn) {
				headerEl.classList.add("smaller-header");
				$("#logo_container").attr('src', SITE_URL + '/public/frontend/images/logo_sm.png');
			} else {
				headerEl.classList.remove("smaller-header");
				$("#logo_container").attr('src', SITE_URL + '/public/frontend/images/logo.png');
			}
		}
	}
	window.addEventListener('scroll', resizeHeaderOnScroll);

	//Language - user_dashboard
	$('#lang').on('click', function (e) {
		e.preventDefault();
		lang = e.target.attributes.value.value;

		url = '{{url('change-lang')}}';
		$.ajax({
			type: 'get',
			url: url,
			data: {lang: lang},
			success: function (msg) {
				if (msg == 1) {
					location.reload();
				}
			}
		});
	});

	// Sidebar

	//Language - user_dashboard
	$('#sidebar').on('click', function (e) {
		
		$('#sidecol').addClass('sidebar-col').removeClass('d-none');
	
	});

	$('#closeCollapse').on('click', function (e) {
		
		$('#sidecol').addClass('d-none').removeClass('sidebar-col');
	
	});

	$(".side-active").closest(".collapse").addClass("show")

</script>
