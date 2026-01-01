<script setup>
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import Header from '@/Components/Header.vue';
import ProductCard from '@/Components/ProductCard.vue';
import ProductModal from '@/Components/ProductModal.vue';
import CategoryFilter from '@/Components/CategoryFilter.vue';

defineProps({
    products: {
        type: Array,
        default: () => [],
    },
    categories: {
        type: Array,
        default: () => [],
    },
    selectedCategory: {
        type: [String, Number],
        default: null,
    },
    auth: {
        type: Object,
        default: () => ({}),
    },
});

const selectedProduct = ref(null);
const showModal = ref(false);

const openProductModal = (product) => {
    selectedProduct.value = product;
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    selectedProduct.value = null;
};
</script>

<template>
    <Head title="Shop" />

    <div class="min-h-screen bg-gray-50">
        <Header :auth="auth" />

        <main class="py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-8">
                    Products
                </h1>

                <!-- Category Filter -->
                <CategoryFilter
                    :categories="categories"
                    :selected-category="selectedCategory"
                />

                <!-- Products Grid -->
                <div
                    v-if="products && products.length > 0"
                    class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6"
                >
                    <ProductCard
                        v-for="product in products"
                        :key="product.id"
                        :product="product"
                        @click="openProductModal"
                    />
                </div>

                <!-- Empty State -->
                <div
                    v-else
                    class="text-center py-12"
                >
                    <p class="text-gray-500 text-lg">
                        No products available at the moment.
                    </p>
                </div>
            </div>
        </main>

        <!-- Product Modal -->
        <ProductModal
            :show="showModal"
            :product="selectedProduct"
            @close="closeModal"
        />
    </div>
</template>
