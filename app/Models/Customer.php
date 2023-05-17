<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Customer extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'email', 'password','status','email_verified_at',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $dates=['created_at','updated_at'];

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }
    public function setPasswordAttribute($value)
    {
        $this->attributes['password']=bcrypt($value);
    }
    public function getStatusAttribute($value)
    {
        return ucfirst($value);
    }

    public function admin(){
        return $this->belongsTo(User::class,'admin_id')->withDefault();
    }
    public function numbers(){
        return $this->hasMany(CustomerNumber::class);
    }
    public function plan(){
        return $this->hasOne(CustomerPlan::class);
    }
    public function messages(){
        return $this->hasMany(Message::class)->orderByDesc('created_at');
    }

    public function sent_messages(){
        return $this->messages()->where('type','sent');
    }
    public function receive_messages(){
        return $this->messages()->where('type','inbox');
    }
    public function drafts(){
        return $this->hasMany(Draft::class)->orderByDesc('created_at');
    }
    public function unread_messages(){
        return $this->receive_messages()->where('read','no');
    }
    public function settings(){
        return $this->hasMany(CustomerSettings::class);
    }
    public function contacts(){
        return $this->hasMany(Contact::class)->orderByDesc('created_at');
    }
    public function groups(){
        return $this->hasMany(Group::class)->orderByDesc('created_at');
    }
    public function active_groups(){
        return $this->groups()->where('status','active');
    }

}
