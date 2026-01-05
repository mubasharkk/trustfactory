<script setup>
import { ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import Header from '@/Components/Header.vue';
import CartItemRow from '@/Components/CartItemRow.vue';

const props = defineProps({
    orders: {
        type: Array,
        default: () => [],
    },
    auth: {
        type: Object,
        default: () => ({}),
    },
});

const collapsedOrders = ref({});

const toggleOrder = (orderId) => {
    collapsedOrders.value[orderId] = !collapsedOrders.value[orderId];
};

const isCollapsed = (orderId) => {
    return collapsedOrders.value[orderId] ?? true;
};

const formatDate = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
};

const getStatusColor = (status) => {
    const colors = {
        pending: 'bg-yellow-100 text-yellow-800',
        processing: 'bg-blue-100 text-blue-800',
        completed: 'bg-green-100 text-green-800',
        cancelled: 'bg-red-100 text-red-800',
    };
    return colors[status] || 'bg-gray-100 text-gray-800';
};

// Transform order items to match CartItemRow structure
const transformOrderItem = (item) => {
    return {
        id: item.product_id,
        quantity: item.quantity,
        product: {
            id: item.product_id,
            name: item.product_name,
            price: item.product_price,
            image: null,
            category: null,
        },
    };
};
</script>

<template>
    <Head title="My Orders" />

    <div class="min-h-screen bg-gray-50">
        <Header :auth="auth" />

        <main class="py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-8">
                    My Orders
                </h1>

                <!-- Empty State -->
                <div
                    v-if="!orders || orders.length === 0"
                    class="text-center py-12 bg-white rounded-lg shadow-sm"
                >
                    <p class="text-gray-500 text-lg mb-4">
                        You haven't placed any orders yet.
                    </p>
                    <Link
                        :href="route('shop')"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition"
                    >
                        Start Shopping
                    </Link>
                </div>

                <!-- Orders List -->
                <div
                    v-else
                    class="space-y-4"
                >
                    <div
                        v-for="order in orders"
                        :key="order.id"
                        class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden"
                    >
                        <!-- Order Header -->
                        <div
                            class="p-4 border-b border-gray-200 cursor-pointer hover:bg-gray-50 transition"
                            @click="toggleOrder(order.id)"
                        >
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                                <div class="flex items-center gap-3">
                                    <button
                                        type="button"
                                        class="text-gray-400 hover:text-gray-600 transition"
                                    >
                                        <svg
                                            class="w-5 h-5 transform transition-transform"
                                            :class="{ 'rotate-90': !isCollapsed(order.id) }"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M9 5l7 7-7 7"
                                            />
                                        </svg>
                                    </button>
                                    <div>
                                        <h3 class="text-base font-semibold text-gray-800">
                                            Order #{{ order.id.substring(0, 8) }}
                                        </h3>
                                        <p class="text-xs text-gray-500 mt-1">
                                            {{ formatDate(order.created_at) }}
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span
                                        :class="getStatusColor(order.status)"
                                        class="px-2 py-1 rounded-full text-xs font-medium capitalize"
                                    >
                                        {{ order.status }}
                                    </span>
                                    <span class="text-base font-bold text-gray-800">
                                        ${{ parseFloat(order.total_price).toFixed(2) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Order Items -->
                        <div
                            v-show="!isCollapsed(order.id)"
                            class="p-4 space-y-2"
                        >
                            <CartItemRow
                                v-for="(item, index) in order.items"
                                :key="index"
                                :cart-item="transformOrderItem(item)"
                                readonly
                            />
                        </div>

                        <!-- Order Footer -->
                        <div class="px-4 py-2 bg-gray-50 border-t border-gray-200">
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-gray-600">
                                    Payment: {{ order.payment_method }}
                                </span>
                                <span class="text-gray-600">
                                    {{ order.quantity }} item(s)
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>
