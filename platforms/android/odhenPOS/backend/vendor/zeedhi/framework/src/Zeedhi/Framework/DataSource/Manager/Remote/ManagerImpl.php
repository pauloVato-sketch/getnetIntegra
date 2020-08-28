<?php
namespace Zeedhi\Framework\DataSource\Manager\Remote;

use Zeedhi\Framework\DTO\Request;
use Zeedhi\Framework\DTO\Response;
use Zeedhi\Framework\DTO\Row;
use Zeedhi\Framework\DataSource\AssociatedWithDataSource;
use Zeedhi\Framework\DataSource\DataSet;
use Zeedhi\Framework\DataSource\FilterCriteria;
use Zeedhi\Framework\DataSource\Manager;
use Zeedhi\Framework\Remote\Server;
use Zeedhi\Framework\Remote\RequestFactory;

class ManagerImpl implements Manager {

    /** @var Server */
    protected $remoteServer;
    /** @var RequestFactory */
    protected $requestFactory;
    /** @var RequestProvider */
    protected $requestProvider;

    /**
     * __construct
     *
     * @param Server          $remoteServer
     * @param RequestFactory  $requestFactory
     * @param RequestProvider $requestProvider
     */
    public function __construct(Server $remoteServer, RequestFactory $requestFactory, RequestProvider $requestProvider) {
        $this->remoteServer    = $remoteServer;
        $this->requestFactory  = $requestFactory;
        $this->requestProvider = $requestProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function persist(DataSet $dataSet) {
        return $this->proxyDataSetRequest($dataSet);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(DataSet $dataSet) {
        return $this->proxyDataSetRequest($dataSet);
    }

    protected function proxyDataSetRequest(DataSet $dataSet) {
        $remoteRequest = $this->factoryDataSetRequest($dataSet);
        $remoteResponse = $this->remoteServer->request($remoteRequest);
        return $this->getDataSetFromResponse($dataSet->getDataSourceName(), $remoteResponse)->getRows();
    }

    /**
     * {@inheritdoc}
     */
    public function findBy(FilterCriteria $filterCriteria) {
        $remoteRequest = $this->factoryFilterRequest($filterCriteria);
        $remoteResponse = $this->remoteServer->request($remoteRequest);

        return $this->getDataSetFromResponse($filterCriteria->getDataSourceName(), $remoteResponse);
    }

    protected function getDataSetFromResponse($dataSourceName, Response $response) {
        $this->checkForRemoteError($response);
        $dataSets = $response->getDataSets();
        return $this->getDataSet($dataSourceName, $dataSets);
    }

    protected function checkForRemoteError(Response $response) {
        $remoteError = $response->getError();
        if ($remoteError !== null) {
            throw Exception::errorOnRemoteServer($remoteError->getMessage());
        }
    }

    protected function getDataSet($dataSetName, $dataSetList) {
        foreach ($dataSetList as $dataSet) {
            if ($dataSet->getDataSourceName() === $dataSetName) {
                return $dataSet;
            }
        }

        throw Exception::dataSetNotFound($dataSetName);
    }

    protected function createRemoteRequest(callable $cbk, $obj) {
        $request = $this->requestProvider->getRequest();
        $userId = $request->getUserId();
        $method = $request->getMethod();
        $route  = $request->getRoutePath();
        $this->requestFactory->setUserId($userId);
        return call_user_func($cbk, $method, $route, $obj);
    }

    protected function factoryDataSetRequest(DataSet $dataSet) {
        return $this->createRemoteRequest(array($this->requestFactory, 'createDataSetRequest'), $dataSet);
    }


    protected function factoryFilterRequest(FilterCriteria $filterCriteria) {
        return $this->createRemoteRequest(array($this->requestFactory, 'createFilterRequest'), $filterCriteria);
    }

}