<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';

const props = defineProps({
    auth: {
        type: Object,
        default: () => ({}),
    },
});

const page = usePage();
const cartCount = computed(() => page.props.cart_count || 0);
</script>

<template>
    <header class="bg-white shadow-md sticky top-0 z-40">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between">
                <!-- Logo -->
                <div class="flex items-center">
                    <Link :href="route('shop')" class="text-xl font-bold text-gray-800">
                        TrustFactory
                    </Link>
                </div>

                <!-- Right side buttons -->
                <div class="flex items-center space-x-4">
                    <!-- Cart Button -->
                    <Link
                        v-if="auth?.user"
                        :href="route('cart.index')"
                        class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition"
                    >
                        <FontAwesomeIcon icon="shopping-cart" class="w-5 h-5" />
                        <span class="ml-2">Cart</span>
                        <span
                            v-if="cartCount > 0"
                            class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center"
                        >
                            {{ cartCount }}
                        </span>
                    </Link>
                    <button
                        v-else
                        type="button"
                        disabled
                        class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-400 bg-white border border-gray-300 rounded-md cursor-not-allowed opacity-60"
                    >
                        <FontAwesomeIcon icon="shopping-cart" class="w-5 h-5" />
                        <span class="ml-2">Cart</span>
                    </button>

                    <!-- Auth Buttons -->
                    <template v-if="!auth?.user">
                        <Link
                            :href="route('login')"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition"
                        >
                            <FontAwesomeIcon icon="right-to-bracket" class="w-5 h-5 mr-2" />
                            Login
                        </Link>
                        <Link
                            :href="route('register')"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition"
                        >
                            <FontAwesomeIcon icon="user-plus" class="w-5 h-5 mr-2" />
                            Register
                        </Link>
                    </template>

                    <!-- User Menu (if authenticated) -->
                    <template v-else>
                        <div class="relative ms-3">
                            <Dropdown align="right" width="48">
                                <template #trigger>
                                    <span class="inline-flex rounded-md">
                                        <button
                                            type="button"
                                            class="inline-flex items-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out hover:text-gray-700 focus:outline-none"
                                        >
                                            {{ auth.user.name }}

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
                                    <DropdownLink :href="route('orders.index')">
                                        Orders
                                    </DropdownLink>
                                    <DropdownLink :href="route('profile.edit')">
                                        Profile
                                    </DropdownLink>
                                    <DropdownLink
                                        :href="route('logout')"
                                        method="post"
                                        as="button"
                                    >
                                        Log Out
                                    </DropdownLink>
                                </template>
                            </Dropdown>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </header>
</template>
