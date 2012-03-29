<?php

include_once 'SymfonyComponents/YAML/sfYaml.php';

/**
 * Enter description here...
 * @author: radzserg
 * @date: 05.10.11
 */
class CsbYaml
{

    /**
     * Load YAML
     * @static
     * @param $input file|yaml formatted text
     * @return array
     */
    public static function load($input)
    {
        $sfYaml = new sfYaml();
        return $sfYaml->load($input);
    }

    /**
     * Dump array into YAML formatted text
     * @static
     * @param $array
     * @return string
     */
    public static function dump($array)
    {
        $sfYaml = new sfYaml();
        return $sfYaml->dump($array);
    }

}
