<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ParcelWrongs extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    
    protected $fillable = [
        'wrong_booking_id',
        'wrong_tracking_id',
        'wrong_subtracking_id',
        'wrong_problem_detail',
        'wrong_description_solve',
        'wrong_status'
    ]; 
}
