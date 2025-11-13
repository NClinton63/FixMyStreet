<template>
    <AppLayout>
        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-6">
                    <h1 class="text-3xl font-bold text-gray-900">Community Issues Map</h1>
                    <p class="mt-2 text-gray-600">View and track reported issues in your neighborhood</p>
                </div>

                <!-- Filters -->
                <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
                    <div class="flex flex-wrap gap-3">
                        <button
                            @click="selectedCategory = null"
                            :class="[
                                'px-4 py-2 rounded-lg text-sm font-medium transition',
                                selectedCategory === null
                                    ? 'bg-blue-600 text-white'
                                    : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                            ]"
                        >
                            All Issues ({{ issues.length }})
                        </button>
                        <button
                            v-for="category in categories"
                            :key="category.id"
                            @click="selectedCategory = category.id"
                            :class="[
                                'px-4 py-2 rounded-lg text-sm font-medium transition flex items-center gap-2',
                                selectedCategory === category.id
                                    ? 'text-white'
                                    : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                            ]"
                            :style="selectedCategory === category.id ? { backgroundColor: category.color } : {}"
                        >
                            <span>{{ category.icon }}</span>
                            <span>{{ category.name }}</span>
                            <span class="text-xs opacity-75">({{ getIssueCount(category.id) }})</span>
                        </button>
                    </div>
                </div>

                <!-- Status Filter -->
                <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Filter by Status:</label>
                    <div class="flex flex-wrap gap-2">
                        <button
                            @click="selectedStatus = null"
                            :class="[
                                'px-3 py-1.5 rounded text-sm font-medium',
                                selectedStatus === null
                                    ? 'bg-gray-800 text-white'
                                    : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                            ]"
                        >
                            All
                        </button>
                        <button
                            @click="selectedStatus = 'pending'"
                            :class="[
                                'px-3 py-1.5 rounded text-sm font-medium',
                                selectedStatus === 'pending'
                                    ? 'bg-yellow-500 text-white'
                                    : 'bg-yellow-100 text-yellow-800 hover:bg-yellow-200'
                            ]"
                        >
                            Pending
                        </button>
                        <button
                            @click="selectedStatus = 'in_progress'"
                            :class="[
                                'px-3 py-1.5 rounded text-sm font-medium',
                                selectedStatus === 'in_progress'
                                    ? 'bg-blue-500 text-white'
                                    : 'bg-blue-100 text-blue-800 hover:bg-blue-200'
                            ]"
                        >
                            In Progress
                        </button>
                        <button
                            @click="selectedStatus = 'resolved'"
                            :class="[
                                'px-3 py-1.5 rounded text-sm font-medium',
                                selectedStatus === 'resolved'
                                    ? 'bg-green-500 text-white'
                                    : 'bg-green-100 text-green-800 hover:bg-green-200'
                            ]"
                        >
                            Resolved
                        </button>
                    </div>
                </div>

                <!-- Map -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <MapView 
                        :issues="filteredIssues" 
                        :selected-category="selectedCategory"
                        map-height="600px"
                        @issue-selected="handleIssueSelected"
                    />
                </div>

                <!-- Stats -->
                <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="bg-white rounded-lg shadow-sm p-4">
                        <div class="text-sm text-gray-600">Total Issues</div>
                        <div class="text-2xl font-bold text-gray-900">{{ issues.length }}</div>
                    </div>
                    <div class="bg-yellow-50 rounded-lg shadow-sm p-4">
                        <div class="text-sm text-yellow-600">Pending</div>
                        <div class="text-2xl font-bold text-yellow-900">{{ getStatusCount('pending') }}</div>
                    </div>
                    <div class="bg-blue-50 rounded-lg shadow-sm p-4">
                        <div class="text-sm text-blue-600">In Progress</div>
                        <div class="text-2xl font-bold text-blue-900">{{ getStatusCount('in_progress') }}</div>
                    </div>
                    <div class="bg-green-50 rounded-lg shadow-sm p-4">
                        <div class="text-sm text-green-600">Resolved</div>
                        <div class="text-2xl font-bold text-green-900">{{ getStatusCount('resolved') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import MapView from '@/Components/MapView.vue';

const props = defineProps({
    issues: {
        type: Array,
        required: true
    },
    categories: {
        type: Array,
        required: true
    }
});

const selectedCategory = ref(null);
const selectedStatus = ref(null);

const filteredIssues = computed(() => {
    let filtered = props.issues;
    
    if (selectedCategory.value !== null) {
        filtered = filtered.filter(issue => issue.category_id === selectedCategory.value);
    }
    
    if (selectedStatus.value !== null) {
        filtered = filtered.filter(issue => issue.status === selectedStatus.value);
    }
    
    return filtered;
});

const getIssueCount = (categoryId) => {
    return props.issues.filter(issue => issue.category_id === categoryId).length;
};

const getStatusCount = (status) => {
    return props.issues.filter(issue => issue.status === status).length;
};

const handleIssueSelected = (issue) => {
    console.log('Issue selected:', issue);
    // You can add more functionality here, like opening a modal with issue details
};
</script>
