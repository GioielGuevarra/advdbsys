<script setup>
import NavLink from "../Components/NavLink.vue";
import { Button } from "@/components/ui/button";
import Logo from "../Components/Logo.vue";
import { Bell, HeartHandshake, Gift, GlassWater, CakeSlice, Dessert, Sparkles, Shapes, HandCoins } from "lucide-vue-next";
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
	<div class="sticky top-0 hidden h-screen border-r md:block">
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
					<NavLink routeName="home" componentName="Home">
						<Shapes class="w-5 h-5" />
						All Products
					</NavLink>

					<NavLink routeName="explore" componentName="Explore">
						<HeartHandshake class="w-5 h-5" />
						Classic
					</NavLink>

					<NavLink routeName="my-rentals" componentName="MyRentals">
						<Sparkles class="w-5 h-5" />
						Gourmet
					</NavLink>

					<NavLink routeName="my-rentals" componentName="MyRentals">
						<CakeSlice class="w-5 h-5" />
						Bars and Squares
					</NavLink>

					<NavLink routeName="my-rentals" componentName="MyRentals">
						<Dessert class="w-5 h-5" />
						Sundaes
					</NavLink>

					<NavLink routeName="my-rentals" componentName="MyRentals">
						<GlassWater class="w-5 h-5" />
						Milkshakes
					</NavLink>

					<NavLink routeName="my-rentals" componentName="MyRentals">
						<Gift class="w-5 h-5" />
						Custom Gift Box
					</NavLink>
				</nav>
			</div>

			<!-- sidebar footer -->
			<Link :href="route('listing.create')" class="sticky bottom-0 p-4 mt-auto border-t">
				<Button size="lg" class="w-full font-light" variant="outline">
					<HandCoins />
					Checkout
				</Button>
			</Link>
		</div>
	</div>
</template>