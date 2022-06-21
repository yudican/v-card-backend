<?php

namespace App\Http\Livewire\Master;

use App\Models\SocialLink;
use Livewire\Component;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;

class SocialLinkController extends Component
{
    use WithFileUploads;
    public $social_link_id;
    public $icon_path;
    public $image_link;
    public $name;
    public $url;
    public $user_id;
    public $image_link_path;


    public $route_name = null;

    public $form_active = false;
    public $form = false;
    public $update_mode = false;
    public $modal = true;

    protected $listeners = ['getDataSocialLinkById', 'getSocialLinkId'];

    public function mount()
    {
        $this->route_name = request()->route()->getName();
    }

    public function render()
    {
        return view('livewire.master.social-link', [
            'items' => SocialLink::all()
        ]);
    }

    public function store()
    {
        $this->_validate();
        $image_link = $this->image_link_path->store('upload', 'public');
        $data = [
            'icon_path'  => $this->icon_path,
            'image_link'  => $image_link,
            'name'  => $this->name,
            'url'  => $this->url,
            'user_id'  => $this->user_id
        ];

        SocialLink::create($data);

        $this->_reset();
        return $this->emit('showAlert', ['msg' => 'Data Berhasil Disimpan']);
    }

    public function update()
    {
        $this->_validate();

        $data = [
            'icon_path'  => $this->icon_path,
            'image_link'  => $this->image_link,
            'name'  => $this->name,
            'url'  => $this->url,
            'user_id'  => $this->user_id
        ];
        $row = SocialLink::find($this->social_link_id);


        if ($this->image_link_path) {
            $image_link = $this->image_link_path->store('upload', 'public');
            $data = ['image_link' => $image_link];
            if (Storage::exists('public/' . $this->image_link)) {
                Storage::delete('public/' . $this->image_link);
            }
        }

        $row->update($data);

        $this->_reset();
        return $this->emit('showAlert', ['msg' => 'Data Berhasil Diupdate']);
    }

    public function delete()
    {
        SocialLink::find($this->social_link_id)->delete();

        $this->_reset();
        return $this->emit('showAlert', ['msg' => 'Data Berhasil Dihapus']);
    }

    public function _validate()
    {
        $rule = [
            'icon_path'  => 'required',
            'name'  => 'required',
            'url'  => 'required',
            'user_id'  => 'required'
        ];

        return $this->validate($rule);
    }

    public function getDataSocialLinkById($social_link_id)
    {
        $this->_reset();
        $row = SocialLink::find($social_link_id);
        $this->social_link_id = $row->id;
        $this->icon_path = $row->icon_path;
        $this->image_link = $row->image_link;
        $this->name = $row->name;
        $this->url = $row->url;
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

    public function getSocialLinkId($social_link_id)
    {
        $row = SocialLink::find($social_link_id);
        $this->social_link_id = $row->id;
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
        $this->social_link_id = null;
        $this->icon_path = null;
        $this->image_link_path = null;
        $this->name = null;
        $this->url = null;
        $this->user_id = null;
        $this->form = false;
        $this->form_active = false;
        $this->update_mode = false;
        $this->modal = true;
    }
}
