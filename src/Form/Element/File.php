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

	/** @var int */
	private $errorHandling;

	/** @var bool */
	private $error = false;

	public function __construct(
		string $name,
		string $targetPath,
		callable $targetName = null,
		int $errorHandling = 0,
		callable $checkFile = null
	) {
		$this->name = $name;
		$this->targetPath = $targetPath;
		$this->targetName = $targetName;
		$this->errorHandling = $errorHandling;
		$this->checkFile = $checkFile;
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
			$status = $this->handleFile($item);

			//Wenn ein Fehler aufgetreten ist
			if($this->error) {
				return $status;
			}

			$fileNames[] = $status;
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
	 * @return array|bool|string
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

		if(!isset($this->targetName)) {
			$filename = $file->getClientFilename();
		} else {
			$targetName = $this->targetName;

			$filename = $targetName($file);
		}

		$file->moveTo($this->targetPath.$filename);

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

		$this->error = true;

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