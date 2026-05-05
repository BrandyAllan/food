<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\FoodModel;
use App\Models\InteractionModel;

class Stats extends BaseController
{
    public function showStats(): string
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        return view('stats');
    }

    public function getStatsData()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Non autorisé.'
            ])->setStatusCode(401);
        }

        $userId = session()->get('user_id');

        $foodModel = new FoodModel();
        $interactionModel = new InteractionModel();

        $allFoods = $foodModel->findAll();

        $seenPlats = $interactionModel->where('user_id', $userId)
                                     ->where('action', 'seen')
                                     ->findAll();
        $likedPlats = $interactionModel->where('user_id', $userId)
                                       ->where('action', 'liked')
                                       ->findAll();
        $superLikedPlats = $interactionModel->where('user_id', $userId)
                                            ->where('action', 'super_liked')
                                            ->findAll();

        $seenIds = array_column($seenPlats, 'food_id');
        $likedIds = array_column($likedPlats, 'food_id');
        $superIds = array_column($superLikedPlats, 'food_id');

        $likedFoods = array_filter($allFoods, function($food) use ($likedIds) {
            return in_array($food['id'], $likedIds);
        });

        $categoryStats = [];
        foreach ($likedFoods as $food) {
            $cat = $food['category'];
            if (!isset($categoryStats[$cat])) {
                $categoryStats[$cat] = 0;
            }
            $categoryStats[$cat]++;
        }

        $total = count($seenIds);
        $likedCount = count($likedIds);
        $pct = $total > 0 ? round(($likedCount / $total) * 100) : 0;

        return $this->response->setJSON([
            'success' => true,
            'data' => [
                'kpis' => [
                    'liked' => $likedCount,
                    'seen' => $total,
                    'super' => count($superIds)
                ],
                'percentage' => $pct,
                'categoryStats' => $categoryStats,
                'allFoods' => $allFoods,
                'likedFoods' => array_values($likedFoods),
                'likedIds' => $likedIds,
                'superIds' => $superIds,
                'total' => $total
            ]
        ]);
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}