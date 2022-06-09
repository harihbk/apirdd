<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FitoutDepositrefund extends Model
{
    use HasFactory;
    protected $table = 'tbl_fitout_deposit_refund';
    protected $primaryKey = 'id';
    public $timestamps = false;
}
