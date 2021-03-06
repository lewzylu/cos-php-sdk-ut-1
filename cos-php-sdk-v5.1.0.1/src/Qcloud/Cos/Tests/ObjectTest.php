<?php
namespace Qcloud\Cos\Tests;
use Qcloud\Cos\Client;
use Qcloud\Cos\Exception\CosException;
class ObjectTest extends \PHPUnit_Framework_TestCase {
    private $cosClient;
    protected function setUp() {
        TestHelper::nuke('testbucket-1252448703');
        $this->cosClient = new Client(array('region' => getenv('COS_REGION'),
                'credentials'=> array(
                   'appId' => getenv('COS_APPID'),
                'secretId'    => getenv('COS_KEY'),
                'secretKey' => getenv('COS_SECRET'))));
    }
    protected function tearDown() {
        TestHelper::nuke('testbucket');
        sleep(2);
    }
    public function testPutObject() {
        try {
            $this->cosClient->createBucket(array('Bucket' => 'testbucket'));
            sleep(5);
            $this->cosClient->putObject(array(
                        'Bucket' => 'testbucket', 'Key' => 'hello.txt', 'Body' => 'Hello World'));
        } catch (\Exception $e) {
            $this->assertFalse(true, $e);
        }
    }
    public function testPutObjectIntoNonexistedBucket() {
        try {
            $this->cosClient->putObject(array(
                        'Bucket' => 'testbucket', 'Key' => 'hello.txt', 'Body' => 'Hello World'));
        } catch (CosException $e) {
            $this->assertTrue($e->getExceptionCode() === 'NoSuchBucket');
            $this->assertTrue($e->getStatusCode() === 404);
        }
    }
    public function testUploadSmallObject() {
        try {
            $result = $this->cosClient->createBucket(array('Bucket' => 'testbucket'));
            sleep(5);
            $this->cosClient->upload('testbucket', '你好.txt', 'Hello World');
        } catch (\Exception $e) {
            $this->assertFalse(true, $e);
        }
    }
    public function testUploadLargeObject() {
        try {
            sleep(2);
            $this->cosClient->createBucket(array('Bucket' => 'testbucket'));
            sleep(5);
            $this->cosClient->upload('testbucket', 'hello.txt', str_repeat('a', 9 * 1024 * 1024));
        } catch (\Exception $e) {
            $this->assertFalse(true, $e);
        }
    }
    public function testGetObject() {
        try {
            $this->cosClient->createBucket(array('Bucket' => 'testbucket'));
            sleep(5);
            $this->cosClient->upload('testbucket', '你好.txt', 'Hello World');
            $this->cosClient->getObject(array(
                                    'Bucket' => 'testbucket',
                                    'Key' => '你好.txt',));
        } catch (\Exception $e) {
            $this->assertFalse(true, $e);
        }
    }
    public function testGetObjectUrl() {
        try{
            $this->cosClient->createBucket(array('Bucket' => 'testbucket'));
            $this->cosClient->getObjectUrl('testbucket', 'hello.txt', '+10 minutes');
        } catch (\Exception $e) {
            $this->assertFalse(true, $e);
        }
    }
}
