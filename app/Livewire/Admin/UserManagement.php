<?php

namespace App\Livewire\Admin;

use App\Models\Pesanan;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class UserManagement extends Component
{
    use WithPagination;

    public string $search = '';

    // Form tambah/edit admin
    public bool $showForm = false;
    public ?int $editingId = null;
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->editingId)],
            'password' => [$this->editingId ? 'nullable' : 'required', 'string', 'min:8', 'confirmed'],
        ];
    }

    protected $messages = [
        'name.required' => 'Nama wajib diisi.',
        'email.required' => 'Email wajib diisi.',
        'email.email' => 'Format email tidak valid.',
        'email.unique' => 'Email ini sudah dipakai admin lain.',
        'password.required' => 'Password wajib diisi.',
        'password.min' => 'Password minimal 8 karakter.',
        'password.confirmed' => 'Konfirmasi password tidak cocok.',
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $user = User::findOrFail($id);

        $this->resetForm();
        $this->editingId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->showForm = true;
    }

    public function closeForm(): void
    {
        $this->showForm = false;
        $this->resetForm();
    }

    public function save(): void
    {
        $this->email = strtolower(trim($this->email));

        $this->validate();

        $data = [
            'name' => trim($this->name),
            'email' => $this->email,
        ];

        if ($this->password !== '') {
            $data['password'] = $this->password; // di-hash oleh cast 'hashed' di model User
        }

        if ($this->editingId) {
            User::findOrFail($this->editingId)->update($data);
            session()->flash('success', 'Data admin berhasil diperbarui.');
        } else {
            User::create($data);
            session()->flash('success', 'Admin baru berhasil ditambahkan.');
        }

        $this->closeForm();
    }

    public function delete(int $id): void
    {
        if ($id === auth()->id()) {
            session()->flash('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
            return;
        }

        $user = User::findOrFail($id);

        DB::transaction(function () use ($user) {
            // FK pesanans.user_id cascade on delete — lepas dulu agar riwayat pesanan tidak ikut terhapus
            Pesanan::where('user_id', $user->id)->update(['user_id' => null]);
            $user->delete();
        });

        session()->flash('success', "Admin {$user->name} berhasil dihapus.");
    }

    protected function resetForm(): void
    {
        $this->reset(['editingId', 'name', 'email', 'password', 'password_confirmation']);
        $this->resetValidation();
    }

    public function render()
    {
        $users = User::query()
            ->when($this->search, fn ($q) =>
                $q->where(function ($sub) {
                    $sub->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%');
                })
            )
            ->orderBy('name')
            ->paginate(15);

        return view('livewire.admin.user-management', [
            'users' => $users,
            'totalAdmins' => User::count(),
        ])->layout('components.layouts.app', ['title' => 'Data Admin']);
    }
}
