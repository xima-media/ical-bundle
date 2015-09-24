iCal Bundle
===========

Wraps the [eluceo/ical](https://github.com/markuspoerschke/iCal) package to a Symfony Bundle with Doctrine Association Mappings and includes Sonata Admin classes.

### Installation
Add the following to your `composer.json`:
```json
"xima-media/ical-bundle": "^0.1.0"`,
"eluceo/ical": "@dev",
"herrera-io/doctrine-dateinterval": "@dev"
```

```json
"repositories" : [
    {
      "type": "vcs",
      "url": "https://github.com/xima-media/iCal"
    }
]
```

**Notes:**
* The dependency to the *eluceo/ical* fork depends on https://github.com/markuspoerschke/iCal/pull/50 and will be removed as soon as the pull request is accepeted.
* The dependency to the dev version of *herrera-io/doctrine-dateinterval* depends on https://github.com/kherge-abandoned/php-doctrine-dateinterval/issues/3 and will be removed as soon as a new release is out.

Load the bundle in `app/AppKernel.php`:
```php
public function registerBundles()
{
    $bundles = array(
    ...
    new Xima\ICalBundle\XimaICalBundle()
}
```      
           


### Configuration
Your projects needs to support the dbal types **json** and **dateinterval**, configured in your `app/config/config.yml`, e.g.:

```yml
doctrine:
    dbal:
      ...
      types:
            json: Sonata\Doctrine\Types\JsonType
            dateinterval:  Herrera\Doctrine\DBAL\Types\DateIntervalType
```

### Integration
##### 1. Create en event entity
You need to create a custom Event class that inherits from ICalBundle's Event class, e.g.:

```php
<?php
use Doctrine\ORM\Mapping as ORM;

/**
 * ICalEvent.
 *
 * @ORM\Entity
 */
class ICalEvent extends \Xima\ICalBundle\Entity\Component\Event
{

}
```

**Note:** ORM auto_mapping should be enabled or configure XimaICalBundle manually in `app/config/config.yml`.

##### 2. Update your database schema
Use your the method of choice to update your database schema, e.g. doctrine migrations.

### Sonata Admin classes

Documentation to be done. Take a look, use or inherit from the admin classes in `xima-media\ical-bundle\Admin\EventAdmin.php` and `xima-media\ical-bundle\Admin\RecurrenceRuleAdmin.php`.

### Usage

##### Get all events in cal format

```php
/**
 * @Route("/ical")
 * Render all events as iCalendar
 */
protected function iCalAction(Array $events)
{
    $vCalendar = new \Eluceo\iCal\Component\Calendar('myCalendar');

    foreach ($events as $event) {
        $vCalendar->addComponent($event);
    }
    
    $response = new Response();
    $response->headers->set('Content-Type', 'text/calendar; charset=utf-8');
    $response->headers->set('Content-Disposition', 'inline; filename="cal.ics"');
    $response->setContent($vCalendar->render());

    return $response;
}
```
