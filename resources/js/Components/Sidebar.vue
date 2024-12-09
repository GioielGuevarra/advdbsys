<script setup>
import NavLink from "../Components/NavLink.vue";
import {
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
import { ref, onMounted, onUnmounted, computed } from "vue";

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
		default: () => [], // Add default value to prevent undefined errors
	},
});

// Define fixed category icons mapping
const fixedCategoryIcons = {
	Classic: HeartHandshake,
	Gourmet: Sparkles,
	"Bars and Squares": CakeSlice,
	Sundaes: Dessert,
	Milkshakes: GlassWater,
	"Gift Box": Gift,
};

// Create array of icons for random assignment (exclude the ones used in fixed mapping)
const randomIcons = [Coffee, Cookie, IceCream, Candy, Pizza, Cake];

// Update the icon mapping function
const getCategoryIcon = (categoryName) => {
	// First check if category has a fixed icon
	if (fixedCategoryIcons[categoryName]) {
		return fixedCategoryIcons[categoryName];
	}

	// If not, assign a random icon consistently
	const index = categoryName.split("").reduce((acc, char) => acc + char.charCodeAt(0), 0);
	return randomIcons[index % randomIcons.length];
};
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
				</nav>
			</div>
		</div>
	</div>
</template>
