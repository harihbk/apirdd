<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class Superuser extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $table = 'tbl_super_users';
    protected $primaryKey = 'id';
    public $timestamps = false;
}
