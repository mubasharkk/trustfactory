<script setup>
import { computed, ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import Dropdown from '@/Components/Dropdown.vue';

const props = defineProps({
    categories: {
        type: Array,
        default: () => [],
    },
    selectedCategory: {
        type: [String, Number],
        default: null,
    },
    // Allow parent to control if filtering should use router or emit events
    useRouter: {
        type: Boolean,
        default: true,
    },
    // Custom route name for filtering (if useRouter is true)
    filterRoute: {
        type: String,
        default: 'shop',
    },
    // Label for the dropdown
    label: {
        type: String,
        default: 'Filter by Category',
    },
});

const emit = defineEmits(['filter-change']);

const selectedCategoryId = ref(props.selectedCategory);

// Watch for prop changes to sync with parent
watch(
    () => props.selectedCategory,
    (newValue) => {
        selectedCategoryId.value = newValue;
    }
);

const selectedCategoryName = computed(() => {
    if (!selectedCategoryId.value) {
        return props.label;
    }
    const category = props.categories.find(
        (cat) => cat.id == selectedCategoryId.value
    );
    return category ? category.name : props.label;
});

const handleCategorySelect = (categoryId) => {
    selectedCategoryId.value = categoryId;

    if (props.useRouter) {
        // Use Inertia router to update URL with query parameter
        router.get(
            route(props.filterRoute),
            categoryId ? { category: categoryId } : {},
            {
                preserveState: true,
                preserveScroll: true,
                only: ['products'],
            }
        );
    } else {
        // Emit event for parent component to handle
        emit('filter-change', categoryId);
    }
};

const clearFilter = () => {
    handleCategorySelect(null);
};
</script>

<template>
    <div class="mb-6">
        <div class="relative">
            <Dropdown align="left" width="48">
                <template #trigger>
                    <span class="inline-flex rounded-md">
                        <button
                            type="button"
                            class="inline-flex items-center rounded-md border border-transparent bg-white px-4 py-2 text-sm font-medium leading-4 text-gray-700 transition duration-150 ease-in-out hover:text-gray-900 focus:outline-none shadow-sm"
                        >
                            {{ selectedCategoryName }}

                            <svg
                                class="-me-0.5 ms-2 h-4 w-4"
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </button>
                    </span>
                </template>

                <template #content>
                    <button
                        @click="clearFilter"
                        :class="{
                            'bg-gray-100': !selectedCategoryId,
                            'bg-white': selectedCategoryId,
                        }"
                        class="block w-full px-4 py-2 text-start text-sm leading-5 text-gray-700 transition duration-150 ease-in-out hover:bg-gray-100 focus:bg-gray-100 focus:outline-none"
                    >
                        All Categories
                    </button>
                    <button
                        v-for="category in categories"
                        :key="category.id"
                        @click="handleCategorySelect(category.id)"
                        :class="{
                            'bg-gray-100': selectedCategoryId == category.id,
                            'bg-white': selectedCategoryId != category.id,
                        }"
                        class="block w-full px-4 py-2 text-start text-sm leading-5 text-gray-700 transition duration-150 ease-in-out hover:bg-gray-100 focus:bg-gray-100 focus:outline-none"
                    >
                        {{ category.name }}
                    </button>
                </template>
            </Dropdown>
        </div>

        <p
            v-if="!categories || categories.length === 0"
            class="mt-2 text-sm text-gray-500"
        >
            No categories available
        </p>
    </div>
</template>
