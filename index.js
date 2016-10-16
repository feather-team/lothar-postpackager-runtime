'use strict';

var path = require('path');

module.exports = function(ret, conf, setting, opt){
    var files = lothar.util.find(__dirname + '/project/plugins');

    files.forEach(function(file){
        var f = lothar.file.wrap(lothar.project.getProjectPath() + '/plugins/' + path.basename(file));
        f.setContent(lothar.util.read(file));
        ret.pkg[f.subpath] = f;
    });

    if(lothar.isPreviewMode){
        var name = lothar.config.get('project.name');
        var www = lothar.project.getTempPath('www'), proj = www + '/project/' + name + '/', preview = www + '/preview/';

        if(!lothar.runtimeCreated){
            if(lothar.util.mtime(__dirname + '/package.json') > lothar.util.mtime(www + '/vendor/autoload.php')){
                lothar.util.copy(__dirname + '/runtime', www);
            }
            
            lothar.util.write(www + '/preview/current', name);
            lothar.util.mkdir(www + '/cache');
            lothar.runtimeCreated = true; 
        }

        lothar.util.map(ret.src, function(subpath, file){
            if(file.isHtmlLike){
                var content = file.getContent().replace(/<<<BLADE_LABEL_HACK>>>/g, ' ');

                if(file.isWidget && lothar.isPreviewMode){
                    content = "@if(!isset($__isRef))\n@include('common._static_', ['type' => 'head'])\n@endif\n" + content + "\n@if(!isset($__isRef))\n@include('common._static_', ['type' => 'bottom'])\n@endif\n";
                }

                file.setContent(content);
            }
        });
    }  
};