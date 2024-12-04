<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Head, router } from "@inertiajs/vue3";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Button } from "@/components/ui/button";
import { ref, watch } from "vue";
import {
	Dialog,
	DialogContent,
	DialogHeader,
	DialogTitle,
	DialogTrigger,
	DialogFooter,
} from "@/components/ui/dialog";
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

defineOptions({ layout: AdminLayout });

const props = defineProps({
	customers: Object,
	filters: Object,
});

const search = ref(props.filters.search || "");
const selectedCustomer = ref(null);
const showOrdersDialog = ref(false);
const restrictDays = ref(7);

function debounce(fn, delay) {
	let timeoutId;
	return (...args) => {
		clearTimeout(timeoutId);
		timeoutId = setTimeout(() => fn(...args), delay);
	};
}

const updateFilters = debounce(() => {
	router.get(
		route("admin.customers.index"),
		{ search: search.value || null },
		{ preserveState: true, preserveScroll: true }
	);
}, 300);

watch(search, () => {
	updateFilters();
});

const toggleBan = (customer) => {
	router.put(route("admin.customers.update", customer.id), {
		action: customer.isBanned ? "unban" : "ban",
	});
};

const restrictCustomer = (customer) => {
	router.put(route("admin.customers.update", customer.id), {
		action: "restrict",
		duration: restrictDays.value,
	});
};
</script>

<template>
	<Head title="| Customers" />

	<div class="space-y-6">
		<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
			<div>
				<h2 class="text-2xl font-semibold">Customers</h2>
				<p class="text-muted-foreground text-sm">Manage your customers</p>
			</div>
			<Input
				type="search"
				placeholder="Search customers..."
				class="w-full sm:w-[300px]"
				v-model="search"
			/>
		</div>

		<div class="grid gap-4">
			<Card v-for="customer in customers.data" :key="customer.id">
				<CardContent class="p-6">
					<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
						<div class="space-y-1">
							<h3 class="font-medium">{{ customer.name }}</h3>
							<p class="text-muted-foreground text-sm">{{ customer.email }}</p>
						</div>
						<div class="flex items-center gap-2">
							<Button
								variant="outline"
								size="sm"
								@click="
									selectedCustomer = customer;
									showOrdersDialog = true;
								"
							>
								View Orders
							</Button>
							<AlertDialog>
								<AlertDialogTrigger asChild>
									<Button
										:variant="customer.isBanned ? 'outline' : 'destructive'"
										size="sm"
									>
										{{ customer.isBanned ? "Unban Customer" : "Ban Customer" }}
									</Button>
								</AlertDialogTrigger>
								<AlertDialogContent>
									<AlertDialogHeader>
										<AlertDialogTitle>
											{{ customer.isBanned ? "Unban" : "Ban" }} {{ customer.name }}?
										</AlertDialogTitle>
										<AlertDialogDescription>
											{{
												customer.isBanned
													? "This will allow the customer to place orders again."
													: "This will prevent the customer from placing new orders."
											}}
										</AlertDialogDescription>
									</AlertDialogHeader>
									<AlertDialogFooter>
										<AlertDialogCancel>Cancel</AlertDialogCancel>
										<AlertDialogAction @click="toggleBan(customer)">
											{{ customer.isBanned ? "Unban Customer" : "Ban Customer" }}
										</AlertDialogAction>
									</AlertDialogFooter>
								</AlertDialogContent>
							</AlertDialog>
						</div>
					</div>
					<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-4">
						<div>
							<p class="text-sm text-muted-foreground">Joined</p>
							<p class="font-medium">{{ customer.joinedDate }}</p>
						</div>
						<div>
							<p class="text-sm text-muted-foreground">Status</p>
							<p
								class="font-medium"
								:class="{
									'text-destructive': customer.isBanned,
									'text-green-600 dark:text-green-400': !customer.isBanned,
								}"
							>
								{{ customer.status }}
							</p>
						</div>
						<div>
							<p class="text-sm text-muted-foreground">Total Orders</p>
							<p class="font-medium">{{ customer.totalOrders }}</p>
						</div>
						<div>
							<p class="text-sm text-muted-foreground">Total Spent</p>
							<p class="font-medium">₱{{ customer.totalSpent.toLocaleString() }}</p>
						</div>
					</div>
				</CardContent>
			</Card>
		</div>

		<!-- Pagination -->
		<div v-if="customers.links.length > 3" class="flex justify-center mt-6">
			<!-- Add pagination component -->
		</div>
	</div>

	<!-- Orders Dialog -->
	<Dialog v-model:open="showOrdersDialog">
		<DialogContent class="max-w-3xl">
			<DialogHeader>
				<DialogTitle>Orders - {{ selectedCustomer?.name }}</DialogTitle>
			</DialogHeader>
			<div class="space-y-4 max-h-[60vh] overflow-y-auto">
				<Card v-for="order in selectedCustomer?.orders" :key="order.id">
					<CardContent class="p-4">
						<div class="flex justify-between items-start mb-4">
							<div>
								<h4 class="font-medium">Order #{{ order.orderNumber }}</h4>
								<p class="text-sm text-muted-foreground">{{ order.date }}</p>
							</div>
							<div class="text-right">
								<p class="font-medium">₱{{ order.amount.toLocaleString() }}</p>
								<p
									class="text-sm"
									:class="{
										'text-destructive': order.status === 'Cancelled',
										'text-primary': order.status === 'Pending',
										'text-green-600': order.status === 'Completed',
									}"
								>
									{{ order.status }}
								</p>
							</div>
						</div>
						<div class="space-y-2">
							<div
								v-for="item in order.items"
								:key="item.name"
								class="flex justify-between text-sm"
							>
								<span>{{ item.name }} × {{ item.quantity }}</span>
								<span>₱{{ item.total.toLocaleString() }}</span>
							</div>
						</div>
					</CardContent>
				</Card>
			</div>
		</DialogContent>
	</Dialog>
</template>
