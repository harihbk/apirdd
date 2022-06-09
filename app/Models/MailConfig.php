<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MailConfig extends Model
{
    protected $table = 'tbl_mail_config_master';
    protected $primaryKey = 'config_id';
    public $timestamps = false;
}
