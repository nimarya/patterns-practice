<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import { Button } from '@/components/ui/button';

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
    { title: 'Courses', href: route('courses.index') },
    { title: props.course.name, href: route('courses.show', props.course.id) },
    { title: 'Purchase', href: route('courses.purchase.show', props.course.id) },
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
                fontFamily: '"Sora", system-ui, -apple-system, sans-serif',
                fontSize: '16px',
                color: '#111827',
                '::placeholder': { color: '#94A3B8' },
            },
        },
    });

    cardElement.value.mount('#card-element');
    stripeReady.value = true;
}

async function createPaymentIntent(): Promise<string> {
    const response = await fetch(route('courses.purchase.intent', props.course.id), {
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
        <section class="mx-auto flex w-full max-w-2xl flex-col gap-6 rounded-3xl border border-border/60 bg-white/80 p-8 shadow-sm backdrop-blur dark:bg-slate-950/70">
            <div class="flex flex-col gap-3">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-emerald-600/80 dark:text-emerald-300/70">Secure checkout</p>
                <h1 class="text-3xl font-semibold tracking-tight text-foreground">{{ props.course.name }}</h1>
                <p class="text-sm text-muted-foreground">{{ props.course.description }}</p>
                <p class="text-lg font-semibold text-emerald-700 dark:text-emerald-300">
                    {{ formatPrice(props.course.price_cents, props.course.currency) }}
                </p>
            </div>

            <div v-if="statusMessage" class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-100">
                {{ statusMessage }}
            </div>

            <div v-if="errorMessage" class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                {{ errorMessage }}
            </div>

            <form @submit.prevent="submit" class="flex flex-col gap-5">
                <div class="flex flex-col gap-2">
                    <label class="text-sm font-medium text-foreground">Card details</label>
                    <div
                        id="card-element"
                        class="rounded-2xl border border-border/70 bg-white px-3 py-3 text-sm shadow-sm focus-within:border-emerald-200 focus-within:ring-4 focus-within:ring-emerald-200/40 dark:bg-slate-950"
                    ></div>
                    <p class="text-xs text-muted-foreground">
                        Use the test card 4242 4242 4242 4242 with any future date, any CVC, and any ZIP.
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <Button type="submit" :disabled="processing || !stripeReady">
                        Pay now
                    </Button>
                </div>
            </form>
        </section>
    </AppLayout>
</template>
