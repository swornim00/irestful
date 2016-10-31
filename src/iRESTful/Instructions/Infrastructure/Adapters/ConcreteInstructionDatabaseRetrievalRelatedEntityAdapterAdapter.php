<?php
namespace iRESTful\Instructions\Infrastructure\Adapters;
use iRESTful\Instructions\Domain\Databases\Retrievals\Relations\Adapters\Adapters\RelatedEntityAdapterAdapter;
use iRESTful\Instructions\Infrastructure\Adapters\ConcreteInstructionDatabaseRetrievalRelatedEntityAdapter;
use iRESTful\Instructions\Domain\Values\Adapters\Adapters\ValueAdapterAdapter;
use iRESTful\Instructions\Domain\Containers\Adapters\Adapters\ContainerAdapterAdapter;

final class ConcreteInstructionDatabaseRetrievalRelatedEntityAdapterAdapter implements RelatedEntityAdapterAdapter {
    private $valueAdapterAdapter;
    private $containerAdapterAdapter;
    public function __construct(
        ValueAdapterAdapter $valueAdapterAdapter,
        ContainerAdapterAdapter $containerAdapterAdapter
    ) {
        $this->valueAdapterAdapter = $valueAdapterAdapter;
        $this->containerAdapterAdapter = $containerAdapterAdapter;
    }

    public function fromDataToRelatedEntityAdapter(array $data) {
        $constants = empty($data['constants']) ? [] : $data['constants'];
        $valueAdapter = $this->valueAdapterAdapter->fromDataToValueAdapter([
            'constants' => $constants
        ]);

        $containerAdapter = $this->containerAdapterAdapter->fromDataToContainerAdapter($data);
        return new ConcreteInstructionDatabaseRetrievalRelatedEntityAdapter($valueAdapter, $containerAdapter);
    }

}
