<script setup>
import { ref, onMounted } from "vue";
import {
	Sheet,
	SheetContent,
	SheetHeader,
	SheetTitle,
	SheetTrigger,
} from "@/components/ui/sheet";
import { Button } from "@/components/ui/button";
import { Separator } from "@/components/ui/separator";
import { ShoppingCart, Trash2 } from "lucide-vue-next";
import { router } from "@inertiajs/vue3";
import axios from "axios";

const cart = ref({ items: [] });
const isOpen = ref(false);

const fetchCart = async () => {
	try {
		const response = await axios.get(route("cart.get"));
		cart.value = response.data;
	} catch (error) {
		console.error("Failed to fetch cart:", error);
	}
};

const removeItem = async (cartItem) => {
	try {
		await axios.delete(route("cart.remove", cartItem.cart_item_id));
		await fetchCart();
	} catch (error) {
		console.error("Failed to remove item:", error);
	}
};

const updateQuantity = async (cartItem, quantity) => {
	if (quantity < 1) return;
	try {
		await axios.patch(route("cart.update", cartItem.cart_item_id), { quantity });
		await fetchCart();
	} catch (error) {
		console.error("Failed to update quantity:", error);
	}
};

const calculateTotal = () => {
	return (
		cart.value?.items?.reduce((total, item) => {
			return total + item.product.price * item.quantity;
		}, 0) || 0
	);
};

const goToCheckout = () => {
	router.get(route("checkout.show"));
	isOpen.value = false;
};

// Listen for cart updates
onMounted(() => {
	fetchCart();
	document.addEventListener("cart-updated", fetchCart);
});

defineExpose({ fetchCart });
</script>

<template>
	<Sheet v-model:open="isOpen">
		<SheetTrigger asChild>
			<Button variant="ghost" size="icon" class="relative">
				<ShoppingCart class="h-5 w-5" />
				<span
					v-if="cart?.items?.length"
					class="absolute -right-1 -top-1 h-4 w-4 rounded-full bg-primary text-[11px] text-white"
				>
					{{ cart.items.length }}
				</span>
			</Button>
		</SheetTrigger>
		<SheetContent class="w-full sm:max-w-lg">
			<SheetHeader>
				<SheetTitle>Shopping Cart</SheetTitle>
			</SheetHeader>

			<div class="flex flex-col h-full">
				<!-- Cart Items -->
				<div v-if="cart?.items?.length" class="flex-1 overflow-y-auto py-6 space-y-4">
					<div v-for="item in cart.items" :key="item.cart_item_id" class="flex gap-4">
						<img
							:src="item.product.product_image"
							:alt="item.product.product_name"
							class="h-20 w-20 rounded object-cover"
						/>
						<div class="flex flex-1 flex-col justify-between">
							<div class="flex justify-between">
								<div>
									<h3 class="font-medium">{{ item.product.product_name }}</h3>
									<p class="text-sm text-muted-foreground">₱{{ item.product.price }}</p>
								</div>
								<Button variant="ghost" size="icon" @click="removeItem(item)">
									<Trash2 class="h-4 w-4" />
								</Button>
							</div>
							<div class="flex items-center gap-2">
								<Button
									size="icon"
									variant="outline"
									@click="updateQuantity(item, item.quantity - 1)"
									>-</Button
								>
								<span class="w-12 text-center">{{ item.quantity }}</span>
								<Button
									size="icon"
									variant="outline"
									@click="updateQuantity(item, item.quantity + 1)"
									>+</Button
								>
							</div>
						</div>
					</div>
				</div>

				<!-- Empty State -->
				<div v-else class="flex-1 flex items-center justify-center py-6">
					<p class="text-muted-foreground">Your cart is empty</p>
				</div>

				<!-- Footer -->
				<div v-if="cart?.items?.length" class="border-t py-6 space-y-4">
					<div class="flex items-center justify-between font-medium">
						<span>Total</span>
						<span>₱{{ calculateTotal().toLocaleString() }}</span>
					</div>
					<Button @click="goToCheckout" class="w-full">Checkout</Button>
				</div>
			</div>
		</SheetContent>
	</Sheet>
</template>
