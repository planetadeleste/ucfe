<?php

namespace PlanetaDelEste\Ucfe;

class Auth
{
    /** @var string */
    protected static $sUsername;

    /** @var string */
    protected static $sPassword;

    /** @var string */
    protected static $sCodComercio;

    /** @var string */
    protected static $sCodTerminal;

    /** @var string */
    protected static $sUrl;

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
    ): void {
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
    public static function setUser(string $sValue): void
    {
        self::$sUsername = $sValue;
    }

    /**
     * Set UCFE password
     *
     * @param string $sValue
     */
    public static function setPassword(string $sValue): void
    {
        self::$sPassword = $sValue;
    }

    /**
     * Set UCFE CodComercio value
     *
     * @param string $sValue
     */
    public static function setCodComercio(string $sValue): void
    {
        self::$sCodComercio = $sValue;
    }

    /**
     * Set UCFE CodTerminal value
     *
     * @param string $sValue
     */
    public static function setCodTerminal(string $sValue): void
    {
        self::$sCodTerminal = $sValue;
    }

    /**
     * Set url subdomain name for UCFE url {URL}.ucfe.com.uy
     *
     * @param string $sUrl
     */
    public static function setUrl(string $sUrl): void
    {
        self::$sUrl = $sUrl;
    }

    /**
     * @return string
     */
    public static function getUser(): ?string
    {
        return self::$sUsername;
    }

    /**
     * @return string
     */
    public static function getPassword(): ?string
    {
        return self::$sPassword;
    }

    /**
     * @return string
     */
    public static function getCodComercio(): ?string
    {
        return self::$sCodComercio;
    }

    /**
     * @return string
     */
    public static function getCodTerminal(): ?string
    {
        return self::$sCodTerminal;
    }

    /**
     * @return string
     */
    public static function getUrl(): ?string
    {
        return self::$sUrl;
    }
}
