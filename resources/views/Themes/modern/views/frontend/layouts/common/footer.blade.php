<footer>
  <div class="container">
      <div class="row">
          <div class="col-md-12">
          	<?php
          		$company_name = settings('name');
          	?>
             <p class="copyright">@lang('message.footer.copyright')&nbsp;© {{date('Y')}} &nbsp;&nbsp; {{ $company_name }} | @lang('message.footer.copyright-text')</p>
          </div>
      </div>
  </div>
</footer>