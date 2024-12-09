<script setup>
import { ref } from "vue";
import { Button } from "@/components/ui/button";
import { SunMoon, Search, ShoppingCart } from "lucide-vue-next";
import Sidebar from "../Components/Sidebar.vue";
import MobileSidebar from "../Components/MobileSidebar.vue";
import UserDropdownMenu from "../Components/UserDropdownMenu.vue";
import { switchTheme } from "../theme";
import { useForm, router } from "@inertiajs/vue3";
import InputField from "@/Components/InputField.vue";
import CartDrawer from "../Components/CartDrawer.vue";

// use route.params() to 'stack' search query parameters coming from different components and pass them as one parameter

const params = route().params;
// searchTerm is automatically passed from controller->view->layout

const props = defineProps({ searchTerm: String, categories: Array });

const form = useForm({
	search: props.searchTerm || "",
});

// Replace the search function with direct router navigation
const search = (e) => {
	e.preventDefault();
	router.get(
		route("home"),
		{
			search: form.search,
		},
		{
			preserveState: true,
			preserveScroll: true,
			replace: true,
		}
	);
};

// Add cart drawer ref
const cartDrawer = ref(null);

// Update cart when route changes
router.on("finish", () => {
	cartDrawer.value?.fetchCart();
});
</script>

<template>
	<!-- layout wrapper -->
	<div class="grid min-h-screen w-full md:grid-cols-[220px_1fr] lg:grid-cols-[240px_1fr]">
		<!-- main sidebar -->
		<Sidebar :categories="categories" />

		<!-- main content (1fr)-->
		<div class="flex flex-col">
			<!-- header -->
			<header class="bg-background sticky top-0 z-10 border-b">
				<div
					class="mx-auto flex h-14 max-w-screen-2xl items-center gap-4 px-6 lg:h-[60px]"
				>
					<!-- Mobile Sidebar -->
					<MobileSidebar :categories="categories" />

					<!-- Search Bar -->
					<div class="flex-1">
						<form @submit.prevent="search">
							<div class="relative">
								<Search
									class="absolute left-2.5 top-3 h-4 w-4 text-muted-foreground z-10"
								/>
								<InputField
									type="search"
									placeholder="Search products..."
									bg="bg-muted"
									addedClass="m-0 py-2 w-full pl-8 appearance-none bg-muted md:w-2/3 lg:w-1/3 h-10"
									v-model="form.search"
								/>
							</div>
						</form>
					</div>

					<!-- Header Actions -->
					<div class="flex items-center gap-1">
						<!-- Theme Toggle -->
						<Button @click="switchTheme()" variant="ghost" size="icon">
							<SunMoon class="h-9 w-9" />
							<span class="sr-only">Toggle dark mode</span>
						</Button>

						<!-- Cart -->
						<CartDrawer ref="cartDrawer" />

						<!-- User Dropdown Menu -->
						<UserDropdownMenu />
					</div>
				</div>
			</header>

			<!-- main content -->
			<main class="lg:max-w-screen-2xl lg:p-8 flex-1 w-full p-6 mx-auto">
				<!-- slot content -->
				<slot />
			</main>
		</div>
	</div>
</template>
