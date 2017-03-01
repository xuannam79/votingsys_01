<?php
namespace App\RepositoriesApi;

use Illuminate\Database\Eloquent\Model;
use App\RepositoriesApi\Contracts\AbstractRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User;

abstract class AbstractRepositoryEloquent implements AbstractRepositoryInterface
{
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function currentUser()
    {
        return Auth::guard('api')->user();
    }

    public function getModel()
    {
        return $this->model;
    }

    public function count()
    {
        return $this->model->count();
    }

    public function all()
    {
        return $this->model->all();
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function findBy($column, $option)
    {
        return $this->model->where($column, $option)->get();
    }

    public function findWhere($wheres = [], $columns = ['*']) {
        foreach ($wheres as $field => $value) {
            $this->model = $this->model->where($field, '=', $value);
        }

        return $model = $this->model->get($columns);
    }

    public function paginate($limit)
    {
        return $this->model->paginate($limit);
    }

    public function create($inputs = [])
    {
        return $this->model->create($inputs);
    }

    public function insert($inputs = [])
    {
        $now = Carbon::now();
        foreach ($inputs as $input) {
            $input['created_at'] = $now;
            $input['updated_at'] = $now;
        }

        return $this->model->insert($inputs);
    }

    public function update($inputs = [], $id)
    {
        return $this->model->find($id)->update($inputs);
    }

    public function delete($ids)
    {
        return $this->model->destroy($ids);
    }

    public function show($id)
    {
        return $this->model->find($id);
    }

    public function getUserId($email = null)
    {
        if ($email) {
            $user = app(User::class)->where('email', $email)->first();

            return ($user ? $user->id : null);
        }

        $authUser = $this->currentUser();

        return $authUser ? $authUser->id : null;
    }
}
