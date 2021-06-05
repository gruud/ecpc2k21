<?php

namespace App\Form;


use App\Entity\Game;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * La classe GameType implémente le formulaire de saisie du résultat réel 
 * d'une rencontre. Il permet notamment des saisir des données précises
 * sur les scores qui permettront ensuite d'évaluer les pronostics des joueurs
 *
 * @author Sébastien ZINS
 */
class GameType extends AbstractType {
    
    /**
     * Construit le formulaire de saisie du résultat d'une rencontre
     * 
     * @param FormBuilderInterface $builder Le constructeur de formulaire Symfony
     */
    public function buildForm(FormBuilderInterface $builder, $options) {
        
        $game = $options['game'];
        
        $builder->add('goalsHome', IntegerType::class, [
            'label' => "Buts " . $game->getHomeTeam()->getName(),
            'required' => true
        ])->add('goalsAway', IntegerType::class, [
            'label' => "Buts " . $game->getAwayTeam()->getName(),
        ])->add('extraTime', CheckboxType::class, [
            'label' => "Prolongations ? ",
            'required' => false,
        ])->add('penaltiesHome', IntegerType::class, [
            'label' => 'Penalties ' . $game->getHomeTeam()->getName(),
            'required' => false
        ])->add('penaltiesAway', IntegerType::class, [
            'label' => 'Penalties ' . $game->getAwayTeam()->getName(),
            'required' => false
        ])->add('submit', SubmitType::class, [
            'label' => "Valider",
        ])->add('cancel', SubmitType::class, [
            'label' => "Annuler"
        ]);

    }
    
    public function configureOptions(OptionsResolver $resolver) {
        parent::configureOptions($resolver);
        
        $resolver->setRequired('game');
        $resolver->setAllowedTypes('game', Game::class);
        
    }
    
    public function getName() {
        return 'game';
    }
}
