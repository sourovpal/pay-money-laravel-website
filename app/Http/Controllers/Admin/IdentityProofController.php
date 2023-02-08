<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\Admin\IdentityProofsDataTable;
use App\Http\Controllers\Users\EmailController;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use App\Http\Helpers\Common;
use App\Models\{EmailTemplate,
    DocumentVerification,
    User
};
use App\Exports\IdentityProofsExport;
use Illuminate\Support\Facades\Config;

class IdentityProofController extends Controller
{
    protected $helper;
    protected $documentVerification;
    protected $email;

    public function __construct()
    {
        $this->helper               = new Common();
        $this->documentVerification = new DocumentVerification();
        $this->email                = new EmailController();
    }

    public function index(IdentityProofsDataTable $dataTable)
    {
        $data['menu']     = 'proofs';
        $data['sub_menu'] = 'identity-proofs';

        $data['documentVerificationStatus'] = $documentVerificationStatus = $this->documentVerification->where(['verification_type' => 'identity'])->select('status')->groupBy('status')->get();

        $data['from']     = isset(request()->from) ? setDateForDb(request()->from) : null;
        $data['to']       = isset(request()->to ) ? setDateForDb(request()->to) : null;
        $data['status']   = isset(request()->status) ? request()->status : 'all';

        return $dataTable->render('admin.verifications.identity_proofs.list', $data);
    }

    public function identityProofsCsv()
    {
        return Excel::download(new IdentityProofsExport(), 'identity_proofs_list_' . time() . '.xlsx');
    }

    public function identityProofsPdf()
    {
        $from = !empty(request()->startfrom) ? setDateForDb(request()->startfrom) : null;
        $to = !empty(request()->endto) ? setDateForDb(request()->endto) : null;
        $status = isset(request()->status) ? request()->status : null;

        $data['identityProofs'] = $this->documentVerification->getDocumentVerificationsList($from, $to, $status)->orderBy('id', 'desc')->get();

        if (isset($from) && isset($to)) {
            $data['date_range'] = $from . ' To ' . $to;
        } else{
            $data['date_range'] = 'N/A';
        }

        $mpdf = new \Mpdf\Mpdf(['tempDir' => __DIR__ . '/tmp']);
        $mpdf = new \Mpdf\Mpdf([
            'mode'        => 'utf-8',
            'format'      => 'A3',
            'orientation' => 'P',
        ]);

        $mpdf->autoScriptToLang         = true;
        $mpdf->autoLangToFont           = true;
        $mpdf->allow_charset_conversion = false;

        $mpdf->WriteHTML(view('admin.verifications.identity_proofs.identity_proofs_report_pdf', $data));
        $mpdf->Output('identity_proofs_report_' . time() . '.pdf', 'D');
    }

    public function identityProofEdit($id)
    {
        $data['menu']     = 'proofs';
        $data['sub_menu'] = 'identity-proofs';

        $data['documentVerification'] = $documentVerification = DocumentVerification::find($id);

        return view('admin.verifications.identity_proofs.edit', $data);
    }

    public function identityProofUpdate(Request $request)
    {
        $documentVerification         = DocumentVerification::find($request->id);
        $documentVerification->status = $request->status;
        $documentVerification->save();

        $user = User::find($request->user_id);
        if ($request->verification_type == 'identity')
        {
            if ($request->status == 'approved')
            {
                $user->identity_verified = true;
            }
            else
            {
                $user->identity_verified = false;
            }
        }
        $user->save();

        if (checkDemoEnvironment() != true)
        {
            /**
             * Mail
             */
            $englishIdentityVerificationEmailTemp = EmailTemplate::where(['temp_id' => 21, 'lang' => 'en', 'type' => 'email'])->select('subject', 'body')->first();
            $identityVerificationEmailTemp        = EmailTemplate::where(['temp_id' => 21, 'language_id' => Session::get('default_language'), 'type' => 'email'])->select('subject', 'body')->first();

            if (!empty($identityVerificationEmailTemp->subject) && !empty($identityVerificationEmailTemp->body))
            {
                $identityVerificationEmailSub  = str_replace('{Identity/Address}', 'Identity', $identityVerificationEmailTemp->subject);
                $identityVerificationEmailBody = str_replace('{user}', $user->first_name . ' ' . $user->last_name, $identityVerificationEmailTemp->body);
            }
            else
            {
                $identityVerificationEmailSub  = str_replace('{Identity/Address}', 'Identity', $englishIdentityVerificationEmailTemp->subject);
                $identityVerificationEmailBody = str_replace('{user}', $user->first_name . ' ' . $user->last_name, $englishIdentityVerificationEmailTemp->body);
            }
            $identityVerificationEmailBody = str_replace('{Identity/Address}', 'Identity', $identityVerificationEmailBody);
            $identityVerificationEmailBody = str_replace('{approved/pending/rejected}', ucfirst($request->status), $identityVerificationEmailBody);
            $identityVerificationEmailBody = str_replace('{soft_name}', settings('name'), $identityVerificationEmailBody);

            if (checkAppMailEnvironment())
            {
                $this->email->sendEmail($user->email, $identityVerificationEmailSub, $identityVerificationEmailBody);
            }

            /**
             * SMS
             */
            $englishIdentityVerificationSmsTemp = EmailTemplate::where(['temp_id' => 21, 'lang' => 'en', 'type' => 'sms'])->select('subject', 'body')->first();
            $identityVerificationSmsTemp        = EmailTemplate::where(['temp_id' => 21, 'language_id' => Session::get('default_language'), 'type' => 'sms'])->select('subject', 'body')->first();

            if (!empty($identityVerificationSmsTemp->subject) && !empty($identityVerificationSmsTemp->body))
            {
                $identityVerificationSmsSub  = str_replace('{Identity/Address}', 'Identity', $identityVerificationSmsTemp->subject);
                $identityVerificationSmsBody = str_replace('{user}', $user->first_name . ' ' . $user->last_name, $identityVerificationSmsTemp->body);
            }
            else
            {
                $identityVerificationSmsSub  = str_replace('{Identity/Address}', 'Identity', $englishIdentityVerificationSmsTemp->subject);
                $identityVerificationSmsBody = str_replace('{user}', $user->first_name . ' ' . $user->last_name, $englishIdentityVerificationSmsTemp->body);
            }
            $identityVerificationSmsBody = str_replace('{Identity/Address}', 'Identity', $identityVerificationSmsBody);
            $identityVerificationSmsBody = str_replace('{approved/pending/rejected}', ucfirst($request->status), $identityVerificationSmsBody);

            if (!empty($user->carrierCode) && !empty($user->phone)) {
                if (checkAppSmsEnvironment()) {
                    sendSMS($user->carrierCode . $user->phone, $identityVerificationSmsBody);
                }
            }
        }
        $this->helper->one_time_message('success', __('The :x has been successfully verified.', ['x' => __('identity')]));
        return redirect(Config::get('adminPrefix').'/identity-proofs');
    }
}
