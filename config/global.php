<?php

return [
    'settings' => [
        'application'  => 'QUOTO! CRM per Hotel',
        'version'      => 'v.3.1.1 beta',
        'url'          => 'https://quotocrm3.it.dvl.to/',
        'name_admin'   => "Network Service s.r.l.",
        'mail_admin'   => 'no-reply@quotocrm.it',
        'author'       => 'Marcello Visigalli',
        'BASE_URL_IMG' => 'https://quotocrm3.it.dvl.to/',

        'FSOCKOPEN_PAYPAL' => 'ssl://www.paypal.com', // UFFICIALE: ssl://www.paypal.com; TEST: ssl://www.sandbox.paypal.com
        'URL_PAYPAL'       => 'https://ipnpb.paypal.com/cgi-bin/webscr', //UFFICIALE: https://www.paypal.com/cgi-bin/webscr; TEST: https://www.sandbox.paypal.com/cgi-bin/webscr
        'KEY_VIRTUALPAY'   => 'E5D49168DEAA74078B22524B02360B6B',
        'URL_NEXI'         => 'https://int-ecommerce.nexi.it/ecomm/ecomm/DispatcherServlet', //UFFICIALE: https://ecommerce.nexi.it/ecomm/ecomm/DispatcherServlet; TEST: https://int-ecommerce.nexi.it/ecomm/ecomm/DispatcherServlet

        'DATA_QUOTO_V2'         => '2019-01-29', // prima di questa data si caricano i moduli OLD in crea proposte, ecc.!!

        'DATA_SERVIZI_VISIBILI' => '2021-02-22', // data di pubblicazione del nuovo modulo, servizi aggiuntivi visibile per ogni porsota di ogni preventivo
    ],

];
