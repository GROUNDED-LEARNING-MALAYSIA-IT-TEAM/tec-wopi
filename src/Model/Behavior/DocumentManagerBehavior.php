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
  public function saveDocumentInDB(PhpWord $phpWord, array $options): bool
  {
    $blob = file_get_contents($phpWord->getWriter('Word2007')->save('php://output'));

    $fileExtension = 'docx';

    $result =  $this->getTable()->createRecord([
      'name' => $options['name'],
      'extension' => $fileExtension,
      'blob' => $blob,
      'size' => strlen($blob),
      'user_id' => $options['user_id'],
      'created' => time(),
      'modified' => time(),
    ]);

    if (!$result instanceof WopiFile) {
      return false;
    }

    return true;
  }
}
