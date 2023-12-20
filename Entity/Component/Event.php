<?php

namespace Xima\ICalBundle\Entity\Component;

use Symfony\Component\HttpFoundation\Request;
use Xima\ICalBundle\Entity\Property\Event\RecurrenceRule;

/**
 * @author Wolfram Eberius <wolfram.eberius@xima.de>
 *
 * @todo Map missing properties: attendees and categories
 */
class Event extends \Eluceo\iCal\Component\Event implements \Stringable
{
    public const MAX_LENGTH_PER_LINE = 75;
    /**
     * @var int
     */
    protected $id;

    /**
     * @var \DateTime
     */
    protected $dateFrom;

    /**
     * @var \DateTime
     */
    protected $timeFrom;

    /**
     * @var \DateTime
     */
    protected $dateTo;

    /**
     * @var \DateTime
     */
    protected $timeTo;

    /**
     * @var RecurrenceRule
     */
    protected $recurrenceRule;


    public function __construct()
    {
        $this->created = new \DateTime();
        parent::__construct(self::generateUniqueId());
    }

    public function __toString(): string
    {
        return static::class;
    }

    /**
     * Generates an unique id according to http://www.kanzaki.com/docs/ical/uid.html with a max length of MAX_LENGTH_PER_LINE.
     *
     * @return string
     */
    public static function generateUniqueId()
    {
        $request = Request::createFromGlobals();
        $leftHandString = time(). '-';
        $rightHandString = '@' . $request->server->get('SERVER_NAME');
        $fillValue = random_int(1_000_000,9_999_999) . '-' .  random_int(1_000_000,9_999_999);

        $fillLength = self::MAX_LENGTH_PER_LINE - strlen($leftHandString) - strlen($rightHandString);
        if (strlen($fillValue) > $fillLength){
            $fillValue = substr($fillValue, 0, $fillLength);
        }

        $uniqueIdCandidate = $leftHandString . $fillValue . $rightHandString;
        $uniqueId = substr($leftHandString . $fillValue . $rightHandString, (strlen($uniqueIdCandidate) > self::MAX_LENGTH_PER_LINE) ? strlen($uniqueIdCandidate)-self::MAX_LENGTH_PER_LINE : 0);

        return $uniqueId;
    }

    public function postLoad()
    {
        $exDatesJson = $this->exDates;
        $this->exDates = [];

        //this->exDates: convert json to DateTimes, can be null or empty array
        if (!empty($exDatesJson) && !is_null($exDatesJson)) {
            foreach ($exDatesJson as $exDateJson) {
                $dateTime = new \DateTime($exDateJson['date'], new \DateTimeZone($exDateJson['timezone']));
                $this->exDates[] = $dateTime;
            }
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

    /**
     * @return boolean
     */
    public function isNoTime()
    {
        return $this->noTime;
    }

    /**
     * @return mixed
     */
    public function getDateFrom()
    {
        return $this->dateFrom;
    }

    public function setDateFrom(mixed $dateFrom)
    {
        $this->dateFrom = $dateFrom;
    }

    /**
     * @return mixed
     */
    public function getTimeFrom()
    {
        return $this->timeFrom;
    }

    public function setTimeFrom(mixed $timeFrom)
    {
        $this->timeFrom = $timeFrom;
    }

    /**
     * @return mixed
     */
    public function getDateTo()
    {
        return $this->dateTo;
    }

    public function setDateTo(mixed $dateTo)
    {
        $this->dateTo = $dateTo;
    }

    /**
     * @return mixed
     */
    public function getTimeTo()
    {
        return $this->timeTo;
    }

    public function setTimeTo(mixed $timeTo)
    {
        $this->timeTo = $timeTo;
    }

    /**
     * @return RecurrenceRule
     */
    public function getRecurrenceRule()
    {
        return $this->recurrenceRule;
    }
}
