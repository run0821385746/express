<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ReceiveJob extends Model
{
      /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'receive_no', 
        'branch_id', 
        'receive_status', 
        'create_by', 
        'update_by', 
        'update_at'
    ]; 
}
