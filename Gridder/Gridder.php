<?php

namespace Gridder;

use Gridder\Operation;
use Doctrine\ORM\EntityRepository;
use Nette\Application\UI\Control;
use Gridder\Persisters\IPersister;
use Nette\Application\UI\Form;
use Nette\ComponentModel\IContainer;

/**
 * Gridder
 *
 * @author Martin
 */
class Gridder extends Control
{

	/** @var Sources\Source */
	private $source;
	private $columns = [];
	private $actionColumns = [];
	private $operations = [];
	private $filters = [];
	private $primaryKey = 'id';
	private $hasOperations;

	/** @var array */
	private $paginatorOptions = [
		'displayedItems' => [2, 5, 10, 20, 30, 40, 50, 100, 200, 500, 1000, 10000],
		'defaultItem' => '10'
	];
	private $translator;

	/** @var Persisters\Persister */
	private $persister;
	private $itemsPerPage = 10;
	private $page = 1;

	/** @var \Nette\Application\UI\Presenter */
	private $presenter;
	private $totalCount;
	private $class;

	private $neo4jMode = FALSE;
	private $neo4jColumn = '';
	/**
	 * @internal 
	 * @var bool
	 */
	public $hasFilters;

	/** @var bool */
	public $autoAddFilters = FALSE;


	const ORDER_BY_ASC = 'up';
	const ORDER_BY_DESC = 'down';

	/**
	 * @return \Gridder\Gridder
	 */
	public function enableNeo4jMode($column)
	{
		$this->neo4jMode = TRUE;
		$this->neo4jColumn = $column;
		return $this;
	}

	/**
	 * Returns paginator options
	 * @return array
	 */
	public function getPaginatorOptions()
	{
		return $this->paginatorOptions;
	}


	/**
	 * Set the translator
	 * @param type $translator 
	 */
	public function setTranslator($translator)
	{
		$this->translator = $translator;
		$this->template->setTranslator($translator);
	}


	/**
	 * Set the datasource for the table rows
	 * @param Sources\Source $source 
	 */
	public function setSource(Sources\Source $source)
	{
		$this->source = $source;
		$this->primaryKey = $source->getPrimaryKey();
		return $this;
	}


	public function setPersister($persister)
	{
		$this->persister = $persister;
		return $this;
	}


	public function setPresenter(\Nette\Application\UI\Presenter $presenter)
	{
		$this->presenter = $presenter;
		return $this;
	}


	/**
	 * Sets the primary key for the records, overrides the datasource setting
	 * @param string $primaryKey
	 * @return Gridder 
	 */
	public function setPrimaryKey($primaryKey)
	{
		$this->primaryKey = $primaryKey;
		return $this;
	}


	public function setInitialItemsPerPage($itemsPerPage)
	{
		$this->itemsPerPage = $itemsPerPage;
		$this->paginatorOptions['defaultItem'] = $itemsPerPage;
		if (array_search($itemsPerPage, $this->paginatorOptions['displayedItems']) == FALSE) {
			array_push($this->paginatorOptions['displayedItems'], $itemsPerPage);
			sort($this->paginatorOptions['displayedItems']);
		}
		return $this;
	}


	/**
	 * Adds a column to show
	 * @param type $name
	 * @param type $type
	 * @return Columns\BaseColumn
	 */
	public function addColumn($name, $type = 'text')
	{
		$this->columns[] = $name;
		return ColumnMapper::map($this, $name, $type, $this->autoAddFilters);
	}


	/**
	 * Adds an action column
	 * @param type $name
	 * @return Columns\ActionColumn 
	 */
	public function addActionColumn($name)
	{
		$this->actionColumns[] = $name;
		$actionColumn = new Columns\ActionColumn($this, $name);
		$actionColumn->setPresenter($this->presenter);
		return $actionColumn;
	}


	/**
	 * Adds an operation
	 * @param string|Operation $name
	 * @param Closure|Callback $callback
	 * @return Operation
	 */
	public function addOperation($name, $callback = NULL)
	{
		$this->hasOperations = TRUE;
		if ($name instanceof Operation) {
			if (in_array($name->getName(), array_keys($this->operations))) {
				throw new Exception(sprintf('Operation with name %s already exists', $name));
			}
			$this->operations[$name->getName()] = $name;
			return $this->operations[$name->getName()];
		} else {
			if (in_array($name, array_keys($this->operations))) {
				throw new Exception(sprintf('Operation with name %s already exists', $name));
			}
			$this->operations[$name] = new Operation($name, $callback);

			return $this->operations[$name];
		}
	}


	protected function createComponentFormFilter($name)
	{
		$form = new Form($this, $name);
		$form->getElementPrototype()->class = 'ajax';
		$form->setTranslator($this->translator);

		$renderer = $form->getRenderer();
		//$renderer->wrappers['form']['container'] = \Nette\Utils\Html::el('div')->class('filter-form');
		$renderer->wrappers['form']['errors'] = FALSE;
		$renderer->wrappers['group']['container'] = 'div';
		$renderer->wrappers['group']['label'] = NULL;
		$renderer->wrappers['pair']['container'] = 'div';
		$renderer->wrappers['controls']['container'] = NULL;
		$renderer->wrappers['control']['container'] = '';
		$renderer->wrappers['control']['.odd'] = 'odd';
		$renderer->wrappers['control']['errors'] = TRUE;
		$renderer->wrappers['label']['container'] = '';
		$renderer->wrappers['label']['suffix'] = ':';
		$renderer->wrappers['control']['requiredsuffix'] = " \xE2\x80\xA2";

		if ($this->hasFilters) {
			$filters = $form->addContainer('filters');
			foreach ($this->getComponents(FALSE, 'Gridder\Columns\Column') as $column) {
				if ($column->hasFilter()) {
					$filters->addComponent($column->getFilter(), $column->name);
					if ($form->isSubmitted()) {
						$httpData = $form->getHttpData();
						if (isset($httpData['btnCancelFilters'])) {
							unset($this->persister->filters);
							$this->persister->selectedCheckboxes = [];
							$filters[$column->name]->setValue(NULL);
						}
					} elseif (isset($this->persister->filters[$column->name])) {
						$filters[$column->name]->setDefaultValue($this->persister->filters[$column->name]->getValue());
					}
				}
			}

			$form->addSubmit('btnApplyFilters', 'Použiť filtre')->onClick[] = callback($this, 'saveFilters');
			$form['btnApplyFilters']->getControlPrototype()->class = 'btn btn-success apply';
			$form->addSubmit('btnCancelFilters', 'Zrušiť filtre')->onClick[] = callback($this, 'cancelFilters');
			$form['btnCancelFilters']->getControlPrototype()->class = 'btn btn-danger cancel';
		}
	}


	public function executeOperation(\Nette\Forms\Controls\Button $button)
	{
		$operationName = $button->name;
		$records = $this->persister->selectedCheckboxes;
		$selectedRecordsIds = array_keys(array_filter($records));
		$selectedRecords = $this->source->getRecordsByIds($selectedRecordsIds);

		$operation = $this->operations[$operationName];
		$message = $operation->execute($selectedRecordsIds, $selectedRecords);
		if ($message instanceof Message) {
			$this->flashMessage($message->getMessage(), $message->getType());
			$this->invalidateControl('flash');
		}
	}


	public function saveFilters(\Nette\Forms\Controls\Button $button)
	{
		$values = $button->form->values;

		$filters = $values['filters'];
		$filterObjects = $this->filters;
		foreach ($filters as $filter => $value) {
			$filterObjects[$filter] = $this->getComponent($filter)->getComponent('filter')->getFilter($value); //apply($this->ds, $value);
		}
		$this->persister->filters = $filterObjects;
		$this->invalidateControl();

		$this->persister->selectedCheckboxes = [];
	}


	public function cancelFilters(\Nette\Forms\Controls\Button $button)
	{
		$this->invalidateControl();
	}


	public function addRecordCheckbox($id)
	{
		if (!$this['formOperations']->isSubmitted()) {
			$this['formOperations']['records']->addCheckbox($id);
			if (isset($this->persister->recordCheckboxes)) {
				$checkboxes = $this->persister->recordCheckboxes;
			} else {
				$checkboxes = [];
			}
			$checkboxes[$id] = $id;
			$this->persister->recordCheckboxes = $checkboxes;
		}
	}


	public function setSelectedCheckboxes($checkboxes)
	{
		$values = [];

		foreach ($checkboxes as $id) {
			$values[$id] = TRUE;
		}

		$this->persister->selectedCheckboxes = $values;
		return $this;
	}


	protected function createComponentFormOperations($name)
	{
		$form = new Form($this, $name);
		$form->getElementPrototype()->class = 'ajax';
		$form->setTranslator($this->translator);

		if ($this->hasOperations) {
			$operations = [];
			foreach ($this->operations as $name => $operation) {
				$operations[$name] = $operation->getAlias();
			}
			
			foreach ($this->operations as $name => $operation) {
				$gridder = $this;
				$form->addSubmit($name, $operation->getAlias())->onClick[] = callback($this, 'executeOperation');
			}

			$records = $form->addContainer('records');
			if ($form->isSubmitted()) {
				if (isset($this->persister->recordCheckboxes)) {
					$checkboxes = $this->persister->recordCheckboxes;
					foreach ($checkboxes as $id) {
						$records->addCheckbox($id);
					}
				}
			}
		}
		$form->onSuccess[] = callback($this, 'formOperationsSubmitted');
		return $form;
	}


	public function formOperationsSubmitted(Form $form)
	{
		$values = $form->getValues();

		if (!is_array($this->persister->selectedCheckboxes)) {
			$this->persister->selectedCheckboxes = [];
		}

		$this->persister->selectedCheckboxes = array_merge($this->persister->selectedCheckboxes, (array) $values->records);

		if ($this->presenter->isAjax()) {
			$this->presenter->terminate();
		}
	}


	public function createComponentFormPaginator($name)
	{
		$form = new Form($this, $name);
		$form->getElementPrototype()->class = 'ajax';
		$options = array_combine(array_values($this->paginatorOptions['displayedItems']), $this->paginatorOptions['displayedItems']);
		$pageItems = [];
		for ($i = 1; $i <= $this->persister->totalPages; $i++) {
			$pageItems[$i] = $i;
		}
		$form->addSelect('page', 'Page', $pageItems)->setDefaultValue($this->persister->page);
		$form->addSelect('itemsPerPage', 'Items per page', $options);
		if (isset($this->persister->itemsPerPage)) {
			$form['itemsPerPage']->setDefaultValue($this->persister->itemsPerPage);
		} else {
			$form['itemsPerPage']->setDefaultValue($this->paginatorOptions['defaultItem']);
		}

		$form->addSubmit('btnSubmitPaginator', 'Ok')->getControlPrototype()->class = 'button apply';
		$form->onSuccess[] = callback($this, 'paginatorSubmitted');
	}


	public function paginatorSubmitted(Form $form)
	{
		unset($this->persister->recordCheckboxes);
		$values = $form->values;
		$this->itemsPerPage = (int) $values->itemsPerPage;
		$this->persister->itemsPerPage = $this->itemsPerPage;
		$this->persister->page = $this->page = (int) $values->page;
		$this->invalidateControl();
	}


	public function handleChangePage($page)
	{
		unset($this->persister->recordCheckboxes);
		$this->persister->page = $page;
		$this->invalidateControl();
	}


	public function handleOrderBy($column)
	{
		$ordering = $this->persister->ordering;
		//$keys = array_keys($this->columns);
		$key = array_search($column, $this->columns);

		if (isset($ordering[$key][$column])) {
			$currentDirection = $ordering[$key][$column];
			switch ($currentDirection) {
				case self::ORDER_BY_DESC:
					$direction = self::ORDER_BY_ASC;
					$ordering[$key] = [$column => $direction];
					break;

				case self::ORDER_BY_ASC:
					unset($ordering[$key]);
					break;
			}
		} else {
			$direction = self::ORDER_BY_DESC;
			$ordering[$key] = [$column => $direction];
		}
		ksort($ordering);
		$this->persister->ordering = $ordering;
		$this->invalidateControl();
	}


	public function handleReset()
	{
		$this->persister->reset();
		$this->invalidateControl();
	}


	public function getClass()
	{
		return $this->class;
	}


	public function setClass($class)
	{
		$this->class = $class;
		return $this;
	}


	public function isOrderedByColumn($column)
	{
		foreach ($this->persister->ordering as $sort) {
			if (key($sort) === $column) {
				return TRUE;
			}
		}
		return FALSE;
	}


	public function getColumnOrder($column)
	{
		foreach ($this->persister->ordering as $sort) {
			if (key($sort) === $column) {
				return $sort[$column];
			}
		}
	}


	public function render()
	{
		if($this->neo4jMode) {
			$this->template->setFile(__DIR__ . '/neo4jTemplate.latte');
			$this->template->columnPrefix = $this->neo4jColumn;
		} else {
			$this->template->setFile(__DIR__ . '/template.latte');
		}
		$this->template->columns = $this->columns;
		$this->template->actionColumns = $this->actionColumns;

		if (isset($this->persister->itemsPerPage)) {
			$this->itemsPerPage = $this->persister->itemsPerPage;
		}
		try {
			$totalCount = $this->source->applyFilters($this->persister->filters)->getTotalCount();
		} catch (\Exception $e) {
			$totalCount = 0;
			$this->flashMessage('Neprípustný rozsah dátumov.', 'error');
		}

		$this->persister->totalPages = $this->template->totalPages = (int) ceil($totalCount / $this->itemsPerPage);

		if (isset($this->persister->page)) {
			$this->page = $this->persister->page;
		}
		if ($this->page > $this->template->totalPages) {
			$this->page = $this->persister->page = 1;
		}

		$this->template->page = $this->page;
		$limit = $this->itemsPerPage;
		$offset = ($this->page - 1) * $limit;

		//apply sorting

		if ($this->persister->ordering === NULL) {
			$this->persister->ordering = [];
		}

		$this->source->applySorting($this->persister->ordering);

		try {
			$rows = $this->source->limit($offset, $limit)->getRows();
		} catch (\Exception $e) {
			//$this->flashMessage('Neprípustný rozsah dátumov.');
			$rows = [];
		}
		$this->template->nextPage = $this->page + 1;
		$this->template->previousPage = $this->page - 1;
		$this->template->from = $offset + 1;
		$this->template->to = $offset + $limit;

		if ($this->template->to > $totalCount) {
			$this->template->to = $totalCount;
		}
		$this->template->totalRecords = $this->totalCount = $totalCount;
		$this->template->primaryKey = $this->primaryKey;
		$this->template->hasOperations = $this->hasOperations;

		if (!$this->source->supportsFiltering()) {
			$this->hasFilters = FALSE;
		}
		$this->template->hasFilters = $this->hasFilters;
		$this->template->supportsSorting = $this->source->supportsSorting();
		$this->template->rows = $rows;
		$this->template->operations = $this->operations;
		$this->template->selectedCheckboxes = $this->persister->selectedCheckboxes;
		$this->template->class = $this->class;
		$this->template->ordering = $this->persister->ordering;
		$this->template->render();
	}


}

