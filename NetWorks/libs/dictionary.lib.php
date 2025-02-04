<?php
namespace networks\libs;
use networks\libs\Users;
use networks\libs\Lang;
use networks\libs\Plugins;
use networks\libs\Web;
use networks\libs\HTMLForm;
use networks\libs\Templates;
use SSQL;

require_once(dirname(__DIR__).'/init.php');
(!defined('NW_DICTIONARY_USER') ? define('NW_DICTIONARY_DEFAULT',array(
    '%USER_LANGUAGE%'=>function(): string{return substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);},
    '%USER_DATETIME%'=>function(): string{return date('Y-m-d H:i:s');},
    '%USER_DATE%'=>function(): string{return date('Y-m-d');},
    '%USER_TIME%'=>function(): string{return date('H:i:s');},
    '%USER_IP%'=>function(): mixed{
        $ipData = (new Users())->IP();
        return is_object($ipData) ? $ipData->ip : $ipData['ip'];
    },
    '%USER_IP_VISIBILITY%'=>function(): mixed{
        $ipData = (new Users())->IP();
        return is_object($ipData) ? $ipData->visibility : $ipData['visibility'];
    },
    '%USER_IS_ONLINE%((.|\n)*?)%END%'=>function($e){if(isset($_COOKIE['user'])) return $e[1];},
    '%USER_IS_OFFLINE%((.|\n)*?)%END%'=>function($e){if(!isset($_COOKIE['user'])) return $e[1];},
    '%USERNAME%'=>function(): mixed{return $_COOKIE['user'];}
)) : '');
(!defined('NW_DICTIONARY_META') ? define('NW_DICTIONARY_META',array(
    '%META_CHARSET=(.+?)%'=>function($e): string{return '<meta charset="'.$e[1].'"/>';},
    '%META_DESCRIPTION=(.+)%'=>function($e): string{return '<meta name="description" content="'.$e[1].'"/>';},
    '%META_VIEWPORT%'=>function(): string{return '<meta name="viewport" content="width=device-width, initial-scale=1.0"/>';},
    '%META_AUTHOR=(.+)%'=>function($e): string{return '<meta name="author" content="'.$e[1].'"/>';},
    '%META_TWITTER_CARD=(.+)%'=>function($e): string{return '<meta name="twitter:card" content="'.$e[1].'"/>';},
    '%META_TWITTER_TITLE=(.+)%'=>function($e): string{return '<meta name="twitter:title" content="'.$e[1].'"/>';},
    '%META_TWITTER_DESCRIPTION=(.+)%'=>function($e): string{return '<meta name="twitter:description" content="'.$e[1].'"/>';},
    '%META_TWITTER_IMAGE=(https?:\/\/([\da-z\.-]+\.[a-z\.]{2,6}|[\d\.]+)([\/:?=&#]{1}[\da-z\.-]+)*[\/\?]?)%'=>function($e){return '<meta name="twitter:image" content="'.$e[1].'"/>';},
    '%META_OG_TITLE=(.+)%'=>function($e): string{return '<meta property="og:title" content="'.$e[1].'"/>';},
    '%META_OG_DESCRIPTION=(.+)%'=>function($e): string{return '<meta property="og:description" content="'.$e[1].'"/>';},
    '%META_OG_IMAGE=(https?:\/\/([\da-z\.-]+\.[a-z\.]{2,6}|[\d\.]+)([\/:?=&#]{1}[\da-z\.-]+)*[\/\?]?)%'=>function($e){return '<meta property="og:image" content="'.$e[1].'"/>';},
    '%META_OG_URL=(https?:\/\/([\da-z\.-]+\.[a-z\.]{2,6}|[\d\.]+)([\/:?=&#]{1}[\da-z\.-]+)*[\/\?]?)%'=>function($e){return '<meta property="og:url" content="'.$e[1].'"/>';},
    '%META_LINKEDIN_NAME=(.+)%'=>function($e): string{return '<meta itemprop="name" content="'.$e[1].'"/>';},
    '%META_LINKEDIN_DESCRIPTION=(.+)%'=>function($e): string{return '<meta itemprop="description" content="'.$e[1].'"/>';},
    '%META_LINKEDIN_IMAGE=(https?:\/\/([\da-z\.-]+\.[a-z\.]{2,6}|[\d\.]+)([\/:?=&#]{1}[\da-z\.-]+)*[\/\?]?)%'=>function($e){return '<meta itemprop="image" content="'.$e[1].'"/>';},
    '%META_LINKEDIN_URL=(https?:\/\/([\da-z\.-]+\.[a-z\.]{2,6}|[\d\.]+)([\/:?=&#]{1}[\da-z\.-]+)*[\/\?]?)%'=>function($e){return '<meta itemprop="url" content="'.$e[1].'"/>';},
    '%META=(.+)%'=>function($e){return '<meta '.$e[1].'/>';}
)) : '');
(!defined('NW_DICTIONARY_HOOKS') ? define('NW_DICTIONARY_HOOKS',array(
    '%CONFIG=(.*?)%'=>function($e): string{
        $footerjs='';
        foreach((new Plugins())->list() as $plugin){
            $footerjs.=(new Plugins($plugin))->init()->setPlacement('config')->setArgs(...explode(';',$e[1]))->exec();
        }
        return $footerjs;
    },
    '%HEAD%'=>function(): string{
        $heading='';
        foreach((new Plugins())->list() as $plugin){
            $heading.=(new Plugins($plugin))->init()->setPlacement('head')->setArgs()->exec();
        }
        return $heading;
    },
    '%BEFORELOAD%'=>function(): string{
        $bLoad='';
        foreach((new Plugins())->list() as $plugin){
            $bLoad.=(new Plugins($plugin))->init()->setPlacement('beforeLoad')->setArgs()->exec();
        }
        return $bLoad;
    },
    '%AFTERLOAD%'=>function(): string{
        $aLoad='';
        foreach((new Plugins())->list() as $plugin){
            $aLoad.=(new Plugins($plugin))->init()->setPlacement('afterLoad')->setArgs()->exec();
        }
        return $aLoad;
    },
    '%FOOTER%'=>function(): string{
        $footer='';
        foreach((new Plugins())->list() as $plugin){
            $footer.=(new Plugins($plugin))->init()->setPlacement('footer')->setArgs()->exec();
        }
        return $footer;
    },
    '%FOOTERJS%'=>function(): string{
        $footerjs='';
        foreach((new Plugins())->list() as $plugin){
            $footerjs.=(new Plugins($plugin))->init()->setPlacement('footerjs')->setArgs()->exec();
        }
        return $footerjs;
    },
    '%BEFOREMAIN%'=>function(): string{
        $db='';
        foreach((new Plugins())->list() as $plugin){
            $db.=(new Plugins($plugin))->init()->setPlacement('beforeMain')->setArgs()->exec();
        }
        return $db;
    },
    '%AFTERMAIN%'=>function(): string{
        $db='';
        foreach((new Plugins())->list() as $plugin){
            $db.=(new Plugins($plugin))->init()->setPlacement('afterMain')->setArgs()->exec();
        }
        return $db;
    }
)) : '');
(!defined('NW_DICTIONARY_LANG') ? define('NW_DICTIONARY_LANG', [
    '%LANG=(.+?)%'=>function($e): mixed{$s = explode(',',$e[1]); return (new Lang())->get($s);},
    '%PATH=(.+?)%'=>function($e): string|null{
        $constantValue = constant($e[1]);
        return is_string($constantValue) ? (new Web($constantValue))->toAccessible() : null;
    },
    '%PATH_RAW=(.+?)%'=>function($e): string|null{
        $constantValue = constant($e[1]);
        return is_string($constantValue) ? $constantValue : null;
    }
]) : '');
(!defined('NW_DICTIONARY_PAGES') ? define('NW_DICTIONARY_PAGES', [
    '%LISTPAGES%'=>function(){
        $out='';
        if(file_exists(NW_SQL_CREDENTIALS)){
            $cred = json_decode(file_get_contents(NW_SQL_CREDENTIALS),true);
            $sql = new SSQL();
            if($sql->setCredential($cred['server'],$cred['user'],$cred['psw'])){
                $db = $sql->selectDB($cred['db']);
                foreach($db->selectData('pages',['*']) as $page){
                    if(is_file(NW_ROOT.NW_DS.$page['pageName'].'.php')){
                        $out.='<li class="nav-item d-flex align-items-center px-2">
                            <i class="material-symbols-rounded">'.$page['pageIcon'].'</i>
                            <a class="nav-link '.(isset((new Web())->getPath()[1]) ? (strtolower($page['pageName'])===strtolower((new Web())->getPath()[count((new Web())->getPath()) - 1]) ? 'active' : '') : (strtolower($page['pageName'])==='home.php' ? 'active' : '')).'" aria-current="page" href="'.($page['pageName']==='home' ? './' : $page['pageName']).'">'.ucfirst($page['pageName']).'</a>
                        </li>';
                    }
                }
            }
        }
        return $out;
    },
    '%DOCERROR%'=>function(): bool|int{
        return http_response_code();
    },
    '%FILE_GET=(.+?)%'=>function($e): string{
        preg_match('/(.*?)(?=[^\/]*$)/',$e[1],$dir);
        $file = trim(str_replace($dir[1],'',$e[1]));
        $temp = new Templates(preg_replace('/\..+$/','',$file));
        $temp->chDir($dir[1]);
        return file_exists($e[1]) ? $temp->load((new Dictionary())->merge(NW_DICTIONARY_LANG,NW_DICTIONARY_DEFAULT,NW_DICTIONARY_CONDITIONS,NW_DICTIONARY_CONFIG,NW_DICTIONARY_HOOKS,NW_DICTIONARY_FORMS)) : '';
    },
    '%RENDER_PLUGIN_LIST%'=>function(): string{
        $plugins = (new Plugins())->list();
        $grid=[];
        for($r=0;$r<ceil(count($plugins)/5);$r++){
            for($c=0;$c<5;$c++){
            $index = $r * 5 + $c;
            if(isset($plugins[$index]))
                $grid[$r][$c] = ['name'=>$plugins[$index], 'icon'=>(new Web(NW_PLUGINS.NW_DS.$plugins[$index].NW_DS.'icon.webp'))->toAccessible()];
            }
        }
        $grid = array_merge(...$grid);
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $pluginsPerPage = 5;
        $totalPlugins = count($grid);
        $totalPages = ceil($totalPlugins / $pluginsPerPage);
        $startIndex = ($page - 1) * $pluginsPerPage;
        $endIndex = min($startIndex + $pluginsPerPage, $totalPlugins);
        $out='';
        $out .= '<div class="row mt-2 plugin-container">';
        for ($i = $startIndex; $i < $endIndex; $i++) {
            $plugin = $grid[$i];
            $pLang = json_decode(file_get_contents(NW_PLUGINS.NW_DS.$plugin['name'].NW_DS.'lang'.NW_DS.(new Lang())->current().'.json'),true);
            $out .= '<div class="col">
            <div class="card h-100" style="width: 18rem;">';
            $out .= '<img class="card-img-top" src="' . $plugin['icon'] . '" alt="' . $plugin['name'] . '">';
            $out .= '<div class="card-body">';
            $out .= '<h3 class="text-center text-capitalize">' . $pLang['name'].' ('.(new Utils())->sizeConversion((new Utils())->folderSize(NW_PLUGINS.NW_DS.$plugin['name']),1). ')</h3>';
            $out .= '<p class="text-muted text-center">'.$pLang['description'].'</p>';
            $out .= '<p class="text-muted text-center"><span class="fw-bold">v'.$pLang['version'].'</span> &#8211; <a data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="'.(new Lang())->get('Dashboard','plugins','lastUpdated').': '.(new Utils())->date_format($pLang['lastUpdated']).'" href="'.$pLang['website'].'">'.$pLang['author'].'</a></p>';
            $out.='<div class="d-inline-flex justify-content-between w-100 align-items-center">
            <div class="form-check form-switch">
                <input class="form-check-input pluginToggle" plugin-name="'.$plugin['name'].'" type="checkbox" '.((new Plugins((string)$plugin['name']))->isActive() ? 'checked ' : '').((new Plugins((string)$plugin['name']))->isDisabled() ? 'disabled ' : '').'role="switch" id="pluginToggle_'.$plugin['name'].'">
                <label class="form-check-label" for="pluginToggle_'.$plugin['name'].'"></label>
            </div>
            '.(method_exists(new $plugin['name'],'config')&&((new Plugins((string)$plugin['name']))->isActive()) ? '<a href="'.(new Web( NW_ROOT))->toAccessible().'/dashboard/config?plugin='.$plugin['name'].'"><button class="btn btn-secondary"><span class="material-symbols-rounded">settings</span>'.(new Lang())->get('Dashboard','config', 'abbr').'</button></a>' : '').'
            '.($plugin['name']==='core' ? '<button class="btn btn-danger" disabled><span class="material-symbols-outlined">delete</span></button>' : '<button data-bs-plugin="'.$plugin['name'].'" data-bs-toggle="modal" data-bs-target="#pluginDeletePrompt" type="button" class="btn btn-danger plugin-remover" plugin-name="'.$plugin['name'].'"><span class="material-symbols-outlined">delete</span></button>').'
            </div>';
            $out .= '</div>';
            $out .= '</div>
            </div>';
        }
        $out.='</div>';
        $out .= '<nav class="navigation mt-5">
        <ul class="pagination justify-content-center">
        <li class="page-item'.($page-1<=0 ? ' disabled' : '').'">
            <a class="page-link" '.($page-1<=0 ? '' : 'href="'.(new Web(NW_ROOT))->toAccessible().'/dashboard/plugins?page='.($page-1).'"').'>'.(new Lang())->get('Pagination','prev').'</a>
        </li>';
        if ($totalPages <= 5) {
            for ($i = 1; $i <= $totalPages; $i++) {
                $out .= '<li class="page-item"><a href="'.(new Web(NW_ROOT))->toAccessible().'/dashboard/plugins?page=' . $i . '"' . ($i === $page ? ' class="page-link active"' : 'class="page-link"') . '>' . $i . '</a></li>';
            }
        } else {
            $out .= '<li class="page-item"><a href="'.(new Web(NW_ROOT))->toAccessible().'/dashboard/plugins?page=1" class="page-link'.($page === 1 ? ' active' : '').'">1</a></li>';
            if ($page > 3) {
                $out .= '<li class="page-item"><span class="page-link">...</span></li>';
            }
            for ($i = max(2, $page - 1); $i <= min($totalPages - 1, $page + 1); $i++) {
                $out .= '<li class="page-item"><a href="'.(new Web(NW_ROOT))->toAccessible().'/dashboard/plugins?page=' . $i . '"' . ($i === $page ? ' class="page-link active"' : 'class="page-link"') . '>' . $i . '</a></li>';
            }
            if ($page < $totalPages - 2) {
                $out .= '<li class="page-item"><span class="page-link">...</span></li>';
            }
            $out .= '<li class="page-item"><a href="'.(new Web(NW_ROOT))->toAccessible().'/dashboard/plugins?page=' . $totalPages . '"' . ($page === $totalPages ? ' class="page-link active"' : 'class="page-link"') . '>' . $totalPages . '</a></li>';
        }
        $out .= '<li class="page-item'.($page+1>$totalPages ? ' disabled' : '').'">
        <a class="page-link"'.($page+1>$totalPages ? '' : ' href="?page='.($page+1).'"').'>'.(new Lang())->get('Pagination','next').'</a>
    </li>
    </ul></nav>';
        return $out;
    }
]) : '');
(!defined('NW_DICTIONARY_CONFIG') ? define('NW_DICTIONARY_CONFIG',[
    '%WEBTITLE%'=>function(){
        if(file_exists(NW_SQL_CREDENTIALS)){
            $cred = json_decode(file_get_contents(NW_SQL_CREDENTIALS),true);
            $sql = new SSQL();
            if($sql->setCredential($cred['server'],$cred['user'],$cred['psw'])){
                $db = $sql->selectDB($cred['db']);
                $data = $db->selectData('config',['title'])[0]['title'];
                $sql->close();
                return $data;
            }
        }else{
            return 'NetWorks';
        }
    },
    '%CONFIGLANG%'=>function(){
        if(file_exists(NW_SQL_CREDENTIALS)){
            $cred = json_decode(file_get_contents(NW_SQL_CREDENTIALS),true);
            $sql = new SSQL();
            if($sql->setCredential($cred['server'],$cred['user'],$cred['psw'])){
                $db = $sql->selectDB($cred['db']);
                $data = $db->selectData('config',['lang'])[0]['lang'];
                $sql->close();
                return explode('-',$data)[0];
            }
        }else return 'en';
    },
    '%DEBUG%'=>function(){
        if(file_exists(NW_SQL_CREDENTIALS)){
            $cred = json_decode(file_get_contents(NW_SQL_CREDENTIALS),true);
            $sql = new SSQL();
            if($sql->setCredential($cred['server'],$cred['user'],$cred['psw'])){
                $db = $sql->selectDB($cred['db']);
                $data = $db->selectData('config',['debug'])[0]['debug'];
                $sql->close();
                return $data;
            }
        }else return false;
    },
    '%DATEFORMAT%'=>function(){
        $cred = json_decode(file_get_contents(NW_SQL_CREDENTIALS),true);
        $sql = new SSQL();
        if($sql->setCredential($cred['server'],$cred['user'],$cred['psw'])){
            $db = $sql->selectDB($cred['db']);
            $data = $db->selectData('config',['dFormat'])[0]['dFormat'];
            $sql->close();
            return $data;
        }
    },
    '%THEME%'=>function(){
        $cred = json_decode(file_get_contents(NW_SQL_CREDENTIALS),true);
        $sql = new SSQL();
        if($sql->setCredential($cred['server'],$cred['user'],$cred['psw'])){
            $db = $sql->selectDB($cred['db']);
            $data = $db->selectData('config',['theme'])[0]['theme'];
            $sql->close();
            return $data;
        }
    },
    '%EDITOR%'=>function(){
        $cred = json_decode(file_get_contents(NW_SQL_CREDENTIALS),true);
        $sql = new SSQL();
        if($sql->setCredential($cred['server'],$cred['user'],$cred['psw'])){
            $db = $sql->selectDB($cred['db']);
            $data = $db->selectData('config',['editor'])[0]['editor'];
            $sql->close();
            return $data;
        }
    },
    '%MEMORY(=true)?%'=>function($e): string|int|float{
        return isset($e[1]) ? (new Utils())->sizeConversion(size: (new Utils())->getMemory()) : (new Utils())->getMemory();
    },
    '%MEMORY_AVAILABLE(=true)?%'=>function($e): string|int|float{
        return isset($e[1]) ? (new Utils())->sizeConversion(size: (new Utils())->getMemory('available')) : (new Utils())->getMemory('available');
    },
    '%MEMORY_PEAK(=true)?%'=>function($e): string|int|float{
        return isset($e[1]) ? (new Utils())->sizeConversion(size: (new Utils())->getMemory('peak')) : (new Utils())->getMemory('peak');
    },
    '%MEMORY_TOTAL(=true)?%'=>function($e): string|int|float{
        $usedMemory = (new Utils())->getMemory();
        $totalMemory = (new Utils())->getMemory('available');
        $res = ($usedMemory / $totalMemory) * 100;
        return isset($e[1]) ? (new Utils())->sizeConversion(size: $res) : $res;
    },
    '%STORAGE(=true)?%'=>function($e): string|int|float{
        return isset($e[1]) ? (new Utils())->sizeConversion(size: (new Utils())->getStorage()) : (new Utils())->getStorage();
    },
    '%STORAGE_FREE(=true)?%'=>function($e): string|int|float{
        return isset($e[1]) ? (new Utils())->sizeConversion(size: (new Utils())->getStorage('free')) : (new Utils())->getStorage('free');
    },
    '%STORAGE_AVAILABLE(=true)?%'=>function($e): string|int|float{
        return isset($e[1]) ? (new Utils())->sizeConversion(size: (new Utils())->getStorage('available')) : (new Utils())->getStorage('available');
    },
    '%STORAGE_TOTAL(=true)?%'=>function($e): string|int|float{
        $usedStorage = (new Utils())->getStorage();
        $totalStorage = (new Utils())->getStorage('available');
        $res = ($usedStorage / $totalStorage) * 100;
        return isset($e[1]) ? (new Utils())->sizeConversion(size: $res) : $res;
    }
]) : '');
(!defined('NW_DICTIONARY_CONDITIONS') ? define('NW_DICTIONARY_CONDITIONS', [
    '%FILE_EXISTS=(.+?)%((.|\n)*?)%END%(%ELSE%((.|\n)*?)%END%)?'=>function($e){
        if(file_exists($e[1])) return $e[2];
        elseif(isset($e[5])) return $e[5];
    },
    '%IS_ADMIN%((.|\n)*?)%END%'=>function($e){
        if(isset($_COOKIE['user'])){
            $cred = json_decode(file_get_contents(NW_SQL_CREDENTIALS),true);
            $sql = new SSQL();
            if($sql->setCredential($cred['server'],$cred['user'],$cred['psw'])){
                $db = $sql->selectDB($cred['db']);
                $data = $db->selectData('users',['*'],'WHERE username="'.$_COOKIE['user'].'"');
                $sql->close();
                if(strtolower($data[0]['permission'])==='admin') return $e[1];
            }
        }
    },
    '%IS_MEMBER%((.|\n)*?)%END%'=>function($e){
        if(isset($_COOKIE['user'])){
            $cred = json_decode(file_get_contents(NW_SQL_CREDENTIALS),true);
            $sql = new SSQL();
            if($sql->setCredential($cred['server'],$cred['user'],$cred['psw'])){
                $db = $sql->selectDB($cred['db']);
                $data = $db->selectData('users',['*'],'WHERE username="'.$_COOKIE['user'].'"');
                $sql->close();
                if(strtolower($data[0]['permission'])==='member') return $e[1];
            }
        }
    },
    '%IS_GUEST%((.|\n)*?)%END%'=>function($e){
        if(isset($_COOKIE['user'])){
            $cred = json_decode(file_get_contents(NW_SQL_CREDENTIALS),true);
            $sql = new SSQL();
            if($sql->setCredential($cred['server'],$cred['user'],$cred['psw'])){
                $db = $sql->selectDB($cred['db']);
                $data = $db->selectData('users',['*'],'WHERE username="'.$_COOKIE['user'].'"');
                $sql->close();
                if(strtolower($data[0]['permission'])==='guest') return $e[1];
            }
        }
    },
    //Make this last
    '%URL_PATH=(.+?)%((.|\n)*?)%END%'=>function($e){
        $url = (new Web())->getPath();
        $e = array_values(array_filter($e,function($i){return trim($i)!=='';}));
        if($url[0]) unset($url[0]);
        return match (strcmp(strtolower($e[1]), strtolower(implode('/', array_values($url))))) {
            0 => $e[2],
            default => false,
        };
    }
]) : '');
(!defined('NW_DICTIONARY_FORMS') ? define('NW_DICTIONARY_FORMS', [
    '%FORM(=(.+?))?%((.|\n)*?)%ENDFORM%'=>function($e){
        $e = array_values(array_filter($e,function($e){return trim($e)!=='';}));
        $form = (new HTMLForm());
        foreach(preg_split('/\r\n|\n/',trim(preg_replace('/\t/','',(isset($e[3]) ? $e[3] : $e[1])))) as $elem){
            foreach(NW_DICTIONARY_FORMS_ELEMENTS as $patt=>$call){
                if(preg_match('/'.$patt.'/',$elem)){
                    $elem = trim(preg_replace_callback('/'.$patt.'/',$call,$elem));
                    $args = explode(';',$elem);
                    if(count($args)>1){
                        $setMethod = $args[0];
                        unset($args[0]);
                        $args = (new Utils())->extractParam($args);
                        $args = array_filter(array: $args,callback: function($value, $key): bool{
                            return $value!=='';
                        },mode: ARRAY_FILTER_USE_BOTH);

                        $form->{$setMethod}(...$args);
                    }else
                        $form->{$args[0]}();
                    break;
                }
            }
        }
        if(isset($e[3])) $formArgs = explode(';',$e[2]);
        if(isset($formArgs)){
            $method='post';
            $action='';
            $enctype='';
            $class='';
            foreach($formArgs as $fa){
                $fa = trim(preg_replace('/[\r\n]+/','',$fa));
                preg_match('/action:(.+);?/',$fa,$matches);
                if(preg_match('/method:(.+);?/',$fa,$matches)) $method = $matches[1];
                if(preg_match('/action:(.+);?/',$fa,$matches)) $action = $matches[1];
                if(preg_match('/enctype:(.+);?/',$fa,$matches)) $enctype = $matches[1];
                if(preg_match('/class:(.+);?/',$fa,$matches)) $class = $matches[1];
            }
            return $form->finalize($method,$action,$enctype,$class);
        }else
            return $form->finalize();
    }
]) : '');
(!defined('NW_DICTIONARY_FORMS_ELEMENTS') ? define('NW_DICTIONARY_FORMS_ELEMENTS', [
    '%ROW(=class:(.+?))?%'=>function($e){
        $e = array_values(array_filter($e,function($e){
            return $e!==''&&preg_match('/.*?:.*?/',$e);
        }));
        unset($e[0]);
        $e = array_values($e);
        $e = array_map(function($e){
            return preg_replace('/^=/','',$e);
        },$e);
        $out='row;';
        foreach($e as $txt){
            
            $s = explode(':',$txt);
            $out .= "{$s[0]}:{$s[1]};";
        }
        $out=preg_replace('/;$/','',$out);
        return $out;
    },
    '%COL(=class:(.+?))?%'=>function($e){
        $e = array_values(array_filter($e,function($e){
            return $e!==''&&preg_match('/.*?:.*?/',$e);
        }));
        unset($e[0]);
        $e = array_values($e);
        $e = array_map(function($e){
            return preg_replace('/^=/','',$e);
        },$e);
        $out='col;';
        foreach($e as $txt){
            
            $s = explode(':',$txt);
            $out .= "{$s[0]}:{$s[1]};";
        }
        $out=preg_replace('/;$/','',$out);
        return $out;
    },
    '%TITLE=(value:(.+?))%'=>function($e){
        $out = 'title;';
        $e = array_values(array_filter($e,function($e){
            return $e!==''&&preg_match('/.*?:.*?/',$e);
        }));
        unset($e[0]);
        $e = array_values($e);
        $e = array_map(function($e){
            return preg_replace('/;$/','',$e);
        },$e);
        
        foreach($e as $txt){
            $s = explode(':',$txt);
            $out .= "{$s[0]}:{$s[1]};";
        }
        $out=preg_replace('/;$/','',$out);
        return $out;
    },
    '%text=(name:(.+?));?(value:(.*?))?(class:(.*?))?(placeholder:(.*?))?(desc:(.*?))?(required:(true|false))?%'=>function($e){
        $e = array_values(array_filter($e,function($e){
            return $e!==''&&preg_match('/.*?:.*?/',$e);
        }));
        unset($e[0]);
        $e = array_values($e);
        $e = array_map(function($e){
            return preg_replace('/;$/','',$e);
        },$e);
        $out='text;';
        foreach($e as $txt){
            
            $s = explode(':',$txt);
            $out.=$s[0].':'.$s[1].';';
        }
        $out=preg_replace('/;$/','',$out);
        return $out;
    },
    '%password=(name:(.+?));?(value:(.*?))?(class:(.*?))?(placeholder:(.*?))?(desc:(.*?))?(required:(true|false))?(measure:(true|false))?%'=>function($e){
        $e = array_values(array_filter($e,function($e){
            return $e!==''&&preg_match('/.*?:.*?/',$e);
        }));
        unset($e[0]);
        $e = array_values($e);
        $e = array_map(function($e){
            return preg_replace('/;$/','',$e);
        },$e);
        $out='password;';
        foreach($e as $txt){
            
            $s = explode(':',$txt);
            $out.=$s[0].':'.$s[1].';';
        }
        $out=preg_replace('/;$/','',$out);
        return $out;
    },
    '%button=(name:(.+?));?(type:(.*?));?(class:(.*?));?(link:(.*?));?%'=>function($e){
        $e = array_values(array_filter($e,function($e){
            return $e!==''&&preg_match('/.*?:.*?/',$e);
        }));
        unset($e[0]);
        $e = array_values($e);
        $out='button;';
        foreach($e as $txt){
            $s = explode(':',$txt);
            $out.=$s[0].':'.$s[1].';';
        }
        $out=preg_replace('/;$/','',$out);
        return $out;
    },
    '%color=(name:(.+?));?(value:(.*?))?(class:(.*?))?(placeholder:(.*?))?(desc:(.*?))?(required:(true|false))?%'=>function($e){
        $e = array_values(array_filter($e,function($e){
            return $e!==''&&preg_match('/.*?:.*?/',$e);
        }));
        unset($e[0]);
        $e = array_values($e);
        $e = array_map(function($e){
            return preg_replace('/;$/','',$e);
        },$e);
        $out='color;';
        foreach($e as $txt){
            
            $s = explode(':',$txt);
            $out.=$s[0].':'.$s[1].';';
        }
        $out=preg_replace('/;$/','',$out);
        return $out;
    }
    ,
    '%recaptcha=(value:(.*?))%'=>function($e){
        $e = array_values(array_filter($e,function($e){
            return $e!==''&&preg_match('/.*?:.*?/',$e);
        }));
        unset($e[0]);
        $e = array_values($e);
        $out='reCAPTCHA;';
        foreach($e as $txt){
            
            $s = explode(':',$txt);
            $out.=$s[0].':'.$s[1].';';
        }
        $out=preg_replace('/;$/','',$out);
        return $out;
    }
]) : '');
/**
 * A variable dictionary for templates
 * @author XHiddenProjects <xhiddenprojects@gmail.com>
 * @license MIT
 * @version 1.0.0
 * @link https://github.com/XHiddenProjects
 */
class Dictionary{
    protected $ui = [];
    /**
     * Creates a variable dictionary
     */
    public function __construct() {
        # Nothing
    }
    /**
     * Adds an item to Dictionary
     *
     * @param string $search Search for a target query. Ex: **%USERNAME%**
     * @param callable $replace [Optional] - Replace the search with a value. Ex: **JohnDoe**
     * @return Dictionary
     */
    public function addItem(string $search, callable $replace) : Dictionary{
        $search = preg_replace('/^\/|\/$/','',$search);
        if(!in_array($search,$this->ui))
            array_push($this->ui,['/'.$search.'/'=>$replace]);
        return $this;
    }
    /**
     * Drop and item from the dictionary
     *
     * @param string $search Search query to drop. Ex: **%USERNAME%**
     * @return Dictionary
     */
    public function dropItem(string $search) : Dictionary{
        $search = preg_replace('/^\/|\/$/','',$search);
        if(in_array($search,$this->ui)) unset($this->ui["/$search/"]);
        return $this;
    }
    /**
     * Sanitizes the array
     *
     * @return array
     */
    private function sanitize():array{
        foreach ($this->ui as $key => $value) {
            // Check if the key matches the format %KEY%
            if (preg_match('/^%(.+)%$/', $key, $matches)) {
                // Extract the inner key
                $newKey = $matches[1]; // This is 'KEY' from %KEY%
                
                // Replace with the original key and keep the value
                // You can modify this part to decide what you want to replace the keys with
                $this->ui[$newKey] = $value;
                unset($this->ui[$key]); // Remove the old key
            }
        }
        return array_merge(...$this->ui);
    }
    /**
     * List everything in the dictionary
     *
     * @return array{Search: string}
     */
    public function listItem() : array{
        return $this->sanitize();
    }
    /**
     * Replace an item in the array
     *
     * @param string $search Search to look for. Ex: **%USERNAME%**
     * @param string $newSearch Search to replace with. Ex: **%NAME%**
     * @param callable|null $replace [Optional] - Replace the value, leave _null_ to set as default replace
     * @return Dictionary
     */
    public function replaceItem(string $search, string $newSearch, callable|null $replace=null) : Dictionary{
        $search = preg_replace('/^\/|\/$/','',$search);
        $newSearch = preg_replace('/^\/|\/$/','',$newSearch);
        if(in_array($search,$this->ui)){
            $this->ui['/'.$newSearch.'/'] = ($replace ?  $replace : $this->ui['/'.$search.'/']);
            unset($this->ui['/'.$search.'/']);
        }
        return $this;
    }
    /**
     * Merages multiple dictionaries
     *
     * @param array<string> ...$dict Dictionary to convert into constants
     * @return array Merged dictionaries
     */
    public function merge(...$dict) : array {
        return array_merge(...$dict);
    }
    /**
     * Undocumented function
     *
     * @param array<string> ...$dict Dictionary to convert into constants
     * @return void
     */
    public function toConst(...$dict):void{
        $dict = array_merge(...$dict);
        foreach($dict as $d=>$f){
            if(!defined($d)) define($d,$f);
        }
    }
}
?>