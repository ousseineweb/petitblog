<?php

namespace App\Components;

use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use ACSEO\TypesenseBundle\Finder\TypesenseQuery;

#[AsLiveComponent('search')]
class SearchComponent
{
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public string $query = '';

    private $postFinder;

    public function __construct($postFinder)
    {
        $this->postFinder = $postFinder;
    }

    public function getPosts(): array
    {
        if (empty($this->query)) return [];

        $results = new TypesenseQuery($this->query, 'content');
        return $this->postFinder->rawQuery($results)->getResults();
    }
}