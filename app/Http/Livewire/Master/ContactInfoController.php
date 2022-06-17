<?php

namespace App\Http\Livewire\Master;

use App\Models\ContactInfo;
use Livewire\Component;


class ContactInfoController extends Component
{

    public $contact_info_id;
    public $name;
    public $icon_path;
    public $user_id;



    public $route_name = null;

    public $form_active = false;
    public $form = false;
    public $update_mode = false;
    public $modal = true;

    protected $listeners = ['getDataContactInfoById', 'getContactInfoId'];

    public function mount()
    {
        $this->route_name = request()->route()->getName();
    }

    public function render()
    {
        return view('livewire.master.contact-info', [
            'items' => ContactInfo::all()
        ]);
    }

    public function store()
    {
        $this->_validate();

        $data = [
            'name'  => $this->name,
            'icon_path'  => $this->icon_path,
            'user_id'  => $this->user_id
        ];

        ContactInfo::create($data);

        $this->_reset();
        return $this->emit('showAlert', ['msg' => 'Data Berhasil Disimpan']);
    }

    public function update()
    {
        $this->_validate();

        $data = [
            'name'  => $this->name,
            'icon_path'  => $this->icon_path,
            'user_id'  => $this->user_id
        ];
        $row = ContactInfo::find($this->contact_info_id);



        $row->update($data);

        $this->_reset();
        return $this->emit('showAlert', ['msg' => 'Data Berhasil Diupdate']);
    }

    public function delete()
    {
        ContactInfo::find($this->contact_info_id)->delete();

        $this->_reset();
        return $this->emit('showAlert', ['msg' => 'Data Berhasil Dihapus']);
    }

    public function _validate()
    {
        $rule = [
            'name'  => 'required',
            'icon_path'  => 'required',
            'user_id'  => 'required'
        ];

        return $this->validate($rule);
    }

    public function getDataContactInfoById($contact_info_id)
    {
        $this->_reset();
        $row = ContactInfo::find($contact_info_id);
        $this->contact_info_id = $row->id;
        $this->name = $row->name;
        $this->icon_path = $row->icon_path;
        $this->user_id = $row->user_id;
        if ($this->form) {
            $this->form_active = true;
            $this->emit('loadForm');
        }
        if ($this->modal) {
            $this->emit('showModal');
        }
        $this->update_mode = true;
    }

    public function getContactInfoId($contact_info_id)
    {
        $row = ContactInfo::find($contact_info_id);
        $this->contact_info_id = $row->id;
    }

    public function toggleForm($form)
    {
        $this->_reset();
        $this->form_active = $form;
        $this->emit('loadForm');
    }

    public function showModal()
    {
        $this->_reset();
        $this->emit('showModal');
    }

    public function _reset()
    {
        $this->emit('closeModal');
        $this->emit('refreshTable');
        $this->contact_info_id = null;
        $this->name = null;
        $this->icon_path = null;
        $this->user_id = null;
        $this->form = false;
        $this->form_active = false;
        $this->update_mode = false;
        $this->modal = true;
    }
}
