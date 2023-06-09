<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactGroup extends Model
{
    protected $fillable=['group_id','contact_id','customer_id'];
    public function contact(){
        return $this->belongsTo(Contact::class)->withDefault();
    }
}
