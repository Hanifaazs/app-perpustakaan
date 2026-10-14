{{-- File: resources/views/profile/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Profil Pengguna')

@section('content')
    <h1>Profil Pengguna</h1>

    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; margin-bottom: 30px; max-width: 500px;">
        <table style="border: none; margin-top: 0;">
            <tr style="border: none;">
                <td style="border: none; font-weight: bold; width: 120px; padding: 6px 0;">Nama</td>
                <td style="border: none; padding: 6px 0;">: {{ $user->name }}</td>
            </tr>
            <tr style="border: none;">
                <td style="border: none; font-weight: bold; padding: 6px 0;">Email</td>
                <td style="border: none; padding: 6px 0;">: {{ $user->email }}</td>
            </tr>
            <tr style="border: none;">
                <td style="border: none; font-weight: bold; padding: 6px 0;">Role</td>
                <td style="border: none; padding: 6px 0;">: <span class="badge {{ $user->role === 'admin' ? 'badge-dikembalikan' : 'badge-dipinjam' }}">{{ ucfirst($user->role) }}</span></td>
            </tr>
        </table>
    </div>

    <h2>Ganti Password</h2>

    <form action="{{ route('profile.password') }}" method="POST" style="max-width: 500px;">
        @csrf
        @method('PUT')

        <label for="current_password">Password Lama</label>
        <input type="password" name="current_password" id="current_password">
        @error('current_password')
            <div class="error">{{ $message }}</div>
        @enderror

        <label for="password">Password Baru</label>
        <input type="password" name="password" id="password">
        @error('password')
            <div class="error">{{ $message }}</div>
        @enderror

        <label for="password_confirmation">Konfirmasi Password Baru</label>
        <input type="password" name="password_confirmation" id="password_confirmation">

        <button type="submit" class="btn" style="margin-top: 20px;">Ubah Password</button>
    </form>
@endsection
