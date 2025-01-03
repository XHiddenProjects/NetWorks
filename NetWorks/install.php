<?php

use networks\libs\Templates;
use networks\libs\Dictionary;

require_once 'init.php';

if(!file_exists(NW_SQL_CREDENTIALS)){
    echo (new Templates(tname: 'install'))->load(dict: (new Dictionary())->merge(NW_DICTIONARY_CONFIG,
        NW_DICTIONARY_LANG,
        NW_DICTIONARY_DEFAULT,
        NW_DICTIONARY_META,
        NW_DICTIONARY_HOOKS
    ));
}else
    echo '<script>window.history.back();</script>';
?>