{{-- File: resources/views/members/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Daftar Anggota')

@section('content')
    <h1>Daftar Anggota</h1>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
        <p style="margin: 0;"><a href="{{ route('members.create') }}" class="btn">+ Tambah Anggota</a></p>

        <form method="GET" action="{{ route('members.index') }}" style="display: flex; gap: 8px;">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama anggota..." style="width: 220px; margin-top: 0;">
            <button type="submit" class="btn" style="margin-top: 0;">Cari</button>
            @if (request('search'))
                <a href="{{ route('members.index') }}" class="btn" style="background: #6b7280; margin-top: 0;">Reset</a>
            @endif
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama</th>
                <th>NIM</th>
                <th>Email</th>
                <th>No. Telepon</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($members as $member)
                <tr>
                    <td>{{ $member['id'] }}</td>
                    <td>{{ $member['nama'] }}</td>
                    <td>{{ $member['nim'] }}</td>
                    <td>{{ $member['email'] }}</td>
                    <td>{{ $member['nomor_telepon'] }}</td>
                    <td>{{ ucfirst($member['status']) }}</td>
                    <td>
                        <a href="{{ route('members.show', $member['id']) }}">Detail</a>
                        |
                        <a href="{{ route('members.edit', $member['id']) }}">Edit</a>
                        |
                        <form class="inline" action="{{ route('members.destroy', $member['id']) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">Belum ada data anggota.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{ $members->appends(request()->query())->links() }}
@endsection
