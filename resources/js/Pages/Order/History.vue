<script setup>
import { Head } from "@inertiajs/vue3";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Separator } from "@/components/ui/separator";
import { Button } from "@/components/ui/button";
import { useForm } from "@inertiajs/vue3";
import {
	AlertDialog,
	AlertDialogAction,
	AlertDialogCancel,
	AlertDialogContent,
	AlertDialogDescription,
	AlertDialogFooter,
	AlertDialogHeader,
	AlertDialogTitle,
	AlertDialogTrigger,
} from "@/components/ui/alert-dialog";
import { ref } from "vue";

defineProps({
	orders: Array,
});

const form = useForm({});

const selectedOrderId = ref(null);

const cancelOrder = () => {
	if (!selectedOrderId.value) return;
	form.delete(route("order.cancel", selectedOrderId.value), {
		preserveScroll: true,
		onSuccess: () => {
			selectedOrderId.value = null;
		},
	});
};
</script>

<template>
	<Head title="| Order History" />

	<div class="space-y-8">
		<div>
			<h1 class="text-2xl font-semibold">Order History</h1>
			<p class="text-muted-foreground text-sm">View all your orders and their status</p>
		</div>

		<div v-if="orders.length" class="space-y-6">
			<Card v-for="order in orders" :key="order.id" class="overflow-hidden">
				<CardHeader class="bg-muted/50">
					<div class="sm:flex-row sm:items-center sm:justify-between flex flex-col gap-4">
						<div>
							<CardTitle class="text-base">Order #{{ order.orderNumber }}</CardTitle>
							<p class="text-muted-foreground text-sm">Placed on {{ order.createdAt }}</p>
						</div>
						<div class="sm:text-right space-y-1 text-sm">
							<p>
								Status:
								<span
									:class="{
										'text-destructive': order.status === 'Cancelled',
										'text-primary': order.status === 'Pending',
										'text-orange-500': order.status === 'Preparing',
										'text-yellow-500': order.status === 'Ready',
										'text-green-600 dark:text-green-400': order.status === 'Completed',
									}"
									class="font-medium"
								>
									{{ order.status }}
								</span>
							</p>
							<p>
								Total: <span class="font-medium">₱{{ order.totalAmount }}</span>
							</p>

							<AlertDialog v-if="order.canCancel">
								<AlertDialogTrigger asChild>
									<Button
										variant="destructive"
										size="sm"
										@click="selectedOrderId = order.id"
									>
										Cancel Order
									</Button>
								</AlertDialogTrigger>
								<AlertDialogContent>
									<AlertDialogHeader>
										<AlertDialogTitle
											>Cancel Order #{{ order.orderNumber }}?</AlertDialogTitle
										>
										<AlertDialogDescription>
											This action cannot be undone. This will permanently cancel your
											order.
										</AlertDialogDescription>
									</AlertDialogHeader>
									<AlertDialogFooter>
										<AlertDialogCancel @click="selectedOrderId = null"
											>No, keep my order</AlertDialogCancel
										>
										<AlertDialogAction @click="cancelOrder" :disabled="form.processing">
											{{ form.processing ? "Cancelling..." : "Yes, cancel order" }}
										</AlertDialogAction>
									</AlertDialogFooter>
								</AlertDialogContent>
							</AlertDialog>
						</div>
					</div>
				</CardHeader>
				<CardContent class="p-6">
					<div class="space-y-4">
						<div v-for="item in order.items" :key="item.id" class="flex gap-4">
							<img
								:src="item.product_image_url"
								:alt="item.name"
								class="object-cover w-20 h-20 rounded"
							/>
							<div>
								<h3 class="font-medium">{{ item.name }}</h3>
								<p class="text-muted-foreground text-sm">Quantity: {{ item.quantity }}</p>
								<p class="text-sm">₱{{ item.total }}</p>
							</div>
						</div>
						<div class="pt-4 space-y-2 text-sm border-t">
							<p>Pickup Time: {{ order.pickupTime }}</p>
							<p v-if="order.note">Notes: {{ order.note }}</p>
						</div>
					</div>
				</CardContent>
			</Card>
		</div>
		<div v-else class="text-muted-foreground py-8 text-center">
			No orders found. Start shopping to place your first order!
		</div>
	</div>
</template>
