<script setup>
import { computed } from "vue";
import { useForm } from "@inertiajs/vue3";
import { Button } from "@/components/ui/button";
import { Separator } from "@/components/ui/separator";
import {
	Select,
	SelectContent,
	SelectItem,
	SelectTrigger,
	SelectValue,
} from "@/components/ui/select";
import { Textarea } from "@/components/ui/textarea";

const props = defineProps({
	cart: Object,
	pickupSlots: Array,
});

const form = useForm({
	pickup_time: "",
	note: "",
});

const submitOrder = () => {
  form.post(route("checkout.process"), {
    onSuccess: () => {
      // The controller redirects to order.confirmation
      console.log("Order placed successfully");
    },
    onError: (errors) => {
      console.error("Order failed:", errors);
    }
  });
};

const calculateTotal = () => {
	return props.cart.items.reduce((total, item) => {
		return total + item.product.price * item.quantity;
	}, 0);
};

// Simplify the logic to just use the slots provided by the backend
const currentDaySlots = computed(() => {
  // Just return the slots as provided by the backend
  // The backend already handles the business hours logic
  return props.pickupSlots;
});

const noAvailableSlots = computed(() => {
  return currentDaySlots.value.length === 0;
});

// Update error text to be more accurate
const errorText = computed(() => {
  if (noAvailableSlots.value) {
    return "No available pickup slots. Please try again during business hours (9 AM - 5 PM).";
  }
  return "";
});
</script>

<template>
	<div class="max-w-3xl mx-auto space-y-8">
		<div>
			<h1 class="text-2xl font-semibold">Checkout</h1>
			<p class="text-muted-foreground text-sm">
				Review your order and select pickup time
			</p>
		</div>

		<div class="space-y-6">
			<!-- Order Summary -->
			<div class="space-y-4">
				<h2 class="font-semibold">Order Summary</h2>
				<div class="space-y-4">
					<div v-for="item in cart.items" :key="item.cart_item_id" class="flex gap-4">
						<img
							:src="item.product.product_image"
							:alt="item.product.product_name"
							class="object-cover w-20 h-20 rounded"
						/>
						<div class="flex-1">
							<h3 class="font-medium">{{ item.product.product_name }}</h3>
							<p class="text-muted-foreground text-sm">Quantity: {{ item.quantity }}</p>
							<p class="text-sm font-medium">₱{{ item.product.price * item.quantity }}</p>
						</div>
					</div>
				</div>
			</div>

			<Separator />

			<!-- Pickup Time Selection -->
			<div class="space-y-4">
				<h2 class="font-semibold">Select Pickup Time</h2>
				
				<div v-if="errorText" class="mb-2 text-sm text-red-500">
					{{ errorText }}
				</div>

				<Select 
					v-model="form.pickup_time"
					:disabled="noAvailableSlots"
				>
					<SelectTrigger>
						<SelectValue placeholder="Choose pickup time" />
					</SelectTrigger>
					<SelectContent>
						<SelectItem
							v-for="slot in currentDaySlots"
							:key="slot.value"
							:value="slot.value"
						>
							{{ slot.label }}
						</SelectItem>
					</SelectContent>
				</Select>
			</div>

			<!-- Additional Notes -->
			<div class="space-y-4">
				<h2 class="font-semibold">Additional Notes</h2>
				<Textarea
					v-model="form.note"
					placeholder="Add any special instructions or notes here..."
				/>
			</div>

			<Separator />

			<!-- Total and Submit -->
			<div class="space-y-4">
				<div class="flex justify-between text-lg font-semibold">
					<span>Total Amount</span>
					<span>₱{{ calculateTotal() }}</span>
				</div>
				<Button
					class="w-full"
					size="lg"
					:disabled="form.processing || !form.pickup_time"
					@click="submitOrder"
				>
					{{ form.processing ? 'Processing...' : 'Place Order' }}
				</Button>
				<p class="text-muted-foreground text-sm text-center">
					By placing this order, you agree to pay in person during pickup.
				</p>
			</div>
		</div>
	</div>
</template>
