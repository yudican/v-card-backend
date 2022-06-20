<?php

namespace App\Http\Livewire\Table;

use App\Models\HideableColumn;
use Mediconesystems\LivewireDatatables\Column;
use App\Http\Livewire\Table\LivewireDatatable;
use App\Models\User;

class UserTable extends LivewireDatatable
{
  protected $listeners = ['refreshTable'];
  // public $hideable = 'select';
  public $hide = [];

  public function builder()
  {
    return User::query()->whereHas('roles', function ($query) {
      $query->where('role_type', 'member');
    });
  }

  public function columns()
  {
    return [
      Column::name('id')->label('No.'),
      Column::name('name')->label('Name')->searchable(),
      Column::callback('username', function ($username) {
        return '<a href="https://zeto.link/' . $username . '" target="_blank">' . $username . '</a>';
      })->label('Username')->searchable(),
      Column::name('email')->label('Email')->searchable(),

      Column::callback(['id'], function ($id) {
        return view('livewire.components.action-button', [
          'id' => $id,
          'segment' => $this->params
        ]);
      })->label(__('Aksi')),
    ];
  }

  public function getDataById($id)
  {
    $this->emit('getDataUserById', $id);
  }

  public function getId($id)
  {
    $this->emit('getUserId', $id);
  }

  public function refreshTable()
  {
    $this->emit('refreshLivewireDatatable');
  }
}
