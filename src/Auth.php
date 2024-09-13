<?php

namespace PlanetaDelEste\Ucfe;

class Auth
{
    /**
     * @var string
     */
    protected static string $sUsername;

    /**
     * @var string
     */
    protected static string $sPassword;

    /**
     * @var string
     */
    protected static string $sCodComercio;

    /**
     * @var string
     */
    protected static string $sCodTerminal;

    /**
     * @var string
     */
    protected static string $sUrl;

    /**
     * Set all credentials
     *
     * @param string | array $sUsername
     * @param string | null  $sPassword
     * @param string | null  $sCodComercio
     * @param string | null  $sCodTerminal
     * @param string | null  $sUrl
     */
    public static function credentials(
        $sUsername,
        ?string $sPassword = null,
        ?string $sCodComercio = null,
        ?string $sCodTerminal = null,
        ?string $sUrl = null
    ): void {
        if (is_array($sUsername)) {
            $sPassword    = $sUsername[1];
            $sCodComercio = $sUsername[2];
            $sCodTerminal = $sUsername[3];
            $sUrl         = $sUsername[4];
            $sUsername    = $sUsername[0];
        }

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
