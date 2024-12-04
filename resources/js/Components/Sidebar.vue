<script setup>
import NavLink from "../Components/NavLink.vue";
import { Button } from "@/components/ui/button";
import Logo from "../Components/Logo.vue";
import {
	Bell,
	HeartHandshake,
	Gift,
	GlassWater,
	CakeSlice,
	Dessert,
	Sparkles,
	Shapes,
	HandCoins,
} from "lucide-vue-next";
import { ref, onMounted, onUnmounted } from "vue";

const theme = ref(document.body.getAttribute("data-theme"));

const updateThemeStatus = () => {
	theme.value = document.body.getAttribute("data-theme");
};

onMounted(() => {
	// create a MutationObserver to track attribute changes
	const observer = new MutationObserver(updateThemeStatus);

	// start observing the body for attribute changes
	observer.observe(document.body, {
		attributes: true,
		attributeFilter: ["data-theme"],
	});

	// clean up observer when the component is unmounted
	onUnmounted(() => {
		observer.disconnect();
	});
});
</script>

<template>
	<!-- sidebar (visible on md and up) -->
	<div class="md:block sticky top-0 hidden h-screen border-r">
		<div class="flex flex-col h-full gap-2">
			<!-- sidebar header -->
			<div
				class="flex h-14 items-center border-b px-4 lg:h-[60px] lg:px-6 sticky top-0 z-10"
			>
				<Link :href="route('home')" class="flex items-center gap-2 font-semibold">
					<span>MELIORAE</span>
				</Link>
				<!-- <Button variant="outline" size="icon" class="w-8 h-8 ml-auto">
                    <Bell class="w-4 h-4" />
                    <span class="sr-only">Toggle notifications</span>
                </Button> -->
			</div>

			<!-- sidebar navigation -->
			<div class="flex-1 overflow-y-auto">
				<nav class="grid items-start gap-1 px-4 text-sm font-medium">
					<!-- nav links -->
					<NavLink
						routeName="home"
						componentName="Home"
						:isActive="$page.component === 'Home'"
					>
						<Shapes class="w-5 h-5" />
						All Products
					</NavLink>

					<NavLink
						:href="route('category.show', 'Classic')"
						componentName="Category/Show"
						:isActive="
							$page.component === 'Category/Show' &&
							$page.props.currentCategory === 'Classic'
						"
					>
						<HeartHandshake class="w-5 h-5" />
						Classic
					</NavLink>

					<NavLink
						:href="route('category.show', 'Gourmet')"
						componentName="Category/Show"
						:isActive="
							$page.component === 'Category/Show' &&
							$page.props.currentCategory === 'Gourmet'
						"
					>
						<Sparkles class="w-5 h-5" />
						Gourmet
					</NavLink>

					<NavLink
						:href="route('category.show', 'Bars and Squares')"
						componentName="Category/Show"
						:isActive="
							$page.component === 'Category/Show' &&
							$page.props.currentCategory === 'Bars and Squares'
						"
					>
						<CakeSlice class="w-5 h-5" />
						Bars and Squares
					</NavLink>

					<NavLink
						:href="route('category.show', 'Sundaes')"
						componentName="Category/Show"
						:isActive="
							$page.component === 'Category/Show' &&
							$page.props.currentCategory === 'Sundaes'
						"
					>
						<Dessert class="w-5 h-5" />
						Sundaes
					</NavLink>

					<NavLink
						:href="route('category.show', 'Milkshakes')"
						componentName="Category/Show"
						:isActive="
							$page.component === 'Category/Show' &&
							$page.props.currentCategory === 'Milkshakes'
						"
					>
						<GlassWater class="w-5 h-5" />
						Milkshakes
					</NavLink>

					<NavLink
						:href="route('category.show', 'Gift Box')"
						componentName="Category/Show"
						:isActive="
							$page.component === 'Category/Show' &&
							$page.props.currentCategory === 'Gift Box'
						"
					>
						<Gift class="w-5 h-5" />
						Custom Gift Box
					</NavLink>
				</nav>
			</div>
		</div>
	</div>
</template>
