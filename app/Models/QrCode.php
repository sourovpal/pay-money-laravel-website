<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class QrCode extends Model
{
	protected $table = 'qr_codes';

    protected $fillable = ['user_id', 'type', 'qr_code', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Create User QR code
     *
     * @param object $user
     * @return void
     */
    public function createUserQrCode($user)
    {
        $qrCode = self::where(['object_id' => $user->id, 'object_type' => 'user', 'status' => 'Active'])->first(['id']);
        if (empty($qrCode))
        {
            $createInstanceOfQrCode              = new self();
            $createInstanceOfQrCode->object_id   = $user->id;
            $createInstanceOfQrCode->object_type = 'user';
            if (!empty($user->formattedPhone)) {
                $createInstanceOfQrCode->secret = convert_string('encrypt', $createInstanceOfQrCode->object_type . '-' . $user->email . '-' . $user->formattedPhone . '-' . Str::random(6));
            } else {
                $createInstanceOfQrCode->secret = convert_string('encrypt', $createInstanceOfQrCode->object_type . '-' . $user->email . '-' . Str::random(6));
            }
            $createInstanceOfQrCode->status = 'Active';
            $createInstanceOfQrCode->save();
        }
    }
}
