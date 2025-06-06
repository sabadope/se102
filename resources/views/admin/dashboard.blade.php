@extends('layouts.admin')

@section('header')
    Admin Dashboard
@endsection

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Quick Actions -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-lg font-semibold mb-4">Quick Actions</h3>
            <div class="space-y-2">
                <a href="{{ route('admin.users.create') }}" class="block w-full text-center bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    Add New User
                </a>
                <a href="{{ route('admin.users.index') }}" class="block w-full text-center bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                    Manage Users
                </a>
            </div>
        </div>

        <!-- Statistics -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-lg font-semibold mb-4">Statistics</h3>
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span>Total Users:</span>
                    <span class="font-semibold">{{ \App\Models\User::where('role', '!=', 'admin')->count() }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Total Interns:</span>
                    <span class="font-semibold">{{ \App\Models\User::where('role', 'intern')->count() }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Total Supervisors:</span>
                    <span class="font-semibold">{{ \App\Models\User::where('role', 'supervisor')->count() }}</span>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-lg font-semibold mb-4">Recent Activity</h3>
            <div class="space-y-2">
                <p class="text-gray-600">No recent activity to display.</p>
            </div>
        </div>
    </div>
@endsection 