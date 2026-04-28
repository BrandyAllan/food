<?php

namespace App\Models;

use CodeIgniter\Model;

class FoodModel extends Model
{
    protected $table = 'foods';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'name',
        'emoji',
        'img',
        'cat',
        'time',
        'cal',
        'rating',
        'desc',
        'description'
    ];
}