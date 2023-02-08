<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
	protected $table = 'settings';
	protected $fillable = ['name', 'value', 'type'];
    public $timestamps = false;

    public function getSingleSetting($constraints, $selectOptions)
    {
        return $this->where($constraints)->first($selectOptions);
    }

    public static function getAll()
    {
        $data = Cache::get(config('cache.prefix') . '-settings');
        if (empty($data)) {
            $data = parent::all();
            Cache::put(config('cache.prefix') . '-settings', $data, 30 * 86400);
        }
        return $data;
    }
}
