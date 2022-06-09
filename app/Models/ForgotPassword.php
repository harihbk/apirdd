<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForgotPassword extends Model
{
    protected $table = 'tbl_forgot_password_otp';
    protected $primaryKey = 'otp_id';
    public $timestamps = false;
}
