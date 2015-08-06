<?php
require 'recipe/common.php';

server('prod', '178.170.71.9', 22)
    ->user('SaladeSFTP')
    ->password('AQ-HLwErRhZda2zsfmTzDrPE9pKbyr7')
    ->env('deploy_path', '/var/home/Salade/public_html')
    ->stage('recette');

task('test', function () {
    echo "ok";
});

?>