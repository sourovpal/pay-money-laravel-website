<?php

namespace App\Services\Mail;

use Exception;


class UserVerificationMailService extends TechVillageMail
{
    /**
     * The array of status and message whether email sent or not.
     *
     * @var array
     */
    protected $response = [];

    public function __construct()
    {
        parent::__construct();
        $this->response = [
            'status'  => true,
            'message' => __('We sent you an activation code. Check your email and click on the link to verify.')
        ];
    }
    /**
     * Send verification email to user
     * @param object $user
     * @return array $response
     */
    public function send($user)
    {
        try {
            $email = $this->getTemplate(17);
            if (!$email['status']) {
                return $email;
            }
            $data = [
                '{user}'  => $user->full_name,
                '{email}' => $user->email,
                '{verification_url}' => url('/user/verify', $user->verifyUser->token),
                '{soft_name}'        => settings('name'),
            ];
            $message = str_replace(array_keys($data), $data, $email['template']->body);
            $this->email->sendEmail($user->email, $email['template']->subject, $message);
        } catch (Exception $e) {
            $this->response['message'] = $email['message'];
        }
        return $this->response;
    }

}
