<?php

namespace MenaraSolutions\Geographer\Services\Poliglottas;

use MenaraSolutions\Geographer\Contracts\TranslationAgencyInterface;
use MenaraSolutions\Geographer\Country;
use MenaraSolutions\Geographer\State;
use MenaraSolutions\Geographer\Earth;
use MenaraSolutions\Geographer\Exceptions\MisconfigurationException;
use MenaraSolutions\Geographer\Exceptions\FileNotFoundException;
use MenaraSolutions\Geographer\Contracts\IdentifiableInterface;
use MenaraSolutions\Geographer\Contracts\PoliglottaInterface;
use MenaraSolutions\Geographer\City;

/**
 * Class Base
 * @package App\Services\Poliglottas
 */
abstract class Base implements PoliglottaInterface
{
    /**
     * @var string
     */
    protected $code;

    /**
     * @var TranslationAgencyInterface
     */
    protected $agency;

    /**
     * @var array
     */
    protected $cache = [];

    /**
     * @var array
     */
    protected $defaultPrepositions = [];

    /**
     * Base constructor.
     * @param TranslationAgencyInterface $agency
     */
    public function __construct(TranslationAgencyInterface $agency)
    {
        $this->agency = $agency;
    }

    /**
     * @param IdentifiableInterface $subject
     * @param string $form
     * @param bool $preposition
     * @return string
     * @throws MisconfigurationException
     */
    public function translate(IdentifiableInterface $subject, $form = 'default', $preposition = true)
    {   
        if (! method_exists($this, 'inflict' . ucfirst($form))) {
            throw new MisconfigurationException('Language ' . $this->code . ' doesn\'t inflict to ' . $form);
        }

        $meta = $this->fromDictionary($subject);
        $result = $this->extract($meta, $subject->expectsLongNames(), $form);

        if (! $result) {
            $template = $this->inflictDefault($meta, $subject->expectsLongNames());
            $result = $this->{'inflict' . ucfirst($form)}($template);
            
            if ($preposition) $result = $this->defaultPrepositions[$form] . ' ' . $result;
        } else if ($result && ! $preposition) {
            $result = mb_substr($result, mb_strpos($result, ' '));
        }

        return $result;
    }

    /**
     * @param IdentifiableInterface $subject
     * @return array
     */
    protected function fromDictionary(IdentifiableInterface $subject)
    {
        $translations = $this->agency->getRepository()->getTranslations($subject, $this->code);
        
        return $translations ?: $subject->getMeta();
    }

    /**
     * @param array $meta
     * @param $long
     * @return string
     */
    protected function inflictDefault(array $meta, $long)
    {
        return $this->extract($meta, $long, 'default');
    }

    /**
     * @param string $template
     * @return string
     */
    protected function inflictIn($template)
    {
	    return $template;
    }

    /**
     * @param string $template
     * @return string
     */
    protected function inflictFrom($template)
    {   
        return $template;
    }
   
    /**
     * @param array $meta
     * @param $long
     * @param $form
     * @return mixed
     */
    protected function extract(array $meta, $long, $form)
    {
        $variants = [];
       
        if (isset($meta['long'][$form])) {
            $variants[] = $meta['long'][$form];
        }
    
        if (isset($meta['short'][$form])) {
            $variants[] = $meta['short'][$form];
        }
        
        if (! $long) $variants = array_reverse($variants);
         
        return !empty($variants) ? $variants[0] : false;
    }
}
