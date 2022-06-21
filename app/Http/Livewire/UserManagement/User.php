<?php

namespace App\Http\Livewire\UserManagement;

use App\Models\Role;
use App\Models\Team;
use App\Models\User as ModelsUser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Str;

class User extends Component
{
    use WithFileUploads;
    public $users_id;
    public $role_id;
    public $team_id = 1;
    public $username;
    public $name;
    public $email;
    public $password;
    public $vcf_info;
    public $vcf_info_path;


    public $form_active = false;
    public $form = false;
    public $update_mode = false;
    public $modal = true;
    protected $listeners = ['getDataUserById', 'getUserId'];
    public $route_name = null;

    public function mount()
    {
        $this->route_name = request()->route()->getName();
        $this->username = Str::random(8);
    }
    public function render()
    {
        return view('livewire.usermanagement.users', [
            'roles' => Role::where('role_type', 'member')->get()
        ]);
    }

    public function store()
    {
        $this->_validate();
        $role = Role::find($this->role_id);

        $data = [
            'username'  => $this->username,
            'name'  => $this->name,
            'email'  => $this->email,
            'password'  => Hash::make($role->role_type . '123')
        ];
        if ($this->vcf_info_path) {
            $vcf_info = $this->vcf_info_path->store('upload/vcf_info', 'public');
            $data['vcf_info'] = $vcf_info;
        }

        $user = ModelsUser::create($data);



        $team = Team::find($this->team_id);
        $team->users()->attach($user, ['role' => $role->role_type]);
        $role->users()->attach($user);
        $this->_reset();
        $this->username = Str::random(8);
        return $this->emit('showAlert', ['msg' => 'Data Berhasil Disimpan']);
    }

    public function update()
    {
        $this->_validate();
        $user = ModelsUser::find($this->users_id);
        $role = Role::find($this->role_id);

        $data = [
            'name'  => $this->name,
            'email'  => $this->email,
        ];

        if ($this->vcf_info_path) {
            $vcf_info = $this->vcf_info_path->store('upload/vcf_info', 'public');
            $data = ['vcf_info' => $vcf_info];
            if (Storage::exists('public/' . $this->vcf_info)) {
                Storage::delete('public/' . $this->vcf_info);
            }
        }
        $user->update($data);
        $this->_reset();
        $this->username = Str::random(8);
        return $this->emit('showAlert', ['msg' => 'Data Berhasil Diupdate']);
    }

    public function delete()
    {
        ModelsUser::find($this->users_id)->delete();

        $this->_reset();
        return $this->emit('showAlert', ['msg' => 'Data Berhasil Dihapus']);
    }

    public function _validate()
    {
        $rule = [
            'name'  => 'required',
            'email'  => 'required',
            'role_id'  => 'required'
        ];

        return $this->validate($rule);
    }

    public function getDataUserById($users_id)
    {
        $users = ModelsUser::find($users_id);
        $this->users_id = $users->id;
        $this->name = $users->name;
        $this->username = $users->username;
        $this->email = $users->email;
        $this->password = $users->password;
        $this->vcf_info = cropString($users->vcf_info);
        $this->role_id = $users->role->id;
        if ($this->form) {
            $this->form_active = true;
            $this->emit('loadForm');
        }
        if ($this->modal) {
            $this->emit('showModal');
        }
        $this->update_mode = true;
    }

    public function getUserId($users_id)
    {
        $users = ModelsUser::find($users_id);
        $this->users_id = $users->id;
    }

    public function toggleForm($form)
    {

        $this->form_active = $form;
        $this->emit('loadForm');
    }

    public function showModal()
    {
        $this->username = Str::random(8);
        $this->emit('showModal');
    }

    public function _reset()
    {
        $this->emit('closeModal');
        $this->emit('refreshTable');
        $this->users_id = null;
        $this->role_id = null;
        $this->username = null;
        $this->name = null;
        $this->email = null;
        $this->password = null;
        $this->vcf_info = null;
        $this->vcf_info_path = null;
        $this->form = false;
        $this->form_active = false;
        $this->update_mode = false;
        $this->modal = true;
    }
}
