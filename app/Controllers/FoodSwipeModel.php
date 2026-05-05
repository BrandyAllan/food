<?php

namespace App\Models;

use CodeIgniter\Model;

class FoodSwipeModel extends Model
{
    protected $table = 'food_swipes';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'user_id',
        'food_id',
        'action' // 'seen', 'like', 'super', 'skip'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'created_at'; // On ne met pas à jour, on garde la date de création

    /**
     * Enregistre une interaction (remplace si existe déjà grâce à UNIQUE)
     */
    public function recordSwipe($userId, $foodId, $action)
    {
        // Vérifier si l'interaction existe déjà
        $existing = $this->where('user_id', $userId)
                        ->where('food_id', $foodId)
                        ->first();

        if ($existing) {
            // Mettre à jour l'action existante
            return $this->update($existing['id'], [
                'action' => $action,
                'created_at' => date('Y-m-d H:i:s') // Mettre à jour la date
            ]);
        }

        // Créer une nouvelle interaction
        return $this->insert([
            'user_id' => $userId,
            'food_id' => $foodId,
            'action' => $action
        ]);
    }

    /**
     * Récupère les IDs des plats pour une action donnée
     */
    public function getFoodIdsByAction($userId, $action)
    {
        $results = $this->where('user_id', $userId)
                       ->where('action', $action)
                       ->findAll();
        
        return array_column($results, 'food_id');
    }

    /**
     * Récupère tous les swipes d'un utilisateur
     */
    public function getUserSwipes($userId)
    {
        return $this->where('user_id', $userId)->findAll();
    }
}