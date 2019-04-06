<?php
namespace wheelform\interfaces;

interface FieldInterface
{
    /**
     * @return array
     */
    public function rules();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return array
     */
    public function getOptions();
}
