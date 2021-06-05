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
}
