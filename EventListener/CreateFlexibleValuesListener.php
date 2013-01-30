<?php
namespace Oro\Bundle\FlexibleEntityBundle\EventListener;

use Oro\Bundle\FlexibleEntityBundle\Model\FlexibleEntityInterface;

use Oro\Bundle\FlexibleEntityBundle\Event\FilterFlexibleEntityEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Oro\Bundle\FlexibleEntityBundle\FlexibleEntityEvents;

/**
 * Aims to add all values / required values when create or load a new flexible :
 * - required : an empty (or default value) for each required attribute
 * - all : an empty (or default value) for each attribute
 *
 * @author    Nicolas Dupont <nicolas@akeneo.com>
 * @copyright 2012 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/MIT MIT
 *
 */
class CreateFlexibleValuesListener implements EventSubscriberInterface
{

    /**
     * Specifies the list of events to listen
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            FlexibleEntityEvents::CREATE_FLEXIBLE_ENTITY => array('onCreateFlexibleEntity'),
        );
    }

    /**
     * Add values for each attribute
     * @param FilterFlexibleEntityEvent $event
     */
    public function onCreateFlexibleEntity(FilterFlexibleEntityEvent $event)
    {
        $flexible = $event->getEntity();
        $manager = $event->getManager();

        if ($flexible instanceof FlexibleEntityInterface) {

            $values = array();
            $attributes = $manager->getAttributeRepository()->findBy(array('entityType' => $manager->getEntityName(), 'required' => false));
            foreach ($attributes as $attribute) {
                $value = $manager->createEntityValue();
                $value->setAttribute($attribute);
                if ($attribute->getDefaultValue() !== null) {
                    $value->setData($attribute->getDefaultValue());
                }
                $flexible->addValue($value);
            }
        }

    }

}
