<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerPlan extends Model
{
    protected $fillable = [
        'plan_id', 'sms_limit','available_sms', 'price',
    ];

    public function plan(){
        return $this->belongsTo(Plan::class,'plan_id')->withDefault();
    }
}
