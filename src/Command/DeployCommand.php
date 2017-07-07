<?php
namespace Guttmann\NautCli\Command;

use Dotenv\Dotenv;
use Dotenv\Exception\InvalidPathException;
use Guttmann\NautCli\Service\DeployLogService;
use Guttmann\NautCli\Service\DeployService;
use Guttmann\NautCli\Service\FetchLatestService;
use GuzzleHttp\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DeployCommand extends Command
{

    protected function configure()
    {
        $this->setName('deploy')
            ->setDescription('Runs a deployment')
            ->setHelp('This command allows you to deploy the latest version of a branch to a specific environment within an instance.')
            ->addArgument('instance', InputArgument::REQUIRED, 'The shortcode for your instance')
            ->addArgument('environment', InputArgument::REQUIRED, 'The environment name')
            ->addArgument('branch', InputArgument::REQUIRED, 'The git branch to deploy');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->loadEnvConfig($output);

        $client = new Client([
            'base_uri' => getenv('NAUT_URL'),
            'cookies' => true,
            'auth' => [
                getenv('NAUT_USERNAME'),
                base64_decode(getenv('NAUT_PASSWORD_B64'))
            ]
        ]);

        $instanceId = $input->getArgument('instance');

        echo 'Fetching latest from git' . PHP_EOL;

        $fetchService = new FetchLatestService();
        $fetchService->fetch($client, $instanceId);

        $environment = $input->getArgument('environment');
        $branch = $input->getArgument('branch');

        echo 'Triggering deployment' . PHP_EOL;

        $deployService = new DeployService();
        $deployLogLink = $deployService->deploy($client, $instanceId, $environment, $branch);

        echo 'Deployment triggered' . PHP_EOL;
        echo 'Found deploy log link: ' . $deployLogLink . PHP_EOL;

        echo 'Streaming deploy log' . PHP_EOL;
        $deployLogService = new DeployLogService();
        $success = $deployLogService->streamLog($client, $deployLogLink);

        if ($success) {
            $output->writeln('Deployment complete');
            exit(0);
        } else {
            $output->writeln('Deployment failed');
            exit(1);
        }
    }

    private function loadEnvConfig(OutputInterface $output)
    {
        $output->writeln('Loading environment config');

        try {
            $dotenv = new Dotenv(getenv('HOME'), ENV_FILE);
            $dotenv->load();
        } catch (InvalidPathException $e) {
            $output->writeln('Failed to load env config, because:');
            $output->writeln($e->getMessage());
            exit(1);
        }

        $output->writeln('Config loaded');
    }

}
