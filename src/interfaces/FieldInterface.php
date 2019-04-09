<?php
namespace wheelform\interfaces;

interface FieldInterface
{
    /**
     * @return array
     */
    public function fieldRules();

    /**
     * @return array
     */
    public function getOptions();
}
