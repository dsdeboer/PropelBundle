<?php

namespace Propel\Bundle\PropelBundle\Tests\Translation;

use Propel\Bundle\PropelBundle\Tests\Fixtures\Model\Translation as Entry;
use Propel\Bundle\PropelBundle\Tests\TestCase;
use Propel\Bundle\PropelBundle\Translation\ModelTranslation;
use Symfony\Component\Translation\MessageCatalogue;

/**
 *
 * @covers Propel\Bundle\PropelBundle\Translation\ModelTranslation
 */
class ModelTranslationTest extends TestCase
{
    const MODEL_CLASS = 'Propel\Bundle\PropelBundle\Tests\Fixtures\Model\Translation';

    /**
     * @var \PropelPDO
     */
    protected $con;

    public function setUp()
    {
        parent::setUp();

        $this->loadPropelQuickBuilder();

        $schema = file_get_contents(__DIR__ . '/../Fixtures/translation_schema.xml');

        $builder = new \PropelQuickBuilder();
        $builder->setSchema($schema);
        if (class_exists('Propel\Bundle\PropelBundle\Tests\Fixtures\Model\map\TranslationTableMap')) {
            $builder->setClassTargets([]);
        }

        $this->con = $builder->build();
    }

    public function testRegisterResources()
    {
        $translation = new Entry();
        $translation
            ->setKey('example.key')
            ->setMessage('This is an example translation.')
            ->setLocale('en_US')
            ->setDomain('test')
            ->setUpdatedAt(new \DateTime())
            ->save();

        $resource = $this->getResource();

        $translator = $this->getMockBuilder('Symfony\Component\Translation\Translator')
            ->setConstructorArgs(['en_US'])
            ->getMock();

        $translator
            ->expects($this->once())
            ->method('addResource')
            ->with('propel', $resource, 'en_US', 'test');

        $resource->registerResources($translator);
    }

    protected function getResource()
    {
        return new ModelTranslation(self::MODEL_CLASS, [
            'columns' => [
                'translation' => 'message',
            ],
        ]);
    }

    public function testIsFreshWithoutEntries()
    {
        $resource = $this->getResource();

        $this->assertTrue($resource->isFresh(date('U')));
    }

    public function testIsFreshUpdates()
    {
        $date = new \DateTime('-2 minutes');

        $translation = new Entry();
        $translation
            ->setKey('example.key')
            ->setMessage('This is an example translation.')
            ->setLocale('en_US')
            ->setDomain('test')
            ->setUpdatedAt($date)
            ->save();

        $resource = $this->getResource();

        $timestamp = (int)$date->format('U');

        $this->assertFalse($resource->isFresh($timestamp - 10));
    }

    public function testLoadInvalidResource()
    {
        $invalidResource = $this->createMock('Symfony\Component\Config\Resource\ResourceInterface');

        $resource  = $this->getResource();
        $catalogue = $resource->load($invalidResource, 'en_US');

        $this->assertEmpty($catalogue->getResources());
    }

    public function testLoadFiltersLocaleAndDomain()
    {
        $date = new \DateTime();

        $translation = new Entry();
        $translation
            ->setKey('example.key')
            ->setMessage('This is an example translation.')
            ->setLocale('en_US')
            ->setDomain('test')
            ->setUpdatedAt($date)
            ->save();

        // different locale
        $translation = new Entry();
        $translation
            ->setKey('example.key')
            ->setMessage('Das ist eine Beispielübersetzung.')
            ->setLocale('de_DE')
            ->setDomain('test')
            ->setUpdatedAt($date)
            ->save();

        // different domain
        $translation = new Entry();
        $translation
            ->setKey('example.key')
            ->setMessage('This is an example translation.')
            ->setLocale('en_US')
            ->setDomain('test2')
            ->setUpdatedAt($date)
            ->save();

        $resource  = $this->getResource();
        $catalogue = $resource->load($resource, 'en_US', 'test');

        $this->assertInstanceOf('Symfony\Component\Translation\MessageCatalogue', $catalogue);
        $this->assertEquals('en_US', $catalogue->getLocale());

        $expected = [
            'test' => [
                'example.key' => 'This is an example translation.',
            ],
        ];

        $this->assertEquals($expected, $catalogue->all());
    }

    public function testDump()
    {
        $catalogue = new MessageCatalogue('en_US', [
            'test'  => [
                'example.key' => 'This is an example translation.',
            ],
            'test2' => [
                'example.key' => 'This is an example translation.',
            ],
        ]);

        $resource = $this->getResource();
        $this->assertEmpty($resource->load($resource, 'en_US', 'test')->all());

        $resource->dump($catalogue);

        $stmt = $this->con->prepare('SELECT `key`, `message`, `locale`, `domain` FROM `translation`;');
        $stmt->execute();

        $result = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $result[] = $row;
        }

        $expected = [
            [
                'key'     => 'example.key',
                'message' => 'This is an example translation.',
                'locale'  => 'en_US',
                'domain'  => 'test',
            ],
            [
                'key'     => 'example.key',
                'message' => 'This is an example translation.',
                'locale'  => 'en_US',
                'domain'  => 'test2',
            ],
        ];

        $this->assertEquals($expected, $result);
    }
}
