<?php

namespace EaglenavigatorSystem\Wopi\Test\TestCase\Model\Behavior;

use Cake\Core\Configure;
use Cake\Event\EventList;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use EaglenavigatorSystem\Wopi\Model\Table\WopiFilesTable;
use EaglenavigatorSystem\Wopi\Model\Behavior\DocumentManagerBehavior;
use PhpOffice\PhpWord\Style\Font;
use PhpOffice\PhpWord\PhpWord;

/**
 * EaglenavigatorSystem\Wopi\Model\Behavior\DocumentManagerBehavior Test Case
 */
class DocumentManagerBehaviorTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \EaglenavigatorSystem\Wopi\Model\Table\WopiFilesTable
     */
    public $WopiFiles;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.EaglenavigatorSystem/Wopi.WopiFiles',
        'plugin.EaglenavigatorSystem/Wopi.Locks',
        'app.Users',
    ];

    /**
     * Test subject
     *
     * @var \EaglenavigatorSystem\Wopi\Model\Behavior\DocumentManagerBehavior
     */
    public $DocumentManager;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('WopiFiles') ? [] : ['className' => WopiFilesTable::class];
        $this->WopiFiles = TableRegistry::getTableLocator()->get('WopiFiles', $config);


        // enable event tracking
        $this->WopiFiles->getEventManager()->setEventList(new EventList());
        $this->DocumentManager = new DocumentManagerBehavior(
            $this->WopiFiles
        );
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->DocumentManager);

        parent::tearDown();
    }

    /**
     * Test initial setup
     *
     * @return void
     */
    public function testInitialization()
    {
        $this->assertInstanceOf(DocumentManagerBehavior::class, $this->DocumentManager);
    }

    public function testSaveDocumentInDB()
    {
        Configure::write('Wopi.versioning',  'timestamp');

        Configure::write('Wopi.valid_versioning',['increment', 'timestamp', 'hash']);
        $phpword = $this->generateDocx();
        $options = [
            'name' => 'test.docx',
            'user_id' => 495,
        ];

        $result = $this->DocumentManager->saveDocumentInDB($phpword, $options);

        dump('-------- result --------');
        dump($result);
        dump('-------- result --------');

        $this->assertNotEmpty($result);

        $this->assertInstanceOf('EaglenavigatorSystem\Wopi\Model\Entity\WopiFile', $result);

        $this->assertFileExists($result->file_path);

        //delete
        $testfile = TMP . 'wopi' . DS  . 'test.docx';
        unlink($testfile);
        unlink($result->file_path);



    }

    private function generateDocx()
    {
        $phpWord = new PhpWord();

        $section = $phpWord->addSection();

        $section->addText('Hello World!', array('name' => 'Tahoma', 'size' => 16, 'bold' => true));

        $section->addTextBreak(2);

        $section->addText('This is a simple docx document.', array('name' => 'Tahoma', 'size' => 12, 'color' => 'red'));

        $section->addTextBreak(2);

        $fontStyle = new Font();
        $fontStyle->setBold(true);
        $fontStyle->setName('Verdana');
        $fontStyle->setSize(14);
        $fontStyle->setColor('blue');

        $section->addText('I am blue and bold!', $fontStyle);

        // $filename = 'example.docx';
        // //SAVE IN FOLDER TEST GO 3 LEVELS UP AND CREATE A FOLDER CALLED "EXAMPLES" AND SAVE IT THERE
        // $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        // $objWriter->save('../../examples/' . $filename);

        return $phpWord;
    }

    /**
     *   public function saveDocumentInDB(PhpWord $phpWord, array $options): bool
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
     */
}
