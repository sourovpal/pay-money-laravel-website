<?php

/**
 * @package PaymentFailedException
 * @author tehcvillage <support@techvill.org>
 * @contributor Md Abdur Rahaman Zihad <[zihad.techvill@gmail.com]>
 * @created 21-11-2022
 */

namespace App\Exceptions\Api\V2;

use Exception;

class PaymentFailedException extends Exception
{
    /**
     * Custom Exception which can store data in array
     *
     * @param array $message Array or data
     *
     */
    public function __construct($message)
    {
        if (is_array($message)) {
            $message = json_encode($message);
        }
        parent::__construct($message);
    }


    /**
     * Custom get message method to get the data already parsed
     *
     * @return array
     */
    public function getDecodedMessage()
    {
        return json_decode($this->getMessage());
    }
}
