<?php
namespace App\Repositories\User;
interface UserRepositoryInterface
{
    public function count();
    public function all();
    public function find($id);
    public function findBy($column, $option);
    public function paginate($limit);
    public function create($inputs);
    public function insert($inputs);
    public function update($inputs, $id);
    public function delete($ids);
    public function search($column, $value);
}
