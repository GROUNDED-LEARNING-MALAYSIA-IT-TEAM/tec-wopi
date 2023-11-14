<?php

namespace EaglenavigatorSystem\Wopi\Interfaces;

use PhpOffice\PhpWord\PhpWord;

interface DocumentManager{

  public function saveDocumentInDB(PhpWord $phpWord, array $options);
}