<?php
namespace Zeedhi\Framework\DataSource\Manager\LogicalToRealDelete;

use Zeedhi\Framework\DataSource\DataSet;
use Zeedhi\Framework\DataSource\FilterCriteria;
use Zeedhi\Framework\DataSource\Manager;

class ManagerImpl implements Manager {

	/** @var Manager */
	protected $manager;
	/** @var string */
	protected $logicalDeleteColumn;
	/** @var mixed */
	protected $deletedValue;
	/** @var mixed */
	protected $nonDeletedValue;

	public function __construct(Manager $manager, $logicalDeleteColumn, $deletedValue, $nonDeletedValue = null) {
		$this->manager = $manager;
		$this->logicalDeleteColumn = $logicalDeleteColumn;
		$this->deletedValue = $deletedValue;
		$this->nonDeletedValue = $nonDeletedValue !== null ? $nonDeletedValue : !$deletedValue;
	}


	/**
	 * Persist all given rows in DataSet.
	 *
	 * @param DataSet $dataSet The collection and description of rows.
	 *
	 * @return array Rows with primary key columns values.
	 */
	public function persist(DataSet $dataSet) {
		$rowsToPersist = array();
		$rowsToDelete = array();
		foreach ($dataSet->getRows() as $row) {
			if ($this->isDeleted($row)) {
				$rowsToDelete[] = $row;
			} else {
				$rowsToPersist[] = $row;
			}
		}
		$dataSetToDelete = new DataSet($dataSet->getDataSourceName(), $rowsToDelete);
		$dataSetToPersist = new DataSet($dataSet->getDataSourceName(), $rowsToPersist);
		$deletedRows = $this->manager->delete($dataSetToDelete);
		$persistedRows = $this->manager->persist($dataSetToPersist);
		return array_merge($deletedRows, $persistedRows);
	}

	/**
	 * Delete all given rows in DataSet.
	 *
	 * @param DataSet $dataSet The collection and description of rows.
	 *
	 * @return array Rows with primary key columns values.
	 */
	public function delete(DataSet $dataSet) {
		return $this->manager->delete($dataSet);
	}

	/**
	 * Return a DataSet with rows that match the given criteria.
	 *
	 * @param FilterCriteria $filterCriteria
	 *
	 * @return DataSet The result of the filter criteria.
	 */
	public function findBy(FilterCriteria $filterCriteria) {
		$dataSet = $this->manager->findBy($filterCriteria);
		$rows = array();
		foreach ($dataSet->getRows() as $row) {
			$row[$this->logicalDeleteColumn] = $this->nonDeletedValue;
			$rows[] = $row;
		}
		return new DataSet($dataSet->getDataSourceName(), $rows);
	}

	/**
	 * @param $row
	 *
	 * @return bool
	 */
	private function isDeleted($row) {
		return $row[$this->logicalDeleteColumn] === $this->deletedValue;
	}
}