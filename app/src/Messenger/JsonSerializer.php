<?php

namespace App\Messenger;

use \Symfony\Component\Serializer\SerializerInterface;
use Psr\Log\LoggerInterface;

class JsonSerializer implements SerializerInterface    
{
    public function serialize(mixed $data, string $format = 'json', array $context = []): string
    {    
        if ($format !== 'json') {
            throw new \InvalidArgumentException("Format must be 'json'");
        }
        return $this->marshal($data);
    }

    public function deserialize(mixed $data, string $type = '', string $format = 'json', array $context = []): mixed
    {
        if ($format !== 'json') { 
            throw new \InvalidArgumentException("Format must be 'json'");
        }
        return $this->unmarshal($data);
    }

    //-------------------------private methods-------------------------

    private function marshal($value): string
    {
        $dataType = gettype($value);
        $entity = [
            'serializedType' => $dataType,
        ];
        switch($dataType) {
            case 'resource':
            case 'unknown type':
                throw new \InvalidArgumentException("Error serializing value of type `{$dataType}`");
            case 'object': $entity['serializedData'] = $this->marshalObject($value);
                break;
            case 'array': $entity['serializedData'] = $this->marshalArray($value);
                break;
            default: $entity['serializedData'] = $value;
        }

        return json_encode($entity);
    }

    /**
     * @param array $config
     * @return mixed|null
     */
    private function unmarshal(string $json)  
    {
        $config = json_decode($json, true);
        $entity = null;
        $dataType = $config['serializedType'];
        switch($dataType) {
            case 'object': $entity = $this->unmarshalObject($config['serializedData']);
                break;
            case 'array': $entity = $this->unmarshalArray($config['serializedData']);
                break;
            default: $entity = $config['serializedData'];
        }

        return $entity;
    }

    /**
     * @param object $object
     * @return array
     */
    private function marshalObject($object): array 
    {
        $entity = [
            'class' => get_class($object),
        ];
        
        $attributes = array_keys(get_object_vars($object));

        foreach ($attributes as $attribute) {
            $entity['attributes'][$attribute] = $this->marshal($object->$attribute);
        }

        return $entity;
    }

    /**
     * @param array $config
     * @return mixed
     */
    private function unmarshalObject(array $config): object
    {
        $className = $config['class'];
        $attributes = $config['attributes'];

        //try {
            $object = new $className();
            foreach ($attributes as $attribute => $value) {
                $object->$attribute = $this->unmarshal($value);
            }
        ////}
        //catch (\Throwable $e) {
        //    $object = null;
        //}

        return $object;
    }

    private function marshalArray($array): array
    {
        return array_map(fn($item) => $this->marshal($item), $array);
    }

    private function unmarshalArray($array): array
    {
        return array_map(fn($item) => $this->unmarshal($item), $array);
    }
}