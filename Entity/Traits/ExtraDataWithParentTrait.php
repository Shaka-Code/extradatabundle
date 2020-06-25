<?php

namespace ExtraDataBundle\Entity\Traits;

trait ExtraDataWithParentTrait
{

    use ExtraDataTrait;

    /**
     * @param string $extraData
     *
     * @return $this
     */
    public function setExtraData($extraData = null)
    {
        $this->extraData = $this->extraDataDiff($extraData);

        return $this;
    }

    /**
     * @param string $extraData
     *
     * @return string
     */
    public function extraDataDiff($extraData = null)
    {
        $parentExtraData = array();
        if (!is_null($this->parent)) {
            $parentExtraData = json_decode($this->parent->getExtraData(), true);
        }

        $extraDataArray = (array)json_decode($extraData, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return json_encode(array_diff($extraDataArray, $parentExtraData), JSON_UNESCAPED_UNICODE);
        }

        return $extraData;
    }

}
