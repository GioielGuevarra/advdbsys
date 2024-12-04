<script setup>
import NavLink from "../Components/NavLink.vue";
import { Button } from "@/components/ui/button";
import { Sheet, SheetContent, SheetTrigger } from "@/components/ui/sheet";
import {
	Menu,
	HeartHandshake,
	Gift,
	GlassWater,
	CakeSlice,
	Dessert,
	Sparkles,
	Shapes,
	HandCoins,
} from "lucide-vue-next";
import Logo from "../Components/Logo.vue";
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
	<Sheet>
		<SheetTrigger as-child>
			<Button variant="outline" size="icon" class="shrink-0 md:hidden">
				<Menu class="w-5 h-5" />
				<span class="sr-only">Toggle navigation menu</span>
			</Button>
		</SheetTrigger>

		<SheetContent side="left" class="flex flex-col h-full max-h-screen gap-2 p-0">
			<div
				class="flex h-14 items-center border-b px-4 lg:h-[60px] lg:px-6 sticky top-0 z-10"
			>
				<Link :href="route('home')" class="flex items-center gap-2 font-semibold">
					<span>MELIORAE</span>
				</Link>
			</div>

			<nav class="flex-1 overflow-y-auto">
				<div class="grid items-start gap-1 px-4 text-sm font-medium">
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
				</div>
			</nav>
		</SheetContent>
	</Sheet>
</template>
