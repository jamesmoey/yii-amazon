<?php

class AmazonS3Component extends CComponent {

  /** @var AmazonS3 $s3 */
  protected $s3;
  protected $config;

  /**
   * @throws CException
   * @param AmazonS3 $s3
   * @param  $config
   *
   */
  public function __construct($s3, $config) {
    $this->s3 = $s3;
    $this->config = CMap::mergeArray($config, array(
    ));
    if (!isset($this->config['defaultBucket'])) throw new CException('Default Bucket must be set.');
    if (!isset($this->config['defaultACL'])) $this->config['defaultACL'] = AmazonS3::ACL_PUBLIC;
    if (!isset($this->config['randomPath'])) $this->config['randomPath'] = true;
    if (!isset($this->config['pathPrefix'])) $this->config['pathPrefix'] = '';
    else if (strripos($this->config['pathPrefix'], '/') !== strlen($this->config['pathPrefix'])-1) $this->config['pathPrefix'] .= '/';
    if (strpos($this->config['pathPrefix'], '/') === 0) $this->config['pathPrefix'] = substr($this->config['pathPrefix'], 1);
  }

  /**
   * Store Upload file to S3.
   * @param CUploadedFile $uploadedFile
   * @param string $bucket The file to create the object
   * @return string url to the file.
   */
  public function store($uploadedFile, $bucket = NULL) {
    if ($this->config['randomPath']) {
      $filePath = $this->config['pathPrefix'].md5(date('His')).'/'.$uploadedFile->getName();
    } else {
      $filePath = $this->config['pathPrefix'].$uploadedFile->getName();
    }
    if ($bucket === NULL) {
      $bucket = $this->config['defaultBucket'];
    }
    /** @var CFResponse $result */
    $result = $this->s3->create_object($bucket, $filePath, array(
      'fileUpload' => $uploadedFile->getTempName(),
      'acl' => $this->config['defaultACL'],
    ));
    if ($result->isOk()) {
      return urldecode($this->s3->get_object_url($bucket, $filePath));
    } else {
      Yii::log("STATUS:".$result->status."\nHEDAER:".$result->header."\nBODY:".$result->body, CLogger::LEVEL_ERROR, "application");
      throw new CEXception($result->status);
    }
  }
}
