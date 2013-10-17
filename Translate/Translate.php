<?php

require_once('../Singleton/Singleton.php');

class FS_Translate extends FS_Singleton
{
    /**
     * Get instance
     * @return FS_Translate
     */
    public static function GetInstance()
    {
        return parent::GetInstance();
    }
    
    /**
     * Translate key
     * @param string $key
     * @param string $lang  Set language to translate. If NULL use current language.
     * @return string
     */
    public function Translate($pKey, $pLang = NULL)
    {
        return $pKey;
    }
    
/*
Charger fichier XML builtin en fonction des messages d'erreurs ou autre.

Charger toutes les langues possibles, avec fallback 
FR_CA => FR_FR

Si fichier fallback n'existe pas, charger la première langue par ordre de priorité défini au préalable (ou ordre de chargement des fichiers de langue ?)

si pas de paramtre de langue, charge langue par défaut, sinon charge trad avec langue demandée
*/
    
}

?>
