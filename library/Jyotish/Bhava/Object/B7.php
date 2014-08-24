<?php
/**
 * @link      http://github.com/kunjara/jyotish for the canonical source repository
 * @license   GNU General Public License version 2 or later
 */

namespace Jyotish\Bhava\Object;

use Jyotish\Tattva\Jiva\Manusha;

/**
 * Class of bhava 7.
 *
 * @author Kunjara Lila das <vladya108@gmail.com>
 */
class B7 extends \Jyotish\Bhava\Bhava {

	/**
	 * Purushartha of bhava.
	 * 
	 * @var string
	 */
	protected $bhavaPurushartha = Manusha::PURUSHARTHA_KAMA;

	public function __construct($options) {
		return $this;
	}

}