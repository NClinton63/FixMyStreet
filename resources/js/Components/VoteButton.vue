<template>
    <button
        @click="toggleVote"
        :disabled="loading"
        :class="[
            'flex items-center space-x-2 px-4 py-2 rounded-lg font-medium transition-all duration-200',
            hasVoted
                ? 'bg-blue-600 text-white hover:bg-blue-700 shadow-lg'
                : 'bg-gray-100 text-gray-700 hover:bg-gray-200 border border-gray-300',
            loading ? 'opacity-50 cursor-not-allowed' : 'hover:scale-105'
        ]"
    >
        <ArrowUpIcon :class="['h-5 w-5', hasVoted ? 'animate-bounce' : '']" />
        <span class="font-semibold">{{ voteCount }}</span>
        <span class="hidden sm:inline">{{ hasVoted ? 'Voted' : 'Vote' }}</span>
    </button>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { ArrowUpIcon } from '@heroicons/vue/24/solid';
import axios from 'axios';

const props = defineProps({
    issueId: {
        type: Number,
        required: true
    },
    initialVoteCount: {
        type: Number,
        default: 0
    }
});

const hasVoted = ref(false);
const voteCount = ref(props.initialVoteCount);
const loading = ref(false);

onMounted(async () => {
    // Check if user has already voted
    try {
        const response = await axios.get(`/issues/${props.issueId}/has-voted`);
        hasVoted.value = response.data.has_voted;
        voteCount.value = response.data.votes_count;
    } catch (error) {
        console.error('Error checking vote status:', error);
    }
});

const toggleVote = async () => {
    if (loading.value) return;
    
    loading.value = true;
    
    try {
        const response = await axios.post(`/issues/${props.issueId}/vote`);
        hasVoted.value = response.data.voted;
        voteCount.value = response.data.votes_count;
    } catch (error) {
        console.error('Error toggling vote:', error);
        alert('Failed to vote. Please try again.');
    } finally {
        loading.value = false;
    }
};
</script>
