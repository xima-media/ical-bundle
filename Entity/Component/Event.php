<?php

namespace Xima\ICalBundle\Entity\Component;

use Symfony\Component\HttpFoundation\Request;

/**
 * @author Wolfram Eberius <wolfram.eberius@xima.de>
 *
 * @todo Map missing properties: attendees and categories
 */
class Event extends \Eluceo\iCal\Component\Event
{
    /**
     * @var int
     */
    private $id;

    public function __construct()
    {
        parent::__construct(self::generateUniqueId());
    }

    public function __toString()
    {
        return get_class($this);
    }

    public static function generateUniqueId()
    {
        $request = Request::createFromGlobals();
        $uniqueId = time().'-'.get_current_user().'@'.$request->server->get('SERVER_NAME');

        return $uniqueId;
    }

    public function postLoad()
    {
        //this->exDates: convert json to DateTimes
        $exDatesJson = $this->exDates;
        $this->exDates = array();

        foreach ($exDatesJson as $exDateJson) {
            $dateTime = new \DateTime($exDateJson['date'], new \DateTimeZone($exDateJson['timezone']));
            $this->exDates[] = $dateTime;
        }
    }

    public function removeRecurrenceRule()
    {
        $this->recurrenceRule = null;

        return $this;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \DateTime
     */
    public function getDtStart()
    {
        return $this->dtStart;
    }
}
