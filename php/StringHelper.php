<?php

echo StringHelper::substituteCharacter($a);

class StringHelper
{
    /**
     * @param $frase &egrave; la stringa nella quale vuole sostituire i caratteri.
     * @return string|string[]
     */
    public static function substituteCharacter($frase){
        $daTrovare = array("à", "ò", "è", "ì", "ù", "é");
        $daSostituire = array("&agrave;", "&ograve;", "&egrave;", "&igrave;", "&ugrave;", "&eacute;");
        for ($i = 0; $i < count($daTrovare); $i=$i+1) {
            $frase=str_replace($daTrovare[$i], $daSostituire[$i], $frase);
        }
        return $frase;
    }

}