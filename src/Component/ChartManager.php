<?php

namespace App\Component;

use App\Entity\Game;
use App\Entity\Prediction;

/**
 * La classe ChartDataManager implémente le gestionnaire de construction des
 * données à destination des graphes. Il contient des méthodes utilitaires
 * statiques fournissant des données au format JSON pour initialiser les
 * graphes dessinés avec Charts.js
 *
 * @author Sébastien ZINS
 */
class ChartManager {

    /**
     * Couleur pour la section de graphe correspondant à une victoire de l'équipe
     * à domicile
     */
    const PIE_COLOR_HOME = "#5cb95c";
    
    /**
     * Couleur pour la section de graphe correspondant à une victoire de l'équipe
     * à l'extérieur
     */
    const PIE_COLOR_AWAY = "#f0ad4e";
    
    /**
     * Couleur pour la section de graphe correspondant à un match nul
     */
    const PIE_COLOR_DRAW = "#333333";
    
    /**
     * Couleur pour les pronostics exacts
     */
    const PIE_COLOR_PERFECT = '#5cb95c';
    
    /**
     * Couleur pour les différences de but exactes
     */
    const PIE_COLOR_GA = '#f0ad4e';
    
    /**
     * Couleur pour les résultats trouvés
     */
    const PIE_COLOR_WINNER = '#333333';
    
    public function __construct() {

    }
    
    /**
     * Calcule la répartition des pronostics par type (parfait, GA? résultat)
     * pour le fournir en entrée d'un graphe
     * @param Game $game La rencontre pour laquelle compiler les données
     */
    public static function getPredictionResultChartData(Game $game) {

    }
    
    /**
     * Récupère les données constitutives du graphique de tendances des pronostics
     * sous la forme d'un objet JSON à injecter directement dans le JS
     * @param array | Prediction[] $predictions La liste des pronostics
     */
    public static function getPredictionTrendsChartData(Game $game, array $predictions, $dataPoolMinSize) {
        $data = [
            "labels" => [
                "Victoire " . $game->getHomeTeam()->getName() . "(%)",
                "Victoire " . $game->getAwayTeam()->getName() . "(%)",
                "Match nul (%)"
            ]
        ];
        
        $predictionsCount = count($predictions);
        
        if ($predictionsCount  >= $dataPoolMinSize) {
            
            /* @var $prediction Prediction */
        
            // On utilise ici le fait que le résultat d'une prédiction sort
            // sous la forme d'un entier. On incrémente donc le chiffre correspondant
            // à l'indice de l'entier de résultat.
            $stats = [0,0,0];

            foreach ($predictions as $prediction) {
                $stats[$prediction->getResult()]++;
            }
            
            $data["datasets"] = [[ 
                'data' => [
                    number_format(self::getPercentage($stats[Game::RESULT_WINNER_HOME], $predictionsCount), 2, ".",""),
                    number_format(self::getPercentage($stats[Game::RESULT_WINNER_AWAY], $predictionsCount), 2, ".", ""),
                    number_format(self::getPercentage($stats[Game::RESULT_DRAW], $predictionsCount), 2, ".", ""),
                ],
                'backgroundColor' => [
                    self::PIE_COLOR_HOME,
                    self::PIE_COLOR_AWAY,
                    self::PIE_COLOR_DRAW
                ]
                
            ]];
        }

        return json_encode($data);
    }
    
    private static function getPercentage($number, $total) {
        return $number * 100 / $total;
    }
}
