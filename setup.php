<html>
<head>
	<style>
		.success{
			 color: #20A200;
		}
		.no-action{
			color: #7B7B7B;
		}
		.error{
			color: #F30000;
		}
	</style>
</head>
<body>
<?php

define('MODX_API_MODE', true);
// this can be used to disable caching in MODX absolutely 
$modx_cache_disabled= false;
//header("Content-type: text/plain");
// include custom core config and define core path 
include(dirname(__FILE__) . '/config.core.php');
if (!defined('MODX_CORE_PATH')) define('MODX_CORE_PATH', dirname(__FILE__) . '/core/');

// include the modX class 
if (!@include_once (MODX_CORE_PATH . "model/modx/modx.class.php")) {
    $errorMessage = 'Site temporarily unavailable';
    @include(MODX_CORE_PATH . 'error/unavailable.include.php');
    header('HTTP/1.1 503 Service Unavailable');
    echo "<html><title>Error 503: Site temporarily unavailable</title><body><h1>Error 503</h1><p>{$errorMessage}</p></body></html>";
    exit();
}

// Create an instance of the modX class 
$modx = new modX();
if (!is_object($modx) || !($modx instanceof modX)) {
    @ob_end_flush();
    $errorMessage = '<a href="setup/">MODX not installed. Install now?</a>';
    @include(MODX_CORE_PATH . 'error/unavailable.include.php');
    header('HTTP/1.1 503 Service Unavailable');
    echo "<html><title>Error 503: Site temporarily unavailable</title><body><h1>Error 503</h1><p>{$errorMessage}</p></body></html>";
    exit();
}

require_once dirname(__FILE__).'/config.core.php';
include_once MODX_CORE_PATH . 'model/modx/modx.class.php';

function core_htaccess(){
    // Создаем файл .htaccess в папке core для решения проблем с безопасностью
    
    echo "<h3>Безопасность</h3>";
    
    $filename = 'core/.htaccess';
    
    if (file_exists($filename)) {
        echo '<span class="no-action">Файл "'.$filename.'" уже существует.</span><br/><br/>';
        return false;
    }
    
    $path = str_replace($_SERVER['SCRIPT_NAME'], "/", $_SERVER['SCRIPT_FILENAME']);

    $text = 'IndexIgnore */*'.PHP_EOL.'<Files *.*>'.PHP_EOL.'Order Deny,Allow'.PHP_EOL.'Deny from all'.PHP_EOL.'</Files>';

    $fh = fopen ($filename, "w+");
    fwrite ($fh, $text);
    fclose ($fh);
    
    if (file_exists($filename)) {
        echo '<span class="success">Файл "'.$filename.'" успешно создан.</span><br/><br/>';
    } else {
        echo '<span class="error">Не удалось создать файл "'.$filename.'", попробуйте создать его вручную.</span><br/><br/>';
    }
}
function wg_widget(){
    //Создаем файл с виджетом студии
    
    echo "<h3>Виджет студии</h3>";
    
    $widgetname = 'manager/controllers/default/dashboard/wg-contact.php';
    
    if (file_exists($widgetname)) {
        echo '<span class="no-action">Файл "'.$widgetname.'" уже существует.</span><br/>';
        return false;
    }
    
    $widgettext = '<?php $page = file_get_contents("http://webgeeks.by/widget.html"); return $page; ?>';
    
    $fh = fopen ($widgetname, "w+");
    fwrite ($fh, $widgettext);
    fclose ($fh);
    
    if (file_exists($widgetname)) {
        echo '<span class="success">Файл "'.$widgetname.'" успешно создан.</span><br/>';
    } else {
        echo '<span class="error">Не удалось создать файл "'.$widgetname.'", попробуйте создать его вручную.</span><br/>';
    }
}

function wg_modx_widget($modx){
    $checker = $modx->getObject('modDashboardWidget',array('name'=>'Разработка и создание сайта - WebGeeks Studio'));
    if(!$checker){
        
        $widget = $modx->newObject('modDashboardWidget');
    
        $widget->set("name","Разработка и создание сайта - WebGeeks Studio");
        $widget->set("type","file");
        $widget->set("content","[[++manager_path]]controllers/default/dashboard/wg-contact.php");
        $widget->set("namespace	","core");
        $widget->set("lexicon","core:dashboards");
        $widget->set("size","full");
        
        $widget->save();
        
        if($widget->id){
        
            $place = $modx->newObject('modDashboardWidgetPlacement');
            $place->set("dashboard",1);
            $place->set("widget",$widget->id);
            $place->set("rank",0);
            
            $place->save();
            
            echo "<span class='success'>Виджет студии успешно создан.</span><br/><br/>";
        }else{
            echo "<span class='error'>Не удалось создать виджет, попробуйте создать его вручную.</span><br/><br/>";
        }
        
    }else{
        echo "<span class='no-action'>Виджет уже существует.</span><br/><br/>";
    }
}

function create_template($modx){
	
	echo "<h3>Шаблоны</h3>";
    
    $main_template = $modx->getObject('modTemplate',1);
    if($main_template->templatename == 'Главная страница'){
        echo "<span class='no-action'>Шаблон главной страницы уже был изменен.</span><br/>";
    }else{
        $main_template->set('templatename','Главная страница');
        $main_template->set('description','');
        $main_template->set('icon','icon-home');
        $main_template->set('content','<html>'.
        PHP_EOL
        .''.
        PHP_EOL
        .'	[[$include]]'.
        PHP_EOL
        .''.
        PHP_EOL
        .'	<body>'.
        PHP_EOL
        .''.
        PHP_EOL
        .'		[[$header]]'.
        PHP_EOL
        .''.
        PHP_EOL
        .'		[[$footer]]'.
        PHP_EOL
        .''.
        PHP_EOL
        .'		[[$include-js]]'.
        PHP_EOL
        .''.
        PHP_EOL
        .'	</body>'.
        PHP_EOL
        .'</html>');
        
        $main_template->save();
        
        echo "<span class='success'>Шаблон главной страницы изменен.</span><br/>";
    }
    
    $default_tpl = '<html>'.
        PHP_EOL
        .''.
        PHP_EOL
        .'	[[$include]]'.
        PHP_EOL
        .''.
        PHP_EOL
        .'	<body>'.
        PHP_EOL
        .''.
        PHP_EOL
        .'		[[$header]]'.
        PHP_EOL
        .''.
        PHP_EOL
        .'		[[$bread]]'.
        PHP_EOL
        .''.
        PHP_EOL
        .'		[[$sidebar]]'.
        PHP_EOL
        .''.
        PHP_EOL
        .'		[[$footer]]'.
        PHP_EOL
        .''.
        PHP_EOL
        .'		[[$include-js]]'.
        PHP_EOL
        .''.
        PHP_EOL
        .'	</body>'.
        PHP_EOL
        .'</html>';
    
    $templates = array(
        array('templatename' => '404',
    	  		'icon' => 'icon-exclamation-triangle',
    	  		'description' => ''
    	  		)
        , array('templatename' => 'Текстовая страница',
    			'icon' => 'icon-pencil-square-o',
    	  		'description' => ''
    			)
        , array('templatename' => 'Контакты',
    			'icon' => 'icon-envelope-o',
    	  		'description' => ''
    			)
    );
    
    foreach ($templates as $attr) {
        $checker = $modx->getObject('modTemplate',array('templatename'=>$attr['templatename']));
        if($checker){
            echo "<span class='no-action'>Шаблон ".$attr['templatename']." уже существует.</span><br/>";
        }else{
            $doc = $modx->newObject('modTemplate');
            $doc->set('templatename',$attr['templatename']);
            $doc->set('icon',$attr['icon']);
            $doc->set('description',$attr['description']);
            $doc->set('content',$default_tpl);
            
            $doc->save();
            
            if($doc->id){
                echo "<span class='success'>Шаблон ".$attr['templatename']." успешно создан.</span><br/>";
            }else{
                echo "<span class='error'>Не удалось создать шаблон ".$attr['templatename'].", попробуйте создать его вручную.</span><br/>";
            }
            
        }
    	
    }
    
    echo "<br/>";
}

function create_resources($modx){
	
	echo "<h3>Ресурсы</h3>";
	
	$template_404 = $modx->getObject('modTemplate',array('templatename'=>'404'));
	
	$template_404_id = $template_404->id ? $template_404->id : 1;
	
    $resources = array(
        array('pagetitle' => 'sitemap',
    	  		'template' => 0,
    	  		'published' => 1,
    	  		'hidemenu' => 1,
    	  		'alias' => 'sitemap', 
    	  		'content_type' => 2, 
    	  		'richtext' => 0, 
    	  		'content' =>'[[!pdoSitemap?]]'
    	  		)
        , array('pagetitle' => 'robots',
    			'template' => 0,
    			'published' => 1,
    			'hidemenu' => 1,
    			'alias' => 'robots',
    			'content_type' => 3,
    			'richtext' => 0, 
    			'content' => '[[!checkRobot? &id=`[[++robot]]`]]' 
    			)
        , array('pagetitle' => 'Ошибка 404',
    			'template' => $template_404_id,
    			'published' => 1,
    			'hidemenu' => 1,
    			'alias' => 'error-404',
    			'content_type' => 1,
    			'richtext' => 0,
                'content' => ''
    			)
    );
    foreach ($resources as $attr) {
        $checker = $modx->getObject('modResource',array('pagetitle'=>$attr['pagetitle']));
        if($checker){
            echo "<span class='no-action'>Страница ".$attr['pagetitle']." уже существует.</span><br/>";
        }else{
            $doc = $modx->newObject('modDocument');
            $doc->set('pagetitle',$attr['pagetitle']);
            $doc->set('template',$attr['template']);
            $doc->set('published',$attr['published']);
            $doc->set('hidemenu',$attr['hidemenu']);
            $doc->set('alias',$attr['alias']);
            $doc->set('content_type',$attr['content_type']);
            $doc->set('richtext',$attr['richtext']);
            $doc->setContent($attr['content']);
            
            $doc->save();
            
            $res = $modx->getObject('modResourceGroup',array('name'=>'Администратор'));
			$access = $modx->newObject('modResourceGroupResource');
			$access->set("document_group",$res->id);
			$access->set("document",$doc->id);
			$access->save();
            
            if($doc->id){
                echo "<span class='success'>Страница ".$attr['pagetitle']." успешно создана.</span><br/>";
            }else{
                echo "<span class='error'>Не удалось создать страницу ".$attr['pagetitle'].", попробуйте создать ее вручную.</span><br/>";
            }
            
        }
    	
    }
    echo "<br/>";
}

function system_settings($modx){
	
	echo "<h3>Системные настройки</h3>";
	
    $error_page = $modx->getObject('modResource',array('alias'=>'error-404'));
    $error_page_id = $error_page->id ? $error_page->id : 1;
    $settings = array(
    	'cultureKey' => 'ru'
    	,'fe_editor_lang' => 'ru'
    	,'publish_default' => 1
    	,'upload_maxsize' => '10485760'
    	, 'locale' => 'ru_RU.utf-8'
    	, 'manager_lang_attribute' => 'ru'
    	, 'manager_language' => 'ru'
        , 'error_page' => $error_page_id
        , 'allow_manager_login_forgot_password' => 0
    	
    	//url
    	, 'automatic_alias' => 1
    	, 'friendly_urls' => 1
    	, 'global_duplicate_uri_check' => 1
        , 'use_alias_path' => 1
    	, 'friendly_alias_translit' => 'russian'
        
        //Ace
        ,'ace.theme' => 'twilight'
    );
    foreach ($settings as $k => $v) {
    	$opt = $modx->getObject('modSystemSetting', array('key' => $k));
    	if (!empty($opt)){
    		$opt->set('value', $v);
    		$opt->save();
          	echo '<span class="success">Отредактировано '.$k.' = '.$v."</span><br/>";
        } else {
        	$newOpt = $modx->newObject('modSystemSetting');
        	$newOpt->set('key', $k);
        	$newOpt->set('value', $v);
        	$newOpt->save();
        	echo '<span class="success">Добавлено '.$k.' = '.$v."</span><br/>";
        }
    }
    echo "<br/>";
}
function create_elements($modx){
	
	echo "<h3>Элементы: категории, чанки и сниппеты</h3>";
	
    $checker = $modx->getObject('modCategory',array('category'=>'main'));
    
    if($checker){
        echo "<span class='no-action'>Категория main уже существует.</span><br/><br/>";
        $category = $modx->getObject('modCategory',array('category'=>'main'));
    }else{
        $category = $modx->newObject('modCategory');
        $category->set('category','main');
        $category->save();
        echo "<span class='success'>Категория main успешно создана.</span><br/><br/>";
    }
    
    $chunks = array();
    $snippets = array();
    
    $chunk_array = array(
        array('name' => 'include',
                'content' => '<head>'.
    PHP_EOL
    .'	<base href="[[++site_url]]" />'.
    PHP_EOL
    .'	<meta charset="UTF-8">'.
    PHP_EOL
    .'	<meta name="viewport" content="width=device-width, initial-scale=1.0">'.
    PHP_EOL
    .'	<meta name="description" content="[[*description]]" />'.
    PHP_EOL
    .'	<meta name="keywords" content="[[+seoPro.keywords]]" />'.
    PHP_EOL
    .'	<meta property="og:type" content="article" />'.
    PHP_EOL
    .'	[[*id:is=`1`:then=`<meta property="og:url" content="[[~[[*id]]]]" />`:else=`<meta property="og:url" content="[[++site_url]][[~[[*id]]]]" />`]]'.
    PHP_EOL
    .'	<meta property="og:title" content="[[+seoPro.title]]" />'.
    PHP_EOL
    .'	<meta property="og:description" content="[[*description]]" />'.
    PHP_EOL
    .'	<meta property="og:image" content="[[++site_url]]images/logo.png" />'.
    PHP_EOL
    .'	<title>[[+seoPro.title]]</title>'.
    PHP_EOL
    .'	<link rel="shortcut icon" href="images/favicon.ico">'.
    PHP_EOL
    .'	[[++ya_var]]'.
    PHP_EOL
    .'	[[++g_var]]'.
    PHP_EOL
    .'</head>'
    	  		)
        , array('name' => 'header',
    			'content' => ''
    			)
        , array('name' => 'include-js',
                'content' => '[[++ya_metrika]]'.
    PHP_EOL
    .'[[++g_anal]]'.
    PHP_EOL
    .'[[++user_script]]'.
    PHP_EOL
    .'<!--'.
    PHP_EOL
    .'Время генерации страницы: [^t^]</br>'.
    PHP_EOL
    .'Время парсинга: [^p^]</br>'.
    PHP_EOL
    .'Время выполнения запросов: [^qt^]</br>'.
    PHP_EOL
    .'Всего запросов: [^q^]</br>'.
    PHP_EOL
    .'Источник: [^s^]</br>'.
    PHP_EOL
    .'Память: [[!mem]]</br>'.
    PHP_EOL
    .'-->'
    			)
        , array('name' => 'footer',
    			'content' => ''
    			)
        , array('name' => 'sidebar',
    			'content' => ''
    			)
        , array('name' => 'bread',
    			'content' => ''
    			)
    );
    
    foreach ($chunk_array as $attr) {
        $checker = $modx->getObject('modChunk',array('name'=>$attr['name']));
        if($checker){
            echo "<span class='no-action'>Чанк ".$attr['name']." уже существует.</span><br/>";
        }else{
            $chunk = $modx->newObject('modChunk');
            $chunk->set('name',$attr['name']);
            $chunk->set('content',$attr['content']);
            
            $chunk->save();
            
            if($chunk->id){
                echo "<span class='success'>Чанк ".$attr['name']." успешно создан.</span><br/>";
                $chunks[] = $chunk;
            }else{
                echo "<span class='error'>Не удалось создать чанк ".$attr['name'].", попробуйте создать его вручную.</span><br/>";
            }
        }
    	
    }
    
    $category->addMany($chunks);
    $category->save();
                
    echo "<br/>";
    
    $snippet_array = array(
        array('name' => 'getYear',
                'content' => '<?php return date("Y");'
    	  		)
        , array('name' => 'dateRU',
    			'content' => '<?php'.
    PHP_EOL
    .'$month_arr = array("01" => "Января","02" => "Февраля","03" => "Марта","04" => "Апреля","05" => "Мая","06" => "Июня","07" => "Июля","08" => "Августа","09" => "Сентября","10" => "Октября","11" => "Ноября","12" => "Декабря");'.
    PHP_EOL
    .'$time = strtotime($input);'.
    PHP_EOL
    .'$month = strftime("%m", $time);'.
    PHP_EOL
    .'$day = strftime("%d", $time);'.
    PHP_EOL
    .'$year = strftime("%Y", $time);'.
    PHP_EOL
    .'return "$day $month_arr[$month] $year";'
    			)
        , array('name' => 'mem',
    			'content' => '<?php return round(memory_get_usage()/1024/1024, 4)." Mb";'
    			)
        , array('name' => 'checkRobot',
    			'content' => '<?php'.
    PHP_EOL
    .'if($id==1){'.
    PHP_EOL
    .'	echo "User-agent: *".PHP_EOL."Disallow: /manager/".PHP_EOL."Disallow: /assets/components/".PHP_EOL."Disallow: /core/".PHP_EOL."Disallow: /connectors/".PHP_EOL."Disallow: /index.php".PHP_EOL."Disallow: /search".PHP_EOL."Disallow: /profile/".PHP_EOL."Disallow: *?".PHP_EOL."Host: [[++site_url]]".PHP_EOL."Sitemap: [[++site_url]]sitemap.xml";'.
    PHP_EOL
    .'}else{'.
    PHP_EOL
    .'	echo "User-agent: *".PHP_EOL."Disallow: /";
}'
    			)
    );
    
    foreach ($snippet_array as $attr) {
        $checker = $modx->getObject('modSnippet',array('name'=>$attr['name']));
        if($checker){
            echo "<span class='no-action'>Сниппет ".$attr['name']." уже существует.</span><br/>";
        }else{
            $snippet = $modx->newObject('modSnippet');
            $snippet->set('name',$attr['name']);
            $snippet->set('content',$attr['content']);
            
            $snippet->save();
            
            if($snippet->id){
                echo "<span class='success'>Сниппет ".$attr['name']." успешно создан.</span><br/>";
                $snippets[] = $snippet;
            }else{
                echo "<span class='error'>Не удалось создать сниппет ".$attr['name'].", попробуйте создать его вручную.</span><br/>";
            }
        }
    	
    }
    
    $category->addMany($snippets);
    $category->save();
                
    echo "<br/>";
}

function pthumb($modx){
	
	echo "<h3>Выключение плагина pthumb</h3>";
	
	$config = $modx->getObject('modPlugin',array('name'=>'phpThumbOfCacheManager'));

	if($config && $config->disabled == 0){
	    $config->set('disabled',1);
	    $config->save();
	    echo "<span class='success'>Плагин успешно отключен.</span><br/><br/>";
	}elseif($config && $config->disabled == 1){
	    echo "<span class='no-action'>Плагин pthumb уже отключен.</span><br/><br/>";
	}else{
		echo "<span class='error'>Не удалось найти плагин, возможно не установлен пакет pthumb.</span><br/><br/>";
	}
}

function filemanager($modx){
	
	echo "<h3>Файловый менеджер</h3>";
	
	$dirname = 'file';
	
	$path = str_replace($_SERVER['SCRIPT_NAME'], "/", $_SERVER['SCRIPT_FILENAME']);
	
	if( is_dir($dirname) || $dirname === "/" ){
		echo "<span class='no-action'>Папка file уже существует.</span><br/>";
	}else{
		mkdir($dirname, 0777);
		echo "<span class='success'>Папка file успешно создана.</span><br/>";
  	}
	
	$filemanager = $modx->getObject('modMediaSource',1);
	
	$props = array(
		'basePath' => 'file/',
		'baseUrl' => 'file/'
	);
	
	if($filemanager){
		
		if($filemanager->name != "Файловая система"){
			$filemanager->set('name','Файловая система');
			$filemanager->setProperties($props);
			$filemanager->save();
			
			echo "<span class='success'>Файловая система успешно отредактирована.</span><br/><br/>";
		}else{
			echo "<span class='no-action'>Файловая система уже отредактирована.</span><br/><br/>";
		}
		
	}else{
		echo "<span class='error'>Не могу найти файловую систему.</span><br/><br/>";
	}
}

function resource_group($modx){
	
	echo "<h3>Группы ресурсов</h3>";
	
	$res = $modx->getObject('modResourceGroup',array('name'=>'Администратор'));
	
	if(!$res){
		$res = $modx->newObject('modResourceGroup');
		$res->set("name","Администратор");
		$res->save();
		
		$access = $modx->newObject('modAccessResourceGroup');
		$access->set('target',$res->id);
		$access->set('principal_class','modUserGroup');
		$access->set('principal',1);
		$access->set('authority',9999);
		$access->set('policy',1);
		$access->set('context_key','mgr');
		$access->save();
		
		if($res){
			echo "<span class='success'>Группа ресурсов успешно создана.</span><br/><br/>";
		}else{
			echo "<span class='error'>Не могу создать группу ресурсов.</span><br/><br/>";
		}
	}else{
		echo "<span class='no-action'>Группа ресурсов уже была создана.</span><br/><br/>";
	}
	
}

function preview($modx){
	
	echo "<h3>TV preview</h3>";
	
	$category = $modx->getObject('modCategory',array('category'=>'Изображения'));
    
    if($category){
        echo "<span class='no-action'>Категория Изображения уже существует.</span><br/>";
    }else{
        $category = $modx->newObject('modCategory');
        $category->set('category','Изображения');
        $category->save();
        echo "<span class='success'>Категория Изображения успешно создана.</span><br/>";
    }
    
    $tv = $modx->getObject('modTemplateVar',array('name'=>'preview'));
    
    if(!$tv){
    	$tv = $modx->newObject('modTemplateVar');
		$tv->set("name","preview");
		$tv->set("caption","Превью");
		$tv->set("type","image");
		$tv->save();
		if($tv){
			echo "<span class='success'>Preview успешно создана.</span><br/>";
		}else{
			echo "<span class='error'>Не могу создать Preview.</span><br/>";
		}
    }else{
    	echo "<span class='no-action'>Preview уже была создана.</span><br/>";
    }
    
	$tv->addOne($category);
    $tv->save();
    
    echo "<br/>";
	
}

function hide_migx($modx){
	
	echo "<h3>Удаление MIGX для контент менеджера</h3>";
	
	$migx = $modx->getObject('modMenu',array('text'=>'migx'));
	
	if($migx){
		$migx->set("permissions","admin");
		$migx->save();
		echo "<span class='success'>Пакет MIGX успешно спрятан из меню для контент менеджера.</span><br/><br/>";
	}else{
		echo "<span class='no-action'>Пакет MIGX не был установлен.</span><br/><br/>";
	}
	
}

function hide_create($modx){
	
	echo "<h3>Удаление Создать ресурс для контент менеджера</h3>";
	
	$create = $modx->getObject('modMenu',array('text'=>'new_resource'));
	
	if($create){
		$create->remove();
		echo "<span class='success'>Создание ресурса успешно спрятано из меню для контент менеджера.</span><br/><br/>";
	}else{
		echo "<span class='no-action'>Создание ресурса не было спрятано.</span><br/><br/>";
	}
	
}

function changeTemplate($modx){
	
	echo "<h3>ChangeTemplate</h3>";
	
	$category = $modx->getObject('modCategory',array('category'=>'Шаблонизатор'));
    
    if($category){
        echo "<span class='no-action'>Категория Шаблонизатор уже существует.</span><br/>";
    }else{
        $category = $modx->newObject('modCategory');
        $category->set('category','Шаблонизатор');
        $category->save();
        echo "<span class='success'>Категория Шаблонизатор успешно создана.</span><br/>";
    }
    
    $tv = $modx->getObject('modTemplateVar',array('name'=>'changeTemplate'));
    
    if(!$tv){
    	$tv = $modx->newObject('modTemplateVar');
		$tv->set("name","changeTemplate");
		$tv->set("caption","Шаблонизатор");
		$tv->set("type","text");
		$tv->save();
		
		$templates = $modx->getCollection('modTemplate');
		foreach ($templates as $k => $tmpl) {
			$tvt = $modx->newObject('modTemplateVarTemplate');
			$tvt->set("tmplvarid",$tv->id);
			$tvt->set("templateid",$tmpl->id);
			$tvt->save();
		}
		
		$res = $modx->getObject('modResourceGroup',array('name'=>'Администратор'));
		$access = $modx->newObject('modTemplateVarResourceGroup');
		$access->set("tmplvarid",$tv->id);
		$access->set("documentgroup",$res->id);
		$access->save();
		
		if($tv){
			echo "<span class='success'>TV Шаблонизатор успешно создана.</span><br/>";
		}else{
			echo "<span class='error'>Не могу создать TV Шаблонизатор.</span><br/>";
		}
    }else{
    	echo "<span class='no-action'>TV Шаблонизатор уже была создана.</span><br/>";
    }
    
    $tv->addOne($category);
    $tv->save();
    
    $plugin = $modx->getObject('modPlugin',array('name'=>'ChangeTemplate'));
    
    if(!$plugin){
    	$plugin = $modx->newObject('modPlugin');
		$plugin->set("name","ChangeTemplate");
		$plugin->set('plugincode','<?php if ($modx->event->name == OnDocFormRender && $mode == modSystemEvent::MODE_NEW) { if ($id = $_REQUEST["id"]) { $resources = array($id); foreach ($modx->getParentIds($id, 10, array("context" => $_REQUEST["context_key"])) as $parentId) { if ($parentId) array_push($resources, $parentId); } $level = 0; $childTemplates = array(); foreach ($resources as $resourceId) { $resource = $modx->getObject("modResource", $resourceId); if ($childTemplatesTV = $resource->getTVValue("changeTemplate")) { $childTemplates = @explode(",", $childTemplatesTV); if (empty($childTemplates)) break; foreach ($childTemplates as $k => $v) $childTemplates[$k] = intval(trim($v)); break; } $level++; } if (!empty($childTemplates)) { $useTemplate = $childTemplates[$level]; if (!empty($useTemplate)) { if (isset($modx->controller)) { $modx->controller->setProperty("template", $useTemplate); } else { $_REQUEST["template"] = $useTemplate; } } } } }');
		$plugin->save();
		
		$event = $modx->newObject('modPluginEvent');
		$event->set("pluginid",$plugin->id);
		$event->set("event","OnDocFormRender");
		$event->save();
		
		if($plugin){
			echo "<span class='success'>Плагин ChangeTemplate успешно создан.</span><br/>";
		}else{
			echo "<span class='error'>Не могу создать плагин ChangeTemplate.</span><br/>";
		}
    }else{
    	echo "<span class='no-action'>плагин ChangeTemplate уже был создан.</span><br/>";
    }
    
	$plugin->addOne($category);
    $plugin->save();
    
    echo "<br/>";
	
}

function create_user($modx){
	
	echo "<h3>Пользователь контент менеджер</h3>";
	
	$policy = $modx->getObject('modAccessPolicy',array('name'=>'Content Editor'));
	
	$policy->set('data','{"about":true,"access_permissions":true,"actions":true,"change_password":true,"change_profile":true,"charsets":true,"class_map":true,"components":true,"content_types":true,"countries":true,"create":true,"credits":true,"customize_forms":true,"dashboards":true,"database":true,"database_truncate":true,"delete_category":true,"delete_chunk":true,"delete_context":true,"delete_document":true,"delete_eventlog":true,"delete_plugin":true,"delete_propertyset":true,"delete_role":true,"delete_snippet":true,"delete_template":true,"delete_tv":true,"delete_user":true,"directory_chmod":true,"directory_create":true,"directory_list":true,"directory_remove":true,"directory_update":true,"edit_category":true,"edit_chunk":true,"edit_context":true,"edit_document":true,"edit_locked":true,"edit_plugin":true,"edit_propertyset":true,"edit_role":true,"edit_snippet":true,"edit_template":true,"edit_tv":true,"edit_user":true,"element_tree":true,"empty_cache":true,"error_log_erase":true,"error_log_view":true,"export_static":true,"file_create":true,"file_list":true,"file_manager":true,"file_remove":true,"file_tree":true,"file_update":true,"file_upload":true,"file_unpack":true,"file_view":true,"flush_sessions":true,"frames":true,"help":true,"home":true,"import_static":true,"languages":true,"lexicons":true,"list":true,"load":true,"logout":true,"logs":true,"menus":true,"menu_reports":true,"menu_security":true,"menu_site":true,"menu_support":true,"menu_system":true,"menu_tools":true,"menu_user":true,"messages":true,"namespaces":true,"new_category":true,"new_chunk":true,"new_context":true,"new_document":true,"new_document_in_root":true,"new_plugin":true,"new_propertyset":true,"new_role":true,"new_snippet":true,"new_static_resource":true,"new_symlink":true,"new_template":true,"new_tv":true,"new_user":true,"new_weblink":true,"packages":true,"policy_delete":true,"policy_edit":true,"policy_new":true,"policy_save":true,"policy_template_delete":true,"policy_template_edit":true,"policy_template_new":true,"policy_template_save":true,"policy_template_view":true,"policy_view":true,"property_sets":true,"providers":true,"publish_document":true,"purge_deleted":true,"remove":true,"remove_locks":true,"resource_duplicate":true,"resourcegroup_delete":true,"resourcegroup_edit":true,"resourcegroup_new":true,"resourcegroup_resource_edit":true,"resourcegroup_resource_list":true,"resourcegroup_save":true,"resourcegroup_view":true,"resource_quick_create":true,"resource_quick_update":true,"resource_tree":true,"save":true,"save_category":true,"save_chunk":true,"save_context":true,"save_document":true,"save_plugin":true,"save_propertyset":true,"save_role":true,"save_snippet":true,"save_template":true,"save_tv":true,"save_user":true,"search":true,"settings":true,"sources":true,"source_delete":true,"source_edit":true,"source_save":true,"source_view":true,"steal_locks":true,"tree_show_element_ids":true,"tree_show_resource_ids":true,"undelete_document":true,"unlock_element_properties":true,"unpublish_document":true,"usergroup_delete":true,"usergroup_edit":true,"usergroup_new":true,"usergroup_save":true,"usergroup_user_edit":true,"usergroup_user_list":true,"usergroup_view":true,"view":true,"view_category":true,"view_chunk":true,"view_context":true,"view_document":true,"view_element":true,"view_eventlog":true,"view_offline":true,"view_plugin":true,"view_propertyset":true,"view_role":true,"view_snippet":true,"view_sysinfo":true,"view_template":true,"view_tv":true,"view_unpublished":true,"view_user":true,"workspaces":true}');
	$policy->save();
	
	$group = $modx->getObject('modUserGroup',array('name'=>'Content'));
	
	if(!$group){
    	$group = $modx->newObject('modUserGroup');
		$group->set("name","Content");
		$group->save();
		
		$context_web = $modx->newObject('modAccessContext');
		$context_web->set('target','web');
		$context_web->set('principal_class','modUserGroup');
		$context_web->set('authority',0);
		$context_web->set('policy',$policy->id);
		$context_web->set('principal',$group->id);
		$context_web->save();
		
		$context_mgr = $modx->newObject('modAccessContext');
		$context_mgr->set('target','mgr');
		$context_mgr->set('principal_class','modUserGroup');
		$context_mgr->set('authority',0);
		$context_mgr->set('policy',$policy->id);
		$context_mgr->set('principal',$group->id);
		$context_mgr->save();
		
		if($group){
			echo "<span class='success'>Группа пользователей успешно создана.</span><br/>";
		}else{
			echo "<span class='error'>Не могу создать группу пользователей.</span><br/>";
		}
    }else{
    	echo "<span class='no-action'>Группа пользователей уже была создана.</span><br/>";
    }
    
    $user = $modx->getObject('modUser',array('username'=>'content'));
	
	if(!$user){
    	$user = $modx->newObject('modUser');
		$user->set("username","content");
		$user->set("password","forrest");
		$user->set("active",1);
		$user->save();
		
		$profile = $modx->newObject('modUserProfile');
		$profile->set('fullname', 'Контент менеджер');
		$profile->set('email', 'info@webgeeks.by');
		$user->addOne($profile);
		
		$profile->save();
		$user->save();
		
		$member = $modx->newObject('modUserGroupMember');
		$member->set('user_group', $group->get('id'));
		$member->set('member', $user->get('id'));
		$member->set('role', 2);
		$member->save();
		
		if($user){
			echo "<span class='success'>Пользователь успешно создан.</span><br/>";
		}else{
			echo "<span class='error'>Не могу создать пользователя.</span><br/>";
		}
    }else{
    	echo "<span class='no-action'>Пользователь уже был создан.</span><br/>";
    }
	
}

core_htaccess();
wg_widget();
wg_modx_widget($modx);
create_template($modx);
preview($modx);
resource_group($modx);
changeTemplate($modx);
create_resources($modx);
system_settings($modx);
create_elements($modx);
pthumb($modx);
filemanager($modx);
hide_migx($modx);
hide_create($modx);
create_user($modx);
?>
</body>
</html>