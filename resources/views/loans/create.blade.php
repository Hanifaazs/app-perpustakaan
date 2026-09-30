{{-- File: resources/views/loans/create.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Peminjaman</title>
    <style>
        body { font-family: sans-serif; margin: 40px; max-width: 500px; }
        label { display: block; margin-top: 12px; font-weight: bold; }
        input, select { width: 100%; padding: 6px; margin-top: 4px; box-sizing: border-box; }
        .checkbox-list { border: 1px solid #ccc; border-radius: 4px; padding: 10px; margin-top: 4px; max-height: 200px; overflow-y: auto; }
        .checkbox-list label { display: block; font-weight: normal; margin-top: 0; }
        .error { color: #b91c1c; font-size: 14px; margin-top: 4px; }
        .btn { margin-top: 20px; padding: 8px 16px; background: #2563eb; color: #fff; border: none; border-radius: 4px; cursor: pointer; }
    </style>
</head>
<body>
    <h1>Tambah Peminjaman</h1>
    <p><a href="{{ route('loans.index') }}">&larr; Kembali ke daftar peminjaman</a></p>

    <form action="{{ route('loans.store') }}" method="POST">
        @csrf

        <label for="member_id">Anggota</label>
        <select name="member_id" id="member_id">
            <option value="">-- Pilih Anggota --</option>
            @foreach ($members as $member)
                <option value="{{ $member['id'] }}" @selected(old('member_id') == $member['id'])>
                    {{ $member['nama'] }} ({{ $member['nim'] }})
                </option>
            @endforeach
        </select>
        @error('member_id')
            <div class="error">{{ $message }}</div>
        @enderror

        <label for="user_id">Petugas</label>
        <select name="user_id" id="user_id">
            <option value="">-- Pilih Petugas --</option>
            @foreach ($users as $user)
                <option value="{{ $user['id'] }}" @selected(old('user_id') == $user['id'])>
                    {{ $user['name'] }}
                </option>
            @endforeach
        </select>
        @error('user_id')
            <div class="error">{{ $message }}</div>
        @enderror
        <p><em>Catatan: dropdown petugas dipilih manual karena login belum ada - otomatis dari user yang login mulai Pertemuan 8.</em></p>

        <label for="tanggal_pinjam">Tanggal Pinjam</label>
        <input type="date" name="tanggal_pinjam" id="tanggal_pinjam" value="{{ old('tanggal_pinjam') }}">
        @error('tanggal_pinjam')
            <div class="error">{{ $message }}</div>
        @enderror

        <label for="tanggal_kembali">Tanggal Kembali</label>
        <input type="date" name="tanggal_kembali" id="tanggal_kembali" value="{{ old('tanggal_kembali') }}">
        @error('tanggal_kembali')
            <div class="error">{{ $message }}</div>
        @enderror

        <label>Buku yang Dipinjam</label>
        <div class="checkbox-list">
            @forelse ($books as $book)
                <label>
                    <input type="checkbox" name="book_ids[]" value="{{ $book['id'] }}"
                        @checked(in_array($book['id'], old('book_ids', [])))>
                    {{ $book['judul'] }} (stok: {{ $book['stok'] }})
                </label>
            @empty
                <p>Belum ada data buku.</p>
            @endforelse
        </div>
        @error('book_ids')
            <div class="error">{{ $message }}</div>
        @enderror

        <button type="submit" class="btn">Simpan</button>
    </form>
</body>
</html>
