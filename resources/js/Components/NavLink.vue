<script setup>
defineProps({
	routeName: String,
	size: String,
	href: String,
	isActive: {
		type: Boolean,
		default: false,
	},
});
</script>

<template>
	<!-- for sm screen -->
	<Link
		v-if="size === 'sm'"
		:href="href || route(routeName)"
		:class="[
			'text-sm flex items-center gap-4 rounded-lg px-3 py-3',
			isActive
				? 'bg-primary/10 text-primary'
				: 'text-muted-foreground hover:text-primary hover:bg-primary/10',
		]"
	>
		<slot />
	</Link>

	<!-- for md-lg screens -->
	<Link
		v-else
		:href="href || route(routeName)"
		:class="[
			'flex items-center gap-4 rounded-lg px-3 py-3 transition-all',
			isActive
				? 'bg-primary/10 text-primary'
				: 'text-muted-foreground hover:bg-primary/10 hover:text-primary',
		]"
	>
		<slot />
	</Link>
</template>
