<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Head, router } from "@inertiajs/vue3";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Button } from "@/components/ui/button";
import {
	Select,
	SelectContent,
	SelectItem,
	SelectTrigger,
	SelectValue,
} from "@/components/ui/select";
import { ref, watch } from "vue";
import {
	Dialog,
	DialogContent,
	DialogHeader,
	DialogTitle,
	DialogTrigger,
} from "@/components/ui/dialog/index.js";

defineOptions({ layout: AdminLayout });

const props = defineProps({
	orders: Object,
	filters: Object,
});

const search = ref(props.filters.search || "");
const statusOptions = [
	{ value: "all", label: "All Status" },
	{ value: "pending", label: "Pending" },
	{ value: "preparing", label: "Preparing" },
	{ value: "completed", label: "Completed" },
	{ value: "cancelled", label: "Cancelled" },
];

const status = ref(props.filters.status || "all");

// Simple debounce function
function debounce(fn, delay) {
	let timeoutId;
	return (...args) => {
		clearTimeout(timeoutId);
		timeoutId = setTimeout(() => fn(...args), delay);
	};
}

const updateFilters = debounce(() => {
	router.get(
		route("admin.orders.index"),
		{
			search: search.value || null,
			status: status.value === "all" ? null : status.value,
		},
		{
			preserveState: true,
			preserveScroll: true,
			replace: true,
		}
	);
}, 300);

// Replace existing updateSearch and updateStatus with this single watcher
watch([search, status], () => {
	updateFilters();
});

const updateOrderStatus = (orderId, newStatus) => {
	router.put(route("admin.orders.update", orderId), {
		status: newStatus,
	});
};

const statusColors = {
	pending: "bg-yellow-100 text-yellow-700",
	preparing: "bg-blue-100 text-blue-700",
	completed: "bg-emerald-100 text-emerald-700",
	cancelled: "bg-red-100 text-red-700",
};

const canUpdateStatus = (status) => {
	return !["completed", "cancelled"].includes(status);
};
</script>

<template>
	<Head title="| Orders" />

	<div class="space-y-6">
		<div class="sm:flex-row sm:items-center sm:justify-between flex flex-col gap-4">
			<h2 class="text-2xl font-semibold">Orders</h2>

			<!-- Filters -->
			<div class="sm:flex-row flex flex-col gap-4">
				<Input
					type="search"
					placeholder="Search order # or customer..."
					class="w-full sm:w-[300px]"
					v-model="search"
				/>

				<Select v-model="status">
					<SelectTrigger class="w-full sm:w-[180px]">
						<SelectValue placeholder="Filter by status" />
					</SelectTrigger>
					<SelectContent>
						<SelectItem
							v-for="option in statusOptions"
							:key="option.value"
							:value="option.value"
						>
							{{ option.label }}
						</SelectItem>
					</SelectContent>
				</Select>
			</div>
		</div>

		<!-- Orders List -->
		<Card v-for="order in orders.data" :key="order.id" class="overflow-hidden">
			<CardHeader
				class="sm:flex-row sm:items-center sm:justify-between bg-muted/50 flex flex-col gap-4"
			>
				<div>
					<CardTitle class="text-base">Order #{{ order.orderNumber }}</CardTitle>
					<p class="text-muted-foreground text-sm">{{ order.createdAt }}</p>
				</div>
				<div class="flex flex-wrap items-center gap-2">
					<span
						:class="[
							'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium',
							statusColors[order.status],
						]"
					>
						{{ order.status }}
					</span>
					<Select
						v-model="order.status"
						@update:modelValue="(newStatus) => updateOrderStatus(order.id, newStatus)"
						:disabled="!canUpdateStatus(order.status)"
					>
						<SelectTrigger class="h-8">
							<SelectValue
								:placeholder="
									canUpdateStatus(order.status) ? 'Update status' : 'Status locked'
								"
							/>
						</SelectTrigger>
						<SelectContent>
							<SelectItem value="pending">Mark as Pending</SelectItem>
							<SelectItem value="preparing">Mark as Preparing</SelectItem>
							<SelectItem value="completed">Mark as Completed</SelectItem>
							<SelectItem value="cancelled">Mark as Cancelled</SelectItem>
						</SelectContent>
					</Select>
				</div>
			</CardHeader>
			<CardContent class="p-6">
				<div class="space-y-4">
					<div class="sm:grid-cols-2 grid gap-4">
						<div>
							<h4 class="font-medium">Customer</h4>
							<p class="text-muted-foreground text-sm">{{ order.customer }}</p>
						</div>
						<div>
							<h4 class="font-medium">Total Amount</h4>
							<p class="text-muted-foreground text-sm">
								₱{{ order.amount.toLocaleString() }}
							</p>
						</div>
						<div>
							<h4 class="font-medium">Pickup Time</h4>
							<p class="text-muted-foreground text-sm">{{ order.pickupTime }}</p>
						</div>
						<div v-if="order.note">
							<h4 class="font-medium">Note</h4>
							<p class="text-muted-foreground text-sm">{{ order.note }}</p>
						</div>
					</div>

					<Dialog>
						<DialogTrigger asChild>
							<Button variant="outline" size="sm">View Items</Button>
						</DialogTrigger>
						<DialogContent>
							<DialogHeader>
								<DialogTitle>Order Items</DialogTitle>
							</DialogHeader>
							<div class="space-y-4">
								<div
									v-for="item in order.items"
									:key="item.name"
									class="flex justify-between gap-4"
								>
									<div>
										<p class="font-medium">{{ item.name }}</p>
										<p class="text-muted-foreground text-sm">
											Quantity: {{ item.quantity }}
										</p>
									</div>
									<p class="text-sm">₱{{ item.total.toLocaleString() }}</p>
								</div>
							</div>
						</DialogContent>
					</Dialog>
				</div>
			</CardContent>
		</Card>

		<!-- Pagination links if needed -->
		<div v-if="orders.links.length > 3" class="flex justify-center mt-6">
			<!-- Add pagination component -->
		</div>
	</div>
</template>
