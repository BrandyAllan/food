<?php

namespace App\Models;

use CodeIgniter\Model;

class Food extends Model
{
    public function getAllFoods()
    {
        return $this->findAll();
    }
}