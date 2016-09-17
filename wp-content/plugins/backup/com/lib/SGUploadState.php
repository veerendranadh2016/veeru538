<?php

require_once(dirname(__FILE__).'/SGState.php');

class SGUploadState extends SGState
{
	private $offset = 0;
	private $activeDirectory = '';
	private $currentUploadChunksCount = 0;
	private $totalUploadChunksCount = 0;
	private $uploadId = 0;
	private $parts = array();
	private $storageType = null;

	function __construct()
	{
	}

	public function setActiveDirectory($activeDirectory)
	{
		$this->activeDirectory = $activeDirectory;
	}

	public function setOffset($offset)
	{
		$this->offset = $offset;
	}

	public function setCurrentUploadChunksCount($currentUploadChunksCount)
	{
		$this->currentUploadChunksCount = $currentUploadChunksCount;
	}

	public function setTotalUploadChunksCount($totalUploadChunksCount)
	{
		$this->totalUploadChunksCount = $totalUploadChunksCount;
	}

	public function setUploadId($uploadId)
	{
		$this->uploadId = $uploadId;
	}

	public function setParts($parts)
	{
		$this->parts = $parts;
	}

	public function setStorageType($storageType)
	{
		$this->storageType = $storageType;
	}

	public function getStorageType()
	{
		return $this->storageType;
	}

	public function getCurrentUploadChunksCount()
	{
		return $this->currentUploadChunksCount;
	}

	public function getTotalUploadChunksCount()
	{
		return $this->totalUploadChunksCount;
	}

	public function getUploadId()
	{
		return $this->uploadId;
	}

	public function getParts()
	{
		return $this->parts;
	}

	public function getActiveDirectory()
	{
		return $this->activeDirectory;
	}

	public function getOffset()
	{
		return $this->offset;
	}

	public function init($stateJson)
	{
		$this->type = $stateJson['type'];
		$this->parts = $stateJson['parts'];
		$this->offset = $stateJson['offset'];
		$this->action = $stateJson['action'];
		$this->uploadId = $stateJson['uploadId'];
		$this->actionId = $stateJson['actionId'];
		$this->progress = $stateJson['progress'];
		$this->storageType = $stateJson['storageType'];
		$this->actionStartTs = $stateJson['actionStartTs'];
		$this->warningsFound = $stateJson['warningsFound'];
		$this->backupFileName = $stateJson['backupFileName'];
		$this->backupFilePath = $stateJson['backupFilePath'];
		$this->activeDirectory = $stateJson['activeDirectory'];
		$this->queuedStorageUploads = $stateJson['queuedStorageUploads'];
		$this->totalUploadChunksCount = $stateJson['totalUploadChunksCount'];
		$this->currentUploadChunksCount = $stateJson['currentUploadChunksCount'];

		return $this;
	}

	public function save()
	{
		file_put_contents(dirname($this->backupFilePath).'/'.SG_STATE_FILE_NAME, json_encode(array(
			'type' => $this->type,
			'parts' => $this->parts,
			'token' => $this->token,
			'offset' => $this->offset,
			'action' => $this->action,
			'uploadId' => $this->uploadId,
			'actionId' => $this->actionId,
			'progress' => $this->progress,
			'inprogress' => $this->inprogress,
			'storageType' => $this->storageType,
			'actionStartTs' => $this->actionStartTs,
			'warningsFound' => $this->warningsFound,
			'backupFileName' => $this->backupFileName,
			'backupFilePath' => $this->backupFilePath,
			'activeDirectory' => $this->activeDirectory,
			'queuedStorageUploads' => $this->queuedStorageUploads,
			'totalUploadChunksCount' => $this->totalUploadChunksCount,
			'currentUploadChunksCount' => $this->currentUploadChunksCount
		)));
	}
}
