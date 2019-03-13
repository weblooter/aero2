<?php

namespace Sprint\Migration\Schema;

use Sprint\Migration\AbstractSchema;
use Sprint\Migration\Helper;
use Sprint\Migration\HelperManager;

class EventSchema extends AbstractSchema
{


    protected function initialize() {
        $this->setTitle('Схема почтовых событий');
    }

    public function getMap() {
        return array('events/');
    }

    protected function isBuilderEnabled() {
        $helper = HelperManager::getInstance();
        return $helper->Event()->isEnabled();
    }

    public function outDescription() {
        $schemas = $this->loadSchemas('events/', array(
            'event' => '',
            'type' => array(),
            'messages' => array(),
        ));

        $ctnTypes = 0;
        $cnt = 0;

        foreach ($schemas as $schema) {
            $cnt += count($schema['messages']);
            $ctnTypes++;
        }

        $this->out('Типы почтовых событий: %d', $ctnTypes);
        $this->out('Почтовые шаблоны: %d', $cnt);
    }

    public function export() {
        $helper = HelperManager::getInstance();
        $eventTypes = $helper->Event()->getEventTypes(array());
        foreach ($eventTypes as $eventType) {
            $eventName = $eventType['EVENT_NAME'];
            $eventUid = strtolower($eventType['EVENT_NAME'] . '_' . $eventType['LID']);

            unset($eventType['ID']);
            unset($eventType['EVENT_NAME']);
            $eventType['DESCRIPTION'] = $this->explodeText($eventType['DESCRIPTION']);

            $messages = $helper->Event()->exportEventMessages($eventName);
            foreach ($messages as $index => $message) {
                $message['MESSAGE'] = $this->explodeText($message['MESSAGE']);
                $messages[$index] = $message;
            }

            $this->saveSchema('events/' . $eventUid, array(
                'event' => $eventName,
                'type' => $eventType,
                'messages' => $messages
            ));
        }
    }

    public function import() {
        $schemas = $this->loadSchemas('events/', array(
            'event' => '',
            'type' => array(),
            'messages' => array(),
        ));


        foreach ($schemas as $schema) {
            $this->addToQueue('saveEventType', $schema['event'], $schema['type']);
            foreach ($schema['messages'] as $message) {
                $this->addToQueue('saveEventMessage', $schema['event'], $message);
            }
        }


        foreach ($schemas as $schema) {
            $skip = array();
            foreach ($schema['messages'] as $message) {
                $skip[] = $this->getUniqMessage($schema['event'], $message);
            }

            $this->addToQueue('cleanEventMessages', $schema['event'], $skip);
        }

        $skip = array();
        foreach ($schemas as $schema) {
            $skip[] = $this->getUniqType($schema['event'], $schema['type']);
        }

        $this->addToQueue('cleanEventTypes', $skip);

    }

    protected function saveEventType($eventName, $fields) {
        $helper = HelperManager::getInstance();
        $helper->Event()->setTestMode($this->testMode);

        if (isset($fields['DESCRIPTION']) && is_array($fields['DESCRIPTION'])) {
            $fields['DESCRIPTION'] = $this->implodeText($fields['DESCRIPTION']);
        }

        $helper->Event()->saveEventType($eventName, $fields);
    }

    protected function saveEventMessage($eventName, $fields) {
        $helper = HelperManager::getInstance();
        $helper->Event()->setTestMode($this->testMode);

        if (isset($fields['MESSAGE']) && is_array($fields['MESSAGE'])) {
            $fields['MESSAGE'] = $this->implodeText($fields['MESSAGE']);
        }

        $helper->Event()->saveEventMessage($eventName, $fields);
    }

    protected function cleanEventTypes($skip = array()) {
        $helper = HelperManager::getInstance();

        $olds = $helper->Event()->getEventTypes(array());
        foreach ($olds as $old) {
            $uniq = $this->getUniqType($old['EVENT_NAME'], $old);
            if (!in_array($uniq, $skip)) {
                $ok = ($this->testMode) ? true : $helper->Event()->deleteEventType($old);
                $this->outWarningIf($ok, 'Тип почтового события %s:%s: удален', $old['EVENT_NAME'], $old['LID']);
            }
        }
    }

    protected function cleanEventMessages($eventName, $skip = array()) {
        $helper = HelperManager::getInstance();

        $olds = $helper->Event()->getEventMessages($eventName);
        foreach ($olds as $old) {
            $uniq = $this->getUniqMessage($old['EVENT_NAME'], $old);
            if (!in_array($uniq, $skip)) {
                $ok = ($this->testMode) ? true : $helper->Event()->deleteEventMessage($old);
                $this->outWarningIf($ok, 'Почтовый шаблон %s:%s: удален', $old['EVENT_NAME'], $old['SUBJECT']);
            }
        }
    }

    protected function getUniqType($eventName, $item) {
        return $eventName . $item['LID'];
    }

    protected function getUniqMessage($eventName, $item) {
        return $eventName . $item['SUBJECT'];
    }

    protected function explodeText($string) {
        $res = array();
        $string = explode(PHP_EOL, $string);
        foreach ($string as $value) {
            $res[] = trim($value);
        }
        return $res;
    }

    protected function implodeText($strings) {
        return implode(PHP_EOL, $strings);
    }
}