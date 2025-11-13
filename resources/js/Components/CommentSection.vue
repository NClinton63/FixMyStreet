<template>
    <div class="space-y-6">
        <!-- Comment Form -->
        <div v-if="canComment" class="bg-white rounded-xl shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <ChatBubbleLeftIcon class="h-5 w-5 mr-2 text-blue-600" />
                Add a Comment
            </h3>
            <form @submit.prevent="submitComment" class="space-y-4">
                <textarea
                    v-model="newComment"
                    rows="3"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                    placeholder="Share your thoughts or provide updates..."
                    required
                ></textarea>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500">
                        {{ newComment.length }}/1000 characters
                    </span>
                    <button
                        type="submit"
                        :disabled="submitting || !newComment.trim()"
                        class="flex items-center space-x-2 bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200"
                    >
                        <PaperAirplaneIcon class="h-5 w-5" />
                        <span>{{ submitting ? 'Posting...' : 'Post Comment' }}</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Login Prompt -->
        <div v-else class="bg-blue-50 border border-blue-200 rounded-xl p-6 text-center">
            <ChatBubbleLeftIcon class="h-12 w-12 text-blue-600 mx-auto mb-3" />
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Join the Discussion</h3>
            <p class="text-gray-600 mb-4">Please log in to add comments and engage with the community.</p>
            <a href="/login" class="inline-flex items-center space-x-2 bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-all duration-200">
                <span>Log In</span>
            </a>
        </div>

        <!-- Comments List -->
        <div class="space-y-4">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <ChatBubbleLeftRightIcon class="h-5 w-5 mr-2 text-gray-600" />
                Comments ({{ comments.length }})
            </h3>

            <div v-if="loading" class="text-center py-8">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
                <p class="text-gray-600 mt-4">Loading comments...</p>
            </div>

            <div v-else-if="comments.length === 0" class="bg-gray-50 rounded-xl p-8 text-center">
                <ChatBubbleLeftIcon class="h-16 w-16 text-gray-300 mx-auto mb-3" />
                <p class="text-gray-600">No comments yet. Be the first to comment!</p>
            </div>

            <div v-else class="space-y-4">
                <div
                    v-for="comment in comments"
                    :key="comment.id"
                    class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition-shadow"
                >
                    <div class="flex items-start space-x-4">
                        <!-- Avatar -->
                        <div class="flex-shrink-0">
                            <div v-if="comment.user?.avatar" class="h-10 w-10 rounded-full overflow-hidden">
                                <img :src="`/storage/${comment.user.avatar}`" :alt="comment.user.name" class="h-full w-full object-cover" />
                            </div>
                            <div v-else class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white font-semibold">
                                {{ getInitials(comment.user?.name || 'Anonymous') }}
                            </div>
                        </div>

                        <!-- Comment Content -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center space-x-2">
                                    <span class="font-semibold text-gray-900">
                                        {{ comment.user?.name || 'Anonymous' }}
                                    </span>
                                    <span v-if="comment.is_admin" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                        <ShieldCheckIcon class="h-3 w-3 mr-1" />
                                        Admin
                                    </span>
                                </div>
                                <span class="text-sm text-gray-500">
                                    {{ formatDate(comment.created_at) }}
                                </span>
                            </div>
                            
                            <p class="text-gray-700 whitespace-pre-wrap">{{ comment.content }}</p>

                            <!-- Comment Actions -->
                            <div v-if="canEditComment(comment)" class="mt-3 flex items-center space-x-4">
                                <button
                                    @click="editComment(comment)"
                                    class="text-sm text-blue-600 hover:text-blue-800 flex items-center space-x-1"
                                >
                                    <PencilIcon class="h-4 w-4" />
                                    <span>Edit</span>
                                </button>
                                <button
                                    @click="deleteComment(comment)"
                                    class="text-sm text-red-600 hover:text-red-800 flex items-center space-x-1"
                                >
                                    <TrashIcon class="h-4 w-4" />
                                    <span>Delete</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Load More -->
            <div v-if="hasMore" class="text-center pt-4">
                <button
                    @click="loadMore"
                    class="text-blue-600 hover:text-blue-800 font-medium"
                >
                    Load More Comments
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { 
    ChatBubbleLeftIcon, 
    ChatBubbleLeftRightIcon,
    PaperAirplaneIcon,
    ShieldCheckIcon,
    PencilIcon,
    TrashIcon
} from '@heroicons/vue/24/outline';
import axios from 'axios';

const props = defineProps({
    issueId: {
        type: Number,
        required: true
    },
    canComment: {
        type: Boolean,
        default: false
    },
    currentUserId: {
        type: Number,
        default: null
    }
});

const comments = ref([]);
const newComment = ref('');
const loading = ref(false);
const submitting = ref(false);
const hasMore = ref(false);

onMounted(() => {
    loadComments();
});

const loadComments = async () => {
    loading.value = true;
    try {
        const response = await axios.get(`/issues/${props.issueId}/comments`);
        comments.value = response.data.data;
        hasMore.value = response.data.next_page_url !== null;
    } catch (error) {
        console.error('Error loading comments:', error);
    } finally {
        loading.value = false;
    }
};

const submitComment = async () => {
    if (!newComment.value.trim() || submitting.value) return;

    submitting.value = true;
    try {
        const response = await axios.post(`/issues/${props.issueId}/comments`, {
            content: newComment.value
        });
        
        comments.value.unshift(response.data.comment);
        newComment.value = '';
    } catch (error) {
        console.error('Error submitting comment:', error);
        alert('Failed to post comment. Please try again.');
    } finally {
        submitting.value = false;
    }
};

const canEditComment = (comment) => {
    return props.currentUserId && props.currentUserId === comment.user_id;
};

const editComment = (comment) => {
    // TODO: Implement edit functionality
    console.log('Edit comment:', comment);
};

const deleteComment = async (comment) => {
    if (!confirm('Are you sure you want to delete this comment?')) return;

    try {
        await axios.delete(`/comments/${comment.id}`);
        comments.value = comments.value.filter(c => c.id !== comment.id);
    } catch (error) {
        console.error('Error deleting comment:', error);
        alert('Failed to delete comment. Please try again.');
    }
};

const loadMore = () => {
    // TODO: Implement pagination
    console.log('Load more comments');
};

const getInitials = (name) => {
    return name
        .split(' ')
        .map(word => word[0])
        .join('')
        .toUpperCase()
        .slice(0, 2);
};

const formatDate = (date) => {
    const d = new Date(date);
    const now = new Date();
    const diff = now - d;
    const minutes = Math.floor(diff / 60000);
    const hours = Math.floor(minutes / 60);
    const days = Math.floor(hours / 24);

    if (minutes < 1) return 'Just now';
    if (minutes < 60) return `${minutes}m ago`;
    if (hours < 24) return `${hours}h ago`;
    if (days < 7) return `${days}d ago`;
    
    return d.toLocaleDateString();
};
</script>
