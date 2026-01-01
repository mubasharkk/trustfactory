<script setup>
defineProps({
    product: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(['click']);

const truncateDescription = (text, maxLength = 100) => {
    if (!text) return '';
    if (text.length <= maxLength) return text;
    return text.substring(0, maxLength) + '...';
};
</script>

<template>
    <div
        @click="emit('click', product)"
        class="bg-white rounded-lg shadow-md overflow-hidden cursor-pointer hover:shadow-lg transition-shadow duration-300 transform hover:-translate-y-1"
    >
        <!-- Product Image -->
        <div class="h-48 bg-gray-200 overflow-hidden flex items-center justify-center">
            <img
                v-if="product.image"
                :src="product.image"
                :alt="product.name"
                class="h-full w-full object-contain"
            />
            <div
                v-else
                class="w-full h-full bg-gray-200 flex items-center justify-center text-gray-400"
            >
                No Image
            </div>
        </div>

        <!-- Product Info -->
        <div class="p-4">
            <h3 class="text-lg font-semibold text-gray-800 mb-2 line-clamp-2">
                {{ product.name }}
            </h3>
            <p class="text-sm text-gray-600 mb-3 line-clamp-3">
                {{ truncateDescription(product.description) }}
            </p>
            <div class="flex items-center justify-between">
                <span class="text-xl font-bold text-indigo-600">
                    ${{ product.price }}
                </span>
                <span
                    v-if="product.stock_quantity > 0"
                    class="text-sm text-green-600"
                >
                    In Stock
                </span>
                <span v-else class="text-sm text-red-600">
                    Out of Stock
                </span>
            </div>
        </div>
    </div>
</template>
