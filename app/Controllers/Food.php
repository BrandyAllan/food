<?php

namespace App\Controllers;

use App\Models\FoodModel;

class Food extends BaseController
{
    public function getFoods()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setJSON([]);
        }

        $foodModel = new FoodModel();
        $foods = $foodModel->findAll();

        return $this->response->setJSON($foods);
    }
}