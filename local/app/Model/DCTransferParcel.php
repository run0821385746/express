<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class DCTransferParcel extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'dc_transfer_no', 
        'dc_transfer_sender',
        'dc_transfer_date', 
        'dc_transfer_qty', 
        'dc_transfer_status', 
        'dc_transfer_isDone',
        'dc_transfer_receiver'
    ];
}
