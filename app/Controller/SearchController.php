<?php namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\SearchString;
use App\Util\String;

class SearchController extends Controller {

	private $minQueryLength = 3;
	private $maxQueryLength = 60;

	public function indexAction($_format) {
		if ($_format == 'osd') {
			return $this->display("index.$_format");
		}
		if (($query = $this->getQuery($_format)) instanceof Response) {
			return $query;
		}

		$lists = array(
			'persons'      => $this->em->getPersonRepository()->getByNames($query['text'], 15),
			'texts'        => $this->em->getTextRepository()->getByTitles($query['text'], 15),
			'books'        => $this->em->getBookRepository()->getByTitles($query['text'], 15),
			'series'       => $this->em->getSeriesRepository()->getByNames($query['text'], 15),
			'sequences'    => $this->em->getSequenceRepository()->getByNames($query['text'], 15),
			'work_entries' => $this->em->getWorkEntryRepository()->getByTitleOrAuthor($query['text']),
			'labels'       => $this->em->getLabelRepository()->getByNames($query['text']),
			'categories'   => $this->em->getCategoryRepository()->getByNames($query['text']),
		);

		$found = array_sum(array_map('count', $lists)) > 0;

		if ($found) {
			$this->logSearch($query['text']);
		} else {
			$this->responseStatusCode = 404;
		}

		$this->view = array(
			'query' => $query,
			'found' => $found,
		) + $lists;

		return $this->display("index.$_format");
	}

	public function personsAction(Request $request, $_format) {
		if ($_format == 'osd') {
			return $this->display("Person:search.$_format");
		}
		if ($_format == 'suggest') {
			$items = $descs = $urls = array();
			$query = $request->query->get('q');
			$persons = $this->em->getPersonRepository()->getByQuery(array(
				'text'  => $query,
				'by'    => 'name',
				'match' => 'prefix',
				'limit' => 10,
			));
			foreach ($persons as $person) {
				$items[] = $person['name'];
				$descs[] = '';
				$urls[] = $this->generateUrl('person_show', array('slug' => $person['slug']), true);
			}

			return $this->displayJson(array($query, $items, $descs, $urls));
		}
		if (($query = $this->getQuery($_format)) instanceof Response) {
			return $query;
		}

		if (empty($query['by'])) {
			$query['by'] = 'name,orig_name,real_name,orig_real_name';
		}
		$persons = $this->em->getPersonRepository()->getByQuery($query);
		if ( ! ($found = count($persons) > 0)) {
			$this->responseStatusCode = 404;
		}
		$this->view = array(
			'query'   => $query,
			'persons' => $persons,
			'found'   => $found,
		);

		return $this->display("Person:search.$_format");
	}

	public function authorsAction(Request $request, $_format) {
		if ($_format == 'suggest') {
			$items = $descs = $urls = array();
			$query = $request->query->get('q');
			$persons = $this->em->getPersonRepository()->asAuthor()->getByQuery(array(
				'text'  => $query,
				'by'    => 'name',
				'match' => 'prefix',
				'limit' => 10,
			));
			foreach ($persons as $person) {
				$items[] = $person['name'];
				$descs[] = '';
				$urls[] = $this->generateUrl('author_show', array('slug' => $person['slug']), true);
			}

			return $this->displayJson(array($query, $items, $descs, $urls));
		}
		return $this->display("Author:search.$_format");
	}

	public function translatorsAction(Request $request, $_format) {
		if ($_format == 'suggest') {
			$items = $descs = $urls = array();
			$query = $request->query->get('q');
			$persons = $this->em->getPersonRepository()->asTranslator()->getByQuery(array(
				'text'  => $query,
				'by'    => 'name',
				'match' => 'prefix',
				'limit' => 10,
			));
			foreach ($persons as $person) {
				$items[] = $person['name'];
				$descs[] = '';
				$urls[] = $this->generateUrl('translator_show', array('slug' => $person['slug']), true);
			}

			return $this->displayJson(array($query, $items, $descs, $urls));
		}
		return $this->display("Translator:search.$_format");
	}

	public function textsAction(Request $request, $_format) {
		if ($_format == 'osd') {
			return $this->display("Text:search.$_format");
		}
		if ($_format == 'suggest') {
			$items = $descs = $urls = array();
			$query = $request->query->get('q');
			$texts = $this->em->getTextRepository()->getByQuery(array(
				'text'  => $query,
				'by'    => 'title',
				'match' => 'prefix',
				'limit' => 10,
			));
			foreach ($texts as $text) {
				$items[] = $text['title'];
				$descs[] = '';
				$urls[] = $this->generateUrl('text_show', array('id' => $text['id']), true);
			}

			return $this->displayJson(array($query, $items, $descs, $urls));
		}
		if (($query = $this->getQuery($_format)) instanceof Response) {
			return $query;
		}

		if (empty($query['by'])) {
			$query['by'] = 'title,subtitle,orig_title';
		}
		$texts = $this->em->getTextRepository()->getByQuery($query);
		if ( ! ($found = count($texts) > 0)) {
			$this->responseStatusCode = 404;
		}
		$this->view = array(
			'query' => $query,
			'texts' => $texts,
			'found' => $found,
		);

		return $this->display("Text:search.$_format");
	}

	public function booksAction(Request $request, $_format) {
		if ($_format == 'osd') {
			return $this->display("Book:search.$_format");
		}
		if ($_format == 'suggest') {
			$items = $descs = $urls = array();
			$query = $request->query->get('q');
			$books = $this->em->getBookRepository()->getByQuery(array(
				'text'  => $query,
				'by'    => 'title',
				'match' => 'prefix',
				'limit' => 10,
			));
			foreach ($books as $book) {
				$items[] = $book['title'];
				$descs[] = '';
				$urls[] = $this->generateUrl('book_show', array('id' => $book['id']), true);
			}

			return $this->displayJson(array($query, $items, $descs, $urls));
		}
		if (($query = $this->getQuery($_format)) instanceof Response) {
			return $query;
		}

		if (empty($query['by'])) {
			$query['by'] = 'title,subtitle,orig_title';
		}
		$books = $this->em->getBookRepository()->getByQuery($query);
		if ( ! ($found = count($books) > 0)) {
			$this->responseStatusCode = 404;
		}
		$this->view = array(
			'query' => $query,
			'books' => $books,
			'found' => $found,
		);

		return $this->display("Book:search.$_format");
	}

	public function seriesAction(Request $request, $_format) {
		if ($_format == 'osd') {
			return $this->display("Series:search.$_format");
		}
		if ($_format == 'suggest') {
			$items = $descs = $urls = array();
			$query = $request->query->get('q');
			$series = $this->em->getSeriesRepository()->getByQuery(array(
				'text'  => $query,
				'by'    => 'name',
				'match' => 'prefix',
				'limit' => 10,
			));
			foreach ($series as $serie) {
				$items[] = $serie['name'];
				$descs[] = '';
				$urls[] = $this->generateUrl('series_show', array('slug' => $serie['slug']), true);
			}

			return $this->displayJson(array($query, $items, $descs, $urls));
		}
		if (($query = $this->getQuery($_format)) instanceof Response) {
			return $query;
		}

		if (empty($query['by'])) {
			$query['by'] = 'name,orig_name';
		}
		$series = $this->em->getSeriesRepository()->getByQuery($query);
		if ( ! ($found = count($series) > 0)) {
			$this->responseStatusCode = 404;
		}
		$this->view = array(
			'query'  => $query,
			'series' => $series,
			'found'  => $found,
		);

		return $this->display("Series:search.$_format");
	}

	public function sequencesAction(Request $request, $_format) {
		if ($_format == 'osd') {
			return $this->display("Sequence:search.$_format");
		}
		if ($_format == 'suggest') {
			$items = $descs = $urls = array();
			$query = $request->query->get('q');
			$sequences = $this->em->getSequenceRepository()->getByQuery(array(
				'text'  => $query,
				'by'    => 'name',
				'match' => 'prefix',
				'limit' => 10,
			));
			foreach ($sequences as $sequence) {
				$items[] = $sequence['name'];
				$descs[] = '';
				$urls[] = $this->generateUrl('sequence_show', array('slug' => $sequence['slug']), true);
			}

			return $this->displayJson(array($query, $items, $descs, $urls));
		}
		if (($query = $this->getQuery($_format)) instanceof Response) {
			return $query;
		}

		if (empty($query['by'])) {
			$query['by'] = 'name';
		}
		$sequences = $this->em->getSequenceRepository()->getByQuery($query);
		if ( ! ($found = count($sequences) > 0)) {
			$this->responseStatusCode = 404;
		}
		$this->view = array(
			'query'     => $query,
			'sequences' => $sequences,
			'found'     => $found,
		);

		return $this->display("Sequence:search.$_format");
	}

	private function getQuery($_format = 'html') {
		$request = $this->get('request')->query;
		$query = trim($request->get('q'));

		if (empty($query)) {
			$this->view = array(
				'latest_strings' => $this->em->getSearchStringRepository()->getLatest(30),
				'top_strings' => $this->em->getSearchStringRepository()->getTop(30),
			);

			return $this->display("list_top_strings.$_format");
		}

		$query = String::fixEncoding($query);

		$matchType = $request->get('match');
		if ($matchType != 'exact') {
			try {
				$this->validateQueryLength($query);
			} catch (\Exception $e) {
				$this->view['message'] = $e->getMessage();
				$this->responseStatusCode = 400;

				return $this->display("message.$_format");
			}
		}

		return array(
			'text'  => $query,
			'by'    => $request->get('by'),
			'match' => $matchType,
		);
	}

	/**
	 * @param string $query
	 */
	private function validateQueryLength($query) {
		$queryLength = mb_strlen($query, 'utf-8');
		if ($queryLength < $this->minQueryLength) {
			throw new \Exception(sprintf('Трябва да въведете поне %d знака.', $this->minQueryLength));
		}
		if ($queryLength > $this->maxQueryLength) {
			throw new \Exception(sprintf('Не може да въвеждате повече от %d знака.', $this->maxQueryLength));
		}
	}

	/**
	 * @param string $query
	 */
	private function logSearch($query) {
		$searchString = $this->em->getSearchStringRepository()->findOneBy(array('name' => $query));
		if ( ! $searchString) {
			$searchString = new SearchString($query);
		}
		$searchString->incCount();
		$this->getEntityManager()->persist($searchString);
		$this->getEntityManager()->flush();
	}

}
