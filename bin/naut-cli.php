<?php

require __DIR__ . '/../vendor/autoload.php';

use Guttmann\NautCli\Application;
use Guttmann\NautCli\Helper\ContainerHelper;

$application = new Application();

$container = ContainerHelper::buildContainer();
$application->setContainer($container);

$application->setName(
<<<TXT
\e[31m
 ▐ ▄  ▄▄▄· ▄• ▄▌▄▄▄▄▄     ▄▄· ▄▄▌  ▪  
•█▌▐█▐█ ▀█ █▪██▌•██      ▐█ ▌▪██•  ██ 
▐█▐▐▌▄█▀▀█ █▌▐█▌ ▐█.▪    ██ ▄▄██▪  ▐█·
██▐█▌▐█ ▪▐▌▐█▄█▌ ▐█▌·    ▐███▌▐█▌▐▌▐█▌
▀▀ █▪ ▀  ▀  ▀▀▀  ▀▀▀     ·▀▀▀ .▀▀▀ ▀▀▀\e[39m
TXT
);

define('NAUT_CLI_VERSION', '4.0.0');
$application->setVersion(NAUT_CLI_VERSION);

$application->addCommands([
    (new \Guttmann\NautCli\Command\ConfigureCommand()),
    (new \Guttmann\NautCli\Command\DeployBranchCommand()),
    (new \Guttmann\NautCli\Command\DeployPromoteFromUatCommand()),
    (new \Guttmann\NautCli\Command\SnapshotListCommand()),
    (new \Guttmann\NautCli\Command\SnapshotDeleteCommand()),
    (new \Guttmann\NautCli\Command\SnapshotDownloadCommand()),
    (new \Guttmann\NautCli\Command\SnapshotCreateCommand())
]);

define('ENV_FILE', '.naut.env');

$application->run();
