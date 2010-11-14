<?php

class AmazonExtension extends CApplicationComponent {

  public
    $key,
    $secret,
    $s3config = array();

  protected $s3 = NULL;

  public function init() {
    parent::init();
    if (@class_exists('AmazonS3', true) == false) {
      include_once('AWSSDKforPHP/sdk.class.php');
    }
    if (@class_exists('AmazonS3', true) == false) {
      throw CException('This extension require AWSSDKforPHP library');
    }
    if (empty($this->key) ||  empty($this->secret)) {
      throw CException('This extension need access key and secret access key to be set.');
    }
    Yii::import('ext.amazon.components.*');
    Yii::registerAutoloader(array('CFLoader', 'autoloader'));
  }

  /**
   * Get Amazon S3.
   * @return AmazonS3Component
   */
  public function getAmazonS3() {
    if ($this->s3 == NULL) {
      $this->s3 = new AmazonS3Component(new AmazonS3($this->key, $this->secret), $this->s3config);
    }
    return $this->s3;
  }
}
