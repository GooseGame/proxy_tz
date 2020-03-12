<?php


namespace Validation;


class DataValidation
{
    public function checkConfigValidation(array $config) {
        $requiredConfigVariables = array('host', 'username', 'dbName', 'baseUrl', 'stores_handler', 'ip', 'port', 'agent');
        foreach ($requiredConfigVariables as $configVariable) {
            if (!isset($config[$configVariable])) {
                throw new \UnexpectedValueException("Variable " . $configVariable . " is not set in app.ini" . PHP_EOL);
            }
        }
    }

    public function checkInputSiteValidation(string $site) {
        $pattern = "([[:alnum:]-]+\.com\/)";
        if (!preg_match($pattern, $site)) {
            throw new \Exception("Site is not valid");
        }
    }
}