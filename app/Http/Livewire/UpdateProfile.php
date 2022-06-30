<?php

namespace App\Http\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class UpdateProfile extends Component
{
    use WithFileUploads;
    public $user_profile_photo;
    public $user_profile_photo_path;
    public $email;
    public $name;
    public $nomor_hp;
    public $alamat;

    public $current_password;
    public $new_password;
    public $confirm_new_password;

    public function mount()
    {
        $user = auth()->user();

        $this->user_profile_photo = $user->profile_photo_path;
        $this->email = $user->email;
        $this->name = $user->name;
        if ($user->role->role_type == 'member') {
            $this->nomor_hp = $user->userDetail->nomor_hp;
            $this->alamat = $user->userDetail->alamat;
        }
    }
    public function render()
    {
        return view('livewire.update-profile');
    }

    public function store()
    {
        $this->_validate();
        $user = auth()->user();
        $data = [
            'email'  => $this->email,
            'name'  => $this->name,
        ];

        if ($this->user_profile_photo_path) {
            $user_profile_photo = $this->user_profile_photo_path->store('upload', 'public');
            $data = ['profile_photo_path' => $user_profile_photo];
            if (Storage::exists('public/' . $this->user_profile_photo)) {
                Storage::delete('public/' . $this->user_profile_photo);
            }
        }

        $user->update($data);
        if ($user->role->role_type == 'member') {
            $user->userDetail()->update(['nomor_hp' => $this->nomor_hp, 'alamat' => $this->alamat]);
        }

        return $this->emit('showAlert', ['msg' => 'Data Berhasil Disimpan']);
    }

    public function updatePassword()
    {
        $this->_validatePassword();

        $user = User::find($this->user_id);
        if ($user) {
            if (!Hash::check($this->current_password, $user->password)) {
                $this->_reset();
                return $this->emit('showAlertError', ['msg' => 'Password Lama Salah']);
            }

            $user->update([
                'password' => Hash::make($this->new_password)
            ]);

            $this->_reset();
            return $this->emit('showAlert', ['msg' => 'Password Berhasil Diupdate']);
        }
    }

    public function _validate()
    {
        $user = auth()->user();
        $rule = [
            'name'  => 'required',
            'email'  => 'required',
        ];

        if ($user->role->role_type == 'member') {
            $rule['nomor_hp'] = 'required';
            $rule['alamat'] = 'required';
        }

        return $this->validate($rule);
    }

    public function _validatePassword()
    {
        $rule = [
            'current_password'  => 'required',
            'new_password'  => 'required|min:8',
            'confirm_new_password'  => 'required|same:new_password',
        ];

        return $this->validate($rule);
    }

    public function _reset()
    {
        $this->current_password = null;
        $this->new_password = null;
        $this->confirm_new_password = null;
    }
}
