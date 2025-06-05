@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Profile Header -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center">
                <div class="h-24 w-24 rounded-full bg-gray-200 flex items-center justify-center">
                    <i class="fas fa-user text-gray-500 text-4xl"></i>
                </div>
                <div class="ml-6">
                    <h2 class="text-2xl font-bold text-gray-800">{{ $intern->name }}</h2>
                    <p class="text-gray-600">{{ $intern->email }}</p>
                    <p class="text-sm text-gray-500 mt-1">Intern ID: #{{ $intern->id }}</p>
                </div>
            </div>
            <button class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                <i class="fas fa-edit mr-2"></i>Edit Profile
            </button>
        </div>

        <!-- Profile Information -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="text-lg font-semibold mb-4">Personal Information</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Full Name</label>
                        <p class="mt-1 text-gray-900">{{ $intern->name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Email</label>
                        <p class="mt-1 text-gray-900">{{ $intern->email }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Department</label>
                        <p class="mt-1 text-gray-900">Software Development</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Start Date</label>
                        <p class="mt-1 text-gray-900">March 1, 2024</p>
                    </div>
                </div>
            </div>
            <div>
                <h3 class="text-lg font-semibold mb-4">Performance Summary</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Overall Rating</label>
                        <div class="mt-1 flex items-center">
                            <div class="text-2xl font-bold text-blue-600">85%</div>
                            <div class="ml-4">
                                <div class="text-sm text-gray-600">Last Month: 80%</div>
                                <div class="text-sm text-green-600">↑ 5%</div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Tasks Completed</label>
                        <p class="mt-1 text-gray-900">8 out of 10 tasks</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Attendance Rate</label>
                        <p class="mt-1 text-gray-900">95%</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance History -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold mb-4">Performance History</h3>
        <div class="space-y-6">
            <!-- March 2024 -->
            <div>
                <h4 class="text-md font-medium text-gray-800 mb-3">March 2024</h4>
                <div class="space-y-4">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex justify-between items-center mb-2">
                            <div class="text-sm font-medium text-gray-900">Week 1</div>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                Excellent
                            </span>
                        </div>
                        <p class="text-gray-600">Completed database optimization task ahead of schedule. Received positive feedback from supervisor.</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex justify-between items-center mb-2">
                            <div class="text-sm font-medium text-gray-900">Week 2</div>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                Good
                            </span>
                        </div>
                        <p class="text-gray-600">Working on API documentation. Making good progress but could improve on code examples.</p>
                    </div>
                </div>
            </div>

            <!-- February 2024 -->
            <div>
                <h4 class="text-md font-medium text-gray-800 mb-3">February 2024</h4>
                <div class="space-y-4">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex justify-between items-center mb-2">
                            <div class="text-sm font-medium text-gray-900">Week 4</div>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                Excellent
                            </span>
                        </div>
                        <p class="text-gray-600">Successfully implemented new features and fixed critical bugs. Great teamwork demonstrated.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 