<?php

namespace ExtraDataBundle\EventListener;

use Sonata\AdminBundle\Event\ConfigureEvent;
use Symfony\Component\Translation\TranslatorInterface;

class SonataAdminConfigureListener
{

    /**
     * @var TranslatorInterface $translator
     */
    private $translator;


    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param ConfigureEvent $event
     */
    public function addOptions(ConfigureEvent $event)
    {
        $formMapper = $event->getMapper();
        if ($formMapper->has('extraData')) {
            $formMapper->addHelp('extraData', $this->trans('help.json_format'));
            $extraDataField = $formMapper->get('extraData');
            $extraDataField->setRequired(false);
        }
    }

    /**
     * @param ConfigureEvent $event
     */
    public function addTemplate(ConfigureEvent $event)
    {
        if ($event->getMapper()->has('extraData')) {
            $extraData = $event->getMapper()->get('extraData');
            $extraData->setTemplate('ExtraDataBundle:CRUD:extra_data_field.html.twig');
            $extraData->setOption('data', $event->getType());
            $extraData->setOption('translation_domain', 'ExtraDataBundle');
        }
    }

    /**
     * @param string $message
     * @param array $options
     *
     * @return string
     */
    public function trans($message, $options = [])
    {
        return $this->translator->trans($message, $options, 'ExtraDataBundle');
    }

}
