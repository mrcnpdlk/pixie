<?php

namespace Pecee\Pixie;

use Pecee\Pixie\QueryBuilder\QueryBuilderHandler;
use Viocon\Container;

/**
 * Class Connection
 *
 * @package Pecee\Pixie
 */
class Connection
{

    /**
     * @var Container
     */
    protected $container;

    /**
     * Name of DB adapter (i.e. Mysql, Pgsql, Sqlite)
     * @var string
     */
    protected $adapter;

    /**
     * @var array
     */
    protected $adapterConfig;

    /**
     * @var \PDO
     */
    protected $pdoInstance;

    /**
     * @var Connection
     */
    protected static $storedConnection;

    /**
     * @var EventHandler
     */
    protected $eventHandler;

    /**
     * @param string $adapter
     * @param array $adapterConfig
     * @param Container|null $container
     */
    public function __construct($adapter, array $adapterConfig, Container $container = null)
    {
        $container = $container ?: new Container();

        $this->container = $container;

        $this->setAdapter($adapter)->setAdapterConfig($adapterConfig)->connect();

        // Create event dependency
        $this->eventHandler = $this->container->build(EventHandler::class);
    }

    /**
     * Returns an instance of Query Builder
     */
    public function getQueryBuilder()
    {
        return $this->container->build(QueryBuilderHandler::class, [$this]);
    }

    /**
     * Create the connection adapter
     */
    protected function connect()
    {
        // Build a database connection if we don't have one connected

        $adapter = '\Pecee\Pixie\ConnectionAdapters\\' . ucfirst(strtolower($this->adapter));

        $adapterInstance = $this->container->build($adapter, [$this->container]);

        $pdo = $adapterInstance->connect($this->adapterConfig);
        $this->setPdoInstance($pdo);

        // Preserve the first database connection with a static property
        if (static::$storedConnection === null) {
            static::$storedConnection = $this;
        }
    }

    /**
     * @param \PDO $pdo
     * @return static
     */
    public function setPdoInstance(\PDO $pdo)
    {
        $this->pdoInstance = $pdo;

        return $this;
    }

    /**
     * @return \PDO
     */
    public function getPdoInstance()
    {
        return $this->pdoInstance;
    }

    /**
     * @param string $adapter
     * @return static
     */
    public function setAdapter($adapter)
    {
        $this->adapter = $adapter;

        return $this;
    }

    /**
     * @return string
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * @param array $adapterConfig
     * @return static
     */
    public function setAdapterConfig(array $adapterConfig)
    {
        $this->adapterConfig = $adapterConfig;

        return $this;
    }

    /**
     * @return array
     */
    public function getAdapterConfig()
    {
        return $this->adapterConfig;
    }

    /**
     * @return Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @return EventHandler
     */
    public function getEventHandler()
    {
        return $this->eventHandler;
    }

    /**
     * @return Connection
     */
    public static function getStoredConnection()
    {
        return static::$storedConnection;
    }
}
