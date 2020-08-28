<?php
namespace Zeedhi\Framework\DataSource\Manager\DataColumnToColumnConversion;

use Zeedhi\Framework\DataSource\Configuration;
use Zeedhi\Framework\DataSource\DataSet;
use Zeedhi\Framework\DataSource\Exception;
use Zeedhi\Framework\DataSource\FilterCriteria;
use Zeedhi\Framework\DataSource\Manager;
use Zeedhi\Framework\DataSource\Operator\DefaultOperator;

class ManagerImpl implements Manager{

    /** @var Manager */
    protected $manager;
    /** @var Manager\Doctrine\NameProvider */
    protected $nameProvider;
    /** @var Configuration */
    protected $config;

    public function __construct(Manager $manager, Manager\Doctrine\NameProvider $nameProvider) {
        $this->manager = $manager;
        $this->nameProvider = $nameProvider;
    }

    /**
     * @param string $dataSourceName
     * @return Configuration
     */
    protected function setConfigByDataSourceName($dataSourceName) {
        return $this->config = $this->nameProvider->getDataSourceByName($dataSourceName);
    }

    /**
     * @param DataSet $dataSet
     */
    protected function setConfigByDataSet(DataSet $dataSet) {
        $this->setConfigByDataSourceName($dataSet->getDataSourceName());
    }

    /**
     * @param FilterCriteria $filterCriteria
     */
    protected function setConfigByFilterCriteria(FilterCriteria $filterCriteria) {
        $this->setConfigByDataSourceName($filterCriteria->getDataSourceName());
    }

    /**
     * @param DataSet $dataSet
     * @return DataSet
     */
    protected function convertDataSetToColumnNames(DataSet $dataSet) {
        $convertedRows = array();
        foreach ($dataSet->getRows() as $row) {
            $convertedRow = array();
            foreach ($row as $dataColumn => $value) {
                try {
                    $columnName = $this->config->getColumnByDataColumn($dataColumn);
                } catch (Exception $e) {
                    $columnName = $dataColumn;
                }
                $convertedRow[$columnName] = $value;
            }

            $convertedRows[] = $convertedRow;
        }

        return new DataSet($dataSet->getDataSourceName(), $convertedRows);
    }

    /**
     * @param DataSet $dataSet
     * @return DataSet
     * @throws \Zeedhi\Framework\DataSource\Exception
     */
    protected function convertDataSetToDataColumnNames(DataSet $dataSet) {
        $convertedRows = array();
        $columns = $this->config->getColumns();
        foreach ($dataSet->getRows() as $row) {
            $convertedRow = array();
            foreach ($row as $column => $value) {
                if (in_array($column, $columns)) {
                    $convertedRow[$this->config->getDataColumnByColumn($column)] = $value;
                } else {
                    $convertedRow[$column] = $value;
                }
            }

            $convertedRows[] = $convertedRow;
        }

        return new DataSet($dataSet->getDataSourceName(), $convertedRows);
    }

    /**
     * @param $persistedRows
     * @return array
     * @throws \Zeedhi\Framework\DataSource\Exception
     */
    protected function convertPkRows($persistedRows) {
        $convertedRows = array();
        foreach ($persistedRows as $persistedRow) {
            $convertedRow = array();
            foreach ($persistedRow as $column => $value) {
                $convertedRow[$this->config->getDataColumnByColumn($column)] = $value;
            }

            $convertedRows[] = $convertedRow;
        }

        return $convertedRows;
    }

    /**
     * Persist all given rows in DataSet.
     *
     * @param DataSet $dataSet The collection and description of rows.
     *
     * @return array Rows with primary key columns values.
     */
    public function persist(DataSet $dataSet) {
        $this->setConfigByDataSet($dataSet);
        return $this->convertPkRows($this->manager->persist($this->convertDataSetToColumnNames($dataSet)));
    }

    /**
     * Delete all given rows in DataSet.
     *
     * @param DataSet $dataSet The collection and description of rows.
     *
     * @return array Rows with primary key columns values.
     */
    public function delete(DataSet $dataSet) {
        $this->setConfigByDataSet($dataSet);
        return $this->convertPkRows($this->manager->delete($this->convertDataSetToColumnNames($dataSet)));
    }

    /**
     * @param FilterCriteria $filterCriteria
     * @return FilterCriteria
     */
    protected function convertFilterCriteria(FilterCriteria $filterCriteria) {
        $convertedConditions = array();
        $conditions = $filterCriteria->getConditions();
        foreach ($conditions as $condition) {
            $convertedConditions[] = array(
                "columnName" => $this->convertConditionDataColumnToColumn($condition),
                "value"      => $condition["value"],
                "operator"   => $condition["operator"]
            );
        }

        $convertedFilterCriteria = new FilterCriteria(
            $filterCriteria->getDataSourceName(),
            $convertedConditions,
            $filterCriteria->getPage(),
            $filterCriteria->getPageSize()
        );

        foreach($filterCriteria->getOrderBy() as $column => $value) {
            $convertedFilterCriteria->addOrderBy($column, $value);
        }

        return $convertedFilterCriteria;
    }

    /**
     * Return a DataSet with rows that match the given criteria.
     *
     * @param FilterCriteria $filterCriteria
     *
     * @return DataSet The result of the filter criteria.
     */
    public function findBy(FilterCriteria $filterCriteria) {
        $this->setConfigByFilterCriteria($filterCriteria);
        return $this->convertDataSetToDataColumnNames($this->manager->findBy($this->convertFilterCriteria($filterCriteria)));
    }

    /**
     * @param $condition
     * @return string
     * @throws \Zeedhi\Framework\DataSource\Exception
     */
    protected function convertConditionDataColumnToColumn($condition) {
        return DefaultOperator::factoryFromStringRepresentation($condition['operator'], $this->config)
            ->convertDataColumnForColumn($condition["columnName"]);
    }
}