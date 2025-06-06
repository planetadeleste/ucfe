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

    /**
     * @param string $sName
     *
     * @return mixed
     */
    public function __get(string $sName)
    {
        return $this->getAttribute($sName);
    }

    /**
     * @param string $sName
     * @param        $sValue
     *
     * @return void
     */
    public function __set(string $sName, $sValue): void
    {
        $this->setAttribute($sName, $sValue);
    }

    /**
     * @param string $sKey
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getAttribute(string $sKey, $default = null)
    {
        $sValue = $this->getOriginalAttr($sKey, $default);

        if ($this->hasAccessor($sKey)) {
            return $this->{$this->getAccesorMethod($sKey)}($sValue);
        }

        return $sValue;
    }

    /**
     * @param string $sKey
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getOriginalAttr(string $sKey, $default = null)
    {
        return array_get($this->arAttributes, $sKey, $default);
    }

    /**
     * @param string $sKey
     * @param mixed  $sValue
     * @param bool   $bWithoutMutate
     *
     * @return void
     */
    public function setAttribute(string $sKey, $sValue, bool $bWithoutMutate = false): void
    {
        if ($this->hasCast($sKey)) {
            $sValue = $this->castAttribute($sKey, $sValue);
        }

        array_set($this->arAttributes, $sKey, $sValue);

        if ($bWithoutMutate !== false || !$this->hasMutator($sKey)) {
            return;
        }

        $this->{$this->getMutatorMethod($sKey)}($sValue);
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
        return 'get'.Str::studly($sKey).'Attribute';
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
        return 'set'.Str::studly($sKey).'Attribute';
    }

    /**
     * @param string $sName
     *
     * @return bool
     */
    public function __isset(string $sName): bool
    {
        return isset($this->arAttributes[$sName]);
    }

    /**
     * @return array
     */
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
                    if (!is_object($sValue) || !method_exists($sValue, 'toArray')) {
                        continue;
                    }

                    $arItem[$sKey] = $sValue->toArray();
                }

                return $arItem;
            }

            return is_object($arItem) && method_exists($arItem, 'toArray') ? $arItem->toArray() : $arItem;
        })->all();
    }

    /**
     * @return array
     */
    protected function accessorsToAttribute(): array
    {
        $arMethods = array_filter(get_class_methods($this), static function ($sName) {
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

    /**
     * @param array $arData
     *
     * @return void
     */
    public function sortAttributes(array &$arData): void
    {
        $arSortKeys = $this->getSortKeys();

        if (empty($arSortKeys)) {
            return;
        }

        $arKeys = array_flip($arSortKeys);
        $result = array_replace($arKeys, $arData);
        $arData = array_intersect_key($result, $arData);
    }

    /**
     * Key array for sort attributes
     *
     * @return array
     */
    abstract public function getSortKeys(): array;

    /**
     * @param array $arAttrs
     *
     * @return void
     */
    public function setAttributes(array $arAttrs): void
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

    /**
     * @param string $sKey
     *
     * @return bool
     */
    protected function hasCast(string $sKey): bool
    {
        return isset($this->arCast) && array_key_exists($sKey, $this->arCast);
    }

    /**
     * Devuelve el array de casts definidos para los atributos.
     *
     * @return array
     */
    public function getCasts(): array
    {
        return empty($this->arCast)
            ? []
            : array_map(
                static function ($sCast) {
                    return $sCast === 'decimal' ? 'decimal:2' : $sCast;
                },
                $this->arCast
            );
    }

    /**
     * @param string $sKey
     * @param $sValue
     *
     * @return mixed
     */
    protected function castAttribute(string $sKey, $sValue): mixed
    {
        if (!$this->hasCast($sKey) || empty($sValue)) {
            return $sValue;
        }

        $castType = $this->getCastType($sKey);

        return match ($castType) {
            'int', 'integer' => (int)$sValue,
            'real', 'float', 'double' => (float)$sValue,
            'decimal' => $this->asDecimal($sValue, $sKey),
            'bool' => (bool)$sValue,
            'array', 'json' => is_string($sValue) ? json_decode($sValue, true) : $sValue,
            'object' => is_string($sValue) ? json_decode($sValue, false) : $sValue,
            'date', 'datetime', 'timestamp' => Carbon::parse($sValue),
            default => $sValue,
        };
    }

    /**
     * Get the type of cast for a given attribute.
     *
     * @param string $sKey
     *
     * @return string|null
     */
    protected function getCastType(string $sKey): ?string
    {
        return $this->hasCast($sKey) ? explode(':', array_get($this->arCast, $sKey))[0] : null;
    }

    /**
     * Casts a value to a decimal with a specified precision.
     *
     * @param mixed $sValue
     * @param string $sKey
     *
     * @return float
     */
    protected function asDecimal(mixed $sValue, string $sKey): float
    {
        return round((float)$sValue, explode(':', array_get($this->getCasts(), $sKey), 2)[1] ?? 2);
    }
}
