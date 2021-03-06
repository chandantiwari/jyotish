<?php
/**
 * @link      http://github.com/kunjara/jyotish for the canonical source repository
 * @license   GNU General Public License version 2 or later
 */

namespace Jyotish\Bhava\Object;

use Jyotish\Bhava\Bhava;
use Jyotish\Tattva\Jiva\Nara\Manusha;

/**
 * Class of bhava 10.
 *
 * @author Kunjara Lila das <vladya108@gmail.com>
 */
class B10 extends BhavaObject {
    /**
     * Bhava key
     * 
     * @var int
     */
    protected $objectKey = 10;
    
    /**
     * All names of the bhava.
     * 
     * @var array
     * @see Maharishi Parashara. Brihat Parashara Hora Shastra. Chapter 7, Verse 37-38.
     * @see Varahamihira. Brihat Jataka. Chapter 1, Verse 15-16.
     */
    protected $objectNames = [
        Bhava::NAME_10,
        'Aspada',
        'Mana',
    ];

    /**
     * Indications of bhava.
     * 
     * @var array
     * @see Maharishi Parashara. Brihat Parashara Hora Shastra. Chapter 11, Verse 11.
     */
    protected $bhavaKarakatva = array(
        'royalty',
        'place',
        'profession',
        'honour',
        'father',
        'living in foreign lands',
        'debts',
    );

    /**
     * Purushartha of bhava.
     * 
     * @var string
     */
    protected $bhavaPurushartha = Manusha::PURUSHARTHA_ARTHA;

    public function __construct($options = null) {
        parent::__construct($options);
    }
}