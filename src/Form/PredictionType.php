<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use App\Entity\Team;
use App\Component\PredictionChecker;

/**
 * La classe PredictionType implémente le formulaire de saisie d'un pronostic
 * par un joueur. Un joueur n'a que deux champs à remplir : le score de l'équipe
 * 1 et le score de l'équipe 2, et peut, une fois par journée choisir le bonus
 * 
 * 
 *
 * @author Sébastien ZINS
 */
class PredictionType extends AbstractType {

    /**
     *
     * @var PredictionChecker Le service de validation des pronostics
     */
    private $predictionChecker;

    /**
     * Construit le formulaire
     * @param FormBuilderInterface $builder L'utilitaire de construction de formulaire
     * @param array $options Les options passées au formulaire pour sa construction
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        parent::buildForm($builder, $options);
        
        $this->predictionChecker = $options['predictionChecker'];
        $game = $options['game'];
        $builder->add('goalsHome', IntegerType::class,
                      [
            'label' => $this->getScoreLabel($game->getHomeTeam()),
        ])->add('goalsAway', IntegerType::class,
                [
            'label' => $this->getScoreLabel($game->getAwayTeam()),
        ])->add('submit', SubmitType::class,
                [
            'label' => "Valider",
            'attr'  => ['class' => '']
        ])->add('cancel', SubmitType::class, [
            'label' => 'Annuler',
            'attr' => ['class' => 'btn-dark pull-right', 'formnovalidate' => 'formnovalidate']
        ]);

        $builder->addEventListener(FormEvents::PRE_SET_DATA,
                                   [$this, 'onPreSetData']);
    }

    public function onPreSetData(FormEvent $event) {
        $form = $event->getForm();
        $prediction = $event->getData();
        $day = $prediction->getGame()->getPhase();

        //On vérifie si le bouton de jackpot peut être présent ou non

        $form->add('jackpot', CheckboxType::class,[
            "label"    => "Je mise double sur cette rencontre",
            "required" => false,
            "disabled" => $this->predictionChecker->jackpotUsedForDay($day) && !$prediction->getJackpot()
        ]);
    }

    public function getName() {
        return 'prediction';
    }

    private function getScoreLabel(Team $team) {
        return "Score " . $team->getName();
    }

    /**
     * Configure les options à passer au formulaire. On ajoute ici, pour le formulaire
     * de prédiction, des références au match et à l'utilisateur associés
     * au pronostic à saisir. 
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver) {
        parent::configureOptions($resolver);
    $resolver->setRequired(['game', 'user', 'predictionChecker']);
    }

}
