<?php

namespace EaglenavigatorSystem\Wopi\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * WopiFilesFixture
 */
class WopiFilesFixture extends TestFixture
{
    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    //NOSONAR start
    public $fields = [
        'id' => [
            'type' => 'integer',
            'length' => 11,
            'unsigned' => false,
            'null' => false,
            'default' => null,
            'comment' => '',
            'autoIncrement' => true,
            'precision' => null
        ],

        'parent_id' => [
            'type' => 'integer',
            'length' => 11,
            'unsigned' => false,
            'null' => true,
            'default' => null,
            'comment' => '',
            'precision' => null
        ],

        'file_uuid' => [
            'type' => 'string',
            'length' => 36,
            'null' => false,
            'default' => null,
            'collate' => 'utf8mb4_unicode_ci',
            'comment' => '',
            'precision' => null,
            'fixed' => null
        ],

        'file_name' => [
            'type' => 'string',
            'length' => 255,
            'null' => false,
            'default' => null,
            'collate' => 'utf8mb4_unicode_ci',
            'comment' => '',
            'precision' => null,
            'fixed' => null
        ],

        'file_size' => [
            'type' => 'integer',
            'length' => 11,
            'unsigned' => false,
            'null' => false,
            'default' => null,
            'comment' => 'file size',
            'autoIncrement' => false,
            'precision' => null
        ],

        'file_extension' => [
            'type' => 'string',
            'length' => 10,
            'null' => false,
            'default' => null,
            'collate' => 'utf8mb4_unicode_ci',
            'comment' => '',
            'precision' => null,
            'fixed' => null
        ],

        'user_id' => [
            'type' => 'integer',
            'length' => 10,
            'unsigned' => false,
            'null' => false,
            'default' => null,
            'comment' => '',
            'precision' => null,
            'autoIncrement' => null
        ],

        'file_data' => [
            'type' => 'binary',
            'length' => null,
            'null' => false,
            'default' => null,
            'comment' => '',
            'precision' => null
        ],

        'file_path' => [
            'type' => 'string',
            'length' => 255,
            'null' => false,
            'default' => null,
            'collate' => 'utf8mb4_unicode_ci',
            'comment' => '',
            'precision' => null,
            'fixed' => null
        ],

        'version' => [
            'type' => 'string',
            'length' => 255,
            'null' => false,
            'default' => null,
            'collate' => 'utf8mb4_unicode_ci',
            'comment' => 'version',
            'precision' => null,
            'fixed' => null
        ],

        'user_info' => [
            'type' => 'string',
            'length' => 1024,
            'null' => true,
            'default' => null,
            'collate' => 'utf8mb4_unicode_ci',
            'comment' => 'version',
            'precision' => null,
            'fixed' => null
        ],


        'created_at' => [
            'type' => 'timestamp',
            'length' => null,
            'null' => false,
            'default' => 'current_timestamp()',
            'comment' => '',
            'precision' => null
        ],

        'updated_at' => [
            'type' => 'timestamp',
            'length' => null,
            'null' => false,
            'default' => 'current_timestamp()',
            'comment' => '',
            'precision' => null
        ],

        'soft_delete' => [
            'type' => 'boolean',
            'length' => null,
            'null' => false,
            'default' => false,
            'comment' => 'soft delete',
            'precision' => null
        ],


        'soft_delete_at' => [
            'type' => 'timestamp',
            'length' => null,
            'null' => true,
            'default' => null,
            'comment' => 'soft delete at',
            'precision' => null
        ],

        '_constraints' => [
            'primary' => [
                'type' => 'primary',
                'columns' => ['id'],
                'length' => []
            ],

        ],

        '_options' => [
            'engine' => 'InnoDB',

            'collation' => 'utf8mb4_unicode_ci'
        ],

    ];
    //NOSONAR end ignore code smell
    // @codingStandardsIgnoreEnd
    /**
     * Init method
     *
     * @return void
     */
    public function init()
    {
        define('TEST_FILE_PATH', dirname(dirname(dirname(__FILE__))) . DS . 'tests' . DS . 'test_files' . DS);
        //TEST_FILE_PATH is defined in tests/bootstrap.php
        $fileTestContent1 = 'From this configuration, there are no apparent errors or issues. The bootstrap file is correctly specified, and the test suite is properly defined. If you are experiencing issues or errors when running tests, the problem might be elsewhere, such as in the actual test code, the bootstrap file, or the environment setup.

        ';

        $filetest1 = TEST_FILE_PATH . 'test.txt';

        //if filetest1 do not exist, create it
        if (!file_exists($filetest1)) {
            touch($filetest1);
            file_put_contents($filetest1, $fileTestContent1);

        } else {

            //if filetest1 exist, overwrite it

            file_put_contents($filetest1, $fileTestContent1);
        }


        $fileContent = file_get_contents(TEST_FILE_PATH . 'test.txt');

        $fileContentD = 'Therefore, WOPI locks must:

            Be associated with a single file.

            Contain a lock ID of maximum length 1024 ASCII characters.

            Prevent all changes to that file unless a proper lock ID is provided.

            Expire after 30 minutes unless refreshed. For more information, see RefreshLock.

            Not be associated with a particular user.';

        $target = TEST_FILE_PATH . 'test_d.txt';

        //if file target do not exist, create it
        //if file target exist, overwrite it
        file_put_contents($target, $fileContentD);
        $this->records = [
            [
                'id' => 1,
                'file_uuid' => '12345678-1234-1234-1234-0d0d0d0e3xcv',
                'file_name' => 'test_d.txt',
                'file_size' => filesize(TEST_FILE_PATH . 'test.txt'),
                'file_extension' => 'txt',
                'user_id' => 495,
                'file_data' => file_get_contents(TEST_FILE_PATH . 'test.txt'),
                'file_path' => TEST_FILE_PATH . 'test.txt',
                'version' => 1,
                'user_info' => 'zH=HmN)0EnU]5a)ead48w?AWWap{]|{KUq*cHF.?SM]WST{_2uMkQ4c\\\'Fg)\\\\Z?`f6Hca16jBk:)H*m%>M[O,s3UVT\\\'y$9Q/69P%X<yvkmZgruh$j1#Ck;~E)\\\'kh0q$c\\"P(|2kG\\"^q3v\\\\IO&F?q|Qv+@p>i]b9=UuDX+dmJa@vL4)n[!x3(c7D\\\'Zf|&fW|6`i1KM)9fC5(VE0>,kV,Xj)foPP7\\\\s\\\'zx@AE;b8z,~OqIaFXExiABWeHkksQ<qXwK#]mru>\\\'=W?11G&i7;vc)Z3M*ebY^*S-##!<a|Bs#:<1pTK+kn<t;+B{Y%_>a-u^DHN-C>fc[/Cjb=KM;,\\\\`+S/nT4Q:/=\\\'4xF8L%NSU\\\\kiVp8RaaU7(!bajD&ZZkK$~Z:F^&I\\\\$CTNx~s\\\'FH]#)#\\"5D|-0%9Ojs*CRD1@$D*<sQ2AzdY\\\\Doi@bl?0/7,PYc7MtCW;MG/.qlz_z].!p:W9>[&oEU*\\\\Y:\\"!IiMSA4`jEIAYXrpMj;Kk\\\'wy>:ug.1lo=--5b|VR|_qt{j;;\\\'{b7\\"-2M3B$PaIU>a#;QFw,YVJk[j\\"7&~?}/UXdz',
                'soft_delete' => false,
                'soft_delete_at' => null,
                'created_at' => 1699632818,
                'updated_at' => 1699632818,
            ],

            [
                'id' => 2,
                'file_uuid' => '12345678-1234-1234-1234-123456789012',
                'file_name' => 'test.txt',
                'file_size' => filesize(TEST_FILE_PATH . 'test_b.txt'),
                'file_extension' => 'txt',
                'user_id' => 495,
                'file_data' =>  $fileContent,
                'file_path' => TEST_FILE_PATH . 'test_b.txt',
                'version' => 1,
                'user_info' => 'zH=HmN)0EnU]5a)ead48w?AWWap{]|{KUq*cHF.?SM]WST{_2uMkQ4c\\\'Fg)\\\\Z?`f6Hca16jBk:)H*m%>M[O,s3UVT\\\'y$9Q/69P%X<yvkmZgruh$j1#Ck;~E)\\\'kh0q$c\\"P(|2kG\\"^q3v\\\\IO&F?q|Qv+@p>i]b9=UuDX+dmJa@vL4)n[!x3(c7D\\\'Zf|&fW|6`i1KM)9fC5(VE0>,kV,Xj)foPP7\\\\s\\\'zx@AE;b8z,~OqIaFXExiABWeHkksQ<qXwK#]mru>\\\'=W?11G&i7;vc)Z3M*ebY^*S-##!<a|Bs#:<1pTK+kn<t;+B{Y%_>a-u^DHN-C>fc[/Cjb=KM;,\\\\`+S/nT4Q:/=\\\'4xF8L%NSU\\\\kiVp8RaaU7(!bajD&ZZkK$~Z:F^&I\\\\$CTNx~s\\\'FH]#)#\\"5D|-0%9Ojs*CRD1@$D*<sQ2AzdY\\\\Doi@bl?0/7,PYc7MtCW;MG/.qlz_z].!p:W9>[&oEU*\\\\Y:\\"!IiMSA4`jEIAYXrpMj;Kk\\\'wy>:ug.1lo=--5b|VR|_qt{j;;\\\'{b7\\"-2M3B$PaIU>a#;QFw,YVJk[j\\"7&~?}/UXdz',
                'soft_delete' => false,
                'soft_delete_at' => null,
                'created_at' => 1699632818,
                'updated_at' => 1699632818,
            ],



            [
                'id' => 3,
                'file_uuid' => uniqid(),
                'file_name' => 'test_g.txt',
                'file_size' => filesize(TEST_FILE_PATH . 'test_g.txt'),
                'file_extension' => 'txt',
                'user_id' => 495,
                'file_data' =>  $fileContent,
                'file_path' => TEST_FILE_PATH . 'test_g.txt',
                'version' => 1,
                'user_info' => 'zH=HmN)0EnU]5a)ead48w?AWWap{]|{KUq*cHF.?SM]WST{_2uMkQ4c\\\'Fg)\\\\Z?`f6Hca16jBk:)H*m%>M[O,s3UVT\\\'y$9Q/69P%X<yvkmZgruh$j1#Ck;~E)\\\'kh0q$c\\"P(|2kG\\"^q3v\\\\IO&F?q|Qv+@p>i]b9=UuDX+dmJa@vL4)n[!x3(c7D\\\'Zf|&fW|6`i1KM)9fC5(VE0>,kV,Xj)foPP7\\\\s\\\'zx@AE;b8z,~OqIaFXExiABWeHkksQ<qXwK#]mru>\\\'=W?11G&i7;vc)Z3M*ebY^*S-##!<a|Bs#:<1pTK+kn<t;+B{Y%_>a-u^DHN-C>fc[/Cjb=KM;,\\\\`+S/nT4Q:/=\\\'4xF8L%NSU\\\\kiVp8RaaU7(!bajD&ZZkK$~Z:F^&I\\\\$CTNx~s\\\'FH]#)#\\"5D|-0%9Ojs*CRD1@$D*<sQ2AzdY\\\\Doi@bl?0/7,PYc7MtCW;MG/.qlz_z].!p:W9>[&oEU*\\\\Y:\\"!IiMSA4`jEIAYXrpMj;Kk\\\'wy>:ug.1lo=--5b|VR|_qt{j;;\\\'{b7\\"-2M3B$PaIU>a#;QFw,YVJk[j\\"7&~?}/UXdz',
                'soft_delete' => false,
                'soft_delete_at' => null,
                'created_at' => 1699632818,
                'updated_at' => 1699632818,
            ],
        ];
        parent::init();
    }
}
