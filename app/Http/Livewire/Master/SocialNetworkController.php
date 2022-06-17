<?php

namespace App\Http\Livewire\Master;

use App\Models\SocialNetwork;
use Livewire\Component;


class SocialNetworkController extends Component
{
    
    public $social_network_id;
    public $name;
public $url;
public $icon_path;
public $user_id;
    
   

    public $route_name = null;

    public $form_active = false;
    public $form = false;
    public $update_mode = false;
    public $modal = true;

    protected $listeners = ['getDataSocialNetworkById', 'getSocialNetworkId'];

    public function mount()
    {
        $this->route_name = request()->route()->getName();
    }

    public function render()
    {
        return view('livewire.master.social-network', [
            'items' => SocialNetwork::all()
        ]);
    }

    public function store()
    {
        $this->_validate();
        
        $data = ['name'  => $this->name,
'url'  => $this->url,
'icon_path'  => $this->icon_path,
'user_id'  => $this->user_id];

        SocialNetwork::create($data);

        $this->_reset();
        return $this->emit('showAlert', ['msg' => 'Data Berhasil Disimpan']);
    }

    public function update()
    {
        $this->_validate();

        $data = ['name'  => $this->name,
'url'  => $this->url,
'icon_path'  => $this->icon_path,
'user_id'  => $this->user_id];
        $row = SocialNetwork::find($this->social_network_id);

        

        $row->update($data);

        $this->_reset();
        return $this->emit('showAlert', ['msg' => 'Data Berhasil Diupdate']);
    }

    public function delete()
    {
        SocialNetwork::find($this->social_network_id)->delete();

        $this->_reset();
        return $this->emit('showAlert', ['msg' => 'Data Berhasil Dihapus']);
    }

    public function _validate()
    {
        $rule = [
            'name'  => 'required',
'url'  => 'required',
'icon_path'  => 'required',
'user_id'  => 'required'
        ];

        return $this->validate($rule);
    }

    public function getDataSocialNetworkById($social_network_id)
    {
        $this->_reset();
        $row = SocialNetwork::find($social_network_id);
        $this->social_network_id = $row->id;
        $this->name = $row->name;
$this->url = $row->url;
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

    public function getSocialNetworkId($social_network_id)
    {
        $row = SocialNetwork::find($social_network_id);
        $this->social_network_id = $row->id;
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
        $this->social_network_id = null;
        $this->name = null;
$this->url = null;
$this->icon_path = null;
$this->user_id = null;
        $this->form = false;
        $this->form_active = false;
        $this->update_mode = false;
        $this->modal = true;
    }
}
