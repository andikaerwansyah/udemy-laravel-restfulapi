<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    const VERIFIED_USER = '1'; // kenapa string karena nanti nilainya akan di compare
    const UNVERIFIED_USER = '0';

    const ADMIN_USER = 'true';
    const REGULAR_USER = 'false';

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'verified',
        'verification_token',
        'admin',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'verification_token',
    ];

    /**
     * Mutator Name
     * @param string $name
     * Store name in lowercase
     */
    public function setNameAttribute($name){
        $this->attributes['name'] = strtolower($name);
    }

     /**
     * Accessor Name
     * @param string $name
     * @return name on UPPERCASE
     */
    public function getNameAttribute($name){
        return ucwords($name);
    }

    /**
     * Mutator Email
     * @param string $email
     */
    public function setEmailAttribute($email){
        $this->attributes['email'] = strtolower($email);
    }

    /**
     * Check is user already Verified
     */
    public function isVerified(){
        return $this->verified == User::VERIFIED_USER;
    }

    /**
     * Check is user admin on regular 
     */
    public function isAdmin(){
        return $this->admin == User::ADMIN_USER;
    }

    public static function generateVerificationCode(){
        return str_random(40);
    }
}
