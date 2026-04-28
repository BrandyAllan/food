<?php

namespace App\Controllers;

use App\Models\Food;

class HomeController extends BaseController
{
    public function showFood()
    {
        $foodModel = new Food();

        $data['foods'] = $foodModel->getAllFoods();

        return view('home', $data);
    }
}