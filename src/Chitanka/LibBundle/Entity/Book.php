<?php

namespace Chitanka\LibBundle\Entity;

use Chitanka\LibBundle\Legacy\Legacy;
use Chitanka\LibBundle\Legacy\Setup;

/**
* @orm:Entity(repositoryClass="Chitanka\LibBundle\Entity\BookRepository")
* @orm:Table(name="book",
*	indexes={
*		@orm:Index(name="title_idx", columns={"title"}),
*		@orm:Index(name="title_author_idx", columns={"title_author"}),
*		@orm:Index(name="subtitle_idx", columns={"subtitle"})}
* )
*/
class Book extends BaseWork
{
	/**
	* @var integer $id
	* @orm:Id @orm:Column(type="integer") @orm:GeneratedValue
	*/
	protected $id;

	/**
	* @var string $slug
	* @orm:Column(type="string", length=50)
	*/
	private $slug;

	/**
	* @var string $title_author
	* @orm:Column(type="string", length=255)
	*/
	private $title_author;

	/**
	* @var string $title
	* @orm:Column(type="string", length=255)
	*/
	private $title;

	/**
	* @var string $subtitle
	* @orm:Column(type="string", length=255)
	*/
	private $subtitle;

	/**
	* @var string
	* @orm:Column(type="string", length=1000)
	*/
	private $title_extra;

	/**
	* @var string $orig_title
	* @orm:Column(type="string", length=255)
	*/
	private $orig_title;

	/**
	* @var string $lang
	* @orm:Column(type="string", length=2)
	*/
	private $lang;

	/**
	* @var string $orig_lang
	* @orm:Column(type="string", length=2, nullable=true)
	*/
	private $orig_lang;

	/**
	* @var integer $year
	* @orm:Column(type="smallint")
	*/
	private $year;

	/**
	* @var integer $trans_year
	* @orm:Column(type="smallint", nullable=true)
	*/
	private $trans_year;

	/**
	* @var string $type
	* @orm:Column(type="string", length=10)
	*/
	private $type;

	/**
	* @var integer
	* @orm:ManyToOne(targetEntity="Sequence", inversedBy="books")
	*/
	private $sequence;

	/**
	* @var integer
	* @orm:Column(type="smallint")
	*/
	private $seqnr;

	/**
	* @var integer
	* @orm:ManyToOne(targetEntity="Category", inversedBy="books")
	*/
	private $category;

	/**
	* @var boolean
	* @orm:Column(type="boolean")
	*/
	private $has_anno;

	/**
	* @var boolean
	* @orm:Column(type="boolean")
	*/
	private $has_cover;

	/**
	* @var string $mode
	* @orm:Column(type="string", length=8)
	*/
	private $mode;

	/**
	* @orm:ManyToMany(targetEntity="Person", inversedBy="books")
	* @orm:JoinTable(name="book_author",
	*	joinColumns={@orm:JoinColumn(name="book_id", referencedColumnName="id")},
	*	inverseJoinColumns={@orm:JoinColumn(name="person_id", referencedColumnName="id")})
	*/
	private $authors;

	/** FIXME doctrine:schema:create does not allow this relation
	* @orm:ManyToMany(targetEntity="Text", inversedBy="books")
	* @orm:JoinTable(name="book_text",
	*	joinColumns={@orm:JoinColumn(name="book_id", referencedColumnName="id")},
	*	inverseJoinColumns={@orm:JoinColumn(name="text_id", referencedColumnName="id")})
	*/
	private $texts;

	/**
	* @var array
	* @orm:OneToMany(targetEntity="BookLink", mappedBy="book")
	*/
	private $links;

	/**
	* @var date
	* @orm:Column(type="date")
	*/
	private $created_at;


	public function getId() { return $this->id; }

	public function setSlug($slug) { $this->slug = $slug; }
	public function getSlug() { return $this->slug; }

	public function setTitleAuthor($titleAuthor) { $this->title_author = $titleAuthor; }
	public function getTitleAuthor() { return $this->title_author; }

	public function setTitle($title) { $this->title = $title; }
	public function getTitle() { return $this->title; }

	public function setSubtitle($subtitle) { $this->subtitle = $subtitle; }
	public function getSubtitle() { return $this->subtitle; }

	public function setTitleExtra($title) { $this->title_extra = $title; }
	public function getTitleExtra() { return $this->title_extra; }

	public function setOrigTitle($origTitle) { $this->orig_title = $origTitle; }
	public function getOrigTitle() { return $this->orig_title; }

	public function setLang($lang) { $this->lang = $lang; }
	public function getLang() { return $this->lang; }

	public function setOrigLang($origLang) { $this->orig_lang = $origLang; }
	public function getOrigLang() { return $this->orig_lang; }

	public function setYear($year) { $this->year = $year; }
	public function getYear() { return $this->year; }

	public function setTransYear($transYear) { $this->trans_year = $transYear; }
	public function getTransYear() { return $this->trans_year; }

	public function setType($type) { $this->type = $type; }
	public function getType() { return $this->type; }

	public function setMode($mode) { $this->mode = $mode; }
	public function getMode() { return $this->mode; }

	public function getAuthors() { return $this->authors; }
	public function addAuthor($author) { $this->authors[] = $author; }

	public function getTexts() { return $this->texts; }

	public function getLinks() { return $this->links; }
	public function addLink($link) { $this->links[] = $link; }

	public function hasAnno() { return $this->has_anno; }
	public function has_anno() { return $this->has_anno; }

	public function hasCover() { return $this->has_cover; }
	public function has_cover() { return $this->has_cover; }

	public function setSequence($sequence) { $this->sequence = $sequence; }
	public function getSequence() { return $this->sequence; }

	public function setSeqnr($seqnr) { $this->seqnr = $seqnr; }
	public function getSeqnr() { return $this->seqnr; }

	public function setCategory($category) { $this->category = $category; }
	public function getCategory() { return $this->category; }


	public
		$textIds = array(),
		$textsById = array();

	protected
		$annotationDir = 'book-anno',
		$infoDir = 'book-info',
		$covers = array();



	public function getDocId()
	{
		return 'http://chitanka.info/book/' . $this->id;
	}

	//public function getType() { return 'book'; }

	public function getAuthor()
	{
		return $this->title_author;
	}


	public function getAuthorsOld()
	{
		if ( ! isset($this->authors) ) {
			$this->authors = array();
			$seen = array();
			foreach ($this->getTextsById() as $text) {
				foreach ($text->getAuthors() as $author) {
					if ( ! in_array($author['id'], $seen) ) {
						$this->authors[] = $author;
						$seen[] = $author['id'];
					}
				}
			}
		}

		return $this->authors;
	}

	public function getMainAuthors()
	{
		if ( ! isset($this->mainAuthors) ) {
			$this->mainAuthors = array();
			foreach ($this->getTextsById() as $text) {
				if ( self::isMainWorkType($text->getType()) ) {
					foreach ($text->getAuthors() as $author) {
						$this->mainAuthors[$author['id']] = $author;
					}
				}
			}
		}

		return $this->mainAuthors;
	}

	public static function isMainWorkType($type)
	{
		return ! in_array($type, array('intro', 'outro'/*, 'interview', 'article'*/));
	}


	public function getAuthorsBy($type)
	{
		if ( ! isset($this->authorsBy[$type]) ) {
			$this->authorsBy[$type] = array();
			foreach ($this->getTextsById() as $text) {
				if ($text->getType() == $type) {
					foreach ($text->getAuthors() as $author) {
						$this->authorsBy[$type][$author['id']] = $author;
					}
				}
			}
		}

		return $this->authorsBy[$type];
	}


	public function getTranslators()
	{
		if ( ! isset($this->translators) ) {
			$this->translators = array();
			$seen = array();
			foreach ($this->getTextsById() as $text) {
				foreach ($text->getTranslators() as $translator) {
					if ( ! in_array($translator['id'], $seen) ) {
						$this->translators[] = $translator;
						$seen[] = $translator['id'];
					}
				}
			}
		}

		return $this->translators;
	}

	public function getLangOld()
	{
		if ( ! isset($this->lang) ) {
			$langs = array();
			foreach ($this->getTextsById() as $text) {
				if ( ! isset($langs[$text->lang]) ) {
					$langs[$text->lang] = 0;
				}
				$langs[$text->lang]++;
			}

			arsort($langs);
			list($this->lang,) = each($langs);
		}

		return $this->lang;
	}

	public function getOrigLangOld()
	{
		if ( ! isset($this->orig_lang) ) {
			$langs = array();
			foreach ($this->getTextsById() as $text) {
				if ( ! isset($langs[$text->orig_lang]) ) {
					$langs[$text->orig_lang] = 0;
				}
				$langs[$text->orig_lang]++;
			}

			arsort($langs);
			list($this->orig_lang,) = each($langs);
		}

		return $this->orig_lang;
	}

	public function getYearOld()
	{
		if ( ! isset($this->year) ) {
			$texts = $this->getTextsById();
			$text = array_shift($texts);
			$this->year = $text->year;
		}

		return $this->year;
	}

	public function getTransYearOld()
	{
		if ( ! isset($this->trans_year) ) {
			$texts = $this->getTextsById();
			$text = array_shift($texts);
			$this->trans_year = $text->trans_year;
		}

		return $this->trans_year;
	}


	public static function newFromId($id)
	{
		$db = Setup::db();
		$res = $db->select(DBT_BOOK, array('id' => $id));
		$data = $db->fetchAssoc($res);
		$book = new Book;
		foreach ($data as $field => $value) {
			$book->$field = $value;
		}

		return $book;
	}


	public static function newFromArray($fields)
	{
		$book = new Book;
		foreach ($fields as $field => $value) {
			$book->$field = $value;
		}

		return $book;
	}


	public function getTemplate()
	{
		$file = Legacy::getContentFilePath('book', $this->id);
		$content = '';
		if ( file_exists($file) ) {
			$content = file_get_contents($file);
		}

		return $content;
	}

	public function getTemplateAsXhtml()
	{
		$template = $this->getTemplate();
		if ($template) {
			$imgDir = Legacy::getContentFilePath('book-img', $this->id).'/';
			$converter = new \Sfblib_SfbToHtmlConverter($template, $imgDir);
			$content = $converter->convert()->getContent();
			//$content = preg_replace('|<p>\n\{(\d+)\}\n</p>|', '{$1}', $content);
			$content = preg_replace('#<h(\d)>(\{text:\d+\})</h\d>#', '<h$1 class="inner-text">$2</h$1>', $content);
			$content = preg_replace('#<h(\d)>([^{].+)</h\d>#', '<h$1 class="inline-text">$2</h$1>', $content);
			// remove comments
			$content = preg_replace('/&lt;!--.+--&gt;/U', '', $content);
			$content = strtr($content, array("<p>\n----\n</p>" => '<hr/>'));
			$content = preg_replace_callback('#<h(\d)>\{file:(.+)\}</h\d>#', array($this, 'pregGetInlineFileForTemplate'), $content);

			return $content;
		}

		return '';
	}


	public function pregGetInlineFileForTemplate($matches)
	{
		$headingLevel = $matches[1];
		$file = $matches[2];

		$imgDir = Legacy::getContentFilePath('book-img', (int) $file) . '/';
		$converter = new \Sfblib_SfbToHtmlConverter(Legacy::getContentFile('text', $file), $imgDir);

		return $converter->convert()->getContent();
	}


	public function getCover($width = null)
	{
		$this->initCovers();

		return is_null($width) ? $this->covers['front'] : Legacy::genThumbnail($this->covers['front'], $width);
	}

	public function getBackCover($width = null)
	{
		$this->initCovers();

		return is_null($width) ? $this->covers['back'] : Legacy::genThumbnail($this->covers['back'], $width);
	}


	public function initCovers()
	{
		if ( empty($this->covers) ) {
			$this->covers['front'] = $this->covers['back'] = null;

			$covers = self::getCovers($this->id, null, 'book-cover');
			if ( ! empty($covers) ) {
				$this->covers['front'] = $covers[0];
			} else {
				// there should not be any covers by texts
				/*foreach ($this->getTextIds() as $textId) {
					$covers = self::getCovers($textId);
					if ( ! empty($covers) ) {
						$this->covers['front'] = $covers[0];
						break;
					}
				}*/
			}

			if ($this->covers['front']) {
				$back = preg_replace('/(.+)\.(\w+)$/', '$1-back.$2', $this->covers['front']);
				if (file_exists($back)) {
					$this->covers['back'] = $back;
				}
			}
		}
	}

	public function getImages()
	{
		return array_merge($this->getLocalImages(), $this->getTextImages());
	}

	public function getThumbImages()
	{
		return $this->getTextThumbImages();
	}

	public function getLocalImages()
	{
		$images = array();

		$dir = Legacy::getContentFilePath('book-img', $this->id);
		foreach (glob("$dir/*") as $img) {
			$images[] = $img;
		}

		return $images;
	}

	public function getTextImages()
	{
		$images = array();

		foreach ($this->getTextsById() as $text) {
			$images = array_merge($images, $text->getImages());
		}

		return $images;
	}

	public function getTextThumbImages()
	{
		$images = array();

		foreach ($this->getTextsById() as $text) {
			$images = array_merge($images, $text->getThumbImages());
		}

		return $images;
	}

	public function getLabels()
	{
		$labels = array();

		foreach ($this->getTextsById() as $text) {
			$labels = array_merge($labels, $text->getLabels());
		}

		$labels = array_unique($labels);

		return $labels;
	}


	public function getContentAsSfb()
	{
		return $this->getTitleAsSfb() . \Sfblib_SfbConverter::EOL
			. $this->getAllAnnotationsAsSfb()
			. $this->getMainBodyAsSfb()
			. $this->getInfoAsSfb();
	}


	public function getMainBodyAsSfb()
	{
		if ( isset($this->_mainBodyAsSfb) ) {
			return $this->_mainBodyAsSfb;
		}

		$nextHeading = \Sfblib_SfbConverter::TITLE_1;
		$headingRepl = array(
			'>' => array(
				"\n>" => "\n>>",
				"\n>>" => "\n>>>",
				"\n>>>" => "\n>>>>",
				"\n>>>>" => "\n>>>>>",
				"\n>>>>>" => "\n#",
			),
			'>>' => array(
				"\n>" => "\n>>>",
				"\n>>" => "\n>>>>",
				"\n>>>" => "\n>>>>>",
				"\n>>>>" => "\n#",
				"\n>>>>>" => "\n#",
			),
			'>>>' => array(
				"\n>" => "\n>>>>",
				"\n>>" => "\n>>>>>",
				"\n>>>" => "\n#",
				"\n>>>>" => "\n#",
				"\n>>>>>" => "\n#",
			),
			'>>>>' => array(
				"\n>" => "\n>>>>>",
				"\n>>" => "\n#",
				"\n>>>" => "\n#",
				"\n>>>>" => "\n#",
				"\n>>>>>" => "\n#",
			),
			'>>>>>' => array(
				"\n>" => "\n#",
				"\n>>" => "\n#",
				"\n>>>" => "\n#",
				"\n>>>>" => "\n#",
				"\n>>>>>" => "\n#",
			),
		);

		$template = $this->getTemplate();
		$div = str_repeat(\Sfblib_SfbConverter::EOL, 2);
		$sfb = '';
		$texts = $this->getTextsById();
		foreach (explode("\n", $template) as $line) {
			if (empty($line)) {
				$sfb .= \Sfblib_SfbConverter::EOL;
			} else if ($line[0] == '>') {
				list($marker) = explode("\t", $line);
				switch ($marker) {
					case '>' : $nextHeading = '>>'; break;
					case '>>' : $nextHeading = '>>>'; break;
					case '>>>' : $nextHeading = '>>>>'; break;
					case '>>>>' : $nextHeading = '>>>>>'; break;
				}
				$sfb .= $line . \Sfblib_SfbConverter::EOL;
			} else if (substr($line, 0, 2) == "\t{") {
				$text = $texts[ (preg_replace('/\D/', '', $line)) ];

				$authors = $this->getTextAuthorIfNotInTitle($text);
				if ( ! empty($authors) ) {
					$authors = $nextHeading . \Sfblib_SfbConverter::CMD_DELIM . $authors . \Sfblib_SfbConverter::EOL;
				}
				$title = $text->getTitleAsSfb();
				$title = strtr($title, array(\Sfblib_SfbConverter::HEADER => $nextHeading));
				$sfb .= $authors . $title . $div
					. ltrim(strtr("\n".$text->getRawContent(), $headingRepl[$nextHeading]), "\n") . $div;
			} else if (trim($line) == '----') {
				$nextHeading = \Sfblib_SfbConverter::TITLE_1;
			} else {
				$sfb .= $line . \Sfblib_SfbConverter::EOL;
			}
		}

		return $this->_mainBodyAsSfb = $sfb;
	}


	public function getMainBodyAsSfbFile()
	{
		if ( isset($this->_mainBodyAsSfbFile) ) {
			return $this->_mainBodyAsSfbFile;
		}

		$this->_mainBodyAsSfbFile = tempnam(BASEDIR . '/cache', 'book');
		file_put_contents($this->_mainBodyAsSfbFile, $this->getMainBodyAsSfb());

		return $this->_mainBodyAsSfbFile;
	}


	/**
	* Return the author of a text if he/she is not on the book title
	*/
	public function getTextAuthorIfNotInTitle($text)
	{
		$authors = $text->authors;
		$names = array();
		foreach ($authors as $i => $author) {
			$names[] = $author['name'];
			if ( strpos($this->title_author, $author['name']) !== false ) {
				unset($authors[$i]);
			}
		}

		if (empty($authors)) {
			return '';
		}

		return implode(', ', $names);
	}


	public function getTitleAsSfb()
	{
		$sfb = '';
		$prefix = \Sfblib_SfbConverter::HEADER . \Sfblib_SfbConverter::CMD_DELIM;

		if ( ! empty($this->title_author) ) {
			$sfb .= $prefix . $this->title_author . \Sfblib_SfbConverter::EOL;
		}

		$sfb .= $prefix . $this->title . \Sfblib_SfbConverter::EOL;

		if ( ! empty($this->subtitle) ) {
			$sfb .= $prefix . $this->subtitle . \Sfblib_SfbConverter::EOL;
		}

		return $sfb;
	}


	public function getAllAnnotationsAsSfb()
	{
		if ( ($text = $this->getAnnotationAsSfb()) ) {
			return $text;
		}

		return $this->getTextAnnotations();
	}


	/* TODO remove: there should not be any annotations by texts */
	public function getTextAnnotations()
	{
		$annotations = array();
		foreach ($this->getTextsById() as $text) {
			$annotation = $text->getAnnotation();
			if ($annotation != '') {
				$annotations[$text->title] = $annotation;
			}
		}

		if (empty($annotations)) {
			return '';
		}

		$bannotation = '';
		$putTitles = count($annotations) > 1;
		foreach ($annotations as $title => $annotation) {
			if ($putTitles) {
				$bannotation .= \Sfblib_SfbConverter::EOL . \Sfblib_SfbConverter::EOL
					. \Sfblib_SfbConverter::SUBHEADER . \Sfblib_SfbConverter::CMD_DELIM . $title
					. \Sfblib_SfbConverter::EOL;
			}
			$bannotation .= $annotation;
		}

		return \Sfblib_SfbConverter::ANNO_S . \Sfblib_SfbConverter::EOL
			. rtrim($bannotation) . \Sfblib_SfbConverter::EOL
			. \Sfblib_SfbConverter::ANNO_E . \Sfblib_SfbConverter::EOL;
	}

	public function getInfoAsSfb()
	{
		return \Sfblib_SfbConverter::INFO_S . \Sfblib_SfbConverter::EOL
			. \Sfblib_SfbConverter::CMD_DELIM . $this->getOriginMarker() . \Sfblib_SfbConverter::EOL
			. rtrim($this->getExtraInfo()) . \Sfblib_SfbConverter::EOL
			. \Sfblib_SfbConverter::INFO_E . \Sfblib_SfbConverter::EOL;
	}


	public function getOriginMarker()
	{
		return sprintf('Свалено от [[ „Моята библиотека“ | %s ]]', $this->getDocId());
	}

	public function getContentAsFb2()
	{
		$imgdir = $this->initTmpImagesDir();

		$conv = new \Sfblib_SfbToFb2Converter($this->getContentAsSfb(), $imgdir);

		$conv->setObjectCount(1);
		$conv->setSubtitle($this->subtitle);
		$conv->setKeywords( implode(', ', $this->getLabels()) );
		$conv->setTextDate($this->getYear());

		if ( ($cover = $this->getCover()) ) {
			$conv->addCoverpage($cover);
		}

		$conv->setLang($this->getLang());
		$orig_lang = $this->getOrigLang();
		$conv->setSrcLang(empty($orig_lang) ? '?' : $orig_lang);

		foreach ($this->getTranslators() as $translator) {
			$conv->addTranslator($translator['name']);
		}

		$conv->setDocId($this->getDocId());

		$conv->enablePrettyOutput();

		$content = $conv->convert()->getContent();

		return $content;
	}


	public function getHeaders()
	{
		if ( isset($this->_headers) ) {
			return $this->_headers;
		}

		require_once BASEDIR . '/include/headerextract.php';
		$this->_headers = array();
		foreach (makeDbRows($this->getMainBodyAsSfbFile(), 4) as $row) {
			$this->_headers[] = array(
				'nr' => $row[0],
				'level' => $row[1],
				'name' => $row[2],
				'fpos' => $row[3],
				'linecnt' => $row[4]
			);
		}

		return $this->_headers;
	}

	public function getEpubChunks($imgDir)
	{
		return $this->getEpubChunksFrom($this->getMainBodyAsSfbFile(), $imgDir);
	}


	public function initTmpImagesDir()
	{
		$dir = sys_get_temp_dir() . '/' . uniqid();
		mkdir($dir);
		foreach ($this->getImages() as $image) {
			copy($image, $dir.'/'.basename($image));
		}

		return $dir;
	}


	public function getNameForFile()
	{
		return trim("$this->title_author - $this->title - $this->subtitle-$this->id", '- ');
	}


	public function getTextIds()
	{
		if ( empty($this->textIds) ) {
			preg_match_all('/\{(text|file):(\d+)\}/', $this->getTemplate(), $matches);
			$this->textIds = $matches[2];
		}

		return $this->textIds;
	}


	public function getTextsById()
	{
		if ( empty($this->textsById) ) {
			foreach ($this->getTextIds() as $id) {
				$this->textsById[$id] = Text::newFromId($id);
			}
		}

		return $this->textsById;
	}


	public function isGamebook()
	{
		return false;
	}


	public function isFromSameAuthor($text)
	{
		return $this->getAuthorIds() == $text->getAuthorIds();
	}


	/** TODO set for a books with only one novel */
	public function getPlainSeriesInfo()
	{
		return '';
	}

	public function getPlainTranslationInfo()
	{
		$info = array();
		foreach ($this->getTranslators() as $translator) {
			$info[] = $translator['name'];
		}

		return sprintf('Превод: %s', implode(', ', $info));
	}



	##################
	# legacy pic stuff
	##################

	const
		ID_SKIP = 636,
		MIRRORS_FILE = 'MIRRORS',
		INFO_FILE = 'INFO',
		THUMB_DIR = 'thumb',

		THUMBS_FILE_TPL = 'thumbs-%d.jpg',
		MAX_JOINED_THUMBS = 50;

	public function getSeriesName($pic = null) {
		if ( is_null($pic) ) {
			$pic = $this;
		}
		if ( empty($pic->series) ) {
			return '';
		}
		$name = $pic->seriesName;
		$picType = picType($pic->seriesType);
		if ( ! empty($picType) ) {
			$name = "$picType „{$name}“";
		}
		return $name;
	}


	public function getIssueName($pic = null) {
		if ( is_null($pic) ) {
			$pic = $this;
		}
		return $pic->__toString();
	}


	public function __toString() {
		if ( empty( $this->series ) ) {
			return $this->name;
		}
		$name = $this->getSeriesName();
		if ( ! empty($this->sernr) ) {
			$name .= ', брой ' . $this->sernr;
		}
		if ( $this->name != $this->seriesName ) {
			$name .= ' — ' . $this->name;
		}
		$name = str_replace('\n', '<br>', $name);

		return $name;
	}


	public function getFiles()
	{
		if ( isset($this->_files) ) {
			return $this->_files;
		}

		$dir = Legacy::getContentFilePath('pic', $this->id - self::ID_SKIP);

		$ignore = array(self::MIRRORS_FILE, self::THUMB_DIR, self::INFO_FILE);

		$files = array();
		foreach (scandir($dir) as $file) {
			if ( $file[0] == '.' || in_array($file, $ignore) ) {
				continue;
			}
			$files[] = $file;
		}

		sort($files);

		return $this->_files = $files;
	}


	public function getMirrors()
	{
		if ( isset($this->_mirrors) ) {
			return $this->_mirrors;
		}

		$file = Legacy::getContentFilePath('pic', $this->id - self::ID_SKIP) . '/' . self::MIRRORS_FILE;

		$mirrors = Setup::setting('mirror_sites_graphic');
		if ( file_exists($file) && filesize($file) > 0 ) {
			$mirrors = array_merge($mirrors,
				file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));
		}

		return $this->_mirrors = $mirrors;
	}


	public function getDocRoot($cache = true)
	{
		if ( isset($this->_docRoot) && $cache ) {
			return $this->_docRoot;
		}

		$mirrors = $this->getMirrors();
		if ( empty($mirrors) ) {
			$this->_docRoot = '';
		} else {
			shuffle($mirrors);
			$this->_docRoot = rtrim($mirrors[0], '/') . '/';
		}

		return $this->_docRoot;
	}


	public function getImageDir()
	{
		if ( ! isset($this->_imageDir) ) {
			$this->_imageDir = Legacy::getContentFilePath('pic', $this->id - self::ID_SKIP);
		}

		return $this->_imageDir;
	}


	public function getThumbDir()
	{
		if ( ! isset($this->_thumbDir) ) {
			$this->_thumbDir = $this->getImageDir() .'/'. self::THUMB_DIR;
		}

		return $this->_thumbDir;
	}


	public function getWebImageDir()
	{
		if ( ! isset($this->_webImageDir) ) {
			$this->_webImageDir = $this->getDocRoot() . $this->getImageDir();
		}

		return $this->_webImageDir;
	}


	public function getWebThumbDir()
	{
		if ( ! isset($this->_webThumbDir) ) {
			$this->_webThumbDir = $this->getDocRoot() . $this->getThumbDir();
		}

		return $this->_webThumbDir;
	}


	public function getThumbFile($currentPage)
	{
		$currentJoinedFile = floor($currentPage / self::MAX_JOINED_THUMBS);

		return sprintf(self::THUMBS_FILE_TPL, $currentJoinedFile);
	}

	public function getThumbClass($currentPage)
	{
		return 'th' . ($currentPage % self::MAX_JOINED_THUMBS);
	}


	public function getSiblings()
	{
		if ( isset($this->_siblings) ) {
			return $this->_siblings;
		}

		$qa = array(
			'SELECT' => 'p.*, s.name seriesName, s.type seriesType',
			'FROM' => DBT_PIC .' p',
			'LEFT JOIN' => array(
				DBT_PIC_SERIES .' s' => 'p.series = s.id'
			),
			'WHERE' => array(
				'series' => $this->series,
				'p.series' => array('>', 0),
			),
			'ORDER BY' => 'sernr ASC'
		);
		$db = Setup::db();
		$res = $db->extselect($qa);
		$siblings = array();
		while ( $row = $db->fetchAssoc($res) ) {
			$siblings[ $row['id'] ] = new PicWork($row);
		}

		return $this->_siblings = $siblings;
	}


	public function getNextSibling() {
		if ( empty($this->series) ) {
			return false;
		}
		$dbkey = array('series' => $this->series);
		if ($this->sernr == 0) {
			$dbkey['p.id'] = array('>', $this->id);
		} else {
			$dbkey[] = 'sernr = '. ($this->sernr + 1)
				. " OR (sernr > $this->sernr AND p.id > $this->id)";
		}
		return self::newFromDB($dbkey);
	}


	public function sameAs($otherPic)
	{
		return $this->id == $otherPic->id;
	}

}
