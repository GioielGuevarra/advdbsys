<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Head } from "@inertiajs/vue3";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Line, Bar, Doughnut } from "vue-chartjs";
import {
	Chart as ChartJS,
	CategoryScale,
	LinearScale,
	PointElement,
	LineElement,
	BarElement,
	ArcElement,
	Title,
	Tooltip,
	Legend,
} from "chart.js";

ChartJS.register(
	CategoryScale,
	LinearScale,
	PointElement,
	LineElement,
	BarElement,
	ArcElement,
	Title,
	Tooltip,
	Legend
);

defineOptions({ layout: AdminLayout });

const props = defineProps({
	monthlyRevenue: Array,
	topProducts: Array,
	categoryStats: Array,
	topCustomers: Array,
});

// Get current theme
const isDark = document.body.getAttribute("data-theme") === "dark";

// Monthly Revenue Chart
const revenueData = {
	labels: props.monthlyRevenue.map((item) => item.month),
	datasets: [
		{
			label: "Revenue",
			data: props.monthlyRevenue.map((item) => item.revenue),
			borderColor: "rgb(255, 197, 0)",
			backgroundColor: "rgba(255, 197, 0, 0.1)",
			fill: true,
			tension: 0.4,
		},
	],
};

// Top Products Chart
const productsData = {
	labels: props.topProducts.map((item) => item.name),
	datasets: [
		{
			label: "Units Sold",
			data: props.topProducts.map((item) => item.total_sold),
			backgroundColor: [
				"rgba(255, 99, 132, 0.8)",
				"rgba(54, 162, 235, 0.8)",
				"rgba(255, 206, 86, 0.8)",
				"rgba(75, 192, 192, 0.8)",
				"rgba(153, 102, 255, 0.8)",
			],
		},
	],
};

// Category Stats Chart
const categoryData = {
	labels: props.categoryStats.map((item) => item.name),
	datasets: [
		{
			label: "Revenue by Category",
			data: props.categoryStats.map((item) => item.revenue),
			backgroundColor: [
				"rgba(255, 99, 132, 0.8)",
				"rgba(54, 162, 235, 0.8)",
				"rgba(255, 206, 86, 0.8)",
				"rgba(75, 192, 192, 0.8)",
				"rgba(153, 102, 255, 0.8)",
			],
		},
	],
};

// Top Customers Chart
const customersData = {
	labels: props.topCustomers.map((item) => item.name),
	datasets: [
		{
			label: "Total Spent",
			data: props.topCustomers.map((item) => item.total_spent),
			backgroundColor: [
				"rgba(75, 192, 192, 0.8)",
				"rgba(153, 102, 255, 0.8)",
				"rgba(255, 159, 64, 0.8)",
				"rgba(54, 162, 235, 0.8)",
				"rgba(255, 99, 132, 0.8)",
			],
		},
		{
			label: "Number of Orders",
			data: props.topCustomers.map((item) => item.total_orders),
			backgroundColor: [
				"rgba(75, 192, 192, 0.5)",
				"rgba(153, 102, 255, 0.5)",
				"rgba(255, 159, 64, 0.5)",
				"rgba(54, 162, 235, 0.5)",
				"rgba(255, 99, 132, 0.5)",
			],
		},
	],
};

const chartOptions = {
	responsive: true,
	maintainAspectRatio: false,
	plugins: {
		legend: {
			position: "top",
			labels: {
				color: isDark ? "#ffffff" : "#000000",
			},
		},
	},
	scales: {
		y: {
			ticks: {
				color: isDark ? "#9ca3af" : "#6b7280",
				callback: (value) => `₱${value.toLocaleString()}`,
			},
			grid: {
				color: isDark ? "rgba(75, 85, 99, 0.3)" : "rgba(209, 213, 219, 0.5)",
			},
		},
		x: {
			ticks: {
				color: isDark ? "#9ca3af" : "#6b7280",
			},
			grid: {
				display: false,
			},
		},
	},
};
</script>

<template>
	<Head title="| Analytics" />

	<div class="space-y-6">
		<!-- Monthly Revenue Chart -->
		<Card>
			<CardHeader>
				<CardTitle>Monthly Revenue</CardTitle>
			</CardHeader>
			<CardContent>
				<div class="h-[400px]">
					<Line :data="revenueData" :options="chartOptions" />
				</div>
			</CardContent>
		</Card>

		<div class="grid gap-6 md:grid-cols-2">
			<!-- Top Products Chart -->
			<Card>
				<CardHeader>
					<CardTitle>Top Selling Products</CardTitle>
				</CardHeader>
				<CardContent>
					<div class="h-[300px]">
						<Bar :data="productsData" :options="chartOptions" />
					</div>
				</CardContent>
			</Card>

			<!-- Category Stats Chart -->
			<Card>
				<CardHeader>
					<CardTitle>Sales by Category</CardTitle>
				</CardHeader>
				<CardContent>
					<div class="h-[300px]">
						<Doughnut :data="categoryData" :options="chartOptions" />
					</div>
				</CardContent>
			</Card>
		</div>

		<!-- Top Customers Chart -->
		<Card>
			<CardHeader>
				<CardTitle>Top Customers</CardTitle>
			</CardHeader>
			<CardContent>
				<div class="h-[400px]">
					<Bar
						:data="customersData"
						:options="{
							...chartOptions,
							scales: {
								...chartOptions.scales,
								y: {
									...chartOptions.scales.y,
									ticks: {
										callback: (value) => `₱${value.toLocaleString()}`,
									},
								},
							},
						}"
					/>
				</div>
			</CardContent>
		</Card>
	</div>
</template>
