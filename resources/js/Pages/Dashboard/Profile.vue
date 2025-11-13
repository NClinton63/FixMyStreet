<template>
    <AuthenticatedLayout>
        <Head title="My Profile" />

        <div class="py-12">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="bg-gradient-to-r from-indigo-50 to-purple-50 px-6 py-4 border-b border-gray-200">
                        <h2 class="text-2xl font-bold text-gray-900">My Profile</h2>
                        <p class="text-sm text-gray-600 mt-1">Manage your account settings and preferences</p>
                    </div>

                    <div class="p-6">
                        <form @submit.prevent="submit" class="space-y-6">
                            <!-- Avatar -->
                            <div class="flex items-center space-x-6">
                                <div class="h-24 w-24 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white text-3xl font-bold">
                                    {{ getInitials(form.name) }}
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">{{ form.name }}</h3>
                                    <p class="text-sm text-gray-600">{{ user.email }}</p>
                                    <p class="text-sm text-indigo-600 font-medium mt-1">
                                        {{ user.reputation_points }} Reputation Points
                                    </p>
                                </div>
                            </div>

                            <!-- Name -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Full Name
                                </label>
                                <input
                                    v-model="form.name"
                                    type="text"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                    required
                                />
                            </div>

                            <!-- Anonymous Mode -->
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex items-start">
                                    <input
                                        v-model="form.is_anonymous"
                                        type="checkbox"
                                        class="mt-1 h-5 w-5 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                    />
                                    <div class="ml-3">
                                        <label class="text-sm font-medium text-gray-900">
                                            Enable Anonymous Mode
                                        </label>
                                        <p class="text-sm text-gray-600 mt-1">
                                            Your real name will be hidden and replaced with your alias when reporting issues
                                        </p>
                                    </div>
                                </div>

                                <!-- Alias (shown when anonymous mode is enabled) -->
                                <div v-if="form.is_anonymous" class="mt-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Public Alias
                                    </label>
                                    <input
                                        v-model="form.alias"
                                        type="text"
                                        placeholder="e.g., CommunityHelper"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                    />
                                </div>
                            </div>

                            <!-- Phone -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Phone Number (Optional)
                                </label>
                                <input
                                    v-model="form.phone"
                                    type="tel"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                />
                            </div>

                            <!-- Bio -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Bio (Optional)
                                </label>
                                <textarea
                                    v-model="form.bio"
                                    rows="4"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                    placeholder="Tell us about yourself..."
                                ></textarea>
                            </div>

                            <!-- Submit Button -->
                            <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                                <Link
                                    href="/dashboard"
                                    class="text-gray-600 hover:text-gray-900 font-medium"
                                >
                                    ← Back to Dashboard
                                </Link>
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-lg font-semibold transition-all disabled:opacity-50"
                                >
                                    {{ form.processing ? 'Saving...' : 'Save Changes' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { useForm, Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    user: {
        type: Object,
        required: true
    }
});

const form = useForm({
    name: props.user.name,
    alias: props.user.alias || '',
    is_anonymous: props.user.is_anonymous || false,
    phone: props.user.phone || '',
    bio: props.user.bio || ''
});

const submit = () => {
    form.put(route('dashboard.profile.update'));
};

const getInitials = (name) => {
    return name
        .split(' ')
        .map(word => word[0])
        .join('')
        .toUpperCase()
        .slice(0, 2);
};
</script>
