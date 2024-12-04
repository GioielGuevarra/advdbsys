<script setup>
import ItemCard from "@/Components/ItemCard.vue";
import { Separator } from "@/components/ui/separator";

defineProps({
	products: Array,
	currentCategory: String,
});
</script>

<template>
	<Head :title="`| ${currentCategory}`" />
	<div class="space-y-12">
		<div class="space-y-1">
			<h2 class="text-2xl font-semibold tracking-tight">
				{{ currentCategory }}
			</h2>
			<p class="text-muted-foreground text-sm">
				Browse our {{ currentCategory.toLowerCase() }} selection
			</p>
		</div>

		<Separator class="my-4" />

		<!-- Products Grid -->
		<div
			v-if="products.length"
			class="gap-x-6 gap-y-10 sm:grid-cols-2 lg:grid-cols-4 xl:gap-x-8 grid grid-cols-1"
		>
			<div v-for="product in products" :key="product.product_id">
				<ItemCard :product="product" />
			</div>
		</div>
		<div v-else class="text-center text-muted-foreground">
			No products found in this category.
		</div>
	</div>
</template>
