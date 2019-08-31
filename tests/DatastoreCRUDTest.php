<?php

use PHPUnit\Framework\TestCase;
use Google\DatastoreCRUD;

final class DatastoreCRUDTest extends TestCase
{
    public function testCanEntityBeCreated(): void
    {
        $filepath = null;
        if (file_exists(__DIR__ . '/../keyfile.json')) {
            $filepath = __DIR__ . '/../keyfile.json';
        }

        $datastore = new DatastoreCRUD('Test', 'Test', $filepath);

        $datastore->setFields([
            'Test' => 'Test Me'
        ]);

        $return = $datastore->insert();

        $this->assertIsString($return);
    }

    public function testCanListEntities(): void
    {
        $filepath = null;
        if (file_exists(__DIR__ . '/../keyfile.json')) {
            $filepath = __DIR__ . '/../keyfile.json';
        }

        $datastore = new DatastoreCRUD('Test', 'Test', $filepath);

        $entities = $datastore->list();

        $this->assertIsArray($entities);
        $this->assertGreaterThan(0, count($entities));
    }

    public function testCanFindEntityWithId(): void
    {
        $filepath = null;
        if (file_exists(__DIR__ . '/../keyfile.json')) {
            $filepath = __DIR__ . '/../keyfile.json';
        }

        $datastore = new DatastoreCRUD('Test', 'Test', $filepath);

        $entities = $datastore->list();

        foreach ($entities as $entity) {
            $id = $datastore->getEntityId($entity);

            $entity = $datastore->find($id);

            $this->assertEquals('Test Me', $entity['Test']);
        }
    }

    public function testCanUpdateField(): void
    {
        $filepath = null;
        if (file_exists(__DIR__ . '/../keyfile.json')) {
            $filepath = __DIR__ . '/../keyfile.json';
        }

        $datastore = new DatastoreCRUD('Test', 'Test', $filepath);

        $entities = $datastore->list();

        foreach ($entities as $entity) {
            $id = $datastore->getEntityId($entity);

            $datastore->update($id, ['Test' => 'Update Test']);

            $entity = $datastore->find($id);

            $this->assertEquals('Update Test', $entity['Test']);

            $datastore->update($id, ['Test' => 'Test Me']);

            $entity = $datastore->find($id);

            $this->assertEquals('Test Me', $entity['Test']);
        }
    }

    public function testCanQuery(): void
    {
        $filepath = null;
        if (file_exists(__DIR__ . '/../keyfile.json')) {
            $filepath = __DIR__ . '/../keyfile.json';
        }

        $datastore = new DatastoreCRUD('Test', 'Test', $filepath);

        $entities = $datastore->query("SELECT * FROM Test");

        $this->assertInstanceOf('Google\Cloud\Datastore\EntityIterator', $entities);
    }

    public function testCanDelete(): void
    {
        $filepath = null;
        if (file_exists(__DIR__ . '/../keyfile.json')) {
            $filepath = __DIR__ . '/../keyfile.json';
        }

        $datastore = new DatastoreCRUD('Test', 'Test', $filepath);

        $entities = $datastore->list();

        foreach ($entities as $entity) {
            $id = $datastore->getEntityId($entity);

            $datastore->remove($id);
            $entity = $datastore->find($id);

            $this->assertNull($entity);
        }
    }
}
