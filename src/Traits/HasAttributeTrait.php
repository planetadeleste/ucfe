<?php

namespace PlanetaDelEste\Ucfe\Traits;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * @property array $arCast
 */
trait HasAttributeTrait
{
    protected array $arAttributes = [];

    public function __get(string $sName)
    {
        return $this->getAttribute($sName);
    }

    public function __set(string $sName, $sValue)
    {
        $this->setAttribute($sName, $sValue);
    }

    /**
     * @param string $sKey
     * @param mixed  $default
     * @return mixed
     */
    public function getAttribute(string $sKey, $default = null)
    {
        $sValue = array_get($this->arAttributes, $sKey, $default);

        if ($this->hasAccessor($sKey)) {
            return $this->{$this->getAccesorMethod($sKey)}($sValue);
        }

        return $sValue;
    }

    /**
     * @param string $sKey
     * @param mixed  $sValue
     * @param bool   $bWithoutMutate
     * @return void
     */
    public function setAttribute(string $sKey, $sValue, bool $bWithoutMutate = false): void
    {
        if ($this->hasCast($sKey)) {
            $sValue = $this->castAttribute($sKey, $sValue);
        }

        array_set($this->arAttributes, $sKey, $sValue);

        if ($bWithoutMutate === false && $this->hasMutator($sKey)) {
            $this->{$this->getMutatorMethod($sKey)}($sValue);
        }
    }

    /**
     * Determine if a get accessor exists for an attribute.
     *
     * @param string $sKey
     *
     * @return bool
     */
    public function hasAccessor(string $sKey): bool
    {
        return method_exists($this, $this->getAccesorMethod($sKey));
    }

    /**
     * get accessor method name
     *
     * @param string $sKey
     *
     * @return string
     */
    public function getAccesorMethod(string $sKey): string
    {
        return 'get' . Str::studly($sKey) . 'Attribute';
    }

    /**
     * Determine if a set mutator exists for an attribute.
     *
     * @param string $sKey
     *
     * @return bool
     */
    public function hasMutator(string $sKey): bool
    {
        return method_exists($this, $this->getMutatorMethod($sKey));
    }

    /**
     * Get mutator method name
     *
     * @param string $sKey
     *
     * @return string
     */
    public function getMutatorMethod(string $sKey): string
    {
        return 'set' . Str::studly($sKey) . 'Attribute';
    }

    public function __isset(string $sName)
    {
        return isset($this->arAttributes[$sName]);
    }

    public function toArray(): array
    {
        $arData = $this->arAttributes + $this->accessorsToAttribute();
        $this->sortAttributes($arData);
        return Collection::make($arData)->map(function ($arItem) {
            if ($arItem instanceof \DateTime) {
                return $arItem->format($this->getDateFormat());
            }

            if (is_array($arItem)) {
                foreach ($arItem as $sKey => $sValue) {
                    if (is_object($sValue) && method_exists($sValue, 'toArray')) {
                        $arItem[$sKey] = $sValue->toArray();
                    }
                }

                return $arItem;
            }

            return is_object($arItem) && method_exists($arItem, 'toArray') ? $arItem->toArray() : $arItem;
        })->all();
    }

    protected function accessorsToAttribute(): array
    {
        $arMethods = array_filter(get_class_methods($this), function ($sName) {
            return $sName !== 'getAttribute' && substr($sName, 0, 3) === 'get' && substr($sName, -9) === 'Attribute';
        });

        if (empty($arMethods)) {
            return [];
        }

        $arAttributes = [];
        foreach ($arMethods as $sMethod) {
            $sAttr = substr($sMethod, 3, -9);
            if ($this->hasAttribute($sAttr)) {
                continue;
            }

            $arAttributes[$sAttr] = $this->{$sMethod}();
        }

        return $arAttributes;
    }

    /**
     * @param string $sKey
     *
     * @return bool
     */
    public function hasAttribute(string $sKey): bool
    {
        return array_key_exists($sKey, $this->arAttributes);
    }

    public function sortAttributes(array &$arData)
    {
        $arSortKeys = $this->getSortKeys();
        if (empty($arSortKeys)) {
            return;
        }

        $arKeys = array_flip($arSortKeys);
        $result = array_replace($arKeys, $arData);       // result = sorted keys + values from input +
        $arData = array_intersect_key($result, $arData); // remove keys are not existing in input array
    }

    /**
     * Key array for sort attributes
     *
     * @return array
     */
    abstract public function getSortKeys(): array;

    public function setAttributes(array $arAttrs)
    {
        if (!\Arr::isAssoc($arAttrs)) {
            return;
        }

        foreach ($arAttrs as $sKey => $sValue) {
            $this->setAttribute($sKey, $sValue);
        }
    }

    /**
     * @return string DateTime format
     */
    public function getDateFormat(): string
    {
        return 'Y-m-d';
    }

    protected function hasCast(string $sKey): bool
    {
        return isset($this->arCast) && array_key_exists($sKey, $this->arCast);
    }

    protected function castAttribute(string $sKey, $sValue)
    {
        if (!$this->hasCast($sKey) || empty($sValue)) {
            return $sValue;
        }

        switch ($this->arCast[$sKey]) {
            case 'int':
            case 'integer':
                return (int)$sValue;
            case 'real':
            case 'float':
            case 'double':
                return (float)$sValue;
            case 'decimal':
                return round((float)$sValue, 2);
            case 'bool':
                return (bool)$sValue;
            case 'array':
            case 'json':
                return is_string($sValue) ? json_decode($sValue, true) : $sValue;
            case 'object':
                return is_string($sValue) ? json_decode($sValue, false) : $sValue;
            case 'date':
            case 'datetime':
            case 'timestamp':
                return Carbon::parse($sValue);
            default:
                return $sValue;
        }
    }
}
