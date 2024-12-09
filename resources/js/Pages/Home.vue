<script setup>
import ItemCard from "@/Components/ItemCard.vue";
import { Separator } from "@/components/ui/separator";
import Hero from "@/Components/Hero.vue";
import { computed } from "vue";

const props = defineProps({
	products: Array,
	categories: Array,
	heroImage: String,
});

const categoriesWithProducts = computed(() => {
	if (!props.products?.length) return [];
	return (
		props.categories?.filter((category) =>
			props.products.some((product) =>
				product.categories.some((c) => c.category_id === category.category_id)
			)
		) || []
	);
});
</script>

<template>
	<Head title="| Home" />
	<div class="space-y-12">
		<!-- Hero Section -->
		<Hero :heroImage="heroImage" />

		<!-- Product Categories -->
		<div v-if="categoriesWithProducts.length > 0">
			<div
				v-for="category in categoriesWithProducts"
				:key="category.category_id"
				class="mb-12"
			>
				<div class="space-y-6">
					<div class="space-y-1">
						<h2 class="text-2xl font-semibold tracking-tight">
							{{ category.category_name }}
						</h2>
					</div>

					<div
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
		</div>

		<!-- Add a message when no products are found -->
		<div v-else class="text-center py-12">
			<p class="text-muted-foreground">No products found.</p>
			<Button variant="link" @click="$inertia.visit(route('home'))">
				View all products
			</Button>
		</div>
	</div>
</template>
