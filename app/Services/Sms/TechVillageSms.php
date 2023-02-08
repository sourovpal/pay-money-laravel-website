<?php

namespace App\Services\Sms;

use App\Models\{
    EmailTemplate
};

abstract class TechVillageSms
{
    /**
     * The array of status and message whether sms template found or not
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
        $this->errorResponse = [
            'status'  => true, 
            'message' => __('SMS can not be sent, please contact the website administrator.')
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
        if (!checkAppSmsEnvironment()) {
            $this->errorResponse['status'] = false;
            return $this->errorResponse;
        }
        if (empty($tempId)) {
            return $this->errorResponse;
        }
        $template = EmailTemplate::tempId($tempId)->defaultLanguage()->type('sms')->first(['subject', 'body']);
        if (empty($template->subject) || empty($template->body)) {
            $template = EmailTemplate::tempId($tempId)->englishLanguage()->type('sms')->first(['subject', 'body']);
        }
        if (!$template) {
            return $this->errorResponse;
        }
        $this->errorResponse['template'] = $template;
        return $this->errorResponse;
    }
}
