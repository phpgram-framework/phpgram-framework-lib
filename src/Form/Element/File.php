<?php
/**
 * phpgram project
 *
 * This File is part of the phpgram Framework Lib
 *
 * Web: https://gitlab.com/grammm/php-gram/phpgram-framework-lib/tree/master
 *
 * @license https://gitlab.com/grammm/php-gram/phpgram-framework-lib/blob/master/LICENSE
 *
 * @author Jörn Heinemann <joernheinemann@gmx.de>
 */

namespace Gram\Project\Lib\Form\Element;

use Gram\Project\Lib\Exceptions\Form\FileUploadException;
use Gram\Project\Lib\Input;
use Psr\Http\Message\UploadedFileInterface;

/**
 * Class File
 * @package Gram\Project\Lib\Form\Element
 *
 * Für Files in Forms
 */
class File implements FormElementInterface
{
	/** @var string */
	private $name;

	/** @var string */
	private $targetPath;

	/** @var callable */
	private $targetName;

	/** @var callable|null */
	private $checkFile;

	/** @var callable|null */
	private $returnHandling;

	/** @var int */
	private $errorHandling;

	/** @var bool */
	private $status = true;

	/**
	 * File constructor.
	 *
	 * @param string $name
	 * Den File Inout Namen
	 *
	 * @param string $targetPath
	 * Wo die File gespeichert werden soll
	 *
	 * @param callable|null $targetName
	 * Die Möglichkeit einen eigenen Filename zu wählen
	 * Bekommt UploadedFileInterface übergeben
	 *
	 * @param int $errorHandling
	 * Was soll gemacht werden wenn ein Error auftritt
	 * = 0 -> gebe false zurück
	 * = 1 -> gebe ein Array mit einer Meldung und dem Error Code zurück
	 * = 2 -> wie bei 1 nur als Exception
	 *
	 * @param callable|null $checkFile
	 * Die Möglichkeit die File zu prüfen
	 * Bekommt UploadedFileInterface übergeben
	 *
	 * @param callable|null $return
	 * Die Möglichkeit eigene Informationen über die File zurück
	 * zugeben
	 * Bekommt UploadedFileInterface und den Filename übergeben
	 * bei null: gebe den Filename zurück
	 */
	public function __construct(
		string $name,
		string $targetPath,
		callable $targetName = null,
		int $errorHandling = 0,
		callable $checkFile = null,
		callable $return = null
	) {
		$this->name = $name;
		$this->targetPath = $targetPath;
		$this->targetName = $targetName;
		$this->errorHandling = $errorHandling;
		$this->checkFile = $checkFile;
		$this->returnHandling = $return;
	}

	/**
	 * @inheritdoc
	 */
	public function getName(): string
	{
		return $this->name;
	}

	/**
	 * @inheritdoc
	 */
	public function getStatus(): bool
	{
		return $this->status;
	}

	/**
	 * @inheritdoc
	 *
	 * kann auch Arrays verarbeiten
	 *
	 * Hole aus dem Request Object die Files raus
	 *
	 * @throws FileUploadException
	 */
	public function evaluateElement(Input $input)
	{
		$request = $input->getRequest();

		/** @var UploadedFileInterface|array $file */
		$file = $request->getUploadedFiles()[$this->name] ?? false;

		//file nicht gesetzt
		if($file === false) {
			return $this->handleError(false,$file,"File Not found",1);
		}

		if(!is_array($file)) {
			return $this->handleFile($file);
		}

		//wenn es ein Array ist. gebe die Filenames in einem Array zurück

		$fileNames = [];

		/** @var UploadedFileInterface $item */
		foreach ($file as $item) {
			$fileStatus = $this->handleFile($item);

			//Wenn ein Fehler aufgetreten ist
			if(!$this->status) {
				return $fileStatus;
			}

			$fileNames[] = $fileStatus;
		}

		return $fileNames;
	}

	/**
	 * Überprüft die File
	 * und speichert diese
	 *
	 * Gibt dann den Filename zurück
	 *
	 * @param UploadedFileInterface $file
	 * @return array|bool|string|mixed
	 * @throws FileUploadException
	 */
	private function handleFile(UploadedFileInterface $file)
	{
		if($file->getError() !== UPLOAD_ERR_OK) {
			return $this->handleError(false,$file,"File Upload failed for {$this->name}",2);
		}

		if(isset($this->checkFile)) {
			$checkFile = $this->checkFile;

			if(false === $checkFile($file)) {
				return $this->handleError(false,$file,"wrong format",3);
			}
		}

		//gebe den Filename an
		if(!isset($this->targetName)) {
			//wenn nichts vom user angegeben -> den client filename
			$filename = $file->getClientFilename();
		} else {
			//sonst lasse den user entscheiden
			$targetName = $this->targetName;

			$filename = $targetName($file);
		}

		$file->moveTo($this->targetPath.$filename);

		//wenn der User eigene Infos über die Files zurück haben möchte
		if(isset($this->returnHandling)) {
			$return = $this->returnHandling;

			return $return($file,$filename);
		}

		return $filename;
	}

	/**
	 * Wenn Exception geworfen werden sollen
	 * sonst gebe false zurück bei Fehlern
	 *
	 * @param bool $check
	 * @param UploadedFileInterface|bool $file
	 * @param string $msg
	 * @param int $code
	 * @return bool|array
	 * @throws FileUploadException
	 */
	private function handleError(bool $check, $file, string $msg = '', int $code = 0)
	{
		if($check === true) {
			return true;
		}

		//Lösche die tmp File wieder
		if($file !== null && $file !== false && $file instanceof UploadedFileInterface) {
			if($file->getSize() !== 0) {
				$stream = $file->getStream();

				if($stream !== null) {
					$stream->close();
				}
			}
		}

		$this->status = false;

		switch ($this->errorHandling) {
			case 1:
				return [$msg,$code];
			case 2:
				throw new FileUploadException($msg,$code);
			default:
				return false;
		}
	}
}