<?php
require_once 'common/FacadeInterface.php';
require_once 'common/DatabaseLogger.php';
require_once 'CharityFacade.php';

class LoggingProxy implements FacadeInterface
{
    private $facade;
    private $logger;

    public function __construct()
    {
        $this->facade = new CharityFacade();
        $this->logger = new DatabaseLogger();
    }

    public function handleRequest($action, $method, $data = [])
    {
        $this->logger->log($action, $method);

        $this->facade->handleRequest($action, $method, $data);
    }
}
