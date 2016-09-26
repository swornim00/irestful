<?php
namespace iRESTful\Rodson\Infrastructure\Inputs\Objects;
use iRESTful\Rodson\Domain\Inputs\Projects\Databases\Relationals\RelationalDatabase;
use iRESTful\Rodson\Domain\Inputs\Projects\Databases\Credentials\Credentials;
use iRESTful\Rodson\Domain\Inputs\Projects\Databases\Relationals\Exceptions\RelationalDatabaseException;

final class ConcreteRelationalDatabase implements RelationalDatabase {
    private $driver;
    private $hostName;
    private $engine;
    private $credentials;
    public function __construct($driver, $hostName, $engine, Credentials $credentials = null) {

        if (empty($driver) || !is_string($driver)) {
            throw new RelationalDatabaseException('The driver must be a non-empty string.');
        }

        if (empty($hostName) || !is_string($hostName)) {
            throw new RelationalDatabaseException('The hostName must be a non-empty string.');
        }

        if (empty($engine) || !is_string($engine)) {
            throw new RelationalDatabaseException('The engine must be a non-empty string.');
        }

        $this->driver = $driver;
        $this->hostName = $hostName;
        $this->engine = $engine;
        $this->credentials = $credentials;

    }

    public function getDriver() {
        return $this->driver;
    }

    public function getHostName() {
        return $this->hostName;
    }

    public function getEngine() {
        return $this->engine;
    }

    public function hasCredentials() {
        return !empty($this->credentials);
    }

    public function getCredentials() {
        return $this->credentials;
    }

    public function getData() {
        $output = [
            'driver' => $this->driver,
            'hostname' => $this->hostName,
            'engine' => $this->engine
        ];

        if ($this->hasCredentials()) {
            $output['credentials'] = $this->credentials->getData();
        }

        return $output;
    }

}
