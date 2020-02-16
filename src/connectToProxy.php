<?php
    require_once 'getRawContent.php';

    function connect($site) {
        /*if one of proxy will be broken, i can connect to another
            if connection established loop kills
        */
        $proxies = array(
                        array('192.200.200.113', '3128'),
                        array('138.197.222.35', '8080'),
                        array('165.227.215.62', '8080'),
                        array('165.227.215.71', '8080'),
                        array('173.192.128.238', '8123'),
                        array('162.243.108.129', '3128')
                    );
        while (true) {
            foreach ($proxies as $proxy) {
                  if (getRawContent($site, $proxy[0], $proxy[1])) {
                      exit;
                  }
            };
        }
    }
?>

