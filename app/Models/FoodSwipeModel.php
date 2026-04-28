<?php

namespace App\Models;

use CodeIgniter\Model;

class FoodSwipeModel extends Model
{
    protected $table = 'food_swipes';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'user_id',
        'food_id',
        'action'
    ];
}