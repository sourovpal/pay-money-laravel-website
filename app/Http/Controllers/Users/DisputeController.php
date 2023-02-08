<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Users\EmailController;
use App\Http\Controllers\Controller;
use App\Rules\CheckValidFile;
use Illuminate\Http\Request;
use App\Http\Helpers\Common;
use App\Models\{
    Dispute,
    DisputeDiscussion,
    Transaction,
    Reason
};
use Illuminate\Support\Facades\{
    Validator,
    Auth
};
use Illuminate\Support\Str;

class DisputeController extends Controller
{
    protected $helper;
    protected $email;

    public function __construct()
    {
        $this->helper = new Common();
        $this->email  = new EmailController();
    }

    public function index()
    {
        $data['menu']     = 'dispute';
        $data['sub_menu'] = 'dispute';

        $data['list']     = Dispute::with([
            'claimant:id,first_name,last_name',
            'defendant:id,first_name,last_name',
            'transaction:id,uuid',
        ])
            ->where('claimant_id', Auth::user()->id)
            ->orWhere('defendant_id', Auth::user()->id)
            ->orderBy('id', 'desc')
            ->paginate(10);
        return view('user_dashboard.dispute.list', $data);
    }

    public function add($id)
    {
        $data['menu']        = 'dispute';
        $data['sub_menu']    = 'dispute';
        $defendant           = [];
        $data['transaction'] = Transaction::find($id, ['id', 'user_id', 'end_user_id']);
        $data['reasons']     = Reason::get(['id', 'title']);
        return view('user_dashboard.dispute.add', $data);
    }

    public function store(Request $request)
    {
        $rules = array(
            'title'          => 'required',
            'reason_id'      => 'required',
            'description'    => 'required',
            'claimant_id'    => 'required',
            'defendant_id'   => 'required',
            'transaction_id' => 'required',
        );

        $fieldNames = array(
            'title'          => 'Title',
            'reason_id'      => 'Reason',
            'description'    => 'Description',
            'claimant_id'    => 'Claimant',
            'defendant_id'   => 'Defendant',
            'transaction_id' => 'Transaction Id',
        );

        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($fieldNames);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        } else {
            $dispute                 = new Dispute();
            $dispute->claimant_id    = $request->claimant_id;
            $dispute->defendant_id   = $request->defendant_id;
            $dispute->transaction_id = $request->transaction_id;
            $dispute->reason_id      = $request->reason_id;
            $dispute->title          = $request->title;
            $dispute->description    = $request->description;
            $dispute->status         = 'Open';
            $dispute->code           = 'DIS-' . strtoupper(Str::random(6));
            $dispute->save();

            $this->helper->one_time_message('success', __('Dispute Created Successfully!'));
            return redirect('disputes');
        }
    }

    public function discussion($id)
    {
        $data['menu'] = 'dispute';
        $data['sub_menu'] = 'dispute';
        $data['content_title'] = 'Dispute';
        $data['icon'] = 'user';
        $data['dispute'] = Dispute::find($id);

        return view('user_dashboard.dispute.discussion', $data);
    }

    public function storeReply(Request $request)
    {

        $rules = array(
            'description' => 'required',
            'file'        => ['nullable', new CheckValidFile(getFileExtensions(1))],
        );

        $fieldNames = array(
            'description' => 'Message',
            'file'        => __('File'),
        );

        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($fieldNames);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        } else {
            $dispute = Dispute::find($request->dispute_id, ['status']);
            if ($dispute->status == 'Open') {
                $file = $request->file('file');
                if (isset($file)) {
                    $ext = $file->getClientOriginalExtension();

                    if (checkFileValidation($ext, 1)) {
                        $fileName        = time() . '_' . $file->getClientOriginalName();
                        $destinationPath = public_path('uploads/files');
                        $file->move($destinationPath, $fileName);
                    } else {
                        $this->helper->one_time_message('error', 'Invalid Image Format!');
                    }
                }
                $discussion             = new DisputeDiscussion();
                $discussion->user_id    = Auth::user()->id;
                $discussion->message    = $request->description;
                $discussion->dispute_id = $request->dispute_id;
                $discussion->file       = isset($fileName) ? $fileName : null;
                $discussion->type       = 'User';
                $discussion->save();

                $this->helper->one_time_message('success', __('Dispute Reply Added Successfully!'));
                return redirect('dispute/discussion/' . $request->dispute_id);
            } else {
                $this->helper->one_time_message('warning', __('Dispute discussion already ended.'));
                return redirect('dispute/discussion/' . $request->dispute_id);
            }
        }
    }

    public function changeReplyStatus(Request $request)
    {
        $dispute         = Dispute::find($request->id, ['id', 'status']);
        $dispute->status = $request->status;
        $dispute->save();
        $data['status'] = 1;
        return json_encode($data);
    }

    public function download($file_name)
    {
        $file_path = public_path('/uploads/files/' . $file_name);
        return response()->download($file_path);
    }
}
