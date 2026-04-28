<?php

namespace App\Controllers;

use App\Models\FoodModel;
use App\Models\FoodSwipeModel;

class Food extends BaseController
{
    public function getFoods()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setJSON([]);
        }

        $db = \Config\Database::connect();

        $userId = session()->get('user_id');

        $foods = $db->table('foods f')
            ->select('f.*')
            ->join('food_swipes s', 's.food_id = f.id AND s.user_id = ' . $userId, 'left')
            ->where('s.id', null)
            ->get()
            ->getResultArray();

        return $this->response->setJSON($foods);
    }

    public function saveSwipe()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Non connecté.'
            ]);
        }

        $data = $this->request->getJSON(true);

        $userId = session()->get('user_id');
        $foodId = $data['food_id'] ?? null;
        $action = $data['action'] ?? null;

        if (!$foodId || !in_array($action, ['seen', 'like', 'super', 'skip'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Données invalides.'
            ]);
        }

        $model = new FoodSwipeModel();

        $existing = $model
            ->where('user_id', $userId)
            ->where('food_id', $foodId)
            ->first();

        if ($existing) {
            $model->update($existing['id'], [
                'action' => $action
            ]);
        } else {
            $model->insert([
                'user_id' => $userId,
                'food_id' => $foodId,
                'action' => $action
            ]);
        }

        return $this->response->setJSON([
            'success' => true
        ]);
    }
}