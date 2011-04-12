<?php

namespace Chitanka\LibBundle\Controller;

use Chitanka\LibBundle\Pagination\Pager;
use Chitanka\LibBundle\Legacy\Setup;

class BookController extends Controller
{
	protected $repository = 'Book';

	public function indexAction()
	{
		$this->view = array(
			'categories' => $this->getRepository('Category')->getAllAsTree(),
		);

		return $this->display('index');
	}

	public function listAction($slug, $page)
	{
		$page = (int)$page;
		$bookRepo = $this->getRepository('Book');
		$category = $this->getRepository('Category')->findBySlug($slug);
		$limit = 30;

		$this->view = array(
			'category' => $category,
			'parents' => array_reverse($category->getAncestors()),
			'books' => $bookRepo->getByCategory($category, $page, $limit),
			'pager'    => new Pager(array(
				'page'  => $page,
				'limit' => $limit,
				'total' => $category->getNrOfBooks()
			)),
			'route' => 'books_by_category',
			'route_params' => array('slug' => $slug),
		);

		return $this->display('list');
	}

	public function showAction($id, $_format)
	{
		list($id) = explode('-', $id); // remove optional slug
		$book = $this->getRepository('Book')->get($id);
		if ( ! $book) {
			throw new NotFoundHttpException("Няма книга с номер $id.");
		}

		switch ($_format) {
			case 'sfb.zip':
			case 'txt.zip':
			case 'fb2.zip':
			case 'epub':
				return $this->urlRedirect($this->processDownload($id, $_format));
		}

		$this->view = array(
			'book' => $book,
			'authors' => $book->getAuthors(),
			'template' => $book->getTemplateAsXhtml(),
			'annotation' => $book->getAnnotationAsXhtml(),
			'info' => $book->getExtraInfoAsXhtml(),
		);

		if ($book->getType() == 'pic') {
			Setup::doSetup($this->container);
		}

		return $this->display('show');
	}


	public function randomAction()
	{
		$id = $this->getRepository('Book')->getRandomId();

		return $this->urlRedirect($this->generateUrl('book_show', array('id' => $id)));
	}

	protected function processDownload($bookId, $format)
	{
		$dlSite = $this->getMirrorServer();
		if ( $dlSite !== false ) {
			return sprintf('%s/book/%d.%s', $dlSite, $bookId, $format);
		}

		$file = null;
		$book = Book::newFromId($bookId);
		$dlFile = new DownloadFile;
		switch ($format) {
			case 'sfb.zip':
			default:
				$file = $dlFile->getSfbForBook($book);
				break;
			case 'txt.zip':
				$file = $dlFile->getTxtForBook($book);
				break;
			case 'fb2.zip':
				$file = $dlFile->getFb2ForBook($book);
				break;
			case 'epub':
				$file = $dlFile->getEpubForBook($book);
				break;
		}

		return $file;
	}

}
