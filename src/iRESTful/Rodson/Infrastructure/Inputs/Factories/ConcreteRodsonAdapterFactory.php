<?php
namespace iRESTful\Rodson\Infrastructure\Inputs\Factories;
use iRESTful\Rodson\Domain\Inputs\Adapters\Factories\RodsonAdapterFactory;
use iRESTful\Rodson\Infrastructure\Inputs\Adapters\ConcreteRodsonAdapter;
use iRESTful\Rodson\Infrastructure\Inputs\Adapters\ConcreteObjectAdapter;
use iRESTful\Rodson\Infrastructure\Inputs\Adapters\ConcreteObjectPropertyAdapter;
use iRESTful\Rodson\Infrastructure\Inputs\Adapters\ConcreteControllerAdapter;
use iRESTful\Rodson\Infrastructure\Inputs\Adapters\ConcreteDatabaseAdapter;
use iRESTful\Rodson\Infrastructure\Inputs\Adapters\ConcreteRelationalDatabaseAdapter;
use iRESTful\Rodson\Infrastructure\Inputs\Adapters\ConcreteDatabaseCredentialsAdapter;
use iRESTful\Rodson\Infrastructure\Inputs\Adapters\ConcreteRESTAPIAdapter;
use iRESTful\Rodson\Infrastructure\Inputs\Adapters\ConcreteTypeAdapter;
use iRESTful\Rodson\Infrastructure\Inputs\Adapters\ConcreteDatabaseTypeAdapter;
use iRESTful\Rodson\Infrastructure\Inputs\Adapters\ConcreteDatabaseTypeBinaryAdapter;
use iRESTful\Rodson\Infrastructure\Inputs\Adapters\ConcreteDatabaseTypeFloatAdapter;
use iRESTful\Rodson\Infrastructure\Inputs\Adapters\ConcreteDatabaseTypeIntegerAdapter;
use iRESTful\Rodson\Infrastructure\Inputs\Adapters\ConcreteDatabaseTypeStringAdapter;
use iRESTful\Rodson\Infrastructure\Inputs\Adapters\ConcreteCodeMethodAdapter;
use iRESTful\Rodson\Infrastructure\Inputs\Adapters\ConcreteAdapterAdapter;
use iRESTful\Rodson\Infrastructure\Inputs\Adapters\ConcreteCodeAdapter;
use iRESTful\Rodson\Infrastructure\Inputs\Adapters\ConcreteCodeLanguageAdapter;
use iRESTful\Rodson\Domain\Inputs\Codes\Code;
use iRESTful\Rodson\Domain\Inputs\Codes\Exceptions\CodeException;
use iRESTful\Rodson\Domain\Inputs\Databases\Exceptions\DatabaseException;
use iRESTful\Rodson\Domain\Inputs\Adapters\Exceptions\AdapterException;
use iRESTful\Rodson\Domain\Inputs\Types\Exceptions\TypeException;
use iRESTful\Rodson\Domain\Inputs\Exceptions\RodsonException;
use iRESTful\Rodson\Infrastructure\Inputs\Adapters\ConcreteObjectPropertyTypeAdapter;
use iRESTful\Rodson\Infrastructure\Inputs\Adapters\ConcreteObjectMethodAdapter;
use iRESTful\Rodson\Infrastructure\Inputs\Adapters\ConcreteAdapterTypeAdapter;
use iRESTful\Rodson\Infrastructure\Inputs\Factories\ConcretePrimitiveFactory;
use iRESTful\Rodson\Infrastructure\Inputs\Adapters\ConcreteObjectSampleAdapter;
use iRESTful\Rodson\Infrastructure\Inputs\Adapters\ConcreteControllerHttpRequestAdapterAdapter;
use iRESTful\Rodson\Infrastructure\Inputs\Adapters\ConcreteControllerHttpRequestCommandAdapter;
use iRESTful\Rodson\Infrastructure\Inputs\Adapters\ConcreteControllerHttpRequestCommandActionAdapter;
use iRESTful\Rodson\Infrastructure\Inputs\Adapters\ConcreteControllerHttpRequestCommandUrlAdapter;
use iRESTful\Rodson\Infrastructure\Inputs\Adapters\ConcreteControllerHttpRequestViewAdapter;
use iRESTful\Rodson\Infrastructure\Inputs\Adapters\ConcreteValueAdapterAdapter;
use iRESTful\Rodson\Infrastructure\Inputs\Adapters\ConcreteControllerViewTemplateAdapter;
use iRESTful\Rodson\Infrastructure\Inputs\Adapters\ConcreteControllerViewAdapter;

final class ConcreteRodsonAdapterFactory implements RodsonAdapterFactory {
    private $codeData;
    private $adaptersData;
    private $databasesData;
    private $typesData;
    private $objectsData;
    public function __construct(array $codeData, array $adaptersData, array $databasesData, array $typesData, array $objectsData) {
        $this->codeData = $codeData;
        $this->adaptersData = $adaptersData;
        $this->databasesData = $databasesData;
        $this->typesData = $typesData;
        $this->objectsData = $objectsData;
    }

    private function getCode() {
        $languageAdapter = new ConcreteCodeLanguageAdapter();
        $codeAdapter = new ConcreteCodeAdapter($languageAdapter);
        return $codeAdapter->fromDataToCode($this->codeData);
    }

    private function getDatabases() {
        $credentialsAdapter = new ConcreteDatabaseCredentialsAdapter();
        $relationalDatabaseAdapter = new ConcreteRelationalDatabaseAdapter($credentialsAdapter);
        $restAPIAdapter = new ConcreteRESTAPIAdapter($credentialsAdapter);
        $databaseAdapter = new ConcreteDatabaseAdapter($relationalDatabaseAdapter, $restAPIAdapter);
        return $databaseAdapter->fromDataToDatabases($this->databasesData);
    }

    private function getAdapters(Code $code, array $types, array $primitives) {
        $methodAdapter = new ConcreteCodeMethodAdapter($code);
        $adapterTypeAdapter = new ConcreteAdapterTypeAdapter();
        $adapterAdapter = new ConcreteAdapterAdapter($adapterTypeAdapter, $methodAdapter, $types, $primitives);
        return $adapterAdapter->fromDataToAdapters($this->adaptersData);
    }

    private function getTypes(Code $code, array $primitives) {

        $typesData = $this->typesData;
        $methodAdapter = new ConcreteCodeMethodAdapter($code);

        $getTypeAdapter = function(array $adapters) use(&$methodAdapter) {
            $binaryAdapter = new ConcreteDatabaseTypeBinaryAdapter();
            $floatAdapter = new ConcreteDatabaseTypeFloatAdapter();
            $integerAdapter = new ConcreteDatabaseTypeIntegerAdapter();
            $stringAdapter = new ConcreteDatabaseTypeStringAdapter();
            $databaseTypeAdapter = new ConcreteDatabaseTypeAdapter($binaryAdapter, $floatAdapter, $integerAdapter, $stringAdapter);
            return new ConcreteTypeAdapter($databaseTypeAdapter, $methodAdapter, $adapters);
        };

        $getValidTypes = function() use(&$getTypeAdapter, &$typesData) {
            $typeAdapter = $getTypeAdapter([]);
            return $typeAdapter->fromDataToValidTypes($typesData);
        };

        $getTypes = function(array $adapters) use(&$getTypeAdapter, &$typesData) {
            $typeAdapter = $getTypeAdapter($adapters);
            return $typeAdapter->fromDataToTypes($typesData);
        };

        $types = $getValidTypes();
        $adapters = $this->getAdapters($code, $types, $primitives);
        return $getTypes($adapters);
    }

    private function getObjectAdapter(Code $code, array $types, array $primitives, array $objects) {
        $databases = $this->getDatabases();

        $sampleAdapter = new ConcreteObjectSampleAdapter();
        $methodAdapter = new ConcreteCodeMethodAdapter($code);
        $objectMethodAdapter = new ConcreteObjectMethodAdapter($methodAdapter);

        $propertyTypeAdapter = new ConcreteObjectPropertyTypeAdapter($types, $primitives, $objects);
        $propertyAdapter = new ConcreteObjectPropertyAdapter($propertyTypeAdapter);
        return new ConcreteObjectAdapter($objectMethodAdapter, $propertyAdapter, $sampleAdapter, $databases);
    }

    private function getObjects(Code $code, array $types, array $primitives) {

        $objects = [];
        $amountObjectsData = count($this->objectsData);
        $amountNewObjects = 0;

        while($amountNewObjects != $amountObjectsData) {
            $objectAdapter = $this->getObjectAdapter($code, $types, $primitives, $objects);
            $newObjects = $objectAdapter->fromDataToValidObjects($this->objectsData);
            $amountNewObjects = count($newObjects);
            if ($amountNewObjects == count($objects)) {
                break;
            }

            $objects = $newObjects;
        }

        return $objects;

    }

    public function create() {

        try {

            $primitiveFactory = new ConcretePrimitiveFactory();
            $primitives = $primitiveFactory->createAll();

            $code = $this->getCode();
            $types = $this->getTypes($code, $primitives);
            $objects = $this->getObjects($code, $types, $primitives);

            $valueAdapterAdapter = new ConcreteValueAdapterAdapter();
            $controllerHttpRequestCommandActionAdapter = new ConcreteControllerHttpRequestCommandActionAdapter();
            $controllerHttpRequestCommandUrlAdapter = new ConcreteControllerHttpRequestCommandUrlAdapter();
            $controllerHttpRequestCommandAdapter = new ConcreteControllerHttpRequestCommandAdapter($controllerHttpRequestCommandActionAdapter, $controllerHttpRequestCommandUrlAdapter);
            $controllerHttpRequestViewAdapter = new ConcreteControllerHttpRequestViewAdapter();
            $controllerHttpRequestAdapterAdapter = new ConcreteControllerHttpRequestAdapterAdapter($controllerHttpRequestCommandAdapter, $controllerHttpRequestViewAdapter, $valueAdapterAdapter);
            $controllerViewTemplateAdapter = new ConcreteControllerViewTemplateAdapter();
            $controllerViewAdapter = new ConcreteControllerViewAdapter($controllerViewTemplateAdapter);
            $controllerAdapter = new ConcreteControllerAdapter($controllerViewAdapter, $controllerHttpRequestAdapterAdapter);
            $objectAdapter = $this->getObjectAdapter($code, $types, $primitives, $objects);
            return new ConcreteRodsonAdapter($objectAdapter, $controllerAdapter);

        } catch (CodeException $exception) {
            throw new RodsonException('There was an exception while converting data to a Code object.', $exception);
        } catch (DatabaseException $exception) {
            throw new RodsonException('There was an exception while converting data to Database objects.', $exception);
        } catch (AdapterException $exception) {
            throw new RodsonException('There was an exception while converting data to Adapter objects.', $exception);
        } catch (TypeException $exception) {
            throw new RodsonException('There was an exception while converting data to Type objects.', $exception);
        }

    }

}
