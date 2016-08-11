<?php
namespace iRESTful\Rodson\Infrastructure\Inputs\Objects;
use iRESTful\Rodson\Domain\Inputs\Controllers\HttpRequests\Commands\Actions\Action;
use iRESTful\Rodson\Domain\Inputs\Controllers\HttpRequests\Commands\Actions\Exceptions\ActionException;

final class ConcreteControllerHttpRequestCommandAction implements Action {
    private $isRetrieval;
    private $isInsert;
    private $isUpdate;
    private $isDelete;
    public function __construct($isRetrieval, $isInsert, $isUpdate, $isDelete) {

        $amount = ($isRetrieval ? 1 : 0) + ($isInsert ? 1 : 0) + ($isUpdate ? 1 : 0) + ($isDelete ? 1 : 0);
        if ($amount != 1) {
            throw new ActionException('The action can be either retrieve, insert, update or delete.  '.$amount.' provided.');
        }

        $this->isRetrieval = (bool) $isRetrieval;
        $this->isInsert = (bool) $isInsert;
        $this->isUpdate = (bool) $isUpdate;
        $this->isDelete = (bool) $isDelete;

    }

    public function isRetrieval() {
        return $this->isRetrieval;
    }

    public function isInsert() {
        return $this->isInsert;
    }

    public function isUpdate() {
        return $this->isUpdate;
    }

    public function isDelete() {
        return $this->isDelete;
    }

}
