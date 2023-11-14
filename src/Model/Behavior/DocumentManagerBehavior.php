<?php

namespace EaglenavigatorSystem\Wopi\Model\Behavior;

use Cake\ORM\Behavior;
use Cake\ORM\Table;
use EaglenavigatorSystem\Wopi\Interfaces\DocumentManager;
use EaglenavigatorSystem\Wopi\Model\Entity\WopiFile;
use PhpOffice\PhpWord\PhpWord;

/**
 * DocumentManager behavior
 */
class DocumentManagerBehavior extends Behavior implements DocumentManager
{

  /**
   * temp path
   */
  const TEMP_PATH = TMP . 'wopi' . DS;

  public function saveDocumentInDB(PhpWord $phpWord, array $options)
  {
    //create writer for Word2007
    $xmlWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
    $fileExtension = 'docx';
    //create temp path and make it writable if not exists
    if (!file_exists(self::TEMP_PATH)) {
      mkdir(self::TEMP_PATH, 0777, true);
    }


    //save into temp path using filename from options
    $xmlWriter->save(self::TEMP_PATH . $options['name']);

    $file = self::TEMP_PATH . $options['name'];



    $result =  $this->getTable()->createRecord([
      'file_source' => $file,
      'name' => $options['name'],
      'file_extension' => $fileExtension,
      'user_id' => $options['user_id'],
    ]);

    if (!$result instanceof WopiFile) {
      return false;
    }

    return $result;
  }
}
