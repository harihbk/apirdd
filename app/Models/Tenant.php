<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class Tenant extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;
    

    protected $table = 'tbl_tenant_master';
    protected $primaryKey = 'tenant_id';
    public $timestamps = false;

    protected $hidden = [
        'access_token',
        'refresh_token'
    ];
}
