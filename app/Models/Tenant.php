<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    protected $table = 'tbl_tenant_master';
    protected $primaryKey = 'tenant_id';
    public $timestamps = false;
}
