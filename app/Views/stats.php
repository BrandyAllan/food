<?php

namespace App\Controllers;

use App\Models\FoodModel;
use App\Models\FoodSwipeModel;
use CodeIgniter\HTTP\RedirectResponse;

class Stats extends BaseController
{
    /**
     * Affiche la page des statistiques
     * Retourne une vue ou une redirection si non connecté
     */
    public function showStats(): string|RedirectResponse
    {
        // Vérifier si l'utilisateur est connecté
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        return view('stats');
    }

    /**
     * Récupère les données statistiques en JSON
     */
    public function getStatsData()
    {
        // Vérifier si l'utilisateur est connecté
        if (!session()->get('logged_in')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Non autorisé.'
            ])->setStatusCode(401);
        }

        $userId = session()->get('user_id');

        $foodModel = new FoodModel();
        $swipeModel = new FoodSwipeModel();

        // Récupérer tous les plats
        $allFoods = $foodModel->findAll();

        // Récupérer les IDs par type d'action
        $seenIds = $swipeModel->getFoodIdsByAction($userId, 'seen');
        $likedIds = $swipeModel->getFoodIdsByAction($userId, 'like');
        $superIds = $swipeModel->getFoodIdsByAction($userId, 'super');

        // Plats aimés avec leurs détails
        $likedFoods = [];
        foreach ($allFoods as $food) {
            if (in_array($food['id'], $likedIds)) {
                $likedFoods[] = $food;
            }
        }

        // Statistiques par catégorie
        $categoryStats = [];
        foreach ($likedFoods as $food) {
            $cat = $food['cat'];
            if (!isset($categoryStats[$cat])) {
                $categoryStats[$cat] = 0;
            }
            $categoryStats[$cat]++;
        }

        // Trier les catégories par nombre décroissant
        arsort($categoryStats);

        // Calcul du pourcentage
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
                'likedFoods' => $likedFoods,
                'likedIds' => $likedIds,
                'superIds' => $superIds,
                'seenIds' => $seenIds,
                'total' => $total
            ]
        ]);
    }

    /**
     * Déconnexion de l'utilisateur
     */
    public function logout(): RedirectResponse
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}