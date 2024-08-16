<?php
 namespace App\Services;

 use App\Repositories\Repository;
 use Illuminate\Support\Str;
 use Symfony\Component\HttpFoundation\Response;

use function PHPUnit\Framework\isNull;

class CrudService implements ICrudService{
    protected Repository $repository;
    protected string $entityName;

    public function __construct(string $entityName)
    {
        $this->entityName = strtolower(preg_replace('/\B([A-Z])/', '_$1', Str::pluralStudly($entityName)));
        $this->repository  = Repository::getRepository($entityName);
    }

    public function create(array $request ,\stdClass &$output) :void
    {

        if(isset($request['translation']))
            config(['globals.translation' => $request['translation']]);

        $entity = $this->repository->create($request);
        $output->{$this->entityName} = $entity;
    }

    public function createMany(array $objects ,\stdClass &$output) :void
    {
        $savedObjects = [];
        foreach ($objects as $object)
        {
            $this->create($object , $output);

            if(isset($output->Error))
                return;

            $savedObjects[] = $output->{$this->entityName};
        }

        $output->{$this->entityName} = $savedObjects;
    }

    public function update(array $request ,\stdClass &$output) :void
    {


        $entity = $this->repository->getById($request['id']);
        if(is_null($entity))
        {
            $output->Error = [__('errors.wrong_identifier')];
            return;
        }

        if(isset($request['translation']))
            config(['globals.translation' => $request['translation']]);

        $output->{$this->entityName} = $this->repository->update($entity , $request);
    }

    public function delete(array $request ,\stdClass &$output) :void
    {
        $output->{$this->entityName} = $this->repository->delete($request['ids']);
    }



    public function getById(array $request ,\stdClass &$output) :void
    {
        $entity = $this->repository->getById($request['id'] , $request['related_objects'] , $request['related_objects_count']);
        if(is_null($entity))
        {
            $output->Error = [__('errors.wrong_identifier')];
            return;
        }

        $output->{$this->entityName} = $entity;
    }

    public function getAll(array $request, \stdClass &$output): void
    {
        $output->{$this->entityName}= $this->repository->getAll($request['related_objects'])->paginate($request['page_size']);
    }
}

