<?php

namespace ExtraDataBundle\Command;

use Buzz\Client\Curl;
use Buzz\Message\Request as HttpRequest;
use Buzz\Message\RequestInterface as HttpRequestInterface;
use Buzz\Message\Response as HttpResponse;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

class UpdateExtraDataCommand extends ContainerAwareCommand
{

    protected function configure()
    {
	$params = array('CMD_USERNAME' => getenv("CMD_USERNAME")?getenv("CMD_USERNAME"):'admin' , 'CMD_PASSWORD' => getenv("CMD_PASSWORD")?getenv("CMD_PASSWORD"):'adminpass');

        $this
                ->setName('extradata:update')
                ->setDescription('Update entity extradata field')
                ->setHelp('Update entity extradata field from remote data')
                ->addOption('url', null, InputOption::VALUE_REQUIRED, 'Remote url', 'http://stats.fd3.flowdat.com/api/onus.json')
                ->addOption('filter', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'Array with filters. e.g., --filter=key1:value1 --filter=key2:value2')
                ->addOption('username', null, InputOption::VALUE_REQUIRED, 'Remote username or CMD_USERNAME enviroment variable', $params["CMD_USERNAME"])
                ->addOption('password', null, InputOption::VALUE_REQUIRED, 'Remote password or CMD_PASSWORD enviroment variable', $params["CMD_PASSWORD"])
                ->addOption('entity-class', null, InputOption::VALUE_REQUIRED, 'Entity namespace. e.g. FTTHBundle:ONU', 'FTTHBundle:ONU')
                ->addOption('id', null, InputOption::VALUE_REQUIRED, 'Entity Id')
                ->addOption('fields', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'Extradata fields to update')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * 
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $url = $input->getOption('url');
        $filter = $input->getOption('filter');
        $username = $input->getOption('username');
        $password = $input->getOption('password');
        $entityClass = $input->getOption('entity-class');
        $id = $input->getOption('id');
        $fields = $input->getOption('fields');
        try {
            $filters = array();
            if (!empty($filter)) {
                foreach ($filter as $value) {
                    $pieces = array_map('trim', explode(':', $value));
                    if (isset($pieces[0]) && isset($pieces[1]) && $pieces[0] && $pieces[1]) {
                        $filters[$pieces[0]] = $pieces[1];
                    }
                }
            }

            $client = new Curl();
            $response = new HttpResponse();
            $request = new HttpRequest(HttpRequestInterface::METHOD_GET, $url . '?' . http_build_query(array('filters' => $filters)));
            $request->addHeader('Authorization: Basic ' . base64_encode($username . ':' . $password));
            $client->send($request, $response);
            $response = $response->getContent();

            $em = $this->getContainer()->get('doctrine')->getEntityManager();
            $repository = $em->getRepository($entityClass);
            $entity = $repository->find($id);
            if (is_null($entity)) {
                $output->writeln(sprintf('<error>Entity: %s id: %s not Found!<error>', $entityClass, $entity->getId()));

                return;
            }
            
            $entityExtraData = (array)$entity->jsonExtraData();
            $extradata = json_decode($response, true);
            if (!empty($fields)) {
                foreach ($fields as $field) {
                    if (isset($extradata[0][$field])) {
                        $entityExtraData[$field] = $extradata[0][$field];
                    }
                }
            } else {
                $entityExtraData = array_merge($entityExtraData, $extradata[0]);
            }
            $entity->setJsonExtraData($entityExtraData);
            $em->flush($entity);

            $output->writeln(sprintf('Entity: %s id: %s updated!', $entityClass, $entity->getId()));
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            die;
        }
    }

}
