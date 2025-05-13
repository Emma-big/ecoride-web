<?php
declare(strict_types=1);

namespace Adminlocal\EcoRide\Tests\Helpers;

use Adminlocal\EcoRide\Helpers\MongoHelper;
use PHPUnit\Framework\TestCase;
use MongoDB\Client;
use MongoDB\Collection;

final class MongoHelperTest extends TestCase
{
    public function testGetCollectionReturnsCollectionInstanceOrSkip(): void
    {
        if (! class_exists(Client::class)) {
            $this->markTestSkipped('mongodb extension not available');
        }

        $collection = MongoHelper::getCollection('test_collection');

        $this->assertInstanceOf(Collection::class, $collection);
        // le nom de la collection doit correspondre
        $this->assertSame('test_collection', $collection->getCollectionName());
    }
}
