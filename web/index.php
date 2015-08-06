<?php
// web/index.php
require_once __DIR__.'/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
$app->register(new Silex\Provider\FormServiceProvider());
$app->register(new Silex\Provider\TranslationServiceProvider(), array(
    'translator.domains' => array(),
));
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());


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
})
->bind('project_edit');

//action de mise à jour d'une recette
$app->post('/ajax/updateRecipe', function (Silex\Application $app, Symfony\Component\HttpFoundation\Request $request)  {
    $content = $request->request->get('content');
    $recipe = $content['recipe'];
    $deploy_file_content = $content['deploy_file_content'];
    
    $file = RECIPES_FOLDER.'/'.$recipe.'/deploy.php';
    file_put_contents($file, $deploy_file_content);
    return $deploy_file_content;
    
});

$app->post('/ajax/deployStaging', function (Silex\Application $app, Symfony\Component\HttpFoundation\Request $request)  {
    $content = $request->request->get('content');
    $recipe = $content['recipe'];
    $folder = RECIPES_FOLDER.'/'.$recipe;
    
    $output = shell_exec('cd '.$folder.'; pwd; dep test recette');
    echo "<pre>$output</pre>";
    return $output;
    
});

$app->match('/project/new', function (Request $request) use ($app) {

    $form = $app['form.factory']->createBuilder('form')
        ->add('name', null, array('required' => true))
        ->add('Create', 'submit')
        ->getForm();

    $form->handleRequest($request);
    if ($form->isValid()) {
        $data = $form->getData();
        
        $folder = RECIPES_FOLDER . '/' . $data['name'];
        if (!file_exists($folder)) {
            mkdir($folder);
            $file = $folder . '/deploy.php';
            file_put_contents($file, '<?php');
        }

        $url = $app['url_generator']->generate('project_edit', array(
            'name' => $data['name']
        ));
        return $app->redirect($url);
    }

    return $app['twig']->render(
        'project-new.html.twig', 
        array(
            'form' => $form->createView()
        )
    );
});

$app->run();

?>