<script setup>
defineProps({
    item: {
        type: Object,
        required: true,
    },
});

const itemTotal = () => {
    return (parseFloat(props.item.product_price) || 0) * (props.item.quantity || 0);
};
</script>

<template>
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="flex flex-col md:flex-row gap-4">
            <!-- Product Image -->
            <div class="flex-shrink-0">
                <div class="w-20 h-20 bg-gray-200 rounded-lg overflow-hidden flex items-center justify-center">
                    <div class="text-gray-400 text-xs">
                        No Image
                    </div>
                </div>
            </div>

            <!-- Product Details -->
            <div class="flex-1 min-w-0">
                <h3 class="text-base font-semibold text-gray-800 mb-1">
                    {{ item.product_name }}
                </h3>
                <p class="text-sm text-gray-500 mb-2">
                    ${{ parseFloat(item.product_price).toFixed(2) }} each
                </p>
            </div>

            <!-- Quantity and Subtotal -->
            <div class="flex flex-col items-end gap-2">
                <div class="text-right">
                    <p class="text-sm text-gray-500">Quantity:</p>
                    <p class="text-base font-semibold text-gray-800">
                        {{ item.quantity }}
                    </p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-500">Subtotal:</p>
                    <p class="text-lg font-bold text-gray-800">
                        ${{ itemTotal().toFixed(2) }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
