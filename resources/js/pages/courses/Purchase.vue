<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';

declare global {
    interface Window {
        Stripe?: (key: string) => any;
    }
}

const props = defineProps<{
    course: {
        id: number;
        name: string;
        description: string;
        price_cents: number;
        currency: string;
    };
    stripePublicKey: string | null;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Courses', href: '/courses' },
    { title: props.course.name, href: `/courses/${props.course.id}` },
    { title: 'Purchase', href: '' },
];

const processing = ref(false);
const errorMessage = ref('');
const statusMessage = ref('');
const stripeReady = ref(false);
const stripe = ref<any>(null);
const elements = ref<any>(null);
const cardElement = ref<any>(null);

function formatPrice(priceCents: number, currency: string) {
    const normalizedCurrency = currency?.toUpperCase() || 'USD';

    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: normalizedCurrency,
    }).format(priceCents / 100);
}

function getCsrfToken(): string {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';
}

function loadStripeJs(): Promise<void> {
    if (window.Stripe) {
        return Promise.resolve();
    }

    return new Promise((resolve, reject) => {
        const script = document.createElement('script');
        script.src = 'https://js.stripe.com/v3/';
        script.onload = () => resolve();
        script.onerror = () => reject(new Error('Failed to load Stripe.js'));
        document.head.appendChild(script);
    });
}

async function setupStripe(): Promise<void> {
    if (!props.stripePublicKey) {
        errorMessage.value = 'Stripe public key is missing. Please set STRIPE_PUBLIC in your .env.';
        return;
    }

    await loadStripeJs();

    if (!window.Stripe) {
        errorMessage.value = 'Stripe.js failed to initialize.';
        return;
    }

    stripe.value = window.Stripe(props.stripePublicKey);
    elements.value = stripe.value.elements();
    cardElement.value = elements.value.create('card', {
        style: {
            base: {
                fontFamily: '"Instrument Sans", system-ui, -apple-system, sans-serif',
                fontSize: '16px',
                color: '#111827',
                '::placeholder': { color: '#9CA3AF' },
            },
        },
    });

    cardElement.value.mount('#card-element');
    stripeReady.value = true;
}

async function createPaymentIntent(): Promise<string> {
    const response = await fetch(`/courses/${props.course.id}/payment-intent`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': getCsrfToken(),
        },
        body: JSON.stringify({}),
    });

    if (!response.ok) {
        const payload = await response.json().catch(() => ({}));
        throw new Error(payload.message ?? 'Unable to create payment intent.');
    }

    const payload = await response.json();

    return payload.client_secret;
}

async function submit(): Promise<void> {
    if (!stripe.value || !cardElement.value) {
        errorMessage.value = 'Stripe Elements is not ready yet.';
        return;
    }

    processing.value = true;
    errorMessage.value = '';
    statusMessage.value = '';

    try {
        const clientSecret = await createPaymentIntent();
        const result = await stripe.value.confirmCardPayment(clientSecret, {
            payment_method: {
                card: cardElement.value,
            },
        });

        if (result.error) {
            errorMessage.value = result.error.message ?? 'Payment failed.';
        } else if (result.paymentIntent?.status === 'succeeded') {
            statusMessage.value = 'Payment succeeded. Redirecting to My Courses...';
            router.visit(route('courses.mine', { notice: 'access-pending' }));
        } else if (result.paymentIntent?.status) {
            statusMessage.value = `Payment status: ${result.paymentIntent.status}.`;
        } else {
            statusMessage.value = 'Payment submitted. Please check your email for updates.';
        }
    } catch (error) {
        errorMessage.value = error instanceof Error ? error.message : 'Payment failed.';
    } finally {
        processing.value = false;
    }
}

onMounted(() => {
    void setupStripe();
});
</script>

<template>
    <Head :title="`Purchase ${props.course.name}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="max-w-xl mx-auto p-6 space-y-6">
            <div class="space-y-2">
                <h1 class="text-2xl font-bold">{{ props.course.name }}</h1>
                <p class="text-gray-700 dark:text-gray-300">{{ props.course.description }}</p>
                <p class="text-lg font-semibold text-rose-800">
                    {{ formatPrice(props.course.price_cents, props.course.currency) }}
                </p>
            </div>

            <div v-if="statusMessage" class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800">
                {{ statusMessage }}
            </div>

            <div v-if="errorMessage" class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-rose-700">
                {{ errorMessage }}
            </div>

            <form @submit.prevent="submit" class="space-y-4">
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Card details
                    </label>
                    <div
                        id="card-element"
                        class="rounded-xl border border-gray-200 dark:border-sidebar-border bg-white dark:bg-gray-900 px-3 py-3"
                    ></div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Use the test card 4242 4242 4242 4242 with any future date, any CVC, and any ZIP.
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <button
                        type="submit"
                        :disabled="processing || !stripeReady"
                        class="bg-rose-800 text-white px-4 py-2 rounded-xl hover:bg-rose-600 disabled:opacity-50"
                    >
                        Pay now
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
