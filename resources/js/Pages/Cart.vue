<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import Header from '@/Components/Header.vue';
import CartItemRow from '@/Components/CartItemRow.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    cartItems: {
        type: Array,
        default: () => [],
    },
    cartCount: {
        type: Number,
        default: 0,
    },
    totalPrice: {
        type: Number,
        default: 0,
    },
    auth: {
        type: Object,
        default: () => ({}),
    },
});

const cartItemsList = ref([...props.cartItems]);
const showCheckoutModal = ref(false);
const processingCheckout = ref(false);
const checkoutError = ref(null);

const handleItemUpdated = (updatedItem) => {
    const index = cartItemsList.value.findIndex(item => item.id === updatedItem.id);
    if (index !== -1) {
        cartItemsList.value[index] = updatedItem;
    }
    // Reload page data to get updated totals
    router.reload({ only: ['cartItems', 'cartCount', 'totalPrice'] });
};

const handleItemRemoved = (itemId) => {
    cartItemsList.value = cartItemsList.value.filter(item => item.id !== itemId);
    // Reload page data to get updated totals
    router.reload({ only: ['cartItems', 'cartCount', 'totalPrice'] });
};

const isEmpty = computed(() => cartItemsList.value.length === 0);

const openCheckoutModal = () => {
    showCheckoutModal.value = true;
    checkoutError.value = null;
};

const closeCheckoutModal = () => {
    showCheckoutModal.value = false;
    checkoutError.value = null;
};

const checkoutForm = useForm({});

const proceedCheckout = () => {
    processingCheckout.value = true;
    checkoutError.value = null;

    checkoutForm.post(route('checkout.store'), {
        preserveScroll: true,
        onSuccess: () => {
            showCheckoutModal.value = false;
            // Redirect is handled by backend
        },
        onError: (errors) => {
            if (errors.checkout) {
                checkoutError.value = errors.checkout;
            } else {
                checkoutError.value = 'Failed to process checkout. Please try again.';
            }
        },
        onFinish: () => {
            processingCheckout.value = false;
        },
    });
};
</script>

<template>
    <Head title="Shopping Cart" />

    <div class="min-h-screen bg-gray-50">
        <Header :auth="auth" />

        <main class="py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-8">
                    Shopping Cart
                </h1>

                <!-- Empty Cart State -->
                <div
                    v-if="isEmpty"
                    class="text-center py-12 bg-white rounded-lg shadow-sm"
                >
                    <p class="text-gray-500 text-lg mb-4">
                        Your cart is empty.
                    </p>
                    <Link
                        :href="route('shop')"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition"
                    >
                        Continue Shopping
                    </Link>
                </div>

                <!-- Cart Items -->
                <div
                    v-else
                    class="grid grid-cols-1 lg:grid-cols-3 gap-8"
                >
                    <!-- Cart Items List -->
                    <div class="lg:col-span-2 space-y-4">
                        <CartItemRow
                            v-for="item in cartItemsList"
                            :key="item.id"
                            :cart-item="item"
                            @item-updated="handleItemUpdated"
                            @item-removed="handleItemRemoved"
                        />
                    </div>

                    <!-- Order Summary -->
                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 sticky top-4">
                            <h2 class="text-xl font-semibold text-gray-800 mb-4">
                                Order Summary
                            </h2>

                            <div class="space-y-3 mb-6">
                                <div class="flex justify-between text-sm text-gray-600">
                                    <span>Items ({{ cartCount }})</span>
                                    <span>${{ totalPrice.toFixed(2) }}</span>
                                </div>
                                <div class="border-t border-gray-200 pt-3">
                                    <div class="flex justify-between text-lg font-bold text-gray-800">
                                        <span>Total</span>
                                        <span>${{ totalPrice.toFixed(2) }}</span>
                                    </div>
                                </div>
                            </div>

                            <PrimaryButton
                                class="w-full"
                                @click="openCheckoutModal"
                            >
                                Proceed to Checkout
                            </PrimaryButton>

                            <Link
                                :href="route('shop')"
                                class="block mt-4 text-center text-sm text-indigo-600 hover:text-indigo-800"
                            >
                                Continue Shopping
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Checkout Confirmation Modal -->
        <Modal :show="showCheckoutModal" max-width="md" @close="closeCheckoutModal">
            <div class="p-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">
                    Confirm Checkout
                </h2>

                <div class="mb-6">
                    <p class="text-gray-600 mb-4">
                        Are you sure you want to proceed with checkout? This will:
                    </p>
                    <ul class="list-disc list-inside text-gray-600 space-y-2 mb-4">
                        <li>Create an order for {{ cartCount }} item(s)</li>
                        <li>Total amount: <strong class="text-gray-800">${{ totalPrice.toFixed(2) }}</strong></li>
                        <li>Clear your cart</li>
                    </ul>
                </div>

                <!-- Error Message -->
                <div
                    v-if="checkoutError"
                    class="mb-4 bg-red-50 border border-red-200 rounded-md p-4"
                >
                    <p class="text-sm text-red-800">
                        {{ checkoutError }}
                    </p>
                </div>

                <div class="flex space-x-4">
                    <PrimaryButton
                        @click="proceedCheckout"
                        :disabled="processingCheckout"
                        class="flex-1"
                    >
                        <span v-if="processingCheckout">Processing...</span>
                        <span v-else>Confirm Checkout</span>
                    </PrimaryButton>
                    <SecondaryButton
                        @click="closeCheckoutModal"
                        :disabled="processingCheckout"
                    >
                        Cancel
                    </SecondaryButton>
                </div>
            </div>
        </Modal>
    </div>
</template>
