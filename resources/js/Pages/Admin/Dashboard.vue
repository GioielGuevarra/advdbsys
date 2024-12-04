<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Separator } from "@/components/ui/separator";
import { Line } from "vue-chartjs";
import {
	Users,
	DollarSign,
	ShoppingBag,
	Package,
	ArrowUpRight,
	ArrowDownRight,
} from "lucide-vue-next";
import DataTable from "@/Components/Admin/DataTable.vue";
import { Head } from "@inertiajs/vue3";
import {
	Chart as ChartJS,
	CategoryScale,
	LinearScale,
	PointElement,
	LineElement,
	Title,
	Tooltip,
	Legend,
} from "chart.js";

// Register Chart.js components
ChartJS.register(
	CategoryScale,
	LinearScale,
	PointElement,
	LineElement,
	Title,
	Tooltip,
	Legend
);

defineOptions({
	layout: AdminLayout,
});

const props = defineProps({
	stats: {
		type: Object,
		required: true,
	},
	recentOrders: {
		type: Array,
		required: true,
	},
	recentCustomers: {
		type: Array,
		required: true,
	},
	monthlyRevenue: {
		type: Array,
		required: true,
	},
});

// Get current theme
const isDark = document.body.getAttribute("data-theme") === "dark";

const chartData = {
	labels: props.monthlyRevenue.map((item) => item.month),
	datasets: [
		{
			label: "Revenue",
			data: props.monthlyRevenue.map((item) => item.revenue),
			borderColor: "rgb(255, 197, 0)", // Solid gold color that works in both themes
			backgroundColor: "rgba(255, 197, 0, 0.1)",
			borderWidth: 3,
			tension: 0.4,
			fill: true,
			pointBackgroundColor: "rgb(255, 197, 0)",
			pointBorderColor: isDark ? "#1e1e2d" : "#ffffff",
			pointBorderWidth: 2,
			pointRadius: 4,
			pointHoverRadius: 6,
		},
	],
};

const chartOptions = {
	responsive: true,
	maintainAspectRatio: false,
	interaction: {
		intersect: false,
		mode: "index",
	},
	plugins: {
		legend: {
			display: false,
		},
		tooltip: {
			backgroundColor: isDark ? "#1e1e2d" : "#ffffff",
			titleColor: isDark ? "#ffffff" : "#1e1e2d",
			bodyColor: isDark ? "#ffffff" : "#1e1e2d",
			borderColor: isDark ? "#2d2d3f" : "#e2e8f0",
			borderWidth: 1,
			padding: 12,
			boxPadding: 4,
			callbacks: {
				label: (context) => `₱${context.parsed.y.toLocaleString()}`,
			},
		},
	},
	scales: {
		x: {
			grid: {
				display: false,
			},
			ticks: {
				color: isDark ? "#9ca3af" : "#6b7280",
			},
		},
		y: {
			grid: {
				color: isDark ? "rgba(75, 85, 99, 0.3)" : "rgba(209, 213, 219, 0.5)",
				drawBorder: false,
			},
			ticks: {
				color: isDark ? "#9ca3af" : "#6b7280",
				callback: (value) => `₱${value.toLocaleString()}`,
			},
			border: {
				display: false,
			},
		},
	},
};

const orderColumns = [
	{ header: "Order #", accessorKey: "order_number" },
	{ header: "Customer", accessorKey: "customer_name" },
	{ header: "Date", accessorKey: "date" },
	{ header: "Amount", accessorKey: "total_amount" },
	{ header: "Status", accessorKey: "status" },
];
</script>

<template>
	<Head title="Admin Dashboard" />
	<div class="space-y-6">
		<!-- Stats Grid -->
		<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
			<Card class="hover:bg-accent transition-colors">
				<CardHeader class="flex flex-row items-center justify-between pb-2 space-y-0">
					<CardTitle class="text-sm font-medium">Total Revenue</CardTitle>
					<DollarSign class="text-muted-foreground w-4 h-4" />
				</CardHeader>
				<CardContent>
					<div class="text-2xl font-bold">₱{{ stats.revenue.toLocaleString() }}</div>
					<div class="text-muted-foreground flex items-center mt-1 text-xs">
						<component
							:is="stats.revenueChange >= 0 ? ArrowUpRight : ArrowDownRight"
							class="w-4 h-4 mr-1"
							:class="stats.revenueChange >= 0 ? 'text-emerald-500' : 'text-red-500'"
						/>
						<span :class="stats.revenueChange >= 0 ? 'text-emerald-500' : 'text-red-500'">
							{{ stats.revenueChange >= 0 ? "+" : "" }}{{ stats.revenueChange }}%
						</span>
						from last month
					</div>
				</CardContent>
			</Card>

			<Card class="hover:bg-accent transition-colors">
				<CardHeader class="flex flex-row items-center justify-between pb-2 space-y-0">
					<CardTitle class="text-sm font-medium">Orders</CardTitle>
					<ShoppingBag class="text-muted-foreground w-4 h-4" />
				</CardHeader>
				<CardContent>
					<div class="text-2xl font-bold">{{ stats.orders }}</div>
					<div class="text-muted-foreground flex items-center mt-1 text-xs">
						<ArrowUpRight class="w-4 h-4 mr-1 text-green-500" />
						<span class="text-green-500">+12.2%</span> from last month
					</div>
				</CardContent>
			</Card>

			<Card class="hover:bg-accent transition-colors">
				<CardHeader class="flex flex-row items-center justify-between pb-2 space-y-0">
					<CardTitle class="text-sm font-medium">Products</CardTitle>
					<Package class="w-4 h-4 text-muted-foreground" />
				</CardHeader>
				<CardContent>
					<div class="text-2xl font-bold">{{ stats.products }}</div>
					<div class="flex items-center text-xs text-muted-foreground mt-1">
						<ArrowDownRight class="w-4 h-4 mr-1 text-red-500" />
						<span class="text-red-500">-2.5%</span> from last month
					</div>
				</CardContent>
			</Card>

			<Card class="hover:bg-accent transition-colors">
				<CardHeader class="flex flex-row items-center justify-between pb-2 space-y-0">
					<CardTitle class="text-sm font-medium">Active Users</CardTitle>
					<Users class="w-4 h-4 text-muted-foreground" />
				</CardHeader>
				<CardContent>
					<div class="text-2xl font-bold">{{ stats.users }}</div>
					<div class="flex items-center text-xs text-muted-foreground mt-1">
						<ArrowUpRight class="w-4 h-4 mr-1 text-green-500" />
						<span class="text-green-500">+4.1%</span> from last month
					</div>
				</CardContent>
			</Card>
		</div>

		<!-- Revenue Chart -->
		<Card class="overflow-hidden">
			<CardHeader
				class="flex flex-col space-y-2 sm:flex-row sm:items-center sm:justify-between"
			>
				<CardTitle>Revenue Overview</CardTitle>
				<div class="text-sm text-muted-foreground">Monthly Revenue</div>
			</CardHeader>
			<CardContent class="p-0 sm:p-6">
				<div class="h-[300px] w-full min-w-0">
					<Line :data="chartData" :options="chartOptions" />
				</div>
			</CardContent>
		</Card>

		<!-- Recent Orders Table -->
		<Card class="overflow-hidden">
			<CardHeader
				class="flex flex-col space-y-2 sm:flex-row sm:items-center sm:justify-between"
			>
				<CardTitle>Recent Orders</CardTitle>
				<div class="text-sm text-muted-foreground">Latest transactions</div>
			</CardHeader>
			<CardContent class="p-0">
				<div class="overflow-x-auto">
					<DataTable :data="recentOrders" :columns="orderColumns" class="min-w-full" />
				</div>
			</CardContent>
		</Card>
	</div>
</template>
