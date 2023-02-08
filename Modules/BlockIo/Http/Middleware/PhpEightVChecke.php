<?php

namespace Modules\BlockIo\Http\Middleware;

use App\Http\Helpers\Common;
use Closure;
use Illuminate\Http\Request;

class PhpEightVChecke
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        
        if ((int) phpversion() < 8) {
            (new Common)->one_time_message('danger', __('PHP version 8 is required for BlockIo.'));
            return redirect()->back();
        }
        return $next($request);
    }
}
