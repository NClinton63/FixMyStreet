<template>
    <div>
        <div id="map" class="w-full" :style="{ height: mapHeight }"></div>
    </div>
</template>

<script setup>
import { onMounted, onUnmounted, ref, watch } from 'vue';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

const props = defineProps({
    issues: {
        type: Array,
        default: () => []
    },
    center: {
        type: Array,
        default: () => [3.848, 11.502] // Yaoundé, Cameroon
    },
    zoom: {
        type: Number,
        default: 13
    },
    mapHeight: {
        type: String,
        default: '600px'
    },
    selectedCategory: {
        type: Number,
        default: null
    }
});

const emit = defineEmits(['issue-selected', 'location-selected']);

let map = null;
let markers = [];

// Fix for default marker icons in Leaflet with Vite
delete L.Icon.Default.prototype._getIconUrl;
L.Icon.Default.mergeOptions({
    iconRetinaUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon-2x.png',
    iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
    shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
});

const initMap = () => {
    map = L.map('map').setView(props.center, props.zoom);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 19,
    }).addTo(map);

    // Add markers for issues
    updateMarkers();
};

const createCustomIcon = (color) => {
    return L.divIcon({
        className: 'custom-marker',
        html: `
            <div style="
                background-color: ${color};
                width: 30px;
                height: 30px;
                border-radius: 50% 50% 50% 0;
                transform: rotate(-45deg);
                border: 3px solid white;
                box-shadow: 0 2px 5px rgba(0,0,0,0.3);
            ">
                <div style="
                    transform: rotate(45deg);
                    margin-top: 5px;
                    margin-left: 7px;
                    width: 10px;
                    height: 10px;
                    background-color: white;
                    border-radius: 50%;
                "></div>
            </div>
        `,
        iconSize: [30, 30],
        iconAnchor: [15, 30],
        popupAnchor: [0, -30]
    });
};

const updateMarkers = () => {
    // Clear existing markers
    markers.forEach(marker => map.removeLayer(marker));
    markers = [];

    // Filter issues by selected category if any
    const filteredIssues = props.selectedCategory 
        ? props.issues.filter(issue => issue.category_id === props.selectedCategory)
        : props.issues;

    // Add new markers
    filteredIssues.forEach(issue => {
        const icon = createCustomIcon(issue.category?.color || '#3B82F6');
        
        const marker = L.marker([issue.latitude, issue.longitude], { icon })
            .addTo(map)
            .bindPopup(`
                <div class="p-2 min-w-[200px]">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-xl">${issue.category?.icon || '📍'}</span>
                        <span class="font-semibold text-sm">${issue.category?.name || 'Issue'}</span>
                    </div>
                    <h3 class="font-bold text-base mb-1">${issue.title}</h3>
                    <p class="text-sm text-gray-600 mb-2">${issue.description.substring(0, 100)}${issue.description.length > 100 ? '...' : ''}</p>
                    ${issue.photo_path ? `<img src="/storage/${issue.photo_path}" alt="${issue.title}" class="w-full h-32 object-cover rounded mb-2" />` : ''}
                    <div class="flex items-center justify-between">
                        <span class="text-xs px-2 py-1 rounded ${getStatusClass(issue.status)}">${issue.status}</span>
                        <span class="text-xs text-gray-500">${new Date(issue.created_at).toLocaleDateString()}</span>
                    </div>
                </div>
            `);

        marker.on('click', () => {
            emit('issue-selected', issue);
        });

        markers.push(marker);
    });
};

const getStatusClass = (status) => {
    const classes = {
        pending: 'bg-yellow-100 text-yellow-800',
        in_progress: 'bg-blue-100 text-blue-800',
        resolved: 'bg-green-100 text-green-800',
        rejected: 'bg-red-100 text-red-800'
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
};

watch(() => props.issues, () => {
    if (map) {
        updateMarkers();
    }
}, { deep: true });

watch(() => props.selectedCategory, () => {
    if (map) {
        updateMarkers();
    }
});

onMounted(() => {
    initMap();
});

onUnmounted(() => {
    if (map) {
        map.remove();
    }
});
</script>

<style>
.custom-marker {
    background: transparent;
    border: none;
}
</style>
