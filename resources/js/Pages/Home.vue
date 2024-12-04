<script setup>
import ItemCard from "@/Components/ItemCard.vue";
import { Separator } from "@/components/ui/separator";
import Hero from "@/Components/Hero.vue";

defineProps({
	products: Array,
	categories: Array,
	heroImage: String,
});
</script>

<template>
	<Head title="| Home" />
	<div class="space-y-12">
		<!-- Hero Section -->
		<Hero :heroImage="heroImage" />

		<!-- Product Categories -->
		<div v-for="category in categories" :key="category.category_id">
			<div class="flex items-center justify-between">
				<div class="space-y-1">
					<h2 class="text-2xl font-semibold tracking-tight">
						{{ category.category_name }}
					</h2>
					<p class="text-muted-foreground text-sm">
						{{ category.description }}
					</p>
				</div>
			</div>

			<Separator class="my-4" />

			<!-- Products Grid -->
			<div
				v-if="products.length"
				class="gap-x-6 gap-y-10 sm:grid-cols-2 lg:grid-cols-4 xl:gap-x-8 grid grid-cols-1"
			>
				<div
					v-for="product in products.filter((p) =>
						p.categories.some((c) => c.category_id === category.category_id)
					)"
					:key="product.product_id"
				>
					<ItemCard :product="product" />
				</div>
			</div>
		</div>
	</div>
</template>
