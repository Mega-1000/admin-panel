<?php

namespace App\Http\Controllers;

use App\Entities\ColumnVisibility;
use App\Http\Requests\UserCreateRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Repositories\UserRepository;
use App\Repositories\UserEmailRepository;
use App\Repositories\WarehouseRepository;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Hash;
use TCG\Voyager\Models\Role;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

/**
 * Class UserController
 * @package App\Http\Controllers
 */
class UserController extends Controller
{
    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @var UserEmailRepository
     */
    protected $userEmailRepository;

    protected $warehouseRepository;

    /**
     * ClientsController constructor.
     *
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository, UserEmailRepository $userEmailRepository, WarehouseRepository $warehouseRepository)
    {
        $this->userRepository = $userRepository;
        $this->userEmailRepository = $userEmailRepository;
        $this->warehouseRepository = $warehouseRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $visibilities = ColumnVisibility::getVisibilities(ColumnVisibility::getModuleId('users'));
        foreach($visibilities as $key => $row)
        {
            $visibilities[$key]->show = json_decode($row->show,true);
            $visibilities[$key]->hidden = json_decode($row->hidden,true);
        }
        return view('users.index',compact('visibilities'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Role::all();
        $warehouses = $this->warehouseRepository->findByField('symbol', 'MEGA-OLAWA');
        return view('users.create', compact('roles', 'warehouses'));
    }

    /**
     * @param UserCreateRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(UserCreateRequest $request)
    {
        $this->checkRole($request->role_id);

        $user = $this->userRepository->create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'role_id' => $request->input('role'),
            'password' => Hash::make($request->input('password')),
            'firstname' => $request->input('firstname'),
            'lastname' => $request->input('lastname'),
            'phone' => $request->input('phone'),
            'phone2' => $request->input('phone2'),
            'warehouse_id' => $request->input('warehouse_id'),
            'rate_hour' => $request->input('rate_hour')
        ]);

        $this->userEmailRepository->create([
            'username' => $request->input('email-username'),
            'host' => $request->input('host'),
            'port' => $request->input('port'),
            'password' => $request->input('email-password'),
            'encryption' => $request->input('encryption'),
            'user_id' => $user->id,
        ]);

        return redirect()->route('users.index')->with([
            'message' => __('users.message.store'),
            'alert-type' => 'success'
        ]);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $currentUser = Auth::user();
        $user = $this->userRepository->find($id);
        $roles = Role::all();
        $selectedRole = $user->role_id;
        $userEmail = $user->userEmailData;
        $warehouses = $this->warehouseRepository->findByField('symbol', 'MEGA-OLAWA');
        if ($currentUser->role_id !== 1 && $user->id === 1) {
            abort(403, 'Nieautoryzowana akcja');
        }
        if (empty($user)) {
            abort(404);
        }

        return view('users.edit', compact('user', 'roles', 'selectedRole', 'userEmail', 'warehouses'));
    }


    /**
     * @param UserUpdateRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UserUpdateRequest $request, $id)
    {
        unset($request->email);

        $currentUser = Auth::user();

        if ($currentUser->role_id == '2') {
            unset($request->status);
            unset($request->role_id);
        }
        $this->checkRole($request->role_id);
        $user = $this->userRepository->find($id);
        if (empty($user)) {
            abort(404);
        }
        $dataToStore = $request->all();

        if ($dataToStore['password'] !== null) {
            $dataToStore['password'] = bcrypt($dataToStore['password']);
        } else {
            unset($dataToStore['password']);
        }
        $avatar = $request->file('avatar');
        if ($request->file('avatar') !== null) {
            $this->removeAvatar($user);
            $dataToStore['avatar'] = $this->storeAvatar($avatar);
        }

        $this->userRepository->update($dataToStore, $user->id);

//        if($request->warehouse_id !== null){
//            dispatch_now(new AddNewWorkHourForUsers());
//        }

        $emailData = $this->userEmailRepository->findWhere(['user_id' => $user->id])->first();
        if(empty($emailData)) {
            $this->userEmailRepository->create([
                'username' => $request->input('email-username'),
                'host' => $request->input('host'),
                'port' => $request->input('port'),
                'password' => $request->input('email-password'),
                'encryption' => $request->input('encryption'),
                'user_id' => $user->id,
            ]);
        } else {
            $this->userEmailRepository->update([
                'username' => $request->input('email-username'),
                'host' => $request->input('host'),
                'port' => $request->input('port'),
                'password' => $request->input('email-password'),
                'encryption' => $request->input('encryption'),
                'user_id' => $user->id,
            ], $emailData->id);
        }


        return redirect()->back()->with([
            'message' => __('users.message.update'),
            'alert-type' => 'success'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $currentUser = Auth::user();
        $user = $this->userRepository->find($id);
        if ($currentUser->role_id !== 1 && $user->role_id === 1) {
            abort(403, 'Nieautoryzowana akcja');
        }
        if ($currentUser->role_id > 2 && $user->id === 2 || $currentUser->role_id > 2 && $user->id === 1) {
            abort(403, 'Nieautoryzowana akcja');
        }
        if (empty($user)) {
            abort(404);
        }
        $this->removeAvatar($user);
        $user->delete($user->id);

        return redirect()->route('users.index')->with([
            'message' => __('users.message.delete'),
            'alert-type' => 'info'
        ]);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changeStatus($id)
    {
        $currentUser = Auth::user();
        $user = $this->userRepository->find($id);
        if ($currentUser->id == 2 && $user->id === 2) {
            abort(403, 'Nieautoryzowana akcja');
        }
        if (empty($user)) {
            abort(404);
        }
        $dataToStore = [];
        $dataToStore['status'] = $user['status'] === 'ACTIVE' ? 'PENDING' : 'ACTIVE';
        $this->userRepository->update($dataToStore, $user->id);

        return redirect()->back()->with([
            'message' => __('users.message.change_status'),
            'alert-type' => 'success'
        ]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function datatable()
    {
        $collection = $this->prepareCollection();

        return DataTables::collection($collection)->make(true);
    }

    /**
     * @return mixed
     */
    public function prepareCollection()
    {
        $currentUser = Auth::user();
        $collection = $this->userRepository->all();
        if ($currentUser->role_id !== 1) {
            $collection->shift();
        }
        if ($currentUser->role_id > 2) {
            $collection->shift();
        }

        return $collection;
    }

    /**
     * Detele user avatar from storage
     *
     * @param $user
     *
     * @return bool
     */
    private function removeAvatar($user)
    {
        if (basename($user->avatar) !== 'default.png' && Storage::disk('public')->exists($user->avatar)) {
            Storage::delete('public/' . $user->avatar);

            return true;
        }

        return false;
    }

    /**
     * Store uploaded image
     *
     * @param $avatar
     *
     * @return string
     */
    private function storeAvatar($avatar)
    {
        $path = 'users/' . date('F') . date('Y');
        $avatarPath = $avatar->store('public/' . $path);
        return $path . '/' . basename($avatarPath);
    }

    /**
     * @return bool
     */
    private function checkRole($role)
    {
        if (Auth::user()->role_id != 1 && $role == 1) {
            abort(403, 'Nieautoryzowana akcja');
        }
        return true;
    }
}
