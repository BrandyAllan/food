<?php

namespace App\Models;

use CodeIgniter\Model;

class Food extends Model
{
    protected $table = 'foods'; // nom de la table
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'name',
        'emoji',
        'img',
        'cat',
        'time',
        'cal',
        'rating',
        'desc'
    ];

    protected $returnType = 'array';

}