<?php
namespace wheelform\db;

use Craft;
use craft\helpers\Html;
use craft\db\ActiveRecord;
use craft\helpers\Template;
use craft\helpers\Assets;
use craft\elements\Asset;
use craft\errors\UploadFailedException;

use wheelform\Plugin;

use yii\web\BadRequestHttpException;
use yii\base\ErrorException;
use craft\web\UploadedFile;

//Using Active Record because it extends Models.
class MessageValue extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%wheelform_message_values}}';
    }

    public function rules()
    {
        return [
            [['field_id'], 'required', 'message' => Craft::t('wheelform', 'Field ID cannot be blank.')],
            [['message_id', 'field_id'], 'integer', 'message' => Craft::t('wheelform', '{attribute} must be a number.')],
            [['message_id', 'field_id'], 'filter', 'filter' => 'intval'],
            ['value', 'required', 'when' => function($model){
                    return (bool)$model->field->required;
                }, 'message' => $this->field->getLabel().Craft::t('wheelform', ' cannot be blank.')
            ],
            ['value', 'string', 'on' => [
                    FormField::TEXT_SCENARIO,
                    FormField::TEXTAREA_SCENARIO,
                    FormField::HIDDEN_SCENARIO,
                    FormField::SELECT_SCENARIO,
                    FormField::RADIO_SCENARIO,
                ],
                'message' => $this->field->getLabel().Craft::t('wheelform', ' must be valid characters.')
            ],
            ['value', 'email', 'on' => FormField::EMAIL_SCENARIO,
                'message' => $this->field->getLabel().Craft::t('wheelform', ' is not a valid email address.')],
            ['value', 'number', 'on' => FormField::NUMBER_SCENARIO,
                'message' => $this->field->getLabel().Craft::t('wheelform', ' must be a number.')],
            ['value', 'file', 'on' => FormField::FILE_SCENARIO,
                'skipOnEmpty' => !(bool) $this->field->required,
                'extensions' => (empty($this->field->options['extensions']) ? null : $this->field->options['extensions'])],
            ['value', function($attribute, $params, $validator){
                if(! is_array($this->$attribute)) {
                    $this->addError($this->field->getLabel().Craft::t('wheelform', ' must be an array.'));
                }
            }, 'on' => FormField::LIST_SCENARIO
        ],
            ['value', 'each', 'rule' => ['string'], 'on' => [
                    FormField::CHECKBOX_SCENARIO,
                    FormField::LIST_SCENARIO,
                ]
            ],
            ['value', 'in', 'range' => function(){
                    return (empty($this->field->options['items']) ? [] : $this->field->options['items']);
                }, 'when' => function($model){
                    return boolval($model->field->options['validate']);
                },
                "allowArray" => true,
                'message' => $this->field->getLabel().Craft::t('wheelform', ' has invalid options.')
            ],
        ];
    }

    public function getMessage()
    {
        return $this->hasOne(Message::classname(), ['id' => 'message_id']);
    }

    public function getField()
    {
        return $this->hasOne(FormField::classname(), ['id' => 'field_id']);
    }

    public function getValue()
    {
        if($this->field->type == FormField::FILE_SCENARIO) {
            $file = json_decode($this->value);
            if(! empty($file->assetId)) {
                $asset = Craft::$app->getAssets()->getAssetById($file->assetId);
                $title = Html::encode($asset->title);
                $url = $asset->getUrl();
                if($url) {
                    $anchor = '<a href="' . $url . '" target="_blank">' . $title . '</a>';
                    return Template::raw($anchor);
                }
                return $title;
            }

            return isset($file->name) ? $file->name : '';
        } elseif($this->field->type == FormField::LIST_SCENARIO) {
            return json_decode($this->value);
        } else {
            return empty($this->value) ? '' : $this->value;
        }
    }

    public function beforeValidate()
    {
        if($this->field->type == FormField::FILE_SCENARIO) {
            $this->value = UploadedFile::getInstanceByName($this->field->name);
        }

        return parent::beforeValidate();
    }

    public function afterValidate()
    {
        if(! $this->hasErrors() && ! empty($this->value) && $this->field->type == FormField::FILE_SCENARIO) {
            $this->value = $this->uploadFile();
        }

        parent::afterValidate();
    }

    public function beforeSave($insert)
    {
        if($this->field->type == FormField::CHECKBOX_SCENARIO && ! empty($this->value)) {
            $this->value = implode(', ', $this->value);
        } elseif (!empty($this->value) && $this->field->type == FormField::LIST_SCENARIO) {
            $this->value = json_encode($this->value);
        }

        return parent::beforesave($insert);
    }

    protected function uploadFile()
    {
        $plugin = Plugin::getInstance();
        $settings = $plugin->getSettings();
        $folder_id = empty($settings->volume_id) ? NULL : $settings->volume_id;
        $fileModel = new \stdClass();
        try {
            $filename = $this->value->name;
            $tempPath = $this->_getUploadedFileTempPath($this->value);
            $filePath = $tempPath;
            $assetId = $folder_id;

            // folder to upload files has been selected
            if(is_numeric($folder_id)) {
                $assets = Craft::$app->getAssets();
                $folder = $assets->getRootFolderByVolumeId($folder_id);
                if (!$folder) {
                    throw new BadRequestHttpException('The target folder provided for uploading is not valid');
                }
                $tempName = $this->value->baseName . '_'. uniqid() .'.' . $this->value->extension;
                $filename = Assets::prepareAssetName($tempName);

                $asset = new Asset();
                $asset->tempFilePath = $tempPath;
                $asset->filename = $filename;
                $asset->newFolderId = $folder->id;
                $asset->volumeId = $folder->volumeId;
                $asset->avoidFilenameConflicts = true;
                $asset->setScenario(Asset::SCENARIO_CREATE);
                $result = Craft::$app->getElements()->saveElement($asset);
                if($result) {
                    $volume = $asset->getVolume();
                    $filename = $asset->filename;
                    $filePath = $volume->getRootPath() . '/' . $asset->filename;
                    $assetId = $asset->id;
                }
            }

        } catch (\Throwable $exception) {
            Craft::error('An error occurred when saving an asset: ' . $exception->getMessage(), __METHOD__);
            Craft::$app->getErrorHandler()->logException($exception);
            return $exception->getMessage();
        }

        $fileModel->name = $filename;
        $fileModel->filePath = $filePath;
        $fileModel->assetId = $assetId;

        return json_encode($fileModel);
    }

    protected function _getUploadedFileTempPath(UploadedFile $uploadedFile)
    {
        if ($uploadedFile->getHasError()) {
            throw new UploadFailedException($uploadedFile->error);
        }
        // Move the uploaded file to the temp folder
        try {
            $tempPath = $uploadedFile->saveAsTempFile();
        } catch (ErrorException $e) {
            throw new UploadFailedException(0);
        }
        if ($tempPath === false) {
            throw new UploadFailedException(UPLOAD_ERR_CANT_WRITE);
        }
        return $tempPath;
    }
}
