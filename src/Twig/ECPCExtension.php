<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use WCPC2K18Bundle\Entity\Team;


/**
 * Description of WCPC2K18Extension
 *
 * @author seb
 */
class ECPCExtension extends AbstractExtension {
    
    
    public function getFilters() {
        return [
            new TwigFilter('flag', [$this, 'flagFilter']),
            new TwigFilter('timeLeft' ,[$this, 'timeLeftFilter'])
        ];
    }

    /**
     * @param Team $team
     * @param $size
     * @return string
     */
    public function flagFilter($team, $size) {
        return 'assets/images/flags/' . $size . "/" . mb_strtolower($team->getAbbreviation()) . ".png";
    }

    public function timeLeftFilter(\DateInterval $interval) {
        if ($interval->format('%R') == "-") {
            return "";
        } else {
            if ($interval->m > 0) {
                $days = "jours" . ($interval->d == 1 ? "" : 's');
                return $interval->format("%m mois %d $days");
            } elseif($interval->d > 0) {
                if ($interval->h == 0) {
                    return $interval->format("%dj");
                } else {
                    return $interval->format("%dj%hh");
                }


            } elseif($interval->h > 0) {
                return $interval->format("%hh%imn");
            } elseif($interval->i > 0) {
                $mins = "minute" . ($interval->i == 1 ? "" : 's');
                $secs = "seconde" . ($interval->s == 1 ? "" : 's');
                return $interval->format("%imn%ss!");
            } else {
                $secs = "seconde" . ($interval->s == 1 ? "" : 's');
                return $interval->format("%ssecondes!!");
            }
        }
    }
}
