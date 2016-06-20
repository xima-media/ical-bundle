<?php

namespace Xima\ICalBundle\Entity\Property\RecurrenceRule;

class NthOccurrence
{
    /**
     * @var int
     */
    protected $id;

    /**
     * Positive or negative integer as string.
     *
     * @var string
     */
    protected $nth = '';

    /**
     * What doe repeat? Weekday, ...
     *
     * @var string
     */
    protected $occurrence = '';

    public function __toString()
    {
        return $this->nth.$this->occurrence;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getOccurrence()
    {
        return $this->occurrence;
    }

    /**
     * @param string $occurrence
     */
    public function setOccurrence($occurrence)
    {
        $this->occurrence = $occurrence;
    }

    /**
     * @return string
     */
    public function getNth()
    {
        return $this->nth;
    }

    /**
     * @param string $nth
     */
    public function setNth($nth)
    {
        $this->nth = $nth;
    }
}
