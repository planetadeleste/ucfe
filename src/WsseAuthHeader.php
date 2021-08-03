<?php

namespace PlanetaDelEste\Ucfe;

use SoapVar;
use stdClass;

class WsseAuthHeader extends \SoapHeader
{
    private $wss_ns = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd';

    function __construct($user, $pass, $ns = null)
    {
        if ($ns) {
            $this->wss_ns = $ns;
        }

        /**
         * Create Username and Password items
         * <wsse:Username></wsse:Username>
         * <wsse:Password></wsse:Password>
         */
        $obAuth = new stdClass();
        $obAuth->Username = $this->sv($user, XSD_STRING);
        $obAuth->Password = $this->sv($pass, XSD_STRING);

        /**
         * Create UsernameToken element
         * <wsse:UsernameToken>
         *   <wsse:Username></wsse:Username>
         *   <wsse:Password></wsse:Password>
         * </wsse:UsernameToken>
         */
        $obUsernameToken = new stdClass();
        $obUsernameToken->UsernameToken = $this->sv($obAuth, SOAP_ENC_OBJECT, 'UsernameToken');

        /**
         * Create Security element
         * <wsse:Security SOAP-ENV:mustUnderstand="1" xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
         *   <wsse:UsernameToken>
         *     <wsse:Username></wsse:Username>
         *     <wsse:Password></wsse:Password>
         *   </wsse:UsernameToken>
         * </wsse:Security>
         */
        $obSecurity = $this->sv(
            $this->sv($obUsernameToken, SOAP_ENC_OBJECT, 'UsernameToken'),
            SOAP_ENC_OBJECT,
            'Security'
        );

        parent::__construct($this->wss_ns, 'Security', $obSecurity, true);
    }

    /**
     * @param mixed       $data
     * @param int         $encoding
     * @param string|null $nodeName
     *
     * @return \SoapVar
     */
    protected function sv($data, int $encoding, string $nodeName = null): SoapVar
    {
        return new SoapVar($data, $encoding, null, $this->wss_ns, $nodeName, $this->wss_ns);
    }
}
