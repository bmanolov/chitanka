<?php namespace App\Entity;

use Doctrine\ORM\EntityManager as DoctrineEntityManager;

class EntityManager {

	private $em;

	public function __construct(DoctrineEntityManager $em) {
		$this->em = $this->configureEntityManager($em);
	}

	/**
	 * @param string $entityName
	 * @return EntityRepository
	 * @see DoctrineEntityManager::getRepository()
	 */
	public function getRepository($entityName) {
		if (strpos($entityName, ':') === false && strpos($entityName, '\\') === false) {
			$entityName = "App:$entityName";
		}
		return $this->em->getRepository($entityName);
	}

	/** @return BookRepository */
	public function getBookRepository() { return $this->getRepository('Book'); }
	/** @return \Doctrine\ORM\EntityRepository */
	public function getBookTextRepository() { return $this->getRepository('BookText'); }
	/** @return BookmarkRepository */
	public function getBookmarkRepository() { return $this->getRepository('Bookmark'); }
	/** @return BookmarkFolderRepository */
	public function getBookmarkFolderRepository() { return $this->getRepository('BookmarkFolder'); }
	/** @return BookRevisionRepository */
	public function getBookRevisionRepository() { return $this->getRepository('BookRevision'); }
	/** @return CategoryRepository */
	public function getCategoryRepository() { return $this->getRepository('Category'); }
	/** @return FeaturedBookRepository */
	public function getFeaturedBookRepository() { return $this->getRepository('FeaturedBook'); }
	/** @return ForeignBookRepository */
	public function getForeignBookRepository() { return $this->getRepository('ForeignBook'); }
	/** @return LabelRepository */
	public function getLabelRepository() { return $this->getRepository('Label'); }
	/** @return NextIdRepository */
	public function getNextIdRepository() { return $this->getRepository('NextId'); }
	/** @return PersonRepository */
	public function getPersonRepository() { return $this->getRepository('Person'); }
	/** @return SearchStringRepository */
	public function getSearchStringRepository() { return $this->getRepository('SearchString'); }
	/** @return SequenceRepository */
	public function getSequenceRepository() { return $this->getRepository('Sequence'); }
	/** @return SeriesRepository */
	public function getSeriesRepository() { return $this->getRepository('Series'); }
	/** @return SiteRepository */
	public function getSiteRepository() { return $this->getRepository('Site'); }
	/** @return SiteNoticeRepository */
	public function getSiteNoticeRepository() { return $this->getRepository('SiteNotice'); }
	/** @return TextRepository */
	public function getTextRepository() { return $this->getRepository('Text'); }
	/** @return TextCommentRepository */
	public function getTextCommentRepository() { return $this->getRepository('TextComment'); }
	/** @return TextLabelLogRepository */
	public function getTextLabelLogRepository() { return $this->getRepository('TextLabelLog'); }
	/** @return TextRatingRepository */
	public function getTextRatingRepository() { return $this->getRepository('TextRating'); }
	/** @return TextRevisionRepository */
	public function getTextRevisionRepository() { return $this->getRepository('TextRevision'); }
	/** @return UserRepository */
	public function getUserRepository() { return $this->getRepository('User'); }
	/** @return UserTextContribRepository */
	public function getUserTextContribRepository() { return $this->getRepository('UserTextContrib'); }
	/** @return UserTextReadRepository */
	public function getUserTextReadRepository() { return $this->getRepository('UserTextRead'); }
	/** @return \Doctrine\ORM\EntityRepository */
	public function getWikiSiteRepository() { return $this->getRepository('WikiSite'); }
	/** @return WorkEntryRepository */
	public function getWorkEntryRepository() { return $this->getRepository('WorkEntry'); }
	/** @return \Doctrine\ORM\EntityRepository */
	public function getWorkContribRepository() { return $this->getRepository('WorkContrib'); }

	/**
	 * @param object $entity
	 * @return object
	 * @see DoctrineEntityManager::merge()
	 */
	public function merge($entity) {
		return $this->em->merge($entity);
	}

	/**
	 * A proxy to Doctrine EntityManager methods
	 * @param string $name
	 * @param array $arguments
	 * @return mixed
	 */
	public function __call($name, $arguments) {
		return call_user_func_array(array($this->em, $name), $arguments);
	}

	private function configureEntityManager(DoctrineEntityManager $em) {
		$em->getConfiguration()->addCustomHydrationMode('id', 'App\Hydration\IdHydrator');
		$em->getConfiguration()->addCustomHydrationMode('key_value', 'App\Hydration\KeyValueHydrator');
		return $em;
	}
}
