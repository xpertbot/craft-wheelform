<?php
namespace wheelform\models\tools;

use Exception;
use craft\base\Model;
use craft\errors\UploadFailedException;

class ImportFile extends Model
{
    public $jsonFile;

    public function rules()
    {
        return [
            [['jsonFile'], 'file', 'skipOnEmpty' => false, 'mimeTypes' => ['application/json','text/plain']]
        ];
    }

    public function getTempPath()
    {

        if ($this->jsonFile->getHasError()) {
            throw new UploadFailedException($this->jsonFile->error);
        }

        // Move the uploaded file to the temp folder
        try {
            $tempPath = $this->jsonFile->saveAsTempFile();
        } catch (ErrorException $e) {
            throw new UploadFailedException(0);
        }

        if ($tempPath === false) {
            throw new UploadFailedException(UPLOAD_ERR_CANT_WRITE);
        }

        return $tempPath;
    }
}
