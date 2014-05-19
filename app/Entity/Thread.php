<?php namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\EntityManager as DoctrineEntityManager;
use FOS\CommentBundle\Entity\Thread as BaseThread;

/**
 * @ORM\Entity
 * @ORM\Table(name="thread")
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 */
class Thread extends BaseThread {
	/**
	 * @var string
	 *
	 * @ORM\Id
	 * @ORM\Column(type="string")
	 */
	protected $id;

	/**
	 * @ORM\OneToOne(targetEntity="WorkEntry", mappedBy="comment_thread")
	 */
	private $workEntry;
//	private $person;
//	private $book;
//	private $text;

	public function isForWorkEntry() {
		return strpos($this->id, 'WorkEntry:') === 0;
	}

	public function getTarget(DoctrineEntityManager $em) {
		list($entity, $id) = explode(':', $this->id);
		$repo = $em->getRepository("App:$entity");
		return $repo ? $repo->find($id) : null;
	}

	/** @return WorkEntry */
	public function getWorkEntry() { return $this->workEntry; }

	public function getPermalink() {
		return rawurldecode(parent::getPermalink());
	}
}
