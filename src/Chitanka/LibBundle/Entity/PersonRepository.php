<?php

namespace Chitanka\LibBundle\Entity;

class PersonRepository extends EntityRepository
{
	protected
		/* role bit: 1 - author, 2 - translator, 3 - both */
		$sqlRole = null;


	public function getBy($filters, $page = 1, $limit = null)
	{
		$query = $this->setPagination($this->getQueryBy($filters), $page, $limit);

		return $query->getArrayResult();
	}

	public function countBy($filters)
	{
		return $this->getCountQueryBy($filters)->getSingleScalarResult();
	}

	public function getBySlug($slug)
	{
		return $this->findOneBy(array('slug' => $slug));
	}


	public function getQueryBy($filters)
	{
		$qb = $this->getQueryBuilder();
		$qb = $this->addFilters($qb, $filters);

		return $qb->getQuery();
	}

	public function getCountQueryBy($filters)
	{
		$qb = $this->getCountQueryBuilder();
		$qb = $this->addFilters($qb, $filters);

		return $qb->getQuery();
	}


	public function getQueryBuilder($orderBys = null)
	{
		$qb = $this->createQueryBuilder('p')
			->select('p', 'pp')
			->leftJoin('p.person', 'pp');
		if ($this->sqlRole) {
			$qb->andWhere("p.role IN ($this->sqlRole)");
		}

		return $qb;
	}

	public function getCountQueryBuilder($alias = 'p')
	{
		$qb = $this->createQueryBuilder($alias)->select('COUNT(p.id)');
		if ($this->sqlRole) {
			$qb->andWhere("p.role IN ($this->sqlRole)");
		}

		return $qb;
	}

	public function getCount($where = array())
	{
		return $this->getCountQueryBuilder()->andWhere($where)
			->getQuery()->getSingleScalarResult();
	}

	public function getCountsByCountry()
	{
		return $this->getCountQueryBuilder()
			->select('p.country', 'COUNT(p.id)')
			->groupBy('p.country')
			->getQuery()->getResult('key_value');
	}


	public function getByNames($name, $limit = 20)
	{
		return $this->getQueryBuilder()
			->where('p.name LIKE ?1 OR p.orig_name LIKE ?1')
			->setParameter(1, "%$name%")
			->getQuery()->setMaxResults($limit)
			->getArrayResult();
	}


	public function asAuthor()
	{
		$this->sqlRole = '1,3';

		return $this;
	}

	public function asTranslator()
	{
		$this->sqlRole = '2,3';

		return $this;
	}


	public function addFilters($qb, $filters)
	{
		$nameField = empty($filters['by']) || $filters['by'] == 'first' ? 'p.name' : 'p.last_name';
		$qb->addOrderBy($nameField);
		if ( ! empty($filters['prefix']) && $filters['prefix'] != '-' ) {
			$qb->andWhere("$nameField LIKE :name")->setParameter('name', $filters['prefix'].'%');
		}

		if ( ! empty($filters['country']) ) {
			$qb->andWhere('p.country = ?1')->setParameter(1, $filters['country']);
		}

		return $qb;
	}
}
