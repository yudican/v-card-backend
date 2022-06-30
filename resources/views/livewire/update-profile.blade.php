<div class="page-inner">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title text-capitalize">
                        <a href="{{route('dashboard')}}">
                            <span><i class="fas fa-arrow-left mr-3"></i>Update Profile</span>
                        </a>
                    </h4>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <x-text-field type="text" name="name" label="Nama Lengkap" />
                    <x-text-field type="text" name="email" label="Email" />
                    @if (auth()->user()->role->role_type == 'member')
                    <x-text-field type="text" name="nomor_hp" label="Nomor Hp" />
                    <x-text-field type="text" name="alamat" label="Alamat" />
                    @endif
                    <x-input-photo foto="{{$user_profile_photo}}"
                        path="{{optional($user_profile_photo_path)->temporaryUrl()}}" name="user_profile_photo_path"
                        label="Foto Produk" />

                    <div class="form-group">
                        <button class="btn btn-primary pull-right" wire:click="store">Simpan</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <x-text-field type="password" name="current_password" label="Password Sekarang" />
                    <x-text-field type="password" name="new_password" label="Password Baru" />
                    <x-text-field type="password" name="confirm_new_password" label="Konfirmasi Password" />

                    <div class="form-group">
                        <button type="button" wire:click="updatePassword" class="btn btn-danger btn-sm">Update</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>