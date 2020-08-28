<?php
namespace Zeedhi\Framework\DataSource\Manager\IdProvider;

use Zeedhi\Framework\DataSource\Configuration;
use Zeedhi\Framework\DataSource\Manager;
use Zeedhi\Framework\DataSource\FilterCriteria;
use Zeedhi\Framework\DataSource\DataSet;


class ManagerImpl implements Manager {

    /** @var Manager */
    protected $manager;
    /** @var \Zeedhi\Framework\DataSource\Manager\IdProvider\IdProvider */
    protected $idProvider;
    /** @var Manager\Doctrine\NameProvider */
    protected $nameProvider;
    /** @var string */
    protected $dataSourceName;
    /** @var Configuration */
    protected $dataSourceConfig; 
    
    public function __construct(Manager\Doctrine\NameProvider $nameProvider, Manager $manager, IdProvider $idProvider){
        $this->manager = $manager;
        $this->idProvider = $idProvider;
        $this->nameProvider = $nameProvider; 
    }
    
    public function persist(DataSet $dataSet){
        $this->dataSourceName = $dataSet->getDataSourceName();
        $this->dataSourceConfig = $this->nameProvider->getDataSourceByName($this->dataSourceName);
        $sequenceName = $this->dataSourceConfig->getSequentialColumn();
        
        foreach($dataSet->getRows() as $row){
            if ($row['__is_new']) {
                $row[$sequenceName] = $this->idProvider->getNextId();
            }
        }
        
        return $this->manager->persist($dataSet);
    }

    
    public function delete(DataSet $dataSet){
        return $this->manager->delete($dataSet);
    }
    
    
    public function findBy(FilterCriteria $filterCriteria){
        return $this->manager->findBy($filterCriteria);
    }
    
    
}