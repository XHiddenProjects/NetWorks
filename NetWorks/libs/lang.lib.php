<?php
    namespace networks\libs;
    require_once(dirname(__DIR__).'/init.php');
    class Lang{
        protected Array $langObj;
        /**
         * Recieves language folder based on language and country
         *
         * @param string $lang Language to use, **en**
         * @param string $country Country to the language, **us**
         */
        public function __construct(String $lang='en', String $country='us') {
            $this->langObj = json_decode(file_get_contents(dirname(__DIR__).'/languages/'.strtolower($lang.'-'.$country).'.json'),true);
            foreach(array_diff(scandir(dirname(__DIR__).'/plugins'),['.','..']) as $plugin){
                if(file_exists(dirname(__DIR__).'/plugins/'.$plugin.'/lang/'.strtolower($lang.'-'.$country).'.json')){
                    $this->langObj = array_merge($this->langObj, json_decode(file_get_contents(dirname(__DIR__).'/plugins/'.$plugin.'/lang/'.strtolower($lang.'-'.$country).'.json'),true));
                }
            }
        }
        /**
         * Recieve the value of the language.
         *
         * @param string|array ...$lookup Subject to look for in array
         * @return mixed Returns the value of the type, otherwise False.
         */
        public function get(string|array ...$lookup) : mixed{
            if(is_array($lookup[0])) $lookup = array_merge(...$lookup);
            $lastLook = null;
            foreach($lookup as $look){
                if(isset($this->langObj[$look])||isset($lastLook[$look])) $lastLook = !$lastLook ? $this->langObj[$look] : $lastLook[$look];
                else return false;
            }
            return $lastLook;
        }
    }
?>