<script setup>
import { router } from "@inertiajs/vue3";

// use route.params() to 'stack' search query parameters coming from different components and pass them as one parameter
const params = route().params;

defineProps({
	product: Object,
});

const selectUser = (id) => {
	router.get(router("home"), {
		// this should redirect to a dedicated profile page for that user
		user_id: id,
	});
};
</script>

<template>
	<Link :href="route('product.show', product.product_id)">
		<div class="group relative">
			<div
				class="aspect-h-1 aspect-w-1 lg:aspect-none group-hover:opacity-75 lg:h-80 bg-muted w-full overflow-hidden rounded-lg"
			>
				<img
					:src="product.product_image"
					:alt="product.product_name"
					class="lg:h-full lg:w-full object-cover object-center w-full h-full"
				/>
			</div>
			<div class="flex justify-between mt-4">
				<div>
					<h3 class="text-foreground text-sm font-medium">
						<span aria-hidden="true" class="absolute inset-0" />
						{{ product.product_name }}
					</h3>
					<p class="text-muted-foreground mt-1 text-sm">{{ product.description }}</p>
				</div>
				<p class="text-primary text-sm font-medium">₱{{ product.price }}</p>
			</div>
		</div>
	</Link>
</template>
