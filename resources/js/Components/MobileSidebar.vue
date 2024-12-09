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
	Coffee,
	Cookie,
	IceCream,
	Candy,
	Pizza,
	Cake,
} from "lucide-vue-next";
import Logo from "../Components/Logo.vue";
import { ref, onMounted, onUnmounted, computed } from "vue";
import { Link } from "@inertiajs/vue3"; // Add this import

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

const props = defineProps({
	categories: {
		type: Array,
		default: () => [], // Add default value
	},
});

const availableIcons = [
	Coffee,
	Cookie,
	IceCream,
	Candy,
	Pizza,
	Cake,
	HeartHandshake,
	Gift,
	GlassWater,
	CakeSlice,
	Dessert,
	Sparkles,
];

// Define same fixed category icons mapping
const fixedCategoryIcons = {
	Classic: HeartHandshake,
	Gourmet: Sparkles,
	"Bars and Squares": CakeSlice,
	Sundaes: Dessert,
	Milkshakes: GlassWater,
	"Gift Box": Gift,
};

// Same random icons array for consistency
const randomIcons = [Coffee, Cookie, IceCream, Candy, Pizza, Cake];

// Same icon mapping function
const getCategoryIcon = (categoryName) => {
	if (fixedCategoryIcons[categoryName]) {
		return fixedCategoryIcons[categoryName];
	}

	const index = categoryName.split("").reduce((acc, char) => acc + char.charCodeAt(0), 0);
	return randomIcons[index % randomIcons.length];
};
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
						v-for="category in categories"
						:key="category.category_id"
						:href="route('category.show', { category: category.category_name })"
						componentName="Category/Show"
						:isActive="
							$page.component === 'Category/Show' &&
							$page.props.currentCategory === category.category_name
						"
					>
						<component :is="getCategoryIcon(category.category_name)" class="w-5 h-5" />
						{{ category.category_name }}
					</NavLink>
				</div>
			</nav>
		</SheetContent>
	</Sheet>
</template>
