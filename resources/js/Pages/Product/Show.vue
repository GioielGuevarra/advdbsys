<script setup>
import Breadcrumbs from "@/Components/Breadcrumbs.vue";
import { Separator } from "@/components/ui/separator";
import ItemCard from "@/Components/ItemCard.vue";
import { useForm } from "@inertiajs/vue3";
import { Button } from "@/components/ui/button";
import {
	NumberField,
	NumberFieldContent,
	NumberFieldInput,
	NumberFieldDecrement,
	NumberFieldIncrement,
} from "@/components/ui/number-field";
import { ref } from "vue";
import { useToast } from "@/Components/ui/toast/use-toast";
import { Toaster } from "@/components/ui/toast";

const props = defineProps({
	product: Object,
	relatedProducts: Array,
});

const form = useForm({
	product_id: props.product.product_id,
	quantity: 1,
});

const cart = ref(null);

const { toast } = useToast();

const addToCart = () => {
	form.post(route("cart.add"), {
		onSuccess: () => {
			document.dispatchEvent(new CustomEvent("cart-updated"));
			toast({
				title: "Success",
				description: "Item added to cart",
				variant: "success",
				duration: 3000,
			});
		},
		onError: () => {
			toast({
				title: "Error",
				description: "Could not add item to cart",
				variant: "destructive",
				duration: 3000,
			});
		},
	});
};
</script>

<template>
	<div class="space-y-8">
		<Breadcrumbs
			:currentPage="product.product_name"
			:category="product.categories[0]?.category_name"
			class="lg:flex hidden"
		/>

		<div class="md:grid-cols-2 grid gap-8">
			<!-- Product Image -->
			<div class="aspect-square bg-muted overflow-hidden rounded-lg">
				<img
					:src="
						product.product_image
							? `/storage/products/${product.product_image}`
							: '/storage/products/default.png'
					"
					:alt="product.product_name"
					class="object-cover object-center w-full h-full"
				/>
			</div>

			<!-- Product Details -->
			<div class="space-y-6">
				<div class="space-y-2">
					<h1 class="text-3xl font-bold">{{ product.product_name }}</h1>
					<p class="text-primary text-2xl font-semibold">₱{{ product.price }}</p>
				</div>

				<Separator />

				<div class="space-y-2">
					<h3 class="font-semibold">Description</h3>
					<p class="text-muted-foreground">{{ product.description }}</p>
				</div>

				<div class="space-y-2">
					<h3 class="font-semibold">Categories</h3>
					<div class="flex gap-2">
						<span
							v-for="category in product.categories"
							:key="category.category_id"
							class="bg-secondary inline-flex items-center px-3 py-1 text-sm rounded-full"
						>
							{{ category.category_name }}
						</span>
					</div>
				</div>

				<!-- Add to Cart Form -->
				<form @submit.prevent="addToCart" class="space-y-4">
					<div class="flex items-center gap-4">
						<NumberField v-model="form.quantity" :min="1" :max="99">
							<NumberFieldContent class="w-[120px]">
								<NumberFieldDecrement />
								<NumberFieldInput />
								<NumberFieldIncrement />
							</NumberFieldContent>
						</NumberField>
						<Button type="submit" class="flex-1"> Add to Cart </Button>
					</div>
				</form>
			</div>
		</div>

		<!-- Related Products -->
		<div v-if="relatedProducts?.length" class="space-y-6">
			<h2 class="text-2xl font-semibold">Related Products</h2>
			<div class="sm:grid-cols-2 lg:grid-cols-4 grid gap-6">
				<ItemCard
					v-for="product in relatedProducts"
					:key="product.product_id"
					:product="product"
				/>
			</div>
		</div>
	</div>
	<Toaster />
</template>
