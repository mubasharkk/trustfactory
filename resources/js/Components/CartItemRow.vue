<script setup>
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    cartItem: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(['item-updated', 'item-removed']);

const quantity = ref(props.cartItem.quantity);
const updating = ref(false);
const error = ref(null);

// Watch for external changes to cartItem
watch(
    () => props.cartItem.quantity,
    (newQuantity) => {
        quantity.value = newQuantity;
    }
);

const maxQuantity = () => {
    return props.cartItem.product?.stock_quantity || 0;
};

const handleQuantityChange = async (newQuantity) => {
    const value = parseInt(newQuantity) || 1;
    
    if (value < 1) {
        quantity.value = 1;
        return;
    }
    
    if (value > maxQuantity()) {
        quantity.value = maxQuantity();
        error.value = `Maximum available: ${maxQuantity()}`;
        return;
    }
    
    quantity.value = value;
    error.value = null;
    
    // Update quantity after a short delay to avoid too many requests
    clearTimeout(updateTimeout.value);
    updateTimeout.value = setTimeout(() => {
        updateQuantity();
    }, 500);
};

const updateTimeout = ref(null);

const updateQuantity = async () => {
    if (quantity.value === props.cartItem.quantity) {
        return; // No change
    }

    updating.value = true;
    error.value = null;

    try {
        const response = await axios.patch(
            route('cart.update', props.cartItem.id),
            {
                quantity: quantity.value,
            }
        );

        // Update cart count globally
        router.reload({ only: ['cart_count'] });
        
        emit('item-updated', {
            ...props.cartItem,
            quantity: quantity.value,
        });
    } catch (err) {
        if (err.response?.data?.errors) {
            error.value = Object.values(err.response.data.errors).flat().join(', ');
        } else {
            error.value = err.response?.data?.message || 'Failed to update quantity. Please try again.';
        }
        // Revert to original quantity on error
        quantity.value = props.cartItem.quantity;
    } finally {
        updating.value = false;
    }
};

const removeItem = async () => {
    if (!confirm('Are you sure you want to remove this item from your cart?')) {
        return;
    }

    updating.value = true;
    error.value = null;

    try {
        await axios.delete(route('cart.destroy', props.cartItem.id));

        // Update cart count globally
        router.reload({ only: ['cart_count'] });
        
        emit('item-removed', props.cartItem.id);
    } catch (err) {
        error.value = err.response?.data?.message || 'Failed to remove item. Please try again.';
    } finally {
        updating.value = false;
    }
};

const itemTotal = () => {
    return (props.cartItem.product?.price || 0) * quantity.value;
};
</script>

<template>
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="flex flex-col md:flex-row gap-4">
            <!-- Product Image -->
            <div class="flex-shrink-0">
                <div class="w-24 h-24 bg-gray-200 rounded-lg overflow-hidden flex items-center justify-center">
                    <img
                        v-if="cartItem.product?.image"
                        :src="cartItem.product.image"
                        :alt="cartItem.product.name"
                        class="w-full h-full object-contain"
                    />
                    <div
                        v-else
                        class="text-gray-400 text-xs"
                    >
                        No Image
                    </div>
                </div>
            </div>

            <!-- Product Details -->
            <div class="flex-1 min-w-0">
                <h3 class="text-lg font-semibold text-gray-800 mb-1">
                    {{ cartItem.product?.name }}
                </h3>
                <p
                    v-if="cartItem.product?.category"
                    class="text-sm text-gray-500 mb-2"
                >
                    {{ cartItem.product.category.name }}
                </p>
                <p class="text-lg font-bold text-indigo-600">
                    ${{ cartItem.product?.price }}
                </p>
            </div>

            <!-- Quantity Controls -->
            <div class="flex flex-col items-end gap-2">
                <div class="flex items-center gap-2">
                    <label class="text-sm text-gray-700">Qty:</label>
                    <input
                        type="number"
                        :value="quantity"
                        @input="handleQuantityChange($event.target.value)"
                        :min="1"
                        :max="maxQuantity()"
                        :disabled="updating"
                        class="w-20 text-center rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-1 px-2 border disabled:bg-gray-100 disabled:cursor-not-allowed"
                    />
                </div>

                <div class="text-right">
                    <p class="text-sm text-gray-500">Subtotal:</p>
                    <p class="text-lg font-bold text-gray-800">
                        ${{ itemTotal().toFixed(2) }}
                    </p>
                </div>

                <!-- Error Message -->
                <p
                    v-if="error"
                    class="text-xs text-red-600"
                >
                    {{ error }}
                </p>

                <!-- Remove Button -->
                <button
                    @click="removeItem"
                    :disabled="updating"
                    class="text-sm text-red-600 hover:text-red-800 disabled:opacity-50 disabled:cursor-not-allowed transition"
                >
                    Remove
                </button>
            </div>
        </div>
    </div>
</template>
