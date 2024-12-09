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
} from "@/Components/ui/dialog";
import { Badge } from "@/components/ui/badge";
import {
	AlertDialog,
	AlertDialogAction,
	AlertDialogCancel,
	AlertDialogContent,
	AlertDialogDescription,
	AlertDialogFooter,
	AlertDialogHeader,
	AlertDialogTitle,
} from "@/components/ui/alert-dialog";

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
	{ value: "ready", label: "Ready for Pickup" },
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

// Add new refs for confirmation dialog
const showConfirmDialog = ref(false);
const pendingStatusUpdate = ref(null);

// Add a ref to store the original status
const originalStatus = ref({});

// Add this new ref to track displayed statuses
const displayedStatuses = ref({});

// Modify updateOrderStatus to handle confirmation
const updateOrderStatus = (orderId, newStatus, currentOrder) => {
	// If status is completed or cancelled, show confirmation
	if (newStatus === "completed" || newStatus === "cancelled") {
		// Store the original status
		originalStatus.value[orderId] = currentOrder.status;
		displayedStatuses.value[orderId] = currentOrder.status; // Keep original status displayed
		// Revert the select value
		currentOrder.status = originalStatus.value[orderId];
		// Show confirmation
		pendingStatusUpdate.value = { orderId, newStatus };
		showConfirmDialog.value = true;
		return;
	}

	// Otherwise proceed with update
	processStatusUpdate(orderId, newStatus);
};

// Add new method to process the actual update
const processStatusUpdate = (orderId, newStatus) => {
	router.put(route("admin.orders.update", orderId), {
		status: newStatus,
	});
	displayedStatuses.value[orderId] = newStatus; // Update displayed status
	// Clear the stored original status
	delete originalStatus.value[orderId];
	showConfirmDialog.value = false;
	pendingStatusUpdate.value = null;
};

// Add this computed/method to get the correct status to display
const getDisplayStatus = (order) => {
	return displayedStatuses.value[order.id] || order.status;
};

const getStatusVariant = (status) => {
	// Convert status to lowercase for consistent comparison
	status = status.toLowerCase();

	// Return the exact variant name that matches our badge variants
	switch (status) {
		case "pending":
			return "pending";
		case "preparing":
			return "preparing";
		case "ready":
			return "ready";
		case "completed":
			return "completed";
		case "cancelled":
			return "cancelled";
		default:
			return "secondary";
	}
};

const getAvailableStatuses = (currentStatus) => {
	// Define valid status transitions
	const transitions = {
		pending: ["preparing", "cancelled"],
		preparing: ["ready", "cancelled"],
		ready: ["completed", "cancelled"],
		completed: [], // No further transitions allowed
		cancelled: [], // No further transitions allowed
	};

	// Only return status options that are valid transitions
	return statusOptions.filter((option) =>
		transitions[currentStatus]?.includes(option.value)
	);
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
				class="sm:flex-row sm:items-start sm:justify-between bg-muted/50 flex flex-col gap-4"
			>
				<div class="space-y-2">
					<Badge :variant="getStatusVariant(order.status)" class="capitalize">
						{{ order.status }}
					</Badge>
					<div>
						<CardTitle class="text-base">Order #{{ order.orderNumber }}</CardTitle>
						<p class="text-muted-foreground text-sm">{{ order.createdAt }}</p>
					</div>
				</div>
				<div>
					<Select
						:model-value="getDisplayStatus(order)"
						@update:model-value="
							(newStatus) => updateOrderStatus(order.id, newStatus, order)
						"
						:disabled="!canUpdateStatus(order.status)"
					>
						<SelectTrigger class="h-8">
							<SelectValue>
								{{
									statusOptions.find((s) => s.value === getDisplayStatus(order))?.label ||
									order.status
								}}
							</SelectValue>
						</SelectTrigger>
						<SelectContent>
							<SelectItem
								v-for="option in getAvailableStatuses(order.status)"
								:key="option.value"
								:value="option.value"
							>
								{{ option.label }}
							</SelectItem>
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
						<DialogContent
							class="sm:max-w-[425px] grid-rows-[auto_minmax(0,1fr)_auto] p-0 max-h-[90dvh]"
						>
							<DialogHeader class="p-6 pb-0">
								<DialogTitle>Order Items</DialogTitle>
							</DialogHeader>
							<div class="px-6 py-4 overflow-y-auto">
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

	<!-- Add confirmation dialog -->
	<AlertDialog :open="showConfirmDialog" @update:open="showConfirmDialog = false">
		<AlertDialogContent>
			<AlertDialogHeader>
				<AlertDialogTitle>Confirm Status Update</AlertDialogTitle>
				<AlertDialogDescription>
					Are you sure you want to mark this order as
					{{ pendingStatusUpdate?.newStatus }}? This action cannot be undone.
				</AlertDialogDescription>
			</AlertDialogHeader>
			<AlertDialogFooter>
				<AlertDialogCancel
					@click="
						showConfirmDialog = false;
						if (pendingStatusUpdate) {
							displayedStatuses.value[pendingStatusUpdate.orderId] =
								originalStatus.value[pendingStatusUpdate.orderId];
						}
					"
				>
					Cancel
				</AlertDialogCancel>
				<AlertDialogAction
					@click="
						processStatusUpdate(
							pendingStatusUpdate.orderId,
							pendingStatusUpdate.newStatus
						)
					"
				>
					Confirm
				</AlertDialogAction>
			</AlertDialogFooter>
		</AlertDialogContent>
	</AlertDialog>
</template>
