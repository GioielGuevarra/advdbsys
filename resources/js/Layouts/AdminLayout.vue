<script setup>
import { ref } from "vue";
import {
	ChartBar,
	Users,
	ShoppingBag,
	Package,
	FileText,
	LayoutDashboard,
	LogOut,
} from "lucide-vue-next";
import { Link, usePage, router } from "@inertiajs/vue3";
import { Button } from "@/components/ui/button";

// Get the current page from Inertia
const page = usePage();

// Updated isActive function using usePage
const isActive = (componentName) => {
	return page.component === componentName;
};

const navigation = [
	{
		name: "Dashboard",
		href: route("admin.dashboard"),
		icon: LayoutDashboard,
		component: "Admin/Dashboard",
	},
	{
		name: "Orders",
		href: route("admin.orders.index"),
		icon: ShoppingBag,
		component: "Admin/Orders/Index",
	},
	{
		name: "Products",
		href: route("admin.products.index"),
		icon: Package,
		component: "Admin/Products/Index",
	},
	{
		name: "Categories",
		href: route("admin.categories.index"),
		icon: FileText,
		component: "Admin/Categories/Index",
	},
	{
		name: "Customers",
		href: route("admin.customers.index"),
		icon: Users,
		component: "Admin/Customers/Index",
	},
	{
		name: "Analytics",
		href: route("admin.analytics"),
		icon: ChartBar,
		component: "Admin/Analytics/Index",
	},
];

const signOut = () => {
	router.post(route("logout"));
};
</script>

<template>
	<div class="min-h-screen">
		<!-- Fixed Sidebar -->
		<div
			class="hidden lg:block fixed top-0 bottom-0 left-0 w-[280px] border-r bg-card z-20"
		>
			<div class="flex flex-col h-full">
				<!-- Logo/Brand -->
				<div class="flex h-[60px] items-center border-b px-6">
					<Link :href="route('admin.dashboard')" class="flex items-center gap-2">
						<span class="text-lg font-semibold">Meliorae</span>
					</Link>
				</div>

				<!-- Navigation -->
				<div class="flex-1 px-4 py-2">
					<nav class="grid items-start gap-1">
						<Link
							v-for="item in navigation"
							:key="item.href"
							:href="item.href"
							:class="[
								'flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium transition-colors relative',
								isActive(item.component)
									? 'bg-primary text-primary-foreground'
									: 'text-muted-foreground hover:bg-muted hover:text-foreground',
							]"
						>
							<component :is="item.icon" class="w-4 h-4" />
							{{ item.name }}
							<div
								v-if="isActive(item.component)"
								class="absolute left-0 top-1/2 h-6 w-1 -translate-y-1/2 rounded-r-md bg-primary"
							/>
						</Link>
					</nav>
				</div>

				<!-- Sign Out Button -->
				<div class="p-6 border-t">
					<Button
						variant="secondary"
						size="lg"
						class="w-full gap-0 hover:bg-muted flex justify-center items-center"
						@click="signOut"
					>
						<LogOut class="mr-2 h-4 w-4" />
						<span>Sign Out</span>
					</Button>
				</div>
			</div>
		</div>

		<!-- Main Content -->
		<div class="lg:pl-[280px]">
			<header class="sticky top-0 z-10 flex h-[60px] items-center border-b bg-card px-6">
				<h1 class="text-foreground text-lg font-semibold">
					{{ navigation.find((item) => isActive(item.component))?.name || "Admin" }}
				</h1>
			</header>
			<main class="md:p-6 flex-1 p-4">
				<slot />
			</main>
		</div>
	</div>
</template>
