<script setup>
import { ref, watch } from 'vue';
import { Link } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    product: {
        type: Object,
        default: null,
    },
    auth: {
        type: Object,
        default: () => ({}),
    },
});

const emit = defineEmits(['close']);

const quantity = ref(1);

// Reset quantity when product changes or modal opens
watch(
    () => props.product,
    (newProduct) => {
        if (newProduct) {
            quantity.value = 1;
        }
    },
    { immediate: true }
);

watch(
    () => props.show,
    (isOpen) => {
        if (isOpen && props.product) {
            quantity.value = 1;
        }
    }
);

const close = () => {
    emit('close');
};

const isLoggedIn = () => {
    return !!props.auth?.user;
};

const maxQuantity = () => {
    return props.product?.stock_quantity || 0;
};

const incrementQuantity = () => {
    if (quantity.value < maxQuantity()) {
        quantity.value++;
    }
};

const decrementQuantity = () => {
    if (quantity.value > 1) {
        quantity.value--;
    }
};

const handleQuantityInput = (event) => {
    const value = parseInt(event.target.value) || 1;
    if (value < 1) {
        quantity.value = 1;
    } else if (value > maxQuantity()) {
        quantity.value = maxQuantity();
    } else {
        quantity.value = value;
    }
};
</script>

<template>
    <Modal :show="show" max-width="4xl" @close="close">
        <div v-if="product" class="p-6">
            <!-- Close Button -->
            <div class="flex justify-end mb-4">
                <button
                    @click="close"
                    class="text-gray-400 hover:text-gray-600 transition"
                >
                    <svg
                        class="w-6 h-6"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"
                        />
                    </svg>
                </button>
            </div>

            <!-- Product Details -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Product Image -->
                <div class="h-96 bg-gray-200 rounded-lg overflow-hidden flex items-center justify-center">
                    <img
                        v-if="product.image"
                        :src="product.image"
                        :alt="product.name"
                        class="h-full w-full object-contain rounded-lg"
                    />
                    <div
                        v-else
                        class="w-full h-full bg-gray-200 rounded-lg flex items-center justify-center text-gray-400"
                    >
                        No Image Available
                    </div>
                </div>

                <!-- Product Information -->
                <div class="space-y-4">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-800 mb-2">
                            {{ product.name }}
                        </h2>
                        <p
                            v-if="product.category"
                            class="text-sm text-gray-500 mb-4"
                        >
                            Category: {{ product.category.name }}
                        </p>
                    </div>

                    <div class="text-3xl font-bold text-indigo-600">
                        ${{ product.price }}
                    </div>

                    <div class="space-y-2">
                        <div class="flex items-center">
                            <span
                                class="text-sm font-medium text-gray-700 mr-2"
                            >
                                Stock:
                            </span>
                            <span
                                :class="{
                                    'text-green-600': product.stock_quantity > 0,
                                    'text-red-600': product.stock_quantity === 0,
                                }"
                                class="text-sm font-semibold"
                            >
                                {{ product.stock_quantity }} available
                            </span>
                        </div>

                        <div v-if="product.sku" class="flex items-center">
                            <span
                                class="text-sm font-medium text-gray-700 mr-2"
                            >
                                SKU:
                            </span>
                            <span class="text-sm text-gray-600">
                                {{ product.sku }}
                            </span>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">
                            Description
                        </h3>
                        <p class="text-gray-600 leading-relaxed">
                            {{ product.description || 'No description available.' }}
                        </p>
                    </div>

                    <!-- Quantity Selection -->
                    <div
                        v-if="isLoggedIn() && product.stock_quantity > 0"
                        class="pt-4 border-t border-gray-200"
                    >
                        <label
                            class="block text-sm font-medium text-gray-700 mb-2"
                        >
                            Quantity
                        </label>
                        <div class="flex items-center space-x-3">
                            <button
                                type="button"
                                @click="decrementQuantity"
                                :disabled="quantity <= 1"
                                class="inline-flex items-center justify-center w-10 h-10 rounded-md border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:bg-gray-100 disabled:text-gray-400 disabled:cursor-not-allowed transition"
                            >
                                <svg
                                    class="w-5 h-5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M20 12H4"
                                    />
                                </svg>
                            </button>
                            <input
                                type="number"
                                :value="quantity"
                                @input="handleQuantityInput"
                                :min="1"
                                :max="maxQuantity()"
                                class="w-20 text-center rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-2 px-3 border"
                            />
                            <button
                                type="button"
                                @click="incrementQuantity"
                                :disabled="quantity >= maxQuantity()"
                                class="inline-flex items-center justify-center w-10 h-10 rounded-md border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:bg-gray-100 disabled:text-gray-400 disabled:cursor-not-allowed transition"
                            >
                                <svg
                                    class="w-5 h-5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 4v16m8-8H4"
                                    />
                                </svg>
                            </button>
                            <span class="text-sm text-gray-500">
                                (Max: {{ maxQuantity() }})
                            </span>
                        </div>
                    </div>

                    <!-- Login Message (if not logged in) -->
                    <div
                        v-if="!isLoggedIn()"
                        class="pt-4 border-t border-gray-200"
                    >
                        <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4 mb-4">
                            <p class="text-sm text-yellow-800">
                                <strong>Please login or register</strong> to add items to your cart.
                            </p>
                            <div class="mt-3 flex space-x-3">
                                <Link
                                    :href="route('login')"
                                    class="text-sm font-medium text-indigo-600 hover:text-indigo-800"
                                >
                                    Login
                                </Link>
                                <span class="text-gray-300">|</span>
                                <Link
                                    :href="route('register')"
                                    class="text-sm font-medium text-indigo-600 hover:text-indigo-800"
                                >
                                    Register
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="pt-4 flex space-x-4">
                        <PrimaryButton
                            :disabled="!isLoggedIn() || product.stock_quantity === 0"
                            class="flex-1"
                        >
                            Add to Cart ({{ quantity }})
                        </PrimaryButton>
                        <SecondaryButton @click="close">
                            Close
                        </SecondaryButton>
                    </div>
                </div>
            </div>
        </div>
    </Modal>
</template>
