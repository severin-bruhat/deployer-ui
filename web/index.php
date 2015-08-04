<?php
// web/index.php
require_once __DIR__.'/../vendor/autoload.php';
define('RECIPES_FOLDER', __DIR__.'/../recipes');

$app = new Silex\Application();

//configuration de l'app
$app['debug'] = true;

$app['recipes'] = function(){
    return  scandir(RECIPES_FOLDER);
};

//déclaration des templates
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../templates', // The path to the templates, which is in our case points to /var/www/templates
));


//accueil
$app->get('/', function (Silex\Application $app)  { // Match the root route (/) and supply the application as argument
  
    $project = array();
    
    foreach($app['recipes']  as $folder){
        if($folder !== '.' && $folder !== '..'){
            $projects[] = $folder;
        }
    }
    return $app['twig']->render( 
        'projects-list.html.twig',
        array(
            'projects' => $projects, // Supply arguments to be used in the template
        )
    );
    
});


//edition d'un projet
$app->get('/projects/{name}', function (Silex\Application $app, $name)  {
  
   $project = array();
   $project['name'] = $name;
   
   if (!in_array($name, $app['recipes'])) {
        $app->abort(404, 'The project could not be found');
    }
  
    //on vérifie que le fichier déploiement existe sino on le créé
    $file_path = RECIPES_FOLDER.'/'.$name.'/deploy.php';
    if (!file_exists($file_path)) {
           touch($file_path);
           chmod($file_path, 0755);
    }
    
    $myfile=fopen($file_path, "r");
    $filesize = filesize($file_path);
    $project['deploy_file_content'] = ($filesize > 0) ? fread($myfile,$filesize) : "";
    fclose($myfile);
  
    
    return $app['twig']->render( 
        'project-single.html.twig',
        array(
            'project' => $project,
        )
    );
    
    return $output; // Return it to so it gets displayed by the browser
});

//action de mise à jour d'une recette
$app->post('/ajax/updateRecipe', function (Silex\Application $app)  {
    die(var_dump("post"));
});

$app->run();

//help : https://www.digitalocean.com/community/tutorials/how-to-get-started-with-silex-on-ubuntu-14-04
?>