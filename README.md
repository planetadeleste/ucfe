# Cliente UCFE

API PHP para consumir datos de servicios URUWARE (UCFE)

[![](https://tokei.rs/b1/github/planetadeleste/ucfe)](https://github.com/XAMPPRocky/tokei_rs).

## Instalación

```bash
composer require planetadeleste/ucfe
```

## Uso

Ante todo, es necesario contratar el servicio de [URUWARE](http://www.uruware.com/).

### Configurar credenciales

```php
\PlanetaDelEste\Ucfe\Auth::credentials(
    $ucfeUsername,
    $ucfePassword,
    $ucfeCodComercio,
    $ucfeCodTerminal,
    $ucfeUrl
);
```

> `$ucfeUrl` es parte de la url proporcianada por el departamento de operaciones de Uruware. Por ejemplo, para la url `testBiz.ucfe.com.uy`, en el campo `$ucfeUrl` solo debe pasar `testBiz`.

### Obtener datos de una empresa desde su RUT

```php
$obResponse = (new \PlanetaDelEste\Ucfe\Service\ActEmpresarial())->get('219000090011');
print_r($obResponse->getResult());
```

#### Respuesta (array)

```json
{
    "RUT": "219000090011",
    "Denominacion": "DGI RUC PRUEBA CEDE Y DIRECCION GENERAL IMPOSITIVA",
    "NombreFantasia": "PRUEBA NUEVOS SERVICIOS  -",
    "TipoEntidad": "27",
    "DescripcionTipoEntidad": "SOCIEDAD CIVIL",
    "EstadoActividad": "AA",
    "FechaInicioActivdad": "2006-09-27",
    "WS_DomFiscalLocPrincipal": {
        "WS_PersonaActEmpresarial.WS_DomFiscalLocPrincipalItem": {
            "Local_Sec_Nro": "8",
            "Local_Nom_Fnt": [],
            "TipoLocal_Id": "1",
            "TipoLocal_Dsc": "PRINCIPAL",
            "Local_Fec_Ini": "2020-12-10",
            "Local_Fec_Canc": "0000-00-00",
            "TipoDom_Id": "2",
            "TipoDom_Des": "FISCAL",
            "CalOcup_id": "2",
            "Calocup_Des": "ARRENDATARIO",
            "TerCod_Id": "0",
            "Tercod_Des": [],
            "Calle_id": "14",
            "Calle_Nom": "12 METROS",
            "Dom_Pta_Nro": "6855",
            "Dom_Bis_Flg": [],
            "Dom_Ap_Nro": [],
            "Loc_Id": "903020",
            "Loc_Nom": "CASUPA",
            "Dpto_Id": "8",
            "Dpto_Nom": "LAVALLEJA",
            "Dom_Pst_Cod": "0",
            "Dom_Coment": [],
            "Dom_Err_Cod": "N",
            "Contactos": {
                "WS_Domicilio.WS_DomicilioItem.Contacto": [
                    {
                        "TipoCtt_Id": "1",
                        "TipoCtt_Des": "CORREO ELECTRONICO",
                        "DomCtt_Val": "PLANPROD@DGI.GUB.UY"
                    },
                    {
                        "TipoCtt_Id": "5",
                        "TipoCtt_Des": "TELEFONO FIJO",
                        "DomCtt_Val": "25062222"
                    }
                ]
            },
            "Complementos": {
                "WS_Domicilio.WS_DomicilioItem.Complemento": {
                    "Cmpl_Id": "21",
                    "Cmpl_Dsc": "SHOPPING",
                    "DomCmpl_Vlr": "ROJITO"
                }
            }
        }
    },
    "WS_PersonaActividades": {
        "WS_PersonaActEmpresarial.WS_PersonaActividadesItem": [
            {
                "GiroCod": "47115",
                "GiroNom": "COMERCIO AL POR MENOR REALIZADO POR LOS FREE SHOPS",
                "GiroFec_Ini": "2020-12-10"
            },
            {
                "GiroCod": "84110",
                "GiroNom": "ACTIVIDADES DE LA ADMINISTRACION PUBLICA EN GENERAL",
                "GiroFec_Ini": "2020-12-10"
            }
        ]
    }
}
```

### Facturación

> Actualmente en desarrollo, no es recomendable su uso en entornos de producción.

La librería permite la creación de eTck, eFact, eFact_Exp, eRem, eRem_Exp, eResg y eBoleta. 

Los comprobantes  generan un XML, el que se envía al WS de URUWARE, quien se encarga de validarlo y enviarlo a DGI.

Dicho XML consta de 3 partes principales; Encabezado, Detalle y CAEData, los que a su vez se ramifican en otras secciones. 

#### Encabezado

##### IdDoc

`\PlanetaDelEste\Ucfe\Cfe\Encabezado\IdDoc`

| Orden | Nombre      | Tipo     | Descripción                                                                      | Ejemplo |
| -----:| ----------- | -------- | -------------------------------------------------------------------------------- | ------- |
| 1     | TipoCFE     | `int`    | 101 \| 102 \| 103 \| 131 \| 132 \| 133 \| 201 \| 202 \| 203 \| 231 \| 232 \| 233 |         |
| 2     | Serie       | `string` |                                                                                  |         |
|       | ClauVenta   | `string` |                                                                                  |         |
|       | CodPaisProp | `string` |                                                                                  |         |
|       | DocProp     | `string` |                                                                                  |         |
|       | DocPropExt  | `string` |                                                                                  |         |

##### Emisor

##### Receptor

##### Totales

#### eTck

```php
$obUcfe = new ETck();

$obIdDoc = $obUcfe->idDoc();
$obIdDoc->FmaPago = 1; // 1 Contado. 2 Crédito
$obIdDoc->FchEmis = '2021-12-20'; // Fecha formato 'YYYY-MM-DD' (string|DateTime)

```


