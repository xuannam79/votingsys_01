<?php

namespace App\RepositoriesApi\Contracts;

interface AbstractRepositoryInterface
{
    public function currentUser();

    public function getModel();

    public function count();

    public function all();

    public function find($id);

    public function findBy($column, $option);

    public function findWhere($array = [], $column = ['*']);

    public function paginate($limit);

    public function create($inputs = []);

    public function insert($inputs = []);

    public function update($inputs = [], $id);

    public function delete($ids);

    public function show($id);
}
