<?php

namespace App\Actions\Common;

use Illuminate\Http\Resources\Json\ResourceCollection;

class BaseResourceCollection extends ResourceCollection
{
    /**
     * @var string[]
     */
    public $with = [
        'status' => 'success',
    ];

    protected ?string $modelClass;

    public function __construct($resource, ?string $modelClass = null)
    {
        parent::__construct($resource);
        $this->modelClass = $modelClass;
    }

    public function guessResourceKey(): string
    {
        if ($this->modelClass !== null) {
            return (new $this->modelClass())->guessResourceKey();
        }

        $resource = $this->collection->first();
        if ($resource !== null && method_exists($resource, 'guessResourceKey')) {
            return $resource->guessResourceKey();
        }

        return 'items';
    }

    /**
     * @phpcs:disable VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
     *
     * @phpcs:disable Generic.CodeAnalysis.UnusedFunctionParameter
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function toArray($request): array
    {
        return [
            $this->guessResourceKey() => $this->collection,
        ];
    }

    public function getModelClass(): ?string
    {
        return $this->modelClass;
    }
}
