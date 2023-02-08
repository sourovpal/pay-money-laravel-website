<?php

namespace App\Services\Mail;

use App\Http\Controllers\Users\EmailController;
use App\Models\{
    EmailTemplate
};

abstract class TechVillageMail
{
    protected $email;
    /**
     * The array of status and message whether email template found or not
     *
     * @var array
     */
    protected $errorResponse = [];

    abstract protected function send($request);

    /**
     * Constructor
     *
     * return void
     */
    public function __construct()
    {
        $this->email = new EmailController();
        $this->errorResponse = [
            'status'  => true, 
            'message' => __('Email can not be sent, please contact the website administrator.')
        ];
    }

    /**
     * Get Email Template based on tempId
     *
     * @param int tempId
     * @return array
     */
    protected function getTemplate($tempId)
    {
        if (!checkAppMailEnvironment() || empty($tempId)) {
            return $this->errorResponse;
        }

        $template = EmailTemplate::tempId($tempId)->defaultLanguage()->type('email')->first(['subject', 'body']);
        if (empty($template->subject) || empty($template->body)) {
            $template = EmailTemplate::tempId($tempId)->englishLanguage()->type('email')->first(['subject', 'body']);
        }
        if (!$template) {
            return $this->errorResponse;
        }

        return $this->errorResponse = [
            'status' => false, 
            'template' => $template
        ];
    }
}
