@extends('layouts.app')

@section('title', 'Intern Details')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center">
            <div class="h-20 w-20 rounded-full bg-gray-200 flex items-center justify-center">
                <i class="fas fa-user text-gray-500 text-3xl"></i>
            </div>
            <div class="ml-4">
                <h2 class="text-2xl font-bold text-gray-800">{{ $intern->name }}</h2>
                <p class="text-gray-600">{{ $intern->email }}</p>
            </div>
        </div>
        <div class="flex space-x-4">
            <button class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                <i class="fas fa-edit mr-2"></i>Edit Profile
            </button>
            <button class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600">
                <i class="fas fa-plus mr-2"></i>Assign Task
            </button>
        </div>
    </div>

    <!-- Performance Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-gray-50 rounded-lg p-4">
            <h3 class="text-lg font-semibold mb-2">Overall Performance</h3>
            <div class="flex items-center">
                <div class="text-3xl font-bold text-blue-600">85%</div>
                <div class="ml-4">
                    <div class="text-sm text-gray-600">Last Month: 80%</div>
                    <div class="text-sm text-green-600">↑ 5%</div>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 rounded-lg p-4">
            <h3 class="text-lg font-semibold mb-2">Task Completion</h3>
            <div class="flex items-center">
                <div class="text-3xl font-bold text-green-600">92%</div>
                <div class="ml-4">
                    <div class="text-sm text-gray-600">Last Month: 88%</div>
                    <div class="text-sm text-green-600">↑ 4%</div>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 rounded-lg p-4">
            <h3 class="text-lg font-semibold mb-2">Attendance Rate</h3>
            <div class="flex items-center">
                <div class="text-3xl font-bold text-purple-600">95%</div>
                <div class="ml-4">
                    <div class="text-sm text-gray-600">Last Month: 93%</div>
                    <div class="text-sm text-green-600">↑ 2%</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Task History -->
    <div class="mb-6">
        <h3 class="text-lg font-semibold mb-4">Task History</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        <th class="px-4 py-3">Task</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Due Date</th>
                        <th class="px-4 py-3">Performance</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <tr>
                        <td class="px-4 py-3">
                            <div class="text-sm font-medium text-gray-900">Database Optimization</div>
                            <div class="text-sm text-gray-500">Technical Task</div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                Completed
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500">2024-03-15</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center">
                                <div class="w-16 bg-gray-200 rounded-full h-2">
                                    <div class="bg-green-600 h-2 rounded-full" style="width: 90%"></div>
                                </div>
                                <span class="ml-2 text-sm text-gray-600">90%</span>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3">
                            <div class="text-sm font-medium text-gray-900">API Documentation</div>
                            <div class="text-sm text-gray-500">Documentation Task</div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                In Progress
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500">2024-03-20</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center">
                                <div class="w-16 bg-gray-200 rounded-full h-2">
                                    <div class="bg-yellow-600 h-2 rounded-full" style="width: 60%"></div>
                                </div>
                                <span class="ml-2 text-sm text-gray-600">60%</span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Performance Comments -->
    <div>
        <h3 class="text-lg font-semibold mb-4">Performance Comments</h3>
        <div class="space-y-4">
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="flex items-center mb-2">
                    <div class="text-sm font-medium text-gray-900">March 15, 2024</div>
                    <span class="ml-2 px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                        Positive
                    </span>
                </div>
                <p class="text-gray-600">Excellent work on the database optimization task. The implementation was clean and efficient.</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="flex items-center mb-2">
                    <div class="text-sm font-medium text-gray-900">March 10, 2024</div>
                    <span class="ml-2 px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                        Neutral
                    </span>
                </div>
                <p class="text-gray-600">Good progress on the API documentation. Consider adding more examples in the documentation.</p>
            </div>
        </div>
    </div>
</div>
@endsection 