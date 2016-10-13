<?php
namespace iRESTful\Classes\Infrastructure\Factories;
use iRESTful\Classes\Domain\Namespaces\Adapters\Factories\ClassNamespaceAdapterFactory;
use iRESTful\Classes\Infrastructure\Adapters\ConcreteNamespaceAdapter;
use iRESTful\Classes\Infrastructure\Adapters\ConcreteClassNamespaceAdapter;

final class ConcreteClassNamespaceAdapterFactory implements ClassNamespaceAdapterFactory {
    private $baseNamespace;
    public function __construct(array $baseNamespace) {
        $this->baseNamespace = $baseNamespace;
    }

    public function create() {
        $namespaceAdapter = new ConcreteNamespaceAdapter($this->baseNamespace);
        return new ConcreteClassNamespaceAdapter($namespaceAdapter);
    }

}
