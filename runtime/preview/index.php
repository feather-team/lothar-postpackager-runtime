<?php
define('ROOT', dirname(__DIR__));
define('CACHE_ROOT', ROOT . '/cache');
define('PREVIEW_ROOT', ROOT . '/preview');

$proj = file_get_contents(PREVIEW_ROOT . '/current');

define('PROJECT_ROOT', sprintf('%s/project/%s', ROOT, $proj));
define('VIEW_ROOT', PROJECT_ROOT . '/view');
define('DATA_ROOT', PROJECT_ROOT . '/data');

require ROOT . '/vendor/autoload.php';

use Illuminate\Container\Container;
use Jenssegers\Blade\Blade;
use Feather2\Blade as BladeProvider;
use Feather2\Resource;

$conf = json_decode(file_get_contents(VIEW_ROOT . '/engine.json'), true);
list($previousPath) = explode('?', ltrim($_SERVER['REQUEST_URI'], '/'));

if(trim($previousPath, '/') == ''){
    $previousPath = 'common/index';
}

//if is data
if(preg_match('/^\/?[^\/]+\/data\//', $previousPath)){
    require DATA_ROOT . '/' . $previousPath;
    exit;
}

$path = trimSuffix($previousPath, $conf['suffix']);

$container = new Container;
$blade = new Blade(VIEW_ROOT, CACHE_ROOT, $container);
$config = $container['config'];
$config['view'] = [
    'paths' => $blade->viewPaths,
    'compiled' => $blade->cachePath,
    'debug' => isset($_GET['debug']),
    'cache' => false
];
$container['config'] = $config;

(new BladeProvider\ResourceProvider($container))->register();

$data = getData($path, $conf);

if(isset($_GET['debugData'])){
    var_dump($data);
}

$data['__debugData'] = $data;
$blade->share($data);

echo $blade->make($path)->render();

function trimSuffix($path, $suffix){
    return preg_replace('#\.' . $suffix . '$#', '', $path);
}

//加载测试数据，包括引用的文件
function getData($path, $conf){
    $datas = array();

    $Maps = new Resource\Maps(VIEW_ROOT . '/_map_');
    $id = $path . '.' . $conf['suffix'];
    $info = $Maps->getIncludeRefs($id);
    $files = isset($info['refs']) ? array_unique($info['refs']) : array();
    $files[] = $id;

    array_unshift($files, 'common:_global_');

    foreach($files as $file){
        $file = str_replace(':', '/', $file);
        $file = trimSuffix($file, $conf['suffix']);
        $sp = explode('/', $file);
        array_splice($sp, 1, 0, 'data');
        $dataFile = sprintf('%s/%s.php', DATA_ROOT, implode('/', $sp));

        if(is_file($dataFile)){
            $data = require $dataFile;
        }else{
            $dataFile = sprintf('%s/%s.json', DATA_ROOT, implode('/', $sp));

            if(is_file($dataFile)){
                $data = json_decode(file_get_contents($dataFile), true);
            }else{
                $data = array();
            }    
        }

        $datas = array_merge($datas, $data);
    }

    return $datas;
}