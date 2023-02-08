<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Request;
use App\Http\Controllers\Controller;
use App\Models\{Meta,
    Pages
};

class ContentController extends Controller
{
    protected $data = [];

    public function pageDetail($url)
    {
        $data['menu'] = 'deposit';
        if ($url == 'send-money') {
            $data['pageInfo']  = 'Request Money';
            $data['exceptionMeta'] = Meta::where('url', $url)->first();
            $data['menu']      = 'send-money';
            return view('frontend.pages.send-money', $data);

        } elseif ($url == 'request-money') {

            $data['pageInfo']  = 'Request Money';
            $data['exceptionMeta'] = Meta::where('url', $url)->first();
            $data['menu']      = 'request-money';
            return view('frontend.pages.request-money', $data);

        } elseif ($url == 'developer') {
            $data['pageInfo']   = 'Developer';
            $data['exceptionMeta']  = Meta::where('url', $url)->first();
            $data['menu']       = 'Developer';
            $type               = Request::get('type');
            if (!empty(settings('publication_status'))) {
                $data['publication_status'] = settings('publication_status');
            }

            if ($type == 'express') {
                return view('frontend.pages.express', $data);
            } elseif ($type == 'woocommerce') {
                if (!empty(settings('plugin_name'))) {
                    $data['plugin_name'] = settings('plugin_name');
                }
                return view('frontend.pages.woocommerce', $data);
            } else {
                return view('frontend.pages.standard', $data);
            }
        } else {
            $info = Pages::where(['url' => $url])->first();
            if (empty($info)) {
                abort(404);
            }
            $data['pageInfo']  = $info;
            $data['exceptionMeta'] = Meta::where('url', $url)->first();
            $data['menu']      = $url;
            return view('frontend.pages.detail', $data);
        }
    }

    public function downloadPackage()
    {
        return response()->download(\Storage::disk('local')->path('paymoney_sdk.zip'));
    }
}
