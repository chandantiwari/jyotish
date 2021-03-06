<?php
/**
 * @link      http://github.com/kunjara/jyotish for the canonical source repository
 * @license   GNU General Public License version 2 or later
 */

namespace Jyotish\Base;

use Jyotish\Graha\Graha;
use Jyotish\Graha\Lagna;
use Jyotish\Graha\Upagraha;
use Jyotish\Bhava\Bhava;
use Jyotish\Bhava\Arudha;
use Jyotish\Ganita\Math;

/**
 * Data class.
 *
 * @author Kunjara Lila das <vladya108@gmail.com>
 */
class Data {
    /**
     * Graha block
     */
    const BLOCK_GRAHA = 'graha';
    /**
     * Upagraha block
     */
    const BLOCK_UPAGRAHA = 'upagraha';
    /**
     * Extra block
     */
    const BLOCK_EXTRA = 'extra';
    /**
     * Bhava block
     */
    const BLOCK_BHAVA = 'bhava';
    /**
     * User block
     */
    const BLOCK_USER  = 'user';
    /**
     * More block
     */
    const BLOCK_MORE  = 'more';

    /**
     * Data template.
     * 
     * @var array
     */
    protected $dataTemplate = [
        self::BLOCK_GRAHA => [
            'element' => ['longitude', 'latitude', 'speed']
        ],
        self::BLOCK_UPAGRAHA => [
            'element' => ['longitude']
        ],
        self::BLOCK_EXTRA => [
            'element' => ['longitude']
        ],
        self::BLOCK_BHAVA => [
            'element' => ['longitude']
        ],
        self::BLOCK_USER => [
            'gender'
        ],
        self::BLOCK_MORE => [
            'lagna'
        ]
    ];

    /**
     * Required data blocks.
     * 
     * @var array
     */
    protected $dataRequired = [
        self::BLOCK_GRAHA, 
        self::BLOCK_EXTRA
    ];

    /**
     * Analyzed data.
     * 
     * @var array
     */
    protected $ganitaData = null;

    /**
     * Array with values ​​of the rashis in the bhavas.
     * 
     * @var array
     */
    protected $rashiInBhava = null;

    /**
     * A point taken as the lagna.
     * 
     * @var string
     */
    protected $lagna = Graha::KEY_LG;

    /**
     * Constructor
     * 
     * @param array $ganitaData
     * @param int $lagna
     */
    public function __construct(array $ganitaData, $lagna = Graha::KEY_LG) {
        $this->checkData($ganitaData);

        foreach ($this->dataRequired as $block){
            foreach($this->ganitaData[$block] as $key => $params){
                if(!isset($params['rashi'])){
                    $units = Math::partsToUnits($params['longitude']);
                    $this->ganitaData[$block][$key]['rashi'] = $units['units'];
                    $this->ganitaData[$block][$key]['degree'] = $units['parts'];
                }
            }
        }

        if(!isset($this->ganitaData[self::BLOCK_BHAVA]) or $this->lagna != $lagna){
            if(array_key_exists($lagna, Graha::$graha)){
                $block = self::BLOCK_GRAHA;
                $this->lagna = $lagna;
            }else{
                $block = self::BLOCK_EXTRA;
                $this->lagna = Graha::KEY_LG;
            }
            $this->ganitaData[self::BLOCK_MORE]['lagna'] = $this->lagna;
            
            $longitude = $this->ganitaData[$block][$lagna]['longitude'];
            for($b = 1; $b <= 12; $b++){
                $this->ganitaData[self::BLOCK_BHAVA][$b]['longitude'] = $longitude < 360 ? $longitude : $longitude - 360;
                $units = Math::partsToUnits($this->ganitaData[self::BLOCK_BHAVA][$b]['longitude']);
                $this->ganitaData[self::BLOCK_BHAVA][$b]['rashi'] = $units['units'];
                $this->ganitaData[self::BLOCK_BHAVA][$b]['degree'] = $units['parts'];
                $longitude += 30;
            }
        }
    }

    /**
     * Check incoming data.
     * 
     * @param $ganitaData Data set
     * @throws Exception\InvalidArgumentException
     */
    protected function checkData($ganitaData)
    {
        foreach ($this->dataRequired as $block){
            if(!key_exists($block, $ganitaData))
                throw new Exception\InvalidArgumentException("Block '$block' is not found in the data.");
        }

        $checkBlock = function($block, $value){
            if($block == self::BLOCK_GRAHA) $elements = Graha::$graha;
            elseif($block == self::BLOCK_BHAVA) $elements = Bhava::$bhava;
            elseif($block == self::BLOCK_EXTRA) $elements = [Graha::KEY_LG => 'Lagna'];
            else $elements = array();

            foreach ($elements as $key => $name){
                if(!isset($value[$key]))
                    throw new Exception\InvalidArgumentException("Key '$key' in block '$block' is not found.");

                foreach ($this->dataTemplate[$block]['element'] as $propName){
                    if(!array_key_exists($propName, $value[$key]))
                        throw new Exception\InvalidArgumentException("Property '$propName' in element '$key $block' is not found.");
                }
            }
        };

        foreach ($ganitaData as $block => $value){
            if(defined('self::BLOCK_'.strtoupper($block))){
                $checkBlock($block, $value);
                $this->ganitaData[$block] = $value;
            }else{
                continue;
            }

        }
    }

    /**
     * Get Ganita data.
     */
    public function getData()
    {
        return $this->ganitaData;
    }
    
    /**
     * Calculation of extra lagnas.
     * 
     * @param null|array $lagnaKeys Array of lagna keys
     */
    public function calcExtraLagna(array $lagnaKeys = null)
    {
        $Lagna = new Lagna($this->ganitaData);
        $generateLagna = $Lagna->generateLagna($lagnaKeys);
        
        foreach ($generateLagna as $key => $data){
            $this->ganitaData[self::BLOCK_EXTRA][$key] = $data;
        }
    }
    
    /**
     * Calculation of arudhas.
     * 
     * @param null|array $arudhaKeys Array of arudha keys
     * @param null|array $options Options to set (optional)
     */
    public function calcBhavaArudha(array $arudhaKeys = null, array $options = null)
    {
        $Arudha = new Arudha($this->ganitaData, $options);
        $generateArudha = $Arudha->generateArudha($arudhaKeys);
        
        foreach ($generateArudha as $key => $data){
            $this->ganitaData[self::BLOCK_EXTRA][$key] = $data;
        }
    }
    
    /**
     * Calculation of upagrahas.
     * 
     * @param null|array $upagrahaKeys Array of upagraha keys
     */
    public function calcUpagraha(array $upagrahaKeys = null)
    {
        $Upagraha = new Upagraha($this->ganitaData);
        $generateUpagraha = $Upagraha->generateUpagraha($upagrahaKeys);
        
        foreach ($generateUpagraha as $key => $data){
            $this->ganitaData[self::BLOCK_UPAGRAHA][$key] = $data;
        }
    }

    /**
     * Get rashi in bhava.
     * 
     * @return array
     */
    public function getRashiInBhava() {
        if(is_null($this->rashiInBhava)){
            foreach ($this->ganitaData[self::BLOCK_BHAVA] as $bhava => $params) {
                $rashi = $params['rashi'];
                $this->rashiInBhava[$rashi] = $bhava;
            }
        }
        return $this->rashiInBhava;
    }

    /**
     * Get bodies in bhava.
     * 
     * @return array
     */
    public function getBodyInBhava() {
        foreach ([self::BLOCK_GRAHA, self::BLOCK_EXTRA, self::BLOCK_UPAGRAHA] as $block){
            if(!isset($this->ganitaData[$block])) continue;
            
            foreach ($this->ganitaData[$block] as $body => $params) {
                $rashi = $params['rashi'];
                $bhava = $this->getRashiInBhava()[$rashi];

                $bodyInBhava[$body] = $bhava;
            }
        }
        return $bodyInBhava;
    }

    /**
     * Get bodies in rashi.
     * 
     * @return array
     */
    public function getBodyInRashi() {
        foreach ([self::BLOCK_GRAHA, self::BLOCK_EXTRA, self::BLOCK_UPAGRAHA] as $block){
            if(!isset($this->ganitaData[$block])) continue;
            
            foreach ($this->ganitaData[$block] as $body => $params) {
                $rashi = $params['rashi'];

                $bodyInRashi[$body] = $rashi;
            }
        }
        return $bodyInRashi;
    }
}