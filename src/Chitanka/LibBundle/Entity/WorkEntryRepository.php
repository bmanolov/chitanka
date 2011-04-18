<?php

namespace Chitanka\LibBundle\Entity;

class WorkEntryRepository extends EntityRepository
{
	public function getLatest($limit = null)
	{
		return $this->getByIds($this->getLatestIdsByDate($limit), 'e.date DESC');
	}


	public function getLatestIdsByDate($limit = null)
	{
		$dql = sprintf('SELECT e.id FROM %s e ORDER BY e.date DESC', $this->getEntityName());
		$query = $this->_em->createQuery($dql)->setMaxResults($limit);

		return $query->getResult('id');
	}


	public function getByTitleOrAuthor($title, $limit = null)
	{
		return $this->getQueryBuilder()
			->where('e.title LIKE ?1 OR e.author LIKE ?1')
			->setParameter(1, "%$title%")
			->getQuery()
			->getArrayResult();
	}


	public function getQueryBuilder($orderBys = null)
	{
		$qb = parent::getQueryBuilder($orderBys)
			->select('e', 'u')
			->leftJoin('e.user', 'u');

		return $qb;
	}

}
