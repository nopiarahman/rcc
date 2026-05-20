<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\VoucherPromoRule;

class VoucherPromoRuleManager extends Component
{
    public bool $showForm = false;
    public ?int $editingId = null;

    public string $description = '';
    public string $min_purchase_amount = '';
    public string $voucher_discount_type = 'fixed';
    public string $voucher_discount_value = '';
    public string $voucher_min_purchase = '0';
    public string $voucher_validity_days = '30';
    public bool $is_active = true;

    protected function rules(): array
    {
        return [
            'description' => 'required|string|max:255',
            'min_purchase_amount' => 'required|numeric|min:1000',
            'voucher_discount_type' => 'required|in:fixed,percentage',
            'voucher_discount_value' => 'required|numeric|min:1',
            'voucher_min_purchase' => 'nullable|numeric|min:0',
            'voucher_validity_days' => 'required|integer|min:1|max:365',
        ];
    }

    protected $messages = [
        'description.required' => 'Deskripsi wajib diisi.',
        'min_purchase_amount.required' => 'Minimal pembelian wajib diisi.',
        'min_purchase_amount.min' => 'Minimal pembelian harus Rp 1.000.',
        'voucher_discount_value.required' => 'Nilai diskon wajib diisi.',
        'voucher_discount_value.min' => 'Nilai diskon minimal 1.',
        'voucher_validity_days.required' => 'Masa berlaku wajib diisi.',
    ];

    public function openCreate(): void
    {
        $this->reset(['description', 'min_purchase_amount', 'voucher_discount_type',
            'voucher_discount_value', 'voucher_min_purchase', 'voucher_validity_days', 'is_active', 'editingId']);
        $this->voucher_discount_type = 'fixed';
        $this->voucher_validity_days = '30';
        $this->voucher_min_purchase = '0';
        $this->is_active = true;
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $rule = VoucherPromoRule::findOrFail($id);
        $this->editingId = $id;
        $this->description = $rule->description;
        $this->min_purchase_amount = (string) $rule->min_purchase_amount;
        $this->voucher_discount_type = $rule->voucher_discount_type;
        $this->voucher_discount_value = (string) $rule->voucher_discount_value;
        $this->voucher_min_purchase = (string) $rule->voucher_min_purchase;
        $this->voucher_validity_days = (string) $rule->voucher_validity_days;
        $this->is_active = $rule->is_active;
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'description' => $this->description,
            'min_purchase_amount' => (int) $this->min_purchase_amount,
            'voucher_discount_type' => $this->voucher_discount_type,
            'voucher_discount_value' => (float) $this->voucher_discount_value,
            'voucher_min_purchase' => (int) ($this->voucher_min_purchase ?: 0),
            'voucher_validity_days' => (int) $this->voucher_validity_days,
            'is_active' => $this->is_active,
        ];

        if ($this->editingId) {
            VoucherPromoRule::findOrFail($this->editingId)->update($data);
            session()->flash('success', 'Promo rule berhasil diupdate.');
        } else {
            VoucherPromoRule::create($data);
            session()->flash('success', 'Promo rule berhasil ditambahkan.');
        }

        $this->showForm = false;
        $this->reset(['editingId']);
    }

    public function toggleActive(int $id): void
    {
        $rule = VoucherPromoRule::findOrFail($id);
        $rule->update(['is_active' => !$rule->is_active]);
    }

    public function delete(int $id): void
    {
        VoucherPromoRule::findOrFail($id)->delete();
        session()->flash('success', 'Promo rule berhasil dihapus.');
    }

    public function render()
    {
        return view('livewire.admin.voucher-promo-rule-manager', [
            'rules' => VoucherPromoRule::latest()->get(),
        ])->layout('components.layouts.app', ['title' => 'Promo Voucher']);
    }
}
