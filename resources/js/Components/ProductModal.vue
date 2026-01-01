<script setup>
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

const close = () => {
    emit('close');
};

const isLoggedIn = () => {
    return !!props.auth?.user;
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
                            Add to Cart
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
