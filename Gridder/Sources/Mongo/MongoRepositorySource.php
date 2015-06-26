<?php

namespace Gridder\Sources\Mongo;


use Doctrine\ODM\MongoDB\DocumentRepository;

/**
 * @author Martin Bažík <martin@bazo.sk>
 */
class MongoRepositorySource extends MongoQueryBuilderSource
{

	/** @var DocumentRepository */
	private $repository;

	public function __construct(DocumentRepository $repository)
	{
		$this->repository	 = $repository;
		$this->builder		 = $repository->createQueryBuilder()->eagerCursor(TRUE);
	}


}
