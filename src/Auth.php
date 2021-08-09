<?php

namespace PlanetaDelEste\Ucfe;

class Auth
{
    /** @var string */
    static protected $sUsername;

    /** @var string */
    static protected $sPassword;

    /** @var string */
    static protected $sCodComercio;

    /** @var string */
    static protected $sCodTerminal;

    /** @var string */
    static protected $sUrl;

    /**
     * Set all credentials
     *
     * @param string $sUsername
     * @param string $sPassword
     * @param string $sCodComercio
     * @param string $sCodTerminal
     * @param string $sUrl
     */
    public static function credentials(
        string $sUsername,
        string $sPassword,
        string $sCodComercio,
        string $sCodTerminal,
        string $sUrl
    ) {
        self::setUser($sUsername);
        self::setPassword($sPassword);
        self::setCodComercio($sCodComercio);
        self::setCodTerminal($sCodTerminal);
        self::setUrl($sUrl);
    }

    /**
     * Set UCFE username
     *
     * @param string $sValue
     */
    public static function setUser(string $sValue)
    {
        self::$sUsername = $sValue;
    }

    /**
     * Set UCFE password
     *
     * @param string $sValue
     */
    public static function setPassword(string $sValue)
    {
        self::$sPassword = $sValue;
    }

    /**
     * Set UCFE CodComercio value
     *
     * @param string $sValue
     */
    public static function setCodComercio(string $sValue)
    {
        self::$sCodComercio = $sValue;
    }

    /**
     * Set UCFE CodTerminal value
     *
     * @param string $sValue
     */
    public static function setCodTerminal(string $sValue)
    {
        self::$sCodTerminal = $sValue;
    }

    /**
     * Set url subdomain name for UCFE url {URL}.ucfe.com.uy
     *
     * @param string $sUrl
     */
    public static function setUrl(string $sUrl)
    {
        self::$sUrl = $sUrl;
    }

    /**
     * @return string
     */
    public static function getUser(): string
    {
        return self::$sUsername;
    }

    /**
     * @return string
     */
    public static function getPassword(): string
    {
        return self::$sPassword;
    }

    /**
     * @return string
     */
    public static function getCodComercio(): string
    {
        return self::$sCodComercio;
    }

    /**
     * @return string
     */
    public static function getCodTerminal(): string
    {
        return self::$sCodTerminal;
    }

    /**
     * @return string
     */
    public static function getUrl(): string
    {
        return self::$sUrl;
    }
}
