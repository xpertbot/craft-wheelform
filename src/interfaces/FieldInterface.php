<?php
namespace wheelform\interfaces;

interface FieldInterface
{
    /**
     * @return array
     */
    public function getFieldRules();

    /**
     * @return array
     */
    public function getConfig();
}
