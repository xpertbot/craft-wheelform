<?php
namespace wheelform\interfaces;

interface FieldInterface
{
    /**
     * @return array
     */
    public function rules();

    /**
     * @return array
     */
    public function getOptions();
}
