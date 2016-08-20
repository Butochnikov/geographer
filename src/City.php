<?php

namespace MenaraSolutions\Geographer;

use MenaraSolutions\Geographer\Collections\MemberCollection;

/**
 * Class City
 * @package MenaraSolutions\Geographer
 */
class City extends Divisible
{
    /**
     * @var string
     */
    protected $memberClass = City::class;

    /**
     * @var string
     */
    protected static $parentClass = State::class;

    /**
     * @var array
     */
    protected $exposed = [
        'code' => 'ids.geonames',
        'geonamesCode' => 'ids.geonames',
        'name',
        'latitude' => 'lat',
        'longitude' => 'lng',
        'population',
        'parent'
    ];

    /**
     * @return MemberCollection
     */
    public function getCity($name)
    {
        return $this->find([
            'name' => $name
        ]);
    }

    /**
     * @return MemberCollection
     */
    public function getCities()
    {
        return $this->getMembers();
    }
}
