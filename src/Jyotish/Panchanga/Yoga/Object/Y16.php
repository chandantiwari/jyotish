<?php
/**
 * @link      http://github.com/kunjara/jyotish for the canonical source repository
 * @license   GNU General Public License version 2 or later
 */

namespace Jyotish\Panchanga\Yoga\Object;

use Jyotish\Tattva\Jiva\Nara\Deva;

/**
 * Class of yoga 16.
 *
 * @author Kunjara Lila das <vladya108@gmail.com>
 */
class Y16 extends YogaObject {
    /**
     * Yoga key
     * 
     * @var int
     */
    protected $yogaKey = 16;

    protected $yogaDeva = Deva::DEVA_VAYU;

    public function __construct($options = null) {
        parent::__construct($options);
    }
}