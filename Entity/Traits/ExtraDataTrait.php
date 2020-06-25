<?php

namespace ExtraDataBundle\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

trait ExtraDataTrait
{

    /**
     * @var string $extraData
     *
     * @ORM\Column(type="text", nullable=true)
     *
     * @Assert\Callback(
     *  callback={"ExtraDataBundle\Validator\JSONValidator", "validate"},
     *  payload={"field"="extraData"}
     * )
     */
    private $extraData;

    /**
     * @var array Pasa el extra data a un array.
     */
    private $_extraData_array = null;


    /**
     * @return string
     */
    public function getExtraData()
    {
        return is_null($this->extraData) ? "" : $this->extraData;
    }

    /**
     * @param string $extraData
     *
     * @return $this
     */
    public function setExtraData($extraData = null)
    {
        $this->extraData = $extraData;
        $this->_extraData_array = json_decode($this->getExtraData(), true);

        return $this;
    }

    /**
     * @return array
     */
    public function jsonExtraData()
    {
        if ($this->_extraData_array == null) {
            $this->_extraData_array = json_decode($this->getExtraData(), true);
        }

        return $this->_extraData_array;
    }

    /**
     * @param mixed $extraData
     *
     * @return ExtraDataTrait
     */
    public function setJsonExtraData($extraData)
    {
        return $this->setExtraData(json_encode($extraData, JSON_UNESCAPED_UNICODE));
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function getData($key)
    {
        $extraData = $this->jsonExtraData();
        $value = null;
        if (isset($extraData[$key])) {
            $value = $extraData[$key];
        }

        return $value;
    }

    /**
    * @param string $name
    *
    * @return boolean
    */
    public function __isset($name)
    {
        $extraData = $this->jsonExtraData();

        return isset($extraData[$name]);
    }

    /**
    * @param string $name
    */
    public function __unset($name)
    {
        $extra = $this->jsonExtraData();
        unset($extra[$name]);
        $this->setJsonExtraData($extra);
    }

    /**
    * @param string $name
    *
    * @return mixed
    */
    public function __get($name)
    {
        return $this->getData($name);
    }

    /**
    * @param string $name
    * @param string $value
    *
    * @return ExtraDataTrait
    */
    public function __set($name, $value)
    {
        $extra = $this->jsonExtraData();
        $extra[$name] = $value;
        $this->setJsonExtraData($extra);

        return $this;
    }
}
