<?php

namespace App\Http\Controllers\Admin;

use App\Filter\UsersFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserCreateRequest;
use App\Http\Requests\UserEditRequest;
use App\Models\User;
use App\Repositories\User\UserRepositoryInterface;
use Illuminate\Http\Request;

class UserController extends Controller
{
    private $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(UsersFilter $filters)
    {
        $users =  User::filter($filters)->where('role', config('roles.user'))->paginate(config('settings.number_of_record_user'));
        $input = $filters->input();
        $linkFilter = $users->appends($input)->links();
        $data = [
            'gender' => [
                config('settings.gender_constant.male') => trans('user.label.gender.male'),
                config('settings.gender_constant.female') => trans('user.label.gender.female'),
                config('settings.gender_constant.other') => trans('user.label.gender.other'),
            ],
        ];

        return view('admins.user.index', compact('users', 'input', 'linkFilter', 'data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admins.user.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  UserCreateRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserCreateRequest $request)
    {
        $input = $request->only(
            'name', 'email', 'chatwork_id', 'gender', 'avatar', 'password'
        );
        $this->userRepository->register($input);

        return redirect()->route('admin.user.index')->with('message', trans('user.message.create_success'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admins.user.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UserEditRequest $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UserEditRequest $request, $id)
    {
        $input = $request->only(
            'name', 'email', 'chatwork_id', 'gender', 'avatar'
        );
        $this->userRepository->update($input, $id);

        return redirect()->route('admin.user.index')->with('message', trans('user.message.update_success'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $message = $this->userRepository->delete($id);

        return redirect()->route('admin.user.index')->with('message', $message);
    }
}
