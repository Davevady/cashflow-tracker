# Dynamic Add Feature - CashFlow Tracker

Fitur untuk menambahkan data baru secara dinamis tanpa meninggalkan halaman form saat ini menggunakan modal AJAX.

## 🎯 Fitur yang Diimplementasikan

### 1. **Dynamic Add Wallet Group** (di form Wallet)
- **Lokasi**: Create & Edit Wallet
- **Files**:
  - Form: `resources/views/wallets/create.blade.php`, `resources/views/wallets/edit.blade.php`
  - Modal: `resources/views/components/modal-add-wallet-group.blade.php`
  - API: `route('wallet-groups.store')`

**Cara Kerja:**
- Klik tombol **[+]** di samping dropdown "Grup Dompet"
- Modal akan muncul untuk input nama, icon, dan deskripsi grup baru
- Data disimpan via AJAX ke endpoint `/wallet-groups`
- Grup baru otomatis ditambahkan ke dropdown dan diselect
- Notifikasi sukses ditampilkan

### 2. **Dynamic Add Category** (di Dashboard & Transaction Edit)
- **Lokasi**: Dashboard (form tambah transaksi) & Transaction Index (edit modal)
- **Files**:
  - Dashboard: `resources/views/dashboard.blade.php` + `modal-add-category.blade.php`
  - Edit: `resources/views/transactions/index.blade.php` + `modal-add-category-edit.blade.php`
  - API: `route('categories.store')`

**Cara Kerja:**
- Klik tombol **[+]** di samping dropdown "Kategori"
- Modal untuk input: Transaction Group, Nama, Icon, Deskripsi
- Auto-select transaction group berdasarkan tipe transaksi saat ini (in/out)
- Kategori baru ditambahkan ke optgroup yang sesuai
- Support untuk dashboard dan edit modal dengan ID berbeda

### 3. **Dynamic Add Transaction Group** (di form Category)
- **Lokasi**: Create & Edit Category
- **Files**:
  - Form: `resources/views/categories/create.blade.php`, `resources/views/categories/edit.blade.php`
  - Modal: `resources/views/components/modal-add-transaction-group.blade.php`
  - API: `route('transaction-groups.store')`

**Cara Kerja:**
- Klik tombol **[+]** di samping dropdown "Grup Transaksi"
- Modal untuk input: Nama, Tipe (in/out), Icon, Deskripsi
- Transaction group baru ditambahkan ke optgroup sesuai tipe
- Tipe: "Cash IN" atau "Cash OUT"

## 📁 Struktur File

```
resources/views/
├── components/
│   ├── modal-add-wallet-group.blade.php       # Modal untuk tambah wallet group
│   ├── modal-add-category.blade.php            # Modal untuk tambah category (dashboard)
│   ├── modal-add-category-edit.blade.php       # Modal untuk tambah category (edit)
│   ├── modal-add-transaction-group.blade.php   # Modal untuk tambah transaction group
│   └── dynamic-select.blade.php                # Component reusable (untuk future use)
├── wallets/
│   ├── create.blade.php                        # ✅ Dynamic wallet group
│   └── edit.blade.php                          # ✅ Dynamic wallet group
├── categories/
│   ├── create.blade.php                        # ✅ Dynamic transaction group
│   └── edit.blade.php                          # ✅ Dynamic transaction group
├── dashboard.blade.php                         # ✅ Dynamic category
└── transactions/
    └── index.blade.php                         # ✅ Dynamic category (edit modal)
```

## 🔧 Cara Kerja Teknis

### 1. HTML Structure
```html
<div class="input-group">
    <select name="wallet_group_id" id="wallet_group_id" class="form-control" required>
        <option value="">-- Pilih Grup --</option>
        @foreach($groups as $group)
            <option value="{{ $group->id }}">{{ $group->name }}</option>
        @endforeach
    </select>
    <div class="input-group-append">
        <button type="button" class="btn btn-primary btn-sm"
                data-toggle="modal" data-target="#modalAddWalletGroup">
            <i class="fa fa-plus"></i>
        </button>
    </div>
</div>
```

### 2. Modal dengan Form AJAX
```javascript
$('#formAddWalletGroup').on('submit', function(e) {
    e.preventDefault();

    $.ajax({
        url: '{{ route("wallet-groups.store") }}',
        method: 'POST',
        data: $(this).serialize(),
        success: function(response) {
            // Add new option to select
            const newOption = new Option(response.data.name, response.data.id, true, true);
            $('#wallet_group_id').append(newOption).trigger('change');

            // Close modal
            $('#modalAddWalletGroup').modal('hide');

            // Show notification
            $.notify({ message: 'Berhasil!' }, { type: 'success' });
        }
    });
});
```

### 3. API Response Format
```json
{
  "success": true,
  "message": "Data berhasil disimpan",
  "data": {
    "id": 5,
    "name": "Nama Grup Baru",
    "icon": "fa fa-wallet",
    "description": "Deskripsi"
  }
}
```

## ✨ Fitur Tambahan

### Auto-Select Transaction Type (Dashboard Category)
```javascript
$('#modalAddCategory').on('show.bs.modal', function() {
    const currentType = $('#transaction_type').val(); // 'in' or 'out'
    if (currentType) {
        $(`#new_category_transaction_group_id option[data-type="${currentType}"]`)
            .first().prop('selected', true);
    }
});
```

### Validation Error Handling
```javascript
error: function(xhr) {
    if (xhr.status === 422) {
        const errors = xhr.responseJSON.errors;
        for (const [field, messages] of Object.entries(errors)) {
            $(`#error_wallet_group_${field}`).text(messages[0]);
        }
    }
}
```

### Modal Reset on Close
```javascript
$('#modalAddWalletGroup').on('hidden.bs.modal', function() {
    $('#formAddWalletGroup')[0].reset();
    $('.text-danger').text('');
});
```

## 🎨 UI/UX Features

1. **Button Design**: Small `btn-sm` dengan icon `fa-plus`
2. **Loading State**: Button disabled dengan spinner saat submit
3. **Notifications**: Toast notification menggunakan Bootstrap Notify
4. **Error Display**: Inline error messages di bawah setiap field
5. **Auto-Select**: Item baru otomatis terpilih di dropdown
6. **Modal Stacking**: Support multiple modals (dashboard + add category)

## 📊 Implementasi per Halaman

| Halaman | Form Field | Dynamic Add | Modal ID | API Endpoint |
|---------|-----------|-------------|----------|--------------|
| Wallet Create | Grup Dompet | ✅ | `modalAddWalletGroup` | `/wallet-groups` |
| Wallet Edit | Grup Dompet | ✅ | `modalAddWalletGroup` | `/wallet-groups` |
| Category Create | Grup Transaksi | ✅ | `modalAddTransactionGroup` | `/transaction-groups` |
| Category Edit | Grup Transaksi | ✅ | `modalAddTransactionGroup` | `/transaction-groups` |
| Dashboard | Kategori | ✅ | `modalAddCategory` | `/categories` |
| Transaction Edit | Kategori | ✅ | `modalAddCategoryEdit` | `/categories` |

## 🚀 Cara Menambahkan Dynamic Add di Form Lain

### 1. Tambahkan Input Group dengan Button
```blade
<div class="input-group">
    <select name="parent_id" id="parent_id" class="form-control" required>
        <!-- options -->
    </select>
    <div class="input-group-append">
        <button type="button" class="btn btn-primary btn-sm"
                data-toggle="modal" data-target="#modalAddParent">
            <i class="fa fa-plus"></i>
        </button>
    </div>
</div>
```

### 2. Buat Modal Component Baru
File: `resources/views/components/modal-add-parent.blade.php`
```blade
<div class="modal fade" id="modalAddParent">
    <form id="formAddParent">
        @csrf
        <!-- form fields -->
        <button type="submit" id="btnSaveParent">Simpan</button>
    </form>
</div>

@push('scripts')
<script>
$('#formAddParent').on('submit', function(e) {
    e.preventDefault();
    $.ajax({
        url: '{{ route("parents.store") }}',
        method: 'POST',
        data: $(this).serialize(),
        success: function(response) {
            const newOption = new Option(response.data.name, response.data.id, true, true);
            $('#parent_id').append(newOption).trigger('change');
            $('#modalAddParent').modal('hide');
            $.notify({ message: 'Berhasil!' }, { type: 'success' });
        }
    });
});
</script>
@endpush
```

### 3. Include Modal di Blade View
```blade
@include('components.modal-add-parent')
```

## 🔒 Security Considerations

1. **CSRF Token**: Semua form AJAX menyertakan `@csrf` token
2. **Validation**: Server-side validation di Controller
3. **Authorization**: Gunakan Policy/Gate jika perlu
4. **Input Sanitization**: Automatic di Laravel (htmlspecialchars)

## 🐛 Troubleshooting

### Modal tidak muncul?
```javascript
// Cek z-index
.modal { z-index: 1050; }
.modal-backdrop { z-index: 1040; }
```

### Option tidak muncul di dropdown?
```javascript
// Pastikan response API mengembalikan data.id dan data.name
console.log(response.data);
```

### Validation error tidak muncul?
```javascript
// Cek ID element error message
<small id="error_wallet_group_name" class="text-danger"></small>
```

### Multiple modals konflik?
```javascript
// Pastikan ID modal dan form unique per halaman
#modalAddCategory      // Dashboard
#modalAddCategoryEdit  // Transaction Edit
```

## 📝 Best Practices

1. **Unique IDs**: Gunakan ID unique untuk modal dan form di setiap halaman
2. **Error Handling**: Selalu handle error 422 (validation) dan 500 (server error)
3. **Loading State**: Disable button dan tampilkan spinner saat submit
4. **Reset Form**: Reset form dan clear error saat modal ditutup
5. **Notification**: Berikan feedback sukses/error kepada user
6. **Auto-Select**: Select item baru otomatis setelah berhasil dibuat

## 🎯 Future Improvements

- [ ] Implement component reusable `dynamic-select.blade.php`
- [ ] Add keyboard shortcut (Ctrl+N) untuk buka modal
- [ ] Add inline edit (double click untuk edit option)
- [ ] Add search/filter di dropdown untuk banyak data
- [ ] Add image upload untuk icon (selain FontAwesome)
- [ ] Add bulk create (tambah multiple items sekaligus)

---

**Built with ❤️ for CashFlow Tracker**
