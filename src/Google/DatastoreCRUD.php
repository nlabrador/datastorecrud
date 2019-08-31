<?php

namespace Google;

use Google\Cloud\Datastore\DatastoreClient;

class DatastoreCRUD
{
    private $entity;
    private $fields = [];
    protected $datastore;

    /***
     * Constructor
     * @param $namespace - String of Entity Namespace
     * @param $kind - String of Entity Kind
     * @param $keyJsonFilepath - String of filepath of Google service json file
     */
    public function __construct($namespace, $kind, $keyJsonFilepath = null) {
        $default = [
            'namespaceId' => $namespace
        ];

        if ($keyJsonFilepath) {
            $default['keyFilePath'] = $keyJsonFilepath;
        }
        
        $datastore = new DatastoreClient($default);

        $this->datastore = $datastore;
        $this->entity = $kind;
    }

    /***
     * Set $entity
     */
    public function setEntity($entity) {
        $this->entity = $entity;
    }

    /***
     * Get $entity
     */
    public function getEntity() {
        return $this->entity;
    }

    /***
     * Set $fields
     * @param $fields - Array of key => value of Entity fields
     */
    public function setFields($fields) {
        $this->fields = $fields;
    }

    /***
     * Get $fields
     */
    public function getFields() {
        return $this->fields;
    }

    /***
     * Insert new data
     * Requires string value for $entity
     * Requires array of key => value pair for $fields. Call setFields method
     */
    public function insert() {
        if ($this->entity && is_array($this->fields) && !empty($this->fields)) {
            $newdata = $this->datastore->entity($this->entity);

            foreach ($this->fields as $key => $value) {
                $newdata[$key] = $value;
            }

            return $this->datastore->insert($newdata);
        }
        else {
            throw new Exception('Need to set values for string $entity which is the value of Kind in DataStore and array key => value $fields.');
        }
    }

    /***
     * Find certain id from an entity
     * @param $id - String
     * @return Entity object or null
     */
    public function find($id) {
        if ($this->entity) {
            $key = $this->datastore->key($this->entity, $id);

            return $this->datastore->lookup($key);
        }
        else {
            throw new Exception('Need to set values for string $entity which is the value of Kind in DataStore.');
        }
    }

    /***
     * Update entity with new data
     * Requires string value for $id of the Entity to update
     * Requires array of key => value pair for $fields. Call setFields method
     */
    public function update($id, $fields) {
        if ($this->entity) {
            $entity = $this->find($id);

            foreach ($fields as $key => $value) {
                $entity[$key] = $value;
            }

            $this->datastore->update($entity);
        }
        else {
            throw new Exception('Need to set values for string $entity which is the value of Kind in DataStore.');
        }
    }

    /***
     * Remove entry
     * Requires string value for $id of Entity to delete
     */
    public function remove($id) {
        if ($this->entity) {
            $entity = $this->datastore->key($this->entity, $id);

            $this->datastore->delete($entity);
        }
        else {
            throw new Exception('Need to set values for string $entity which is the value of Kind in DataStore.');
        }
    }

    /***
     * List all entries of an entity
     * @return Array of Entity object or empty array
     */
    public function list() {
        if ($this->entity) {
            $result = $this->query("SELECT * FROM ".$this->entity);

            foreach ($result as $entity) {
                $entities[] = $entity; 
            }

            return $entities;
        }
        else {
            throw new Exception('Need to set values for string $entity which is the value of Kind in DataStore.');
        }
    }

    /***
     * Create own gqpQuery to fetch data.
     * Example query("SELECT * FROM Employee")
     */
    public function query($query) {
        $query = $this->datastore->gqlQuery($query);
        $result = $this->datastore->runQuery($query); 

        return $result;
    }

    public function getEntityId($entity) {
        $entity = (array) $entity;
        
        $key = array_shift($entity);
        $key = (array) $key;
        array_shift($key);
        $key = array_shift($key);
        $key = array_shift($key);

        return $key['id'];
    }
}
